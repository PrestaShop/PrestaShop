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
use FeatureFlag;
use Image;
use ImageManager;
use ImageType;
use Language;
use Link;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
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

    private $isMultipleImageFormatFeatureActive;

    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->isMultipleImageFormatFeatureActive = FeatureFlag::isEnabled(FeatureFlagSettings::FEATURE_FLAG_MULTIPLE_IMAGE_FORMAT);
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

        // Get path of original uploaded image we will use to get thumbnails (original image is always .jpg)
        $originalImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.jpg',
        ]);

        $configuredImageFormats = explode(',', Configuration::get('PS_IMAGE_FORMAT'));
        $rewriteLink = !empty($object->link_rewrite) ? $object->link_rewrite : $object->name;
        foreach ($image_types as $image_type) {
            $additionalSources = [];

            // in legacy, jpg image is always generated
            if (!$this->isMultipleImageFormatFeatureActive) {
                $this->generateImageType($originalImagePath, $imageFolderPath, $id_image, $image_type, 'jpg');
                $additionalSources['jpg'] = $this->link->$getImageURL($rewriteLink, $id_image, $image_type['name'], '.jpg');
                $this->generateImageType($originalImagePath, $imageFolderPath, $id_image, $image_type, 'jpg', true);
            } else {
                foreach ($configuredImageFormats as $imageFormat) {
                    $this->generateImageType($originalImagePath, $imageFolderPath, $id_image, $image_type, $imageFormat);
                    $additionalSources[$imageFormat] = $this->link->$getImageURL($rewriteLink, $id_image, $image_type['name'], '.' . $imageFormat);

                    if ($generateHighDpiImages) {
                        $this->generateImageType($originalImagePath, $imageFolderPath, $id_image, $image_type, $imageFormat, true);
                    }
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
     * @param $originalImagePath
     * @param $imageFolderPath
     * @param $idImage
     * @param $imageTypeData
     * @param $ext
     * @param $hdpi
     *
     * @return void
     */
    private function generateImageType($originalImagePath, $imageFolderPath, $idImage, $imageTypeData, $ext, $hdpi = false)
    {
        $fileName = sprintf('%s-%s.%s', $idImage, $imageTypeData['name'], $ext);

        if ($hdpi) {
            $fileName = sprintf('%s-%s2x%s', $idImage, $imageTypeData['name'], $ext);
            $imageTypeData['width'] *= 2;
            $imageTypeData['height'] *= 2;
        }

        $resizedImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $fileName,
        ]);

        if (!file_exists($resizedImagePath)) {
            ImageManager::resize(
                $originalImagePath,
                $this->makeResizedDestinationPath($imageFolderPath, $idImage, $imageTypeData['name'], $ext, $hdpi),
                (int) $imageTypeData['width'],
                (int) $imageTypeData['height'],
                $ext,
                true
            );
        }
    }

    /**
     * @param string $imageFolderPath
     * @param $idImage
     * @param string $imageType
     * @param string $extension
     * @param $hdpi
     *
     * @return string
     */
    private function makeResizedDestinationPath(string $imageFolderPath, $idImage, string $imageType, string $extension, $hdpi = false): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            sprintf('%s-%s%s.%s', $idImage, $imageType, $hdpi ? '2x' : '', $extension),
        ]);
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
