<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchQuery
{
    private $id_category;

    public function setIdCategory($id_category)
    {
        $this->id_category = $id_category;
        return $this;
    }

    public function getIdCategory()
    {
        return $this->id_category;
    }

    public function setSortOption(SortOption $option)
    {
        $this->sortOption = $sortOption;
        return $this;
    }
}
