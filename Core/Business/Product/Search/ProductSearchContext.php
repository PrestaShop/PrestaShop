<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchContext
{
    private $id_shop;
    private $id_lang;

    public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;
        return $this;
    }

    public function getIdShop()
    {
        return $this->id_shop;
    }

    public function setIdLang($id_lang)
    {
        $this->id_lang = $id_lang;
        return $this;
    }

    public function getIdLang()
    {
        return $this->id_lang;
    }
}
