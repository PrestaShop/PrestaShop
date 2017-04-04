<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Configuration $object
 */
class AdminPPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        $warehouse_list = Warehouse::getWarehouses();
        $warehouse_no = array(array('id_warehouse' => 0,'name' => $this->trans('No default warehouse (default setting)', array(), 'Admin.Shopparameters.Feature')));
        $warehouse_list = array_merge($warehouse_no, $warehouse_list);

        $this->fields_options = array(
            'products' => array(
                'title' =>    $this->trans('Products (general)', array(), 'Admin.Shopparameters.Feature'),
                'fields' =>    array(
                    'PS_CATALOG_MODE' => array(
                        'title' => $this->trans('Catalog mode', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('When active, all shopping features will be disabled.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                    'PS_NB_DAYS_NEW_PRODUCT' => array(
                        'title' => $this->trans('Number of days for which the product is considered \'new\'', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isUnsignedInt',
                        'cast' => 'intval',
                        'type' => 'text'
                    ),
                    'PS_PRODUCT_SHORT_DESC_LIMIT' => array(
                        'title' => $this->trans('Max size of product summary', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Set the maximum size of the summary of your product description (in characters).', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('characters', array(), 'Admin.Shopparameters.Help'),
                    ),
                    'PS_QTY_DISCOUNT_ON_COMBINATION' => array(
                        'title' => $this->trans('Quantity discounts based on', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('How to calculate quantity discounts.', array(), 'Admin.Shopparameters.Help'),
                        'cast' => 'intval',
                        'show' => true,
                        'required' => false,
                        'type' => 'radio',
                        'validation' => 'isBool',
                        'choices' => array(
                            0 => $this->trans('Products', array(), 'Admin.Global'),
                            1 => $this->trans('Combinations', array(), 'Admin.Catalog.Feature')
                        )
                    ),
                    'PS_FORCE_FRIENDLY_PRODUCT' => array(
                        'title' => $this->trans('Force update of friendly URL', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('When active, friendly URL will be updated on every save.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                    'PS_PRODUCT_ACTIVATION_DEFAULT' => array(
                        'title' => $this->trans('Default activation status', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('When active, new products will be activated by default during creation.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'order_by_pagination' => array(
                'title' =>    $this->trans('Pagination', array(), 'Admin.Shopparameters.Feature'),
                'fields' =>    array(
                    'PS_PRODUCTS_PER_PAGE' => array(
                        'title' => $this->trans('Products per page', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Number of products displayed per page. Default is 10.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isUnsignedInt',
                        'cast' => 'intval',
                        'type' => 'text'
                    ),
                    'PS_PRODUCTS_ORDER_BY' => array(
                        'title' => $this->trans('Default order by', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('The order in which products are displayed in the product list.', array(), 'Admin.Shopparameters.Help'),
                        'type' => 'select',
                        'list' => array(
                            array('id' => '0', 'name' => $this->trans('Product name', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '1', 'name' => $this->trans('Product price', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '2', 'name' => $this->trans('Product add date', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '3', 'name' => $this->trans('Product modified date', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '4', 'name' => $this->trans('Position inside category', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '5', 'name' => $this->trans('Brand', array(), 'Admin.Global')),
                            array('id' => '6', 'name' => $this->trans('Product quantity', array(), 'Admin.Shopparameters.Feature')),
                            array('id' => '7', 'name' => $this->trans('Product reference', array(), 'Admin.Shopparameters.Feature'))
                        ),
                        'identifier' => 'id'
                    ),
                    'PS_PRODUCTS_ORDER_WAY' => array(
                        'title' => $this->trans('Default order method', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Default order method for product list.', array(), 'Admin.Shopparameters.Help'),
                        'type' => 'select',
                        'list' => array(
                            array(
                                'id' => '0',
                                'name' => $this->trans('Ascending', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => '1',
                                'name' => $this->trans('Descending', array(), 'Admin.Global')
                            )
                        ),
                        'identifier' => 'id'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'fo_product_page' => array(
                'title' =>    $this->trans('Product page', array(), 'Admin.Shopparameters.Feature'),
                'fields' =>    array(
                    'PS_DISPLAY_QTIES' => array(
                        'title' => $this->trans('Display available quantities on the product page', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                    'PS_LAST_QTIES' => array(
                        'title' => $this->trans('Display remaining quantities when the quantity is lower than', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Set to "0" to disable this feature.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isUnsignedId',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text'
                    ),
                    'PS_DISP_UNAVAILABLE_ATTR' => array(
                        'title' => $this->trans('Display unavailable product attributes on the product page', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                    'PS_ATTRIBUTE_CATEGORY_DISPLAY' => array(
                        'title' => $this->trans('Display the "add to cart" button when a product has attributes', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Display or hide the "add to cart" button on category pages for products that have attributes forcing customers to see product details.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_ATTRIBUTE_ANCHOR_SEPARATOR' => array(
                        'title' => $this->trans('Separator of attribute anchor on the product links', array(), 'Admin.Shopparameters.Feature'),
                        'type' => 'select',
                        'list' => array(
                            array('id' => '-', 'name' => '-'),
                            array('id' => ',', 'name' => ','),
                        ),
                        'identifier' => 'id'
                    ),
                    'PS_DISPLAY_DISCOUNT_PRICE' => array(
                        'title' => $this->trans('Display discounted price', array(), 'Admin.Shopparameters.Feature'),
                        'desc' => $this->trans('In the volume discounts board, display the new price with the applied discount instead of showing the discount (ie. "-5%").', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'stock' => array(
                'title' =>    $this->trans('Products stock', array(), 'Admin.Shopparameters.Feature'),
                'fields' =>    array(
                    'PS_ORDER_OUT_OF_STOCK' => array(
                        'title' => $this->trans('Allow ordering of out-of-stock products', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('By default, the Add to Cart button is hidden when a product is unavailable. You can choose to have it displayed in all cases.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool'
                    ),
                    'PS_STOCK_MANAGEMENT' => array(
                        'title' => $this->trans('Enable stock management', array(), 'Admin.Shopparameters.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool',
                        'js' => array(
                            'on' => 'onchange="stockManagementActivationAuthorization()"',
                            'off' => 'onchange="stockManagementActivationAuthorization()"'
                        )
                    ),
                    /*'PS_ADVANCED_STOCK_MANAGEMENT' => array(
                        'title' => $this->trans('Enable advanced stock management'),
                        'hint' => $this->trans('Allows you to manage physical stock, warehouses and supply orders in a new Stock menu.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool',
                        'visibility' => Shop::CONTEXT_ALL,
                        'js' => array(
                            'on' => 'onchange="advancedStockManagementActivationAuthorization()"',
                            'off' => 'onchange="advancedStockManagementActivationAuthorization()"'
                        )
                    ),
                    'PS_FORCE_ASM_NEW_PRODUCT' => array(
                        'title' => $this->trans('New products use advanced stock management'),
                        'hint' => $this->trans('New products will automatically use advanced stock management and depends on stock, but no warehouse will be selected'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => false,
                        'type' => 'bool',
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                    'PS_DEFAULT_WAREHOUSE_NEW_PRODUCT' => array(
                        'title' => $this->trans('Default warehouse on new products'),
                        'hint' => $this->trans('Automatically set a default warehouse when new product is created'),
                        'type' => 'select',
                        'list' => $warehouse_list,
                        'identifier' => 'id_warehouse'
                    ),*/
                    'PS_PACK_STOCK_TYPE' => array(
                        'title' =>  $this->trans('Default pack stock management', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('When selling packs of products, how do you want your stock to be calculated?', array(), 'Admin.Shopparameters.Help'),
                        'type' => 'select',
                        'list' =>array(
                            array(
                                'pack_stock' => 0,
                                'name' => $this->trans('Decrement pack only.', array(), 'Admin.Shopparameters.Feature')
                            ),
                            array(
                                'pack_stock' => 1,
                                'name' => $this->trans('Decrement products in pack only.', array(), 'Admin.Shopparameters.Feature')
                            ),
                            array(
                                'pack_stock' => 2,
                                'name' => $this->trans('Decrement both.', array(), 'Admin.Shopparameters.Feature')
                            ),
                        ),
                        'identifier' => 'pack_stock',
                    ),
                ),
                'bottom' => '<script type="text/javascript">stockManagementActivationAuthorization();advancedStockManagementActivationAuthorization();</script>',
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
        );
    }

    public function beforeUpdateOptions()
    {
        if (!Tools::getValue('PS_STOCK_MANAGEMENT', true)) {
            $_POST['PS_ORDER_OUT_OF_STOCK'] = 1;
            $_POST['PS_DISPLAY_QTIES'] = 0;
        }

        // if advanced stock management is disabled, updates concerned tables
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 1 && (int)Tools::getValue('PS_ADVANCED_STOCK_MANAGEMENT') == 0) {
            $id_shop_list = Shop::getContextListShopID();
            $sql_shop = 'UPDATE `'._DB_PREFIX_.'product_shop` SET `advanced_stock_management` = 0 WHERE
			`advanced_stock_management` = 1 AND (`id_shop` = '.implode(' OR `id_shop` = ', $id_shop_list).')';

            $sql_stock = 'UPDATE `'._DB_PREFIX_.'stock_available` SET `depends_on_stock` = 0, `quantity` = 0
					 WHERE `depends_on_stock` = 1 AND (`id_shop` = '.implode(' OR `id_shop` = ', $id_shop_list).')';

            $sql = 'UPDATE `'._DB_PREFIX_.'product` SET `advanced_stock_management` = 0 WHERE
			`advanced_stock_management` = 1 AND (`id_shop_default` = '.implode(' OR `id_shop_default` = ', $id_shop_list).')';

            Db::getInstance()->execute($sql_shop);
            Db::getInstance()->execute($sql_stock);
            Db::getInstance()->execute($sql);
        }

        if (Tools::getIsset('PS_CATALOG_MODE')) {
            Tools::clearSmartyCache();
            Media::clearCache();
        }
    }
}
