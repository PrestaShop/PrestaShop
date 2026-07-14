<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Image;

use Category;
use Combination;
use Configuration;
use Db;
use Image;
use ImageManager;
use ImageType;
use Language;
use Link;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use Shop;
use Store;
use Supplier;

/**
 * This class is mainly responsible of Product images.
 */
class ImageRetriever
{
    /**
     * @var Link
     */
    private $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * @param array $product
     * @param Language $language
     *
     * @return array
     */
    public function getAllProductImages(array $product, Language $language, ?array $prefetchedImages = null, ?array $prefetchedCombinationImages = null)
    {
        $productInstance = new Product(
            $product['id_product'],
            false,
            $language->id
        );

        // Get all product images that are related to this object.
        // WHY: when the caller already loaded the image rows in bulk (see getProductsImages),
        // reuse them instead of running one getImages() query per product on a listing.
        $images = $prefetchedImages ?? $productInstance->getImages($language->id);
        if (empty($images)) {
            return [];
        }

        // Load all pairs of images assigned to combinations (bulk-provided by the caller when available).
        $combinationImages = $prefetchedCombinationImages ?? $productInstance->getCombinationImages($language->id);
        if (!$combinationImages) {
            $combinationImages = [];
        }

        // And resolve them by id_image
        // We can't assign them directly because the $images array keys are not id_image
        $imageToCombinations = [];
        foreach ($combinationImages as $imgs) {
            foreach ($imgs as $img) {
                $imageToCombinations[$img['id_image']][] = $img['id_product_attribute'];
            }
        }

        $images = array_map(function (array $image) use (
            $productInstance,
            $imageToCombinations
        ) {
            // Now let's fetch extra information about thumbnail sizes etc. and add this information.
            $finalImage = array_merge(
                $image,
                $this->getImage($productInstance, $image['id_image'])
            );

            // The only special thing we can't just merge is the legend.
            // If there is a legend on the image object, we will use it.
            // If not, we keep the one we got from getImage method (product name).
            if (!empty($image['legend'])) {
                $finalImage['legend'] = $image['legend'];
            }

            // Assign a list of variants related to the given image
            if (isset($imageToCombinations[$image['id_image']])) {
                $finalImage['associatedVariants'] = $imageToCombinations[$image['id_image']];
            } else {
                $finalImage['associatedVariants'] = [];
            }

            return $finalImage;
        }, $images);

        return $images;
    }

    /**
     * Load, for a whole set of products, the same image data that {@see getAllProductImages}
     * returns for a single one - but with the per-product image and combination-image queries
     * collapsed into one query each. Used by the product listing presenter to avoid an N+1.
     *
     * @param int[] $productIds
     * @param Language $language
     *
     * @return array<int, array> Image lists keyed by product identifier (identical shape to getAllProductImages)
     */
    public function getProductsImages(array $productIds, Language $language): array
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        if (empty($productIds)) {
            return [];
        }

        $imagesByProduct = $this->getImageRowsForProducts($productIds, (int) $language->id);
        $combinationImagesByProduct = $this->getCombinationImageRowsForProducts($productIds, (int) $language->id);

        $result = [];
        foreach ($productIds as $idProduct) {
            // A product with no image rows resolves to no images without loading the Product object.
            if (empty($imagesByProduct[$idProduct])) {
                $result[$idProduct] = [];

                continue;
            }

            $result[$idProduct] = $this->getAllProductImages(
                ['id_product' => $idProduct],
                $language,
                $imagesByProduct[$idProduct],
                $combinationImagesByProduct[$idProduct] ?? []
            );
        }

