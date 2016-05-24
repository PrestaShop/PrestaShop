<?php

class Cart extends CartCore
{
    public $delivery_option;

    /** @var bool Allow to seperate order in multiple package in order to recieve as soon as possible the available products */
    public $allow_seperated_package = false;

    protected static $_nbProducts = array();
    protected static $_isVirtualCart = array();

    protected $_products = null;
    protected static $_totalWeight = array();
    protected $_taxCalculationMethod = PS_TAX_EXC;
    protected static $_carriers = null;
    protected static $_taxes_rate = null;
    protected static $_attributesLists = array();
    protected static $_customer = null;

    public static function deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0)
    {
        $result = Hook::exec('ppbsDeleteCartProduct', array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_customization' => $id_customization,
                'id_address_delivery' => $id_address_delivery,
            ),
            null, false);
        if ($result == false) {
            parent::deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0);
        }
    }

    protected function _getProducts($refresh = false, $id_product = false, $id_country = null)
    {
        $products = parent::getProducts($refresh, $id_product, $id_country);

        if (_PS_VERSION_ >= 1.6) {
            $params = Hook::exec('ppbsGetProducts', array('products'=>$products), null, true);
            if (isset($params['productpricebysize']['products'])) {
                return $params['productpricebysize']['products'];
            } else {
                return $products;
            }
        } else {
            $params = Hook::exec('ppbsGetProducts', array('products'=>$products), null);
            $params = Tools::jsonDecode($params, true);
            if (isset($params['products'])) {
                return $params['products'];
            } else {
                return $products;
            }
        }
    }
}
