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
use Configuration;
use Image;
use ImageManager;
use ImageType;
use Language;
use Link;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use Store;

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

        $images = $productInstance->getImages($language->id);

        if (empty($images)) {
            return [];
        }

        $combinationImages = $productInstance->getCombinationImages($language->id);
        if (!$combinationImages) {
            $combinationImages = [];
        }
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
            $image = array_merge($this->getImage(
                $productInstance,
                $image['id_image']
            ), $image);

            if (isset($imageToCombinations[$image['id_image']])) {
                $image['associatedVariants'] = $imageToCombinations[$image['id_image']];
            } else {
                $image['associatedVariants'] = [];
            }

            return $image;
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
     * @param Product|Store|Category $object
     * @param int $id_image
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

        if (get_class($object) === 'Product') {
            $type = 'products';
            $getImageURL = 'getImageLink';
            $root = _PS_PRODUCT_IMG_DIR_;
            $imageFolderPath = implode(DIRECTORY_SEPARATOR, [
                rtrim($root, DIRECTORY_SEPARATOR),
                rtrim(Image::getImgFolderStatic($id_image), DIRECTORY_SEPARATOR),
            ]);
        } elseif (get_class($object) === 'Store') {
            $type = 'stores';
            $getImageURL = 'getStoreImageLink';
            $root = _PS_STORE_IMG_DIR_;
            $imageFolderPath = rtrim($root, DIRECTORY_SEPARATOR);
        } else {
            $type = 'categories';
            $getImageURL = 'getCatImageLink';
            $root = _PS_CAT_IMG_DIR_;
            $imageFolderPath = rtrim($root, DIRECTORY_SEPARATOR);
        }

        $urls = [];
        $image_types = ImageType::getImagesTypes($type, true);
        $generateHighDpiImages = (bool) Configuration::get('PS_HIGHT_DPI');
        $ext = 'jpg';

        // Get path of original uploaded image we will use to get thumbnails
        $originalImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.' . $ext,
        ]);

        $rewriteLink = isset($object->link_rewrite) ? $object->link_rewrite : $object->name;
        foreach ($image_types as $image_type) {
            $additionalSources = [];
            // Final path thumbnail in our size
            $thumbnailPath = implode(DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $id_image . '-' . $image_type['name'] . '.' . $ext,
            ]);

            // Check if the thumbnail exists, if not, create it automatically on the fly
            if (!file_exists($thumbnailPath)) {
                ImageManager::resize(
                    $originalImagePath,
                    $thumbnailPath,
                    (int) $image_type['width'],
                    (int) $image_type['height']
                );
            }

            $generateAdditionalWebP = (bool) Configuration::get('PS_ADDITIONAL_IMAGE_QUALITY_WEBP');
            // We try to use the imageavif() function.
            // It can fail even if `function_exists('imageavif')` returns true.
            // @see https://stackoverflow.com/questions/71739530/php-8-1-imageavif-avif-image-support-has-been-disabled
            // @todo When this issue will be fixed on main OS (Debian, CentOS), we need to remove this patch
            /* try {
                $image = imagecreatetruecolor(250, 250);
                imageavif($image, 'test.avif');
            } catch {

            }*/
            $generateAdditionalAvif = version_compare(PHP_VERSION, '8.1') >= 0 && (bool) Configuration::get('PS_ADDITIONAL_IMAGE_QUALITY_AVIF') && function_exists('imageavif') && is_callable('imageavif');

            if ($generateAdditionalWebP) {
                $resizedImagePathWebP = implode(DIRECTORY_SEPARATOR, [
                    $imageFolderPath,
                    $id_image . '-' . $image_type['name'] . '.webp',
                ]);

                if (!file_exists($resizedImagePathWebP)) {
                    ImageManager::resize(
                        $originalImagePath,
                        $thumbnailPath,
                        (int) $image_type['width'],
                        (int) $image_type['height'],
                        'webp',
                        true
                    );
                }

                $additionalSources['webp'] = $this->link->$getImageURL($rewriteLink, $id_image, $image_type['name'], '.webp');
            }

            if (version_compare(PHP_VERSION, '8.1') >= 0 && $generateAdditionalAvif) {
                $resizedImagePathAvif = implode(DIRECTORY_SEPARATOR, [
                    $imageFolderPath,
                    $id_image . '-' . $image_type['name'] . '.avif',
                ]);

                if (!file_exists($resizedImagePathAvif)) {
                    ImageManager::resize(
                        $originalImagePath,
                        $thumbnailPath,
                        (int) $image_type['width'],
                        (int) $image_type['height'],
                        'avif',
                        true
                    );
                }

                $additionalSources['avif'] = $this->link->$getImageURL($rewriteLink, $id_image, $image_type['name'], '.avif');
            }

            /*
            * If High-DPI images are enabled, we will also generate a thumbnail in
            * double the size, so it can be used in src-sets.
            */
            if ($generateHighDpiImages) {
                $thumbnailPathHighDpi = implode(DIRECTORY_SEPARATOR, [
                    $imageFolderPath,
                    $id_image . '-' . $image_type['name'] . '2x.' . $ext,
                ]);
                if (!file_exists($thumbnailPathHighDpi)) {
                    ImageManager::resize(
                        $originalImagePath,
                        $thumbnailPathHighDpi,
                        (int) $image_type['width'] * 2,
                        (int) $image_type['height'] * 2
                    );
                }
            }

            // Thumbnail done, now let's generate it's seo-friendly URL and add it to our output
            // Primary (fake) image name is object rewrite, fallbacks are name and ID
            if (!empty($object->link_rewrite)) {
                $rewrite = $object->link_rewrite;
            } elseif (!empty($object->name)) {
                $rewrite = $object->name;
            } else {
                $rewrite = $id_image;
            }
            $url = $this->link->$getImageURL(
                $rewrite,
                $id_image,
                $image_type['name']
            );

            $urlJpg = $this->link->$getImageURL($rewriteLink, $id_image, $image_type['name']);
            $additionalSources['jpg'] = $urlJpg;

            $urls[$image_type['name']] = [
                'url' => $urlJpg,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
                'sources' => $additionalSources,
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
