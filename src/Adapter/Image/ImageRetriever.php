<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Image;

use Category;
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
    public function getAllProductImages(array $product, Language $language)
    {
        $productInstance = new Product(
            $product['id_product'],
            false,
            $language->id
        );

        // Get all product images that are related to this object
        $images = $productInstance->getImages($language->id);
        if (empty($images)) {
            return [];
        }

        // Load all pairs of images assigned to combinations
        $combinationImages = $productInstance->getCombinationImages($language->id);
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

        $urls = [];

        // Get path of original uploaded image we will use to get thumbnails (original image extension is always .jpg)
        $originalImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.jpg',
        ]);

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

        // For JPG images, we let Imagemanager decide what to do and choose between JPG/PNG.
        // For webp and avif extensions, we want it to follow our command and ignore the original format.
        $forceFormat = ($imageFormat !== 'jpg');

        // Check if the thumbnail exists and generate it if needed
        if (!file_exists($resizedImagePath)) {
            ImageManager::resize(
                $originalImagePath,
                $resizedImagePath,
                (int) $imageTypeData['width'],
                (int) $imageTypeData['height'],
                $imageFormat,
                $forceFormat
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
        $type = 'products';
        $imageTypes = ImageType::getImagesTypes($type, true);

        if (empty($imageTypes)) {
            throw new PrestaShopException(sprintf('There is no image type defined for "%s".', $type));
        }

        foreach ($imageTypes as $imageType) {
            $url = $this->link->getImageLink(
                '',
                $language->iso_code . '-default',
                $imageType['name']
            );

            $urls[$imageType['name']] = [
                'url' => $url,
                'width' => (int) $imageType['width'],
                'height' => (int) $imageType['height'],
            ];
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
