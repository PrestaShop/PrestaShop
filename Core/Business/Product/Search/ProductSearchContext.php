<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class ProductSearchContext
{
    private $id_shop;
    private $id_lang;
    private $id_currency;
    private $id_customer;

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

    public function setIdCurrency($id_currency)
    {
        $this->id_currency = $id_currency;
        return $this;
    }

    public function getIdCurrency()
    {
        return $this->id_currency;
    }

    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
        return $this;
    }

    public function getIdCustomer()
    {
        return $this->id_customer;
    }
}
