<?php

class Cart extends CartCore
{
    /*
    * module: pscsx32412
    * date: 2018-12-26 14:14:05
    * version: 1
    */
    public function updateAddressId($id_address, $id_address_new)
    {
        $to_update = false;
        if (!isset($this->id_address_invoice) || $this->id_address_invoice == $id_address) {
            $to_update = true;
            $this->id_address_invoice = $id_address_new;
        }
        if (!isset($this->id_address_delivery) || $this->id_address_delivery == $id_address) {
            $to_update = true;
            $this->id_address_delivery = $id_address_new;
        }
        if ($to_update) {
            $this->update();
        }
        Db::getInstance()->execute($sql);
    }

    /*
    * module: pscsx32412
    * date: 2018-12-26 14:14:05
    * version: 1
    */
    public function delete()
    {
        if ($this->OrderExists()) { //NOT delete a cart which is associated with an order
            return false;
        }
        $uploaded_files = Db::getInstance()->executeS(
            '
			SELECT cd.`value`
			FROM `' . _DB_PREFIX_ . 'customized_data` cd
			INNER JOIN `' . _DB_PREFIX_ . 'customization` c ON (cd.`id_customization`= c.`id_customization`)
			WHERE cd.`type`= 0 AND c.`id_cart`=' . (int) $this->id
        );
        foreach ($uploaded_files as $must_unlink) {
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value'] . '_small');
            unlink(_PS_UPLOAD_DIR_ . $must_unlink['value']);
        }
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
			WHERE `id_customization` IN (
				SELECT `id_customization`
				FROM `' . _DB_PREFIX_ . 'customization`
				WHERE `id_cart`=' . (int) $this->id . '
			)'
        );
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'customization`
			WHERE `id_cart` = ' . (int) $this->id
        );
        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `id_cart` = ' . (int) $this->id)
         || !Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $this->id)) {
            return false;
        }

        return parent::delete();
    }

    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    public $delivery_option;

    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    public $allow_seperated_package = false;
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_nbProducts = [];
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_isVirtualCart = [];
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected $_products = null;
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_totalWeight = [];
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected $_taxCalculationMethod = PS_TAX_EXC;
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_carriers = null;
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_taxes_rate = null;
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_attributesLists = [];
    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected static $_customer = null;

    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    public function deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0)
    {
        $result = Hook::exec(
            'ppbsDeleteCartProduct',
            [
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_customization' => $id_customization,
                'id_address_delivery' => $id_address_delivery,
            ],
            null,
            false
        );
        if ($result == false) {
            parent::deleteProduct($id_product, $id_product_attribute = null, $id_customization = null, $id_address_delivery = 0);
        }
    }

    /*
    * module: pscsx3241
    * date: 2018-12-26 14:14:06
    * version: 1
    */
    protected function _getProducts($refresh = false, $id_product = false, $id_country = null)
    {
        $products = parent::getProducts($refresh, $id_product, $id_country);
        if (_PS_VERSION_ >= 1.6) {
            $params = Hook::exec('ppbsGetProducts', ['products' => $products], null, true);
            if (isset($params['productpricebysize']['products'])) {
                return $params['productpricebysize']['products'];
            } else {
                return $products;
            }
        } else {
            $params = Hook::exec('ppbsGetProducts', ['products' => $products], null);
            $params = json_decode($params, true);
            if (isset($params['products'])) {
                return $params['products'];
            } else {
                return $products;
            }
        }
    }
}
