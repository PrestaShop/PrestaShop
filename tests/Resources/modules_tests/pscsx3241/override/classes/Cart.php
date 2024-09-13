<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class Cart extends CartCore
{
    public $delivery_option;

    /** @var bool Allow to seperate order in multiple package in order to recieve as soon as possible the available products */
    public $allow_seperated_package = false;

    protected static $_nbProducts = [];
    protected static $_isVirtualCart = [];

    protected $_products = null;
    protected static $_totalWeight = [];
    protected $_taxCalculationMethod = PS_TAX_EXC;
    protected static $_carriers = null;
    protected static $_taxes_rate = null;
    protected static $_attributesLists = [];
    protected static $_customer = null;

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
