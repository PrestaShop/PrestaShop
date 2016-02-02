<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use Product;

class ProductColorsRetriever
{
    public function getColoredVariants($id_product)
    {
        return (is_array(Product::getAttributesColorList([$id_product]))) ? current(Product::getAttributesColorList([$id_product])) : null;
    }
}
