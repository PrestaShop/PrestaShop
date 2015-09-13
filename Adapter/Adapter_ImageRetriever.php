<?php

class Adapter_ImageRetriever
{
    private $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function getProductImages(Product $product, Language $language)
    {
        $images = $product->getImages($language->id);

        return array_map(function (array $image) use ($product) {
            $image =  array_merge($image, $this->getImage(
                $product,
                $image['id_image']
            ));

            return $image;
        }, $images);
    }

    public function getImage($object, $id_image)
    {
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

        return [
            'bySize' => $urls,
            'small'  => $small,
            'medium' => $medium,
            'large'  => $large,
            'legend' => $object->meta_title
        ];
    }
}
