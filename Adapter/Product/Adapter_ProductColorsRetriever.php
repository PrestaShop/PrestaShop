<?php

class Adapter_ProductColorsRetriever
{
    public function getColoredVariants($id_product)
    {
        return current(Product::getAttributesColorList([$id_product]));
    }
}
