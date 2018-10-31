<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image;

use Link;
use Language;
use Product;
use ImageType;
use Image;
use ImageManager;

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
    public function getProductImages(array $product, Language $language)
    {
        $productAttributeId = $product['id_product_attribute'];
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
            $imageToCombinations,
            $productAttributeId
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

        $filteredImages = array();

        foreach ($images as $image) {
            if (in_array($productAttributeId, $image['associatedVariants'])) {
                $filteredImages[] = $image;
            }
        }

        return (0 === count($filteredImages)) ? $images : $filteredImages;
    }

    /**
     * @param $object
     * @param int $id_image
     *
     * @return array|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getImage($object, $id_image)
    {
        if (!$id_image) {
            return null;
        }

        if (get_class($object) === 'Product') {
            $type = 'products';
            $getImageURL = 'getImageLink';
            $root = _PS_PROD_IMG_DIR_;
            $imageFolderPath = implode(DIRECTORY_SEPARATOR, array(
                rtrim($root, DIRECTORY_SEPARATOR),
                rtrim(Image::getImgFolderStatic($id_image), DIRECTORY_SEPARATOR),
            ));
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

        $extPath = $imageFolderPath . DIRECTORY_SEPARATOR . 'fileType';
        $ext = @file_get_contents($extPath) ?: 'jpg';

        $mainImagePath = implode(DIRECTORY_SEPARATOR, [
            $imageFolderPath,
            $id_image . '.' . $ext,
        ]);

        foreach ($image_types as $image_type) {
            $resizedImagePath = implode(DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $id_image . '-' . $image_type['name'] . '.' . $ext,
            ]);

            if (!file_exists($resizedImagePath)) {
                ImageManager::resize(
                    $mainImagePath,
                    $resizedImagePath,
                    (int) $image_type['width'],
                    (int) $image_type['height']
                );
            }

            $url = $this->link->$getImageURL(
                isset($object->link_rewrite) ? $object->link_rewrite : $object->name,
                $id_image,
                $image_type['name']
            );

            $urls[$image_type['name']] = [
                'url' => $url,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
            ];
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return array(
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => isset($object->meta_title) ? $object->meta_title : $object->name,
        );
    }

    /**
     * @param string $imageHash
     *
     * @return array
     */
    public function getCustomizationImage($imageHash)
    {
        $large_image_url = rtrim($this->link->getBaseLink(), '/') . '/upload/' . $imageHash;
        $small_image_url = $large_image_url . '_small';

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
     * @throws \PrestaShopDatabaseException
     */
    public function getNoPictureImage(Language $language)
    {
        $urls = [];
        $type = 'products';
        $image_types = ImageType::getImagesTypes($type, true);

        foreach ($image_types as $image_type) {
            $url = $this->link->getImageLink(
                '',
                $language->iso_code . '-default',
                $image_type['name']
            );

            $urls[$image_type['name']] = [
                'url' => $url,
                'width' => (int) $image_type['width'],
                'height' => (int) $image_type['height'],
            ];
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return array(
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
        );
    }
}