        return $result;
    }

    /**
     * Batched equivalent of Product::getImages() for several products at once.
     *
     * @param int[] $productIds
     *
     * @return array<int, array> Image rows keyed by product identifier
     */
    private function getImageRowsForProducts(array $productIds, int $idLang): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT i.`id_product`, image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
            FROM `' . _DB_PREFIX_ . 'image` i
            ' . Shop::addSqlAssociation('image', 'i') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . $idLang . ')
            WHERE i.`id_product` IN (' . implode(',', $productIds) . ')
            ORDER BY i.`id_product`, `position`'
        ) ?: [];

        $byProduct = [];
        foreach ($rows as $row) {
            $idProduct = (int) $row['id_product'];
            unset($row['id_product']);
            $byProduct[$idProduct][] = $row;
        }

        return $byProduct;
    }

    /**
     * Batched equivalent of Product::getCombinationImages() for several products at once.
     *
     * @param int[] $productIds
     *
     * @return array<int, array> Combination-image rows grouped by id_product_attribute, keyed by product identifier
     */
    private function getCombinationImageRowsForProducts(array $productIds, int $idLang): array
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }

        $rows = Db::getInstance()->executeS(
            'SELECT i.`id_product`, pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
            INNER JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE i.`id_product` IN (' . implode(',', $productIds) . ') AND il.`id_lang` = ' . $idLang . '
            ORDER BY i.`id_product`, i.`position`'
        ) ?: [];

        $byProduct = [];
        foreach ($rows as $row) {
            $idProduct = (int) $row['id_product'];
            unset($row['id_product']);
            $byProduct[$idProduct][$row['id_product_attribute']][] = $row;
        }

        return $byProduct;
    }

    /**
     * @param array $product
     * @param Language $language
     *
     * @return array
     */
    public function getProductImages(array $product, Language $language)
    {
        $images = $this->getAllProductImages($product, $language);

        $productAttributeId = $product['id_product_attribute'];
        $filteredImages = [];

        foreach ($images as $image) {
            if (in_array($productAttributeId, $image['associatedVariants'])) {
                $filteredImages[] = $image;
            }
        }

        return (0 === count($filteredImages)) ? $images : $filteredImages;
    }

    /**
     * @param Product|Store|Category|Manufacturer|Supplier $object
     * @param int|string $id_image Identifier of the image
     *
     * @return array|null
     *
     * @throws PrestaShopDatabaseException
     */
    public function getImage($object, $id_image)
    {
        if (!$id_image) {
            return null;
        }

        // Resolve functions we will use to get image links from Link class
        if ($object::class === 'Product') {
            $type = 'products';
            $getImageURL = 'getImageLink';
            // Product images are the only exception in path structure, they are placed in folder
            // tree according to their ID.
            $imageFolderPath = implode(DIRECTORY_SEPARATOR, [
                rtrim(_PS_PRODUCT_IMG_DIR_, DIRECTORY_SEPARATOR),
                rtrim(Image::getImgFolderStatic($id_image), DIRECTORY_SEPARATOR),
            ]);
        } elseif ($object::class === 'Store') {
            $type = 'stores';
            $getImageURL = 'getStoreImageLink';
            $imageFolderPath = rtrim(_PS_STORE_IMG_DIR_, DIRECTORY_SEPARATOR);
        } elseif ($object::class === 'Manufacturer') {
            $type = 'manufacturers';
            $getImageURL = 'getManufacturerImageLink';
            $imageFolderPath = rtrim(_PS_MANU_IMG_DIR_, DIRECTORY_SEPARATOR);
        } elseif ($object::class === 'Supplier') {
            $type = 'suppliers';
            $getImageURL = 'getSupplierImageLink';
            $imageFolderPath = rtrim(_PS_SUPP_IMG_DIR_, DIRECTORY_SEPARATOR);
        } else {
            $type = 'categories';
            $getImageURL = 'getCatImageLink';
            $imageFolderPath = rtrim(_PS_CAT_IMG_DIR_, DIRECTORY_SEPARATOR);
        }

        // Get path of original uploaded image we will use to get thumbnails (original image extension is always .jpg)
        $originalImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.jpg',
        ]);

        $urls = [];

        /*
         * Let's resolve which formats we will use for image generation.
         *
         * In case of .jpg images, the actual format inside is decided by ImageManager.
         */
        $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();

        // Primary (fake) image name is object rewrite, fallbacks are name and ID
        if (!empty($object->link_rewrite)) {
            $rewrite = $object->link_rewrite;
        } elseif (!empty($object->name)) {
            $rewrite = $object->name;
        } else {
            $rewrite = $id_image;
        }

        // Check and generate each thumbnail size
        $image_types = ImageType::getImagesTypes($type, true);
        foreach ($image_types as $image_type) {
            $sources = [];
            $formattedName = ImageType::getFormattedName('small');

            if ($type === 'categories' && $formattedName === $image_type['name']) {
                $originalFileName = $id_image . '_thumb.jpg';
            } else {
                $originalFileName = $id_image . '.jpg';
            }

            // Get path of original uploaded image we will use to get thumbnails (original image extension is always .jpg)
            $originalImagePath = implode(DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $originalFileName,
            ]);

            foreach ($configuredImageFormats as $imageFormat) {
                // Generate the thumbnail
                $this->checkOrGenerateImageType($originalImagePath, $imageFolderPath, $id_image, $image_type, $imageFormat);

                // Get the URL of the thumb and add it to sources
                // Manufacturer and supplier use only IDs
                if ($object::class === 'Manufacturer' || $object::class === 'Supplier') {
                    $sources[$imageFormat] = $this->link->$getImageURL($id_image, $image_type['name'], $imageFormat);
                // Products, categories and stores pass both rewrite and ID
                } else {
                    $sources[$imageFormat] = $this->link->$getImageURL($rewrite, $id_image, $image_type['name'], $imageFormat);
                }
            }

            // Let's resolve the base image URL we will use
            if (isset($sources['jpg'])) {
                $baseUrl = $sources['jpg'];
            } elseif (isset($sources['png'])) {
                $baseUrl = $sources['png'];
            } else {
                $baseUrl = reset($sources);
            }

            // And add this size to our list
            $urls[$image_type['name']] = [
                'url' => $baseUrl,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
                'sources' => $sources,
            ];
        }

        // Sort thumbnails by size
        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        // Resolve some basic sizes - the smallest, middle and largest
        $keys = array_keys($urls);
        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return [
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => !empty($object->meta_title) ? $object->meta_title : $object->name,
            'id_image' => $id_image,
        ];
    }

    /**
     * @param string $originalImagePath
     * @param string $imageFolderPath
     * @param int|string $idImage
     * @param array $imageTypeData
     * @param string $imageFormat
     *
     * @return void
     */
    private function checkOrGenerateImageType(string $originalImagePath, string $imageFolderPath, int|string $idImage, array $imageTypeData, string $imageFormat)
    {
        $fileName = sprintf('%s-%s.%s', $idImage, $imageTypeData['name'], $imageFormat);
        $resizedImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $fileName,
        ]);

        // Check if the thumbnail exists and generate it if needed
        if (!file_exists($resizedImagePath)) {
            ImageManager::resize(
                $originalImagePath,
                $resizedImagePath,
                (int) $imageTypeData['width'],
                (int) $imageTypeData['height'],
                $imageFormat
            );
        }
    }

    /**
     * @param string $imageHash
     *
     * @return array
     */
    public function getCustomizationImage($imageHash)
    {
        $large_image_url = $this->link->getPageLink('upload', null, null, ['file' => $imageHash]);
        $small_image_url = $this->link->getPageLink('upload', null, null, ['file' => $imageHash . '_small']);

        $small = [
            'url' => $small_image_url,
        ];

        $large = [
            'url' => $large_image_url,
        ];

        $medium = $large;

        return [
            'bySize' => [
                'small' => $small,
                'medium' => $medium,
                'large' => $large,
            ],
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
        ];
    }

    /**
     * @param Language $language
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException if the image type is not found
     */
    public function getNoPictureImage(Language $language)
    {
        $urls = [];

        // Set images to regenerate with all theirs specific directories
        $objectsToRegenerate = [
            ['type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_],
            ['type' => 'manufacturers', 'dir' => _PS_MANU_IMG_DIR_],
            ['type' => 'suppliers', 'dir' => _PS_SUPP_IMG_DIR_],
            ['type' => 'products', 'dir' => _PS_PRODUCT_IMG_DIR_],
            ['type' => 'stores', 'dir' => _PS_STORE_IMG_DIR_],
        ];

        foreach ($objectsToRegenerate as $object) {
            // Get all image types present on shops for this object
            $imageTypes = ImageType::getImagesTypes($object['type'], true);

            // We get the "no image available" in the folder of the object
            $originalImagePath = implode(DIRECTORY_SEPARATOR, [
                rtrim($object['dir'], DIRECTORY_SEPARATOR),
                $language->getIsoCode() . '.jpg',
            ]);

            if (!file_exists($originalImagePath)) {
                // If it doesn't exist, we use an image for default language
                $originalImagePath = implode(DIRECTORY_SEPARATOR, [
                    rtrim($object['dir'], DIRECTORY_SEPARATOR),
                    Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT')) . '.jpg',
                ]);

                if (!file_exists($originalImagePath)) {
                    // If it doesn't exist, we use a fallback one in the root of img directory
                    $originalImagePath = implode(DIRECTORY_SEPARATOR, [
                        rtrim(_PS_IMG_DIR_, DIRECTORY_SEPARATOR),
                        'noimageavailable.jpg',
                    ]);
                }
            }

            // Get all image sizes for product objects
            foreach ($imageTypes as $imageType) {
                // Get path of the final thumbnail
                $resizedImagePath = implode(DIRECTORY_SEPARATOR, [
                    rtrim($object['dir'], DIRECTORY_SEPARATOR),
                    $language->getIsoCode() . '-default-' . $imageType['name'] . '.jpg',
                ]);

                // Check if the thumbnail exists and generate it if needed
                if (!file_exists($resizedImagePath)) {
                    ImageManager::resize(
                        $originalImagePath,
                        $resizedImagePath,
                        (int) $imageType['width'],
                        (int) $imageType['height']
                    );
                }

                // Build image URL for that thumbnail
                $imageUrl = $this->link->getImageLink(
                    '',
                    $language->iso_code . '-default',
                    $imageType['name']
                );

                // And add it to the list
                $urls[$imageType['name']] = [
                    'url' => $imageUrl,
                    'width' => (int) $imageType['width'],
                    'height' => (int) $imageType['height'],
                ];
            }
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return [
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
        ];
    }
}
