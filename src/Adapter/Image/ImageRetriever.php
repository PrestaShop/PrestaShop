<?php

namespace PrestaShop\PrestaShop\Adapter\Image;

use Link;
use Language;
use Product;
use ImageType;

class ImageRetriever
{
    private $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

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
            $image =  array_merge($this->getImage(
                $productInstance,
                $image['id_image']
            ), $image);

            if (isset($imageToCombinations[$image['id_image']])) {
                $image['associatedVariants'] = $imageToCombinations[$image['id_image']];
            } else {
                $image['associatedVariants'] = [];
            }

            if (in_array($productAttributeId, $image['associatedVariants'])) {
                return $image;
            }
        }, $images);

        return array_filter($images);
    }

    public function getImage($object, $id_image)
    {
        if (!$id_image) {
            return null;
        }

        if (get_class($object) === 'Product') {
            $type = 'products';
            $getImageURL = 'getImageLink';
        } else {
            $type = 'categories';
            $getImageURL = 'getCatImageLink';
        }

        $urls  = [];
        $image_types = ImageType::getImagesTypes($type, true);

        foreach ($image_types as $image_type) {
            $url = $this->link->$getImageURL(
                $object->link_rewrite,
                $id_image,
                $image_type['name']
            );

            $urls[$image_type['name']] = [
                'url'      => $url,
                'width'     => (int)$image_type['width'],
                'height'    => (int)$image_type['height'],
            ];
        }

        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        $keys = array_keys($urls);

        $small  = $urls[$keys[0]];
        $large  = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return array(
            'bySize' => $urls,
            'small'  => $small,
            'medium' => $medium,
            'large'  => $large,
            'legend' => $object->meta_title,
        );
    }

    public function getCustomizationImage($imageHash)
    {
        $large_image_url = rtrim($this->link->getBaseLink(), '/') . '/upload/' . $imageHash;
        $small_image_url = $large_image_url . '_small';

        $small = [
            'url' => $small_image_url
        ];

        $large = [
            'url' => $large_image_url
        ];

        $medium = $large;

        return [
            'bySize' => [
                'small' => $small,
                'medium' => $medium,
                'large' => $large
            ],
            'small'  => $small,
            'medium' => $medium,
            'large'  => $large,
            'legend' => ''
        ];
    }
}
