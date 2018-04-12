<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

@ini_set('max_execution_time', 0);
/** No max line limit since the lines can be more than 4096. Performance impact is not significant. */
define('MAX_LINE_SIZE', 0);

/** Used for validatefields diying without user friendly error or not */
define('UNFRIENDLY_ERROR', false);

/** this value set the number of columns visible on each page */
define('MAX_COLUMNS', 6);

/** correct Mac error on eof */
@ini_set('auto_detect_line_endings', '1');

class AdminImportControllerCore extends AdminController
{
    public static $column_mask;

    public $entities = array();

    public $available_fields = array();

    public $required_fields = array();

    public static $default_values = array();

    public static $validators = array(
        'active' => array('AdminImportController', 'getBoolean'),
        'tax_rate' => array('AdminImportController', 'getPrice'),
        /** Tax excluded */
        'price_tex' => array('AdminImportController', 'getPrice'),
        /** Tax included */
        'price_tin' => array('AdminImportController', 'getPrice'),
        'reduction_price' => array('AdminImportController', 'getPrice'),
        'reduction_percent' => array('AdminImportController', 'getPrice'),
        'wholesale_price' => array('AdminImportController', 'getPrice'),
        'ecotax' => array('AdminImportController', 'getPrice'),
        'name' => array('AdminImportController', 'createMultiLangField'),
        'description' => array('AdminImportController', 'createMultiLangField'),
        'description_short' => array('AdminImportController', 'createMultiLangField'),
        'meta_title' => array('AdminImportController', 'createMultiLangField'),
        'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
        'meta_description' => array('AdminImportController', 'createMultiLangField'),
        'link_rewrite' => array('AdminImportController', 'createMultiLangField'),
        'available_now' => array('AdminImportController', 'createMultiLangField'),
        'available_later' => array('AdminImportController', 'createMultiLangField'),
        'category' => array('AdminImportController', 'split'),
        'online_only' => array('AdminImportController', 'getBoolean'),
        'accessories' => array('AdminImportController', 'split'),
        'image_alt' => array('AdminImportController', 'split'),
        'delivery_in_stock' => array('AdminImportController', 'createMultiLangField'),
        'delivery_out_stock' => array('AdminImportController', 'createMultiLangField'),
    );

    public $separator;
    public $convert;
    public $multiple_value_separator;

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        $this->entities = array(
            $this->trans('Categories', array(), 'Admin.Global'),
            $this->trans('Products', array(), 'Admin.Global'),
            $this->trans('Combinations', array(), 'Admin.Global'),
            $this->trans('Customers', array(), 'Admin.Global'),
            $this->trans('Addresses', array(), 'Admin.Global'),
            $this->trans('Brands', array(), 'Admin.Global'),
            $this->trans('Suppliers', array(), 'Admin.Global'),
            $this->trans('Alias', array(), 'Admin.Shopparameters.Feature'),
            $this->trans('Store contacts', array(), 'Admin.Advparameters.Feature'),
        );

        // @since 1.5.0
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->entities = array_merge(
                $this->entities,
                array(
                    $this->trans('Supply Orders', array(), 'Admin.Advparameters.Feature'),
                    $this->trans('Supply Order Details', array(), 'Admin.Advparameters.Feature'),
                )
            );
        }

        $this->entities = array_flip($this->entities);

        switch ((int)Tools::getValue('entity')) {
            case $this->entities[$this->trans('Combinations', array(), 'Admin.Global')]:
                $this->required_fields = array(
                    'group',
                    'attribute'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id_product' => array('label' => $this->trans('Product ID', array(), 'Admin.Advparameters.Feature')),
                    'product_reference' => array('label' => $this->trans('Product Reference', array(), 'Admin.Advparameters.Feature')),
                    'group' => array(
                        'label' => $this->trans('Attribute (Name:Type:Position)', array(), 'Admin.Advparameters.Feature').'*'
                    ),
                    'attribute' => array(
                        'label' => $this->trans('Value (Value:Position)', array(), 'Admin.Advparameters.Feature').'*'
                    ),
                    'supplier_reference' => array('label' => $this->trans('Supplier reference', array(), 'Admin.Advparameters.Feature')),
                    'reference' => array('label' => $this->trans('Reference', array(), 'Admin.Global')),
                    'ean13' => array('label' => $this->trans('EAN13', array(), 'Admin.Advparameters.Feature')),
                    'upc' => array('label' => $this->trans('UPC', array(), 'Admin.Advparameters.Feature')),
                    'wholesale_price' => array('label' => $this->trans('Cost price', array(), 'Admin.Catalog.Feature')),
                    'price' => array('label' => $this->trans('Impact on price', array(), 'Admin.Catalog.Feature')),
                    'ecotax' => array('label' => $this->trans('Ecotax', array(), 'Admin.Catalog.Feature')),
                    'quantity' => array('label' => $this->trans('Quantity', array(), 'Admin.Global')),
                    'minimal_quantity' => array('label' => $this->trans('Minimal quantity', array(), 'Admin.Advparameters.Feature')),
                    'low_stock_threshold' => array('label' => $this->trans('Low stock level', array(), 'Admin.Catalog.Feature')),
                    'low_stock_alert' => array('label' => $this->trans('Send me an email when the quantity is under this level', array(), 'Admin.Catalog.Feature')),
                    'weight' => array('label' => $this->trans('Impact on weight', array(), 'Admin.Catalog.Feature')),
                    'default_on' => array('label' => $this->trans('Default (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'available_date' => array('label' => $this->trans('Combination availability date', array(), 'Admin.Advparameters.Feature')),
                    'image_position' => array(
                        'label' => $this->trans('Choose among product images by position (1,2,3...)', array(), 'Admin.Advparameters.Feature')
                    ),
                    'image_url' => array('label' => $this->trans('Image URLs (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'image_alt' => array('label' => $this->trans('Image alt texts (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->trans('Advanced Stock Management', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Enable Advanced Stock Management on product (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Help')
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->trans('Depends on stock', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('0 = Use quantity set in product, 1 = Use quantity from warehouse.', array(), 'Admin.Advparameters.Help')
                    ),
                    'warehouse' => array(
                        'label' => $this->trans('Warehouse', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('ID of the warehouse to set as storage.', array(), 'Admin.Advparameters.Help')
                    ),
                );

                self::$default_values = array(
                    'reference' => '',
                    'supplier_reference' => '',
                    'ean13' => '',
                    'upc' => '',
                    'wholesale_price' => 0,
                    'price' => 0,
                    'ecotax' => 0,
                    'quantity' => 0,
                    'minimal_quantity' => 1,
                    'low_stock_threshold' => null,
                    'low_stock_alert' => false,
                    'weight' => 0,
                    'default_on' => null,
                    'advanced_stock_management' => 0,
                    'depends_on_stock' => 0,
                    'available_date' => date('Y-m-d')
                );
                break;

            case $this->entities[$this->trans('Categories', array(), 'Admin.Global')]:
                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'parent' => array('label' => $this->trans('Parent category', array(), 'Admin.Catalog.Feature')),
                    'is_root_category' => array(
                        'label' => $this->trans('Root category (0/1)', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('A category root is where a category tree can begin. This is used with multistore.', array(), 'Admin.Advparameters.Help')
                        ),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'link_rewrite' => array('label' => $this->trans('Rewritten URL', array(), 'Admin.Shopparameters.Feature')),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                );

                self::$default_values = array(
                    'active' => '1',
                    'parent' => Configuration::get('PS_HOME_CATEGORY'),
                    'link_rewrite' => ''
                );
                break;

            case $this->entities[$this->trans('Products', array(), 'Admin.Global')]:
                self::$validators['image'] = array(
                    'AdminImportController',
                    'split'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'category' => array('label' => $this->trans('Categories (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'price_tex' => array('label' => $this->trans('Price tax excluded', array(), 'Admin.Advparameters.Feature')),
                    'price_tin' => array('label' => $this->trans('Price tax included', array(), 'Admin.Advparameters.Feature')),
                    'id_tax_rules_group' => array('label' => $this->trans('Tax rule ID', array(), 'Admin.Advparameters.Feature')),
                    'wholesale_price' => array('label' => $this->trans('Cost price', array(), 'Admin.Catalog.Feature')),
                    'on_sale' => array('label' => $this->trans('On sale (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'reduction_price' => array('label' => $this->trans('Discount amount', array(), 'Admin.Advparameters.Feature')),
                    'reduction_percent' => array('label' => $this->trans('Discount percent', array(), 'Admin.Advparameters.Feature')),
                    'reduction_from' => array('label' => $this->trans('Discount from (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature')),
                    'reduction_to' => array('label' => $this->trans('Discount to (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature')),
                    'reference' => array('label' => $this->trans('Reference #', array(), 'Admin.Advparameters.Feature')),
                    'supplier_reference' => array('label' => $this->trans('Supplier reference #', array(), 'Admin.Advparameters.Feature')),
                    'supplier' => array('label' => $this->trans('Supplier', array(), 'Admin.Global')),
                    'manufacturer' => array('label' => $this->trans('Brand', array(), 'Admin.Global')),
                    'ean13' => array('label' => $this->trans('EAN13', array(), 'Admin.Advparameters.Feature')),
                    'upc' => array('label' => $this->trans('UPC', array(), 'Admin.Advparameters.Feature')),
                    'ecotax' => array('label' => $this->trans('Ecotax', array(), 'Admin.Catalog.Feature')),
                    'width' => array('label' => $this->trans('Width', array(), 'Admin.Global')),
                    'height' => array('label' => $this->trans('Height', array(), 'Admin.Global')),
                    'depth' => array('label' => $this->trans('Depth', array(), 'Admin.Global')),
                    'weight' => array('label' => $this->trans('Weight', array(), 'Admin.Global')),
                    'delivery_in_stock' => array(
                        'label' => $this->trans(
                            'Delivery time of in-stock products:',
                            array(),
                            'Admin.Catalog.Feature'
                        )
                    ),
                    'delivery_out_stock' => array(
                        'label' => $this->trans(
                            'Delivery time of out-of-stock products with allowed orders:',
                            array(),
                            'Admin.Advparameters.Feature'
                        )
                    ),
                    'quantity' => array('label' => $this->trans('Quantity', array(), 'Admin.Global')),
                    'minimal_quantity' => array('label' => $this->trans('Minimal quantity', array(), 'Admin.Advparameters.Feature')),
                    'low_stock_threshold' => array('label' => $this->trans('Low stock level', array(), 'Admin.Catalog.Feature')),
                    'low_stock_alert' => array('label' => $this->trans('Send me an email when the quantity is under this level', array(), 'Admin.Catalog.Feature')),
                    'visibility' => array('label' => $this->trans('Visibility', array(), 'Admin.Catalog.Feature')),
                    'additional_shipping_cost' => array('label' => $this->trans('Additional shipping cost', array(), 'Admin.Advparameters.Feature')),
                    'unity' => array('label' => $this->trans('Unit for the price per unit', array(), 'Admin.Advparameters.Feature')),
                    'unit_price' => array('label' => $this->trans('Price per unit', array(), 'Admin.Advparameters.Feature')),
                    'description_short' => array('label' => $this->trans('Summary', array(), 'Admin.Catalog.Feature')),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'tags' => array('label' => $this->trans('Tags (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'link_rewrite' => array('label' => $this->trans('Rewritten URL', array(), 'Admin.Advparameters.Feature')),
                    'available_now' => array('label' => $this->trans('Label when in stock', array(), 'Admin.Catalog.Feature')),
                    'available_later' => array('label' => $this->trans('Label when backorder allowed', array(), 'Admin.Advparameters.Feature')),
                    'available_for_order' => array('label' => $this->trans('Available for order (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'available_date' => array('label' => $this->trans('Product availability date', array(), 'Admin.Advparameters.Feature')),
                    'date_add' => array('label' => $this->trans('Product creation date', array(), 'Admin.Advparameters.Feature')),
                    'show_price' => array('label' => $this->trans('Show price (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'image' => array('label' => $this->trans('Image URLs (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'image_alt' => array('label' => $this->trans('Image alt texts (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'delete_existing_images' => array(
                        'label' => $this->trans('Delete existing images (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')
                    ),
                    'features' => array('label' => $this->trans('Feature (Name:Value:Position:Customized)', array(), 'Admin.Advparameters.Feature')),
                    'online_only' => array('label' => $this->trans('Available online only (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'condition' => array('label' => $this->trans('Condition', array(), 'Admin.Catalog.Feature')),
                    'customizable' => array('label' => $this->trans('Customizable (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'uploadable_files' => array('label' => $this->trans('Uploadable files (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'text_fields' => array('label' => $this->trans('Text fields (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'out_of_stock' => array('label' => $this->trans('Action when out of stock', array(), 'Admin.Advparameters.Feature')),
                    'is_virtual' => array('label' => $this->trans('Virtual product (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature')),
                    'file_url' => array('label' => $this->trans('File URL', array(), 'Admin.Advparameters.Feature')),
                    'nb_downloadable' => array(
                        'label' => $this->trans('Number of allowed downloads', array(), 'Admin.Catalog.Feature'),
                        'help' => $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', array(), 'Admin.Catalog.Help'),
                    ),
                    'date_expiration' => array('label' => $this->trans('Expiration date (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature')),
                    'nb_days_accessible' => array(
                        'label' => $this->trans('Number of days', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', array(), 'Admin.Catalog.Help'),
                    ),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->trans('Advanced Stock Management', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Enable Advanced Stock Management on product (0 = No, 1 = Yes).', array(), 'Admin.Advparameters.Help')
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->trans('Depends on stock', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('0 = Use quantity set in product, 1 = Use quantity from warehouse.', array(), 'Admin.Advparameters.Help')
                    ),
                    'warehouse' => array(
                        'label' => $this->trans('Warehouse', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('ID of the warehouse to set as storage.', array(), 'Admin.Advparameters.Help')
                    ),
                    'accessories' => array('label' => $this->trans('Accessories (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                );

                self::$default_values = array(
                    'id_category' => array((int)Configuration::get('PS_HOME_CATEGORY')),
                    'id_category_default' => null,
                    'active' => '1',
                    'width' => 0.000000,
                    'height' => 0.000000,
                    'depth' => 0.000000,
                    'weight' => 0.000000,
                    'visibility' => 'both',
                    'additional_shipping_cost' => 0.00,
                    'unit_price' => 0,
                    'quantity' => 0,
                    'minimal_quantity' => 1,
                    'low_stock_threshold' => null,
                    'low_stock_alert' => false,
                    'price' => 0,
                    'id_tax_rules_group' => 0,
                    'description_short' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
                    'link_rewrite' => array((int)Configuration::get('PS_LANG_DEFAULT') => ''),
                    'online_only' => 0,
                    'condition' => 'new',
                    'available_date' => date('Y-m-d'),
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                    'customizable' => 0,
                    'uploadable_files' => 0,
                    'text_fields' => 0,
                    'advanced_stock_management' => 0,
                    'depends_on_stock' => 0,
                    'is_virtual' => 0,
                );
                break;

            case $this->entities[$this->trans('Customers', array(), 'Admin.Global')]:
                //Overwrite required_fields AS only email is required whereas other entities
                $this->required_fields = array('email', 'passwd', 'lastname', 'firstname');

                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active  (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'id_gender' => array('label' => $this->trans('Titles ID (Mr = 1, Ms = 2, else 0)', array(), 'Admin.Advparameters.Feature')),
                    'email' => array('label' => $this->trans('Email', array(), 'Admin.Global').'*'),
                    'passwd' => array('label' => $this->trans('Password', array(), 'Admin.Global').'*'),
                    'birthday' => array('label' => $this->trans('Birth date (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature')),
                    'lastname' => array('label' => $this->trans('Last name', array(), 'Admin.Global').'*'),
                    'firstname' => array('label' => $this->trans('First name', array(), 'Admin.Global').'*'),
                    'newsletter' => array('label' => $this->trans('Newsletter (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'optin' => array('label' => $this->trans('Partner offers (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'date_add' => array('label' => $this->trans('Registration date (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature')),
                    'group' => array('label' => $this->trans('Groups (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'id_default_group' => array('label' => $this->trans('Default group ID', array(), 'Admin.Advparameters.Feature')),
                    'id_shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                );

                self::$default_values = array(
                    'active' => '1',
                    'id_shop' => Configuration::get('PS_SHOP_DEFAULT'),
                );
                break;

            case $this->entities[$this->trans('Addresses', array(), 'Admin.Global')]:
                //Overwrite required_fields
                $this->required_fields = array(
                    'alias',
                    'lastname',
                    'firstname',
                    'address1',
                    'postcode',
                    'country',
                    'customer_email',
                    'city'
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'alias' => array('label' => $this->trans('Alias', array(), 'Admin.Shopparameters.Feature').'*'),
                    'active' => array('label' => $this->trans('Active  (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'customer_email' => array('label' => $this->trans('Customer email', array(), 'Admin.Advparameters.Feature').'*'),
                    'id_customer' => array('label' => $this->trans('Customer ID', array(), 'Admin.Advparameters.Feature')),
                    'manufacturer' => array('label' => $this->trans('Brand', array(), 'Admin.Global')),
                    'supplier' => array('label' => $this->trans('Supplier', array(), 'Admin.Global')),
                    'company' => array('label' => $this->trans('Company', array(), 'Admin.Global')),
                    'lastname' => array('label' => $this->trans('Last name', array(), 'Admin.Global').'*'),
                    'firstname' => array('label' => $this->trans('First name ', array(), 'Admin.Global').'*'),
                    'address1' => array('label' => $this->trans('Address', array(), 'Admin.Global').'*'),
                    'address2' => array('label' => $this->trans('Address (2)', array(), 'Admin.Global')),
                    'postcode' => array('label' => $this->trans('Zip/postal code', array(), 'Admin.Global').'*'),
                    'city' => array('label' => $this->trans('City', array(), 'Admin.Global').'*'),
                    'country' => array('label' => $this->trans('Country', array(), 'Admin.Global').'*'),
                    'state' => array('label' => $this->trans('State', array(), 'Admin.Global')),
                    'other' => array('label' => $this->trans('Other', array(), 'Admin.Global')),
                    'phone' => array('label' => $this->trans('Phone', array(), 'Admin.Global')),
                    'phone_mobile' => array('label' => $this->trans('Mobile Phone', array(), 'Admin.Global')),
                    'vat_number' => array('label' => $this->trans('VAT number', array(), 'Admin.Orderscustomers.Feature')),
                    'dni' => array('label' => $this->trans('Identification number', array(), 'Admin.Orderscustomers.Feature')),
                );

                self::$default_values = array(
                    'alias' => 'Alias',
                    'postcode' => 'X'
                );
                break;
            case $this->entities[$this->trans('Brands', array(), 'Admin.Global')]:
            case $this->entities[$this->trans('Suppliers', array(), 'Admin.Global')]:
                //Overwrite validators AS name is not MultiLangField
                self::$validators = array(
                    'description' => array('AdminImportController', 'createMultiLangField'),
                    'short_description' => array('AdminImportController', 'createMultiLangField'),
                    'meta_title' => array('AdminImportController', 'createMultiLangField'),
                    'meta_keywords' => array('AdminImportController', 'createMultiLangField'),
                    'meta_description' => array('AdminImportController', 'createMultiLangField'),
                );

                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'short_description' => array('label' => $this->trans('Short description', array(), 'Admin.Catalog.Feature')),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of group shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                );

                self::$default_values = array(
                    'shop' => Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),
                );
                break;
            case $this->entities[$this->trans('Alias', array(), 'Admin.Shopparameters.Feature')]:
                //Overwrite required_fields
                $this->required_fields = array(
                    'alias',
                    'search',
                );
                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'alias' => array('label' => $this->trans('Alias', array(), 'Admin.Shopparameters.Feature').'*'),
                    'search' => array('label' => $this->trans('Search', array(), 'Admin.Shopparameters.Feature').'*'),
                    'active' => array('label' => $this->trans('Active', array(), 'Admin.Global')),
                    );
                self::$default_values = array(
                    'active' => '1',
                );
                break;
            case $this->entities[$this->trans('Store contacts', array(), 'Admin.Advparameters.Feature')]:
                unset(self::$validators['name']);
                self::$validators = array(
                    'hours' => array('AdminImportController', 'split')
                );
                $this->required_fields = array(
                    'address1',
                    'city',
                    'country',
                    'latitude',
                    'longitude',
                );
                $this->available_fields = array(
                    'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'address1' => array('label' => $this->trans('Address', array(), 'Admin.Global').'*'),
                    'address2' => array('label' => $this->trans('Address (2)', array(), 'Admin.Advparameters.Feature')),
                    'postcode' => array('label' => $this->trans('Zip/postal code', array(), 'Admin.Global')),
                    'state' => array('label' => $this->trans('State', array(), 'Admin.Global')),
                    'city' => array('label' => $this->trans('City', array(), 'Admin.Global').'*'),
                    'country' => array('label' => $this->trans('Country', array(), 'Admin.Global').'*'),
                    'latitude' => array('label' => $this->trans('Latitude', array(), 'Admin.Advparameters.Feature').'*'),
                    'longitude' => array('label' => $this->trans('Longitude', array(), 'Admin.Advparameters.Feature').'*'),
                    'phone' => array('label' => $this->trans('Phone', array(), 'Admin.Global')),
                    'fax' => array('label' => $this->trans('Fax', array(), 'Admin.Global')),
                    'email' => array('label' => $this->trans('Email address', array(), 'Admin.Global')),
                    'note' => array('label' => $this->trans('Note', array(), 'Admin.Advparameters.Feature')),
                    'hours' => array('label' => $this->trans('Hours (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', array(), 'Admin.Advparameters.Help'),
                    ),
                );
                self::$default_values = array(
                    'active' => '1',
                );
                break;
        }

        // @since 1.5.0
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            switch ((int)Tools::getValue('entity')) {
                case $this->entities[$this->trans('Supply Orders', array(), 'Admin.Advparameters.Feature')]:
                    // required fields
                    $this->required_fields = array(
                        'id_supplier',
                        'id_warehouse',
                        'reference',
                        'date_delivery_expected',
                    );
                    // available fields
                    $this->available_fields = array(
                        'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                        'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                        'id_supplier' => array('label' => $this->trans('Supplier ID *', array(), 'Admin.Advparameters.Feature')),
                        'id_lang' => array('label' => $this->trans('Lang ID', array(), 'Admin.Advparameters.Feature')),
                        'id_warehouse' => array('label' => $this->trans('Warehouse ID *', array(), 'Admin.Advparameters.Feature')),
                        'id_currency' => array('label' => $this->trans('Currency ID *', array(), 'Admin.Advparameters.Feature')),
                        'reference' => array('label' => $this->trans('Supply Order Reference *', array(), 'Admin.Advparameters.Feature')),
                        'date_delivery_expected' => array('label' => $this->trans('Delivery Date (Y-M-D)*', array(), 'Admin.Advparameters.Feature')),
                        'discount_rate' => array('label' => $this->trans('Discount rate', array(), 'Admin.Advparameters.Feature')),
                        'is_template' => array('label' => $this->trans('Template', array(), 'Admin.Advparameters.Feature')),
                    );
                    // default values
                    self::$default_values = array(
                        'id_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
                        'id_currency' => Currency::getDefaultCurrency()->id,
                        'discount_rate' => '0',
                        'is_template' => '0',
                    );
                    break;
                case $this->entities[$this->trans('Supply Order Details', array(), 'Admin.Advparameters.Feature')]:
                    // required fields
                    $this->required_fields = array(
                        'supply_order_reference',
                        'id_product',
                        'unit_price_te',
                        'quantity_expected',
                    );
                    // available fields
                    $this->available_fields = array(
                        'no' => array('label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature')),
                        'supply_order_reference' => array('label' => $this->trans('Supply Order Reference *', array(), 'Admin.Advparameters.Feature')),
                        'id_product' => array('label' => $this->trans('Product ID *', array(), 'Admin.Advparameters.Feature')),
                        'id_product_attribute' => array('label' => $this->trans('Product Attribute ID', array(), 'Admin.Advparameters.Feature')),
                        'unit_price_te' => array('label' => $this->trans('Unit Price (tax excl.)*', array(), 'Admin.Advparameters.Feature')),
                        'quantity_expected' => array('label' => $this->trans('Quantity Expected *', array(), 'Admin.Advparameters.Feature')),
                        'discount_rate' => array('label' => $this->trans('Discount Rate', array(), 'Admin.Advparameters.Feature')),
                        'tax_rate' => array('label' => $this->trans('Tax Rate', array(), 'Admin.Advparameters.Feature')),
                    );
                    // default values
                    self::$default_values = array(
                        'discount_rate' => '0',
                        'tax_rate' => '0',
                    );
                    break;
            }
        }

        $this->separator = ($separator = Tools::substr(strval(trim(Tools::getValue('separator'))), 0, 1)) ? $separator :  ';';
        $this->convert = false;
        $this->multiple_value_separator = ($separator = Tools::substr(strval(trim(Tools::getValue('multiple_value_separator'))), 0, 1)) ? $separator :  ',';
    }

    public function setMedia()
    {
        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
            .'template')) {
            $bo_theme = 'default';
        }

        // We need to set parent media first, so that jQuery is loaded before the dependant plugins
        parent::setMedia();

        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');
    }

    public function renderForm()
    {
        if (!is_dir(AdminImportController::getPath())) {
            return !($this->errors[] = $this->trans('The import directory doesn\'t exist. Please check your file path.', array(), 'Admin.Advparameters.Notification'));
        }

        if (!is_writable(AdminImportController::getPath())) {
            $this->displayWarning($this->trans('The import directory must be writable (CHMOD 755 / 777).', array(), 'Admin.Advparameters.Notification'));
        }

        $files_to_import = scandir(AdminImportController::getPath());
        uasort($files_to_import, array('AdminImportController', 'usortFiles'));
        foreach ($files_to_import as $k => &$filename) {
            //exclude .  ..  .svn and index.php and all hidden files
            if (preg_match('/^\..*|index\.php/i', $filename) || is_dir(AdminImportController::getPath().$filename)) {
                unset($files_to_import[$k]);
            }
        }
        unset($filename);

        $this->fields_form = array('');

        $this->toolbar_scroll = false;
        $this->toolbar_btn = array();

        // adds fancybox
        $this->addJqueryPlugin(array('fancybox'));

        $entity_selected = 0;
        if (isset($this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))])) {
            $entity_selected = $this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))];
            $this->context->cookie->entity_selected = (int)$entity_selected;
        } elseif (isset($this->context->cookie->entity_selected)) {
            $entity_selected = (int)$this->context->cookie->entity_selected;
        }

        $csv_selected = '';
        if (isset($this->context->cookie->csv_selected) &&
            @filemtime(AdminImportController::getPath(
                urldecode($this->context->cookie->csv_selected)
            ))) {
            $csv_selected = urldecode($this->context->cookie->csv_selected);
        } else {
            $this->context->cookie->csv_selected = $csv_selected;
        }

        $id_lang_selected = '';
        if (isset($this->context->cookie->iso_lang_selected) && $this->context->cookie->iso_lang_selected) {
            $id_lang_selected = (int)Language::getIdByIso(urldecode($this->context->cookie->iso_lang_selected));
        }

        $separator_selected = $this->separator;
        if (isset($this->context->cookie->separator_selected) && $this->context->cookie->separator_selected) {
            $separator_selected = urldecode($this->context->cookie->separator_selected);
        }

        $multiple_value_separator_selected = $this->multiple_value_separator;
        if (isset($this->context->cookie->multiple_value_separator_selected) && $this->context->cookie->multiple_value_separator_selected) {
            $multiple_value_separator_selected = urldecode($this->context->cookie->multiple_value_separator_selected);
        }

        //get post max size
        $post_max_size = ini_get('post_max_size');
        $bytes         = (int) trim($post_max_size);
        $last          = strtolower($post_max_size[strlen($post_max_size) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
                // no break to fall-through
            case 'm':
                $bytes *= 1024;
                // no break to fall-through
            case 'k':
                $bytes *= 1024;
        }

        if (!isset($bytes) || $bytes == '') {
            $bytes = 20971520;
        } // 20Mb

        $this->tpl_form_vars = array(
            'post_max_size' => (int)$bytes,
            'module_confirmation' => Tools::isSubmit('import') && (isset($this->warnings) && !count($this->warnings)),
            'path_import' => AdminImportController::getPath(),
            'entities' => $this->entities,
            'entity_selected' => $entity_selected,
            'csv_selected' => $csv_selected,
            'separator_selected' => $separator_selected,
            'multiple_value_separator_selected' => $multiple_value_separator_selected,
            'files_to_import' => $files_to_import,
            'languages' => Language::getLanguages(false),
            'id_language' => ($id_lang_selected) ? $id_lang_selected : $this->context->language->id,
            'available_fields' => $this->getAvailableFields(),
            'truncateAuthorized' => (Shop::isFeatureActive() && $this->context->employee->isSuperAdmin()) || !Shop::isFeatureActive(),
            'PS_ADVANCED_STOCK_MANAGEMENT' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
        );

        return parent::renderForm();
    }

    public function ajaxProcessuploadCsv()
    {
        $filename_prefix = date('YmdHis').'-';

        if (isset($_FILES['file']) && !empty($_FILES['file']['error'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_FILES['file']['error'] = $this->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.', array(), 'Admin.Advparameters.Notification');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_FILES['file']['error'] = $this->trans('The uploaded file exceeds the post_max_size directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess, for example:', array(), 'Admin.Advparameters.Notification')
                    .'<br/><a href="'.$this->context->link->getAdminLink('AdminMeta').'" >
					<code>php_value post_max_size 20M</code> '.
                    $this->trans('(click to open "Generators" page)', array(), 'Admin.Advparameters.Notification').'</a>';
                    break;
                break;
                case UPLOAD_ERR_PARTIAL:
                    $_FILES['file']['error'] = $this->trans('The uploaded file was only partially uploaded.', array(), 'Admin.Advparameters.Notification');
                    break;
                break;
                case UPLOAD_ERR_NO_FILE:
                    $_FILES['file']['error'] = $this->trans('No file was uploaded.', array(), 'Admin.Advparameters.Notification');
                    break;
                break;
            }
        } elseif (!preg_match('#([^\.]*?)\.(csv|xls[xt]?|o[dt]s)$#is', $_FILES['file']['name'])) {
            $_FILES['file']['error'] = $this->trans('The extension of your file should be .csv.', array(), 'Admin.Advparameters.Notification');
        } elseif (!@filemtime($_FILES['file']['tmp_name']) ||
            !@move_uploaded_file($_FILES['file']['tmp_name'], AdminImportController::getPath().$filename_prefix.str_replace("\0", '', $_FILES['file']['name']))) {
            $_FILES['file']['error'] = $this->trans('An error occurred while uploading / copying the file.', array(), 'Admin.Advparameters.Notification');
        } else {
            @chmod(AdminImportController::getPath().$filename_prefix.$_FILES['file']['name'], 0664);
            $_FILES['file']['filename'] = $filename_prefix.str_replace('\0', '', $_FILES['file']['name']);
        }

        die(json_encode($_FILES));
    }

    public function renderView()
    {
        $this->addJS(_PS_JS_DIR_.'admin/import.js');

        $handle = $this->openCsvFile();
        $nb_column = $this->getNbrColumn($handle, $this->separator);
        $nb_table = ceil($nb_column / MAX_COLUMNS);

        $res = array();
        foreach ($this->required_fields as $elem) {
            $res[] = '\''.$elem.'\'';
        }

        $data = array();
        for ($i = 0; $i < $nb_table; $i++) {
            $data[$i] = $this->generateContentTable($i, $nb_column, $handle, $this->separator);
        }

        $this->context->cookie->entity_selected = (int)Tools::getValue('entity');
        $this->context->cookie->iso_lang_selected = urlencode(Tools::getValue('iso_lang'));
        $this->context->cookie->separator_selected = urlencode($this->separator);
        $this->context->cookie->multiple_value_separator_selected = urlencode($this->multiple_value_separator);
        $this->context->cookie->csv_selected = urlencode(Tools::getValue('csv'));

        $this->tpl_view_vars = array(
            'import_matchs' => Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'import_match', true, false),
            'fields_value' => array(
                'csv' => Tools::getValue('csv'),
                'entity' => (int)Tools::getValue('entity'),
                'iso_lang' => Tools::getValue('iso_lang'),
                'truncate' => Tools::getValue('truncate'),
                'forceIDs' => Tools::getValue('forceIDs'),
                'regenerate' => Tools::getValue('regenerate'),
                'sendemail' => Tools::getValue('sendemail'),
                'match_ref' => Tools::getValue('match_ref'),
                'separator' => $this->separator,
                'multiple_value_separator' => $this->multiple_value_separator
            ),
            'nb_table' => $nb_table,
            'nb_column' => $nb_column,
            'res' => implode(',', $res),
            'max_columns' => MAX_COLUMNS,
            'no_pre_select' => array('price_tin', 'feature'),
            'available_fields' => $this->available_fields,
            'data' => $data
        );

        return parent::renderView();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            case 'import':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }

                $this->toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->trans('Cancel', array(), 'Admin.Actions')
                );
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save-import'] = array(
                    'href' => '#',
                    'desc' => $this->trans('Import .CSV data', array(), 'Admin.Advparameters.Feature')
                );
                break;
        }
    }

    protected function generateContentTable($current_table, $nb_column, $handle, $glue)
    {
        $html = '<table id="table'.$current_table.'" style="display: none;" class="table table-bordered"><thead><tr>';
        // Header
        for ($i = 0; $i < $nb_column; $i++) {
            if (MAX_COLUMNS * (int)$current_table <= $i && (int)$i < MAX_COLUMNS * ((int)$current_table + 1)) {
                $html .= '<th>
							<select id="type_value['.$i.']"
								name="type_value['.$i.']"
								class="type_value">
								'.$this->getTypeValuesOptions($i).'
							</select>
						</th>';
            }
        }
        $html .= '</tr></thead><tbody>';

        AdminImportController::setLocale();
        for ($current_line = 0; $current_line < 10 && $line = fgetcsv($handle, MAX_LINE_SIZE, $glue); $current_line++) {
            /* UTF-8 conversion */
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $html .= '<tr id="table_'.$current_table.'_line_'.$current_line.'">';
            foreach ($line as $nb_c => $column) {
                if ((MAX_COLUMNS * (int)$current_table <= $nb_c) && ((int)$nb_c < MAX_COLUMNS * ((int)$current_table + 1))) {
                    $html .= '<td>'.htmlentities(Tools::substr($column, 0, 200), ENT_QUOTES, 'UTF-8').'</td>';
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        AdminImportController::rewindBomAware($handle);
        return $html;
    }

    public function init()
    {
        parent::init();
        if (Tools::isSubmit('submitImportFile')) {
            $this->display = 'import';
        }
    }

    public function initContent()
    {
        if ($this->display == 'import') {
            if (Tools::getValue('csv')) {
                $this->content .= $this->renderView();
            } else {
                $this->errors[] = $this->trans('To proceed, please upload a file first.', array(), 'Admin.Advparameters.Notification');
                $this->content .= $this->renderForm();
            }
        } else {
            $this->content .= $this->renderForm();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    protected static function rewindBomAware($handle)
    {
        // A rewind wrapper that skips BOM signature wrongly
        if (!is_resource($handle)) {
            return false;
        }
        rewind($handle);
        if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }

    protected static function getBoolean($field)
    {
        return (bool)$field;
    }

    protected static function getPrice($field)
    {
        $field = ((float)str_replace(',', '.', $field));
        $field = ((float)str_replace('%', '', $field));
        return $field;
    }

    protected static function split($field)
    {
        if (empty($field)) {
            return array();
        }

        $separator = Tools::getValue('multiple_value_separator');
        if (is_null($separator) || trim($separator) == '') {
            $separator = ',';
        }

        $tab = '';
        $uniqid_path = false;

        // try data:// protocole. If failed, old school file on filesystem.
        if (($fd = @fopen('data://text/plain;base64,'.base64_encode($field), 'rb')) === false) {
            do {
                $uniqid_path = _PS_UPLOAD_DIR_.uniqid();
            } while (file_exists($uniqid_path));
            file_put_contents($uniqid_path, $field);
            $fd = fopen($uniqid_path, 'r');
        }

        if ($fd === false) {
            return array();
        }

        $tab = fgetcsv($fd, MAX_LINE_SIZE, $separator);
        fclose($fd);
        if ($uniqid_path !== false && file_exists($uniqid_path)) {
            @unlink($uniqid_path);
        }

        if (empty($tab) || (!is_array($tab))) {
            return array();
        }
        return $tab;
    }

    protected static function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }

    protected function getTypeValuesOptions($nb_c)
    {
        $i = 0;
        $no_pre_select = array('price_tin', 'feature');

        $options = '';
        foreach ($this->available_fields as $k => $field) {
            $options .= '<option value="'.$k.'"';
            if ($k === 'price_tin') {
                ++$nb_c;
            }
            if ($i === ($nb_c + 1) && (!in_array($k, $no_pre_select))) {
                $options .= ' selected="selected"';
            }
            $options .= '>'.$field['label'].'</option>';
            ++$i;
        }
        return $options;
    }

    /*
    * Return fields to be display AS piece of advise
    *
    * @param $in_array boolean
    * @return string or return array
    */
    public function getAvailableFields($in_array = false)
    {
        $i = 0;
        $fields = array();
        $keys = array_keys($this->available_fields);
        array_shift($keys);
        foreach ($this->available_fields as $k => $field) {
            if ($k === 'no') {
                continue;
            }
            if ($k === 'price_tin') { // Special case for Product : either one or the other. Not both.
                $fields[$i - 1] = '<div>'.$this->available_fields[$keys[$i - 1]]['label'].'<br/>&nbsp;&nbsp;<i>'.$this->trans('or', array(), 'Admin.Advparameters.Help').'</i>&nbsp;&nbsp; '.$field['label'].'</div>';
            } else {
                if (isset($field['help'])) {
                    $html = '&nbsp;<a href="#" class="help-tooltip" data-toggle="tooltip" title="'.$field['help'].'"><i class="icon-info-sign"></i></a>';
                } else {
                    $html = '';
                }
                $fields[] = '<div>'.$field['label'].$html.'</div>';
            }
            ++$i;
        }
        if ($in_array) {
            return $fields;
        } else {
            return implode("\n\r", $fields);
        }
    }

    protected function receiveTab()
    {
        $type_value = Tools::getValue('type_value') ? Tools::getValue('type_value') : array();
        foreach ($type_value as $nb => $type) {
            if ($type != 'no') {
                self::$column_mask[$type] = $nb;
            }
        }
    }

    public static function getMaskedRow($row)
    {
        $res = array();
        if (is_array(self::$column_mask)) {
            foreach (self::$column_mask as $type => $nb) {
                $res[$type] = isset($row[$nb]) ? trim($row[$nb]) : null;
            }
        }

        return $res;
    }

    protected static function setDefaultValues(&$info)
    {
        foreach (self::$default_values as $k => $v) {
            if (!isset($info[$k]) || $info[$k] == '') {
                $info[$k] = $v;
            }
        }
    }

    protected static function setEntityDefaultValues(&$entity)
    {
        $members = get_object_vars($entity);
        foreach (self::$default_values as $k => $v) {
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members)) {
                $entity->$k = $v;
            }
        }
    }

    protected static function fillInfo($infos, $key, &$entity)
    {
        $infos = trim($infos);
        if (isset(self::$validators[$key][1]) && self::$validators[$key][1] == 'createMultiLangField' && Tools::getValue('iso_lang')) {
            $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
            $tmp = call_user_func(self::$validators[$key], $infos);
            foreach ($tmp as $id_lang_tmp => $value) {
                if (empty($entity->{$key}[$id_lang_tmp]) || $id_lang_tmp == $id_lang) {
                    $entity->{$key}[$id_lang_tmp] = $value;
                }
            }
        } elseif (!empty($infos) || $infos == '0') { // ($infos == '0') => if you want to disable a product by using "0" in active because empty('0') return true
                $entity->{$key} = isset(self::$validators[$key]) ? call_user_func(self::$validators[$key], $infos) : $infos;
        }

        return true;
    }

    /**
     * @param $array
     * @param $funcname
     * @param mixed $user_data
     * @return bool
     */
    public static function arrayWalk(&$array, $funcname, &$user_data = false)
    {
        if (!is_callable($funcname)) {
            return false;
        }

        foreach ($array as $k => $row) {
            if (!call_user_func_array($funcname, array($row, $k, &$user_data))) {
                return false;
            }
        }
        return true;
    }

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled.
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     * @return bool
     */
    protected static function copyImg($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_.(int)$id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_.(int)$id_entity;
                break;
            case 'stores':
                $path = _PS_STORE_IMG_DIR_.(int)$id_entity;
                break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/'.implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = array();
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parced_url);

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);
                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path.'.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            $images_types = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previous_path = null;
                $path_infos = array();
                $path_infos[] = array($tgt_width, $tgt_height, $path.'.jpg');
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize(
                        $tmpfile,
                        $path.'-'.stripslashes($image_type['name']).'.jpg',
                        $image_type['width'],
                        $image_type['height'],
                        'jpg',
                        false,
                        $error,
                        $tgt_width,
                        $tgt_height,
                        5,
                        $src_width,
                        $src_height
                    )) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = array($tgt_width, $tgt_height, $path.'-'.stripslashes($image_type['name']).'.jpg');
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg');
                            }
                        }
                    }
                    if (in_array($image_type['id_image_type'], $watermark_types)) {
                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                    }
                }
            }
        } else {
            @unlink($orig_tmpfile);
            return false;
        }
        unlink($orig_tmpfile);
        return true;
    }

    protected static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }
        return $path;
    }

    public function categoryImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();


        $cat_moved = array();
        if ($crossStepsVariables !== false && array_key_exists('cat_moved', $crossStepsVariables)) {
            $cat_moved = $crossStepsVariables['cat_moved'];
        }

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->categoryImportOne(
                $info,
                $default_language_id,
                $id_lang,
                $force_ids,
                $regenerate,
                $shop_is_feature_active,
                $cat_moved, // by ref
                $validateOnly
            );
        }

        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['cat_moved'] = $cat_moved;
        }
        return $line_count;
    }

    protected function categoryImportOne($info, $default_language_id, $id_lang, $force_ids, $regenerate, $shop_is_feature_active, &$cat_moved, $validateOnly = false)
    {
        $tab_categ = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
        if (isset($info['id']) && in_array((int)$info['id'], $tab_categ)) {
            $this->errors[] = $this->trans('The category ID must be unique. It can\'t be the same as the one for Root or Home category.', array(), 'Admin.Advparameters.Notification');
            return;
        }
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $category = new Category((int)$info['id']);
        } else {
            if (isset($info['id']) && (int)$info['id'] && Category::existsInDatabase((int)$info['id'], 'category')) {
                $category = new Category((int)$info['id']);
            } else {
                $category = new Category();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $category);

        // Parent category
        if (isset($category->parent) && is_numeric($category->parent)) {
            // Validation for parenting itself
            if ($validateOnly && ($category->parent == $category->id) || (isset($info['id']) && $category->parent == (int)$info['id'])) {
                $this->errors[] = $this->trans(
                    'The category ID must be unique. It can\'t be the same as the one for the parent category (ID: %1$s).',
                    array((isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'),
                    'Admin.Advparameters.Notification'
                );
                return;
            }
            if (isset($cat_moved[$category->parent])) {
                $category->parent = $cat_moved[$category->parent];
            }
            $category->id_parent = $category->parent;
        } elseif (isset($category->parent) && is_string($category->parent)) {
            // Validation for parenting itself
            if ($validateOnly && isset($category->name) && ($category->parent == $category->name)) {
                $this->errors[] = $this->trans(
                    'A category can\'t be its own parent. You should rename it (current name: %1$s).',
                    array($category->parent),
                    'Admin.Advparameters.Notification'
                );
                return;
            }
            $category_parent = Category::searchByName($id_lang, $category->parent, true);
            if ($category_parent['id_category']) {
                $category->id_parent = (int)$category_parent['id_category'];
                $category->level_depth = (int)$category_parent['level_depth'] + 1;
            } else {
                $category_to_create = new Category();
                $category_to_create->name = AdminImportController::createMultiLangField($category->parent);
                $category_to_create->active = 1;
                $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$id_lang]);
                $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create

                if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move the position of this test. Only ->add() should not be triggered is !validateOnly. Previous tests should be always run.
                    $category_to_create->add()) {
                    $category->id_parent = $category_to_create->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] =$this->trans('%category_name% (ID: %id%) cannot be saved', array(
                            '%category_name%' => $category_to_create->name[$id_lang],
                            '%id%' => (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'), 'Admin.Advparameters.Notification');
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }
        if (isset($category->link_rewrite) && !empty($category->link_rewrite[$default_language_id])) {
            $valid_link = Validate::isLinkRewrite($category->link_rewrite[$default_language_id]);
        } else {
            $valid_link = false;
        }

        if (!$shop_is_feature_active) {
            $category->id_shop_default = 1;
        } else {
            $category->id_shop_default = (int)Context::getContext()->shop->id;
        }

        $bak = $category->link_rewrite[$default_language_id];
        if ((isset($category->link_rewrite) && empty($category->link_rewrite[$default_language_id])) || !$valid_link) {
            $category->link_rewrite = Tools::link_rewrite($category->name[$default_language_id]);
            if ($category->link_rewrite == '') {
                $category->link_rewrite = 'friendly-url-autogeneration-failed';
                $this->warnings[] = $this->trans('URL rewriting failed to auto-generate a friendly URL for: %category_name%', array('%category_name%' => $category->name[$default_language_id]), 'Admin.Advparameters.Notification');
            }
            $category->link_rewrite = AdminImportController::createMultiLangField($category->link_rewrite);
        }

        if (!$valid_link) {
            $this->informations[] = $this->trans('Rewrite link for %1$s (ID %2$s): re-written as %3$s.', array(
                '%1$s' => $bak,
                '%2$s' => (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                '%3$s' => $category->link_rewrite[$default_language_id],
            ), 'Admin.Advparameters.Notification');
        }
        $res = false;
        if (($field_error = $category->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $category->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && empty($this->errors)) {
            $category_already_created = Category::searchByNameAndParentCategoryId(
                $id_lang,
                $category->name[$id_lang],
                $category->id_parent
            );

            // If category already in base, get id category back
            if ($category_already_created['id_category']) {
                $cat_moved[$category->id] = (int)$category_already_created['id_category'];
                $category->id = (int)$category_already_created['id_category'];
                if (Validate::isDate($category_already_created['date_add'])) {
                    $category->date_add = $category_already_created['date_add'];
                }
            }

            if ($category->id && $category->id == $category->id_parent) {
                $this->errors[] = sprintf(
                    $this->trans('A category cannot be its own parent. The parent category ID is either missing or unknown (ID: %1$s).', array(), 'Admin.Advparameters.Notification'),
                    (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                );
                return;
            }

            /* No automatic nTree regeneration for import */
            $category->doNotRegenerateNTree = true;

            // If id category AND id category already in base, trying to update
            $categories_home_root = array(Configuration::get('PS_ROOT_CATEGORY'), Configuration::get('PS_HOME_CATEGORY'));
            if ($category->id &&
                $category->categoryExists($category->id) &&
                !in_array($category->id, $categories_home_root) &&
                !$validateOnly) {
                $res = $category->update();
            }
            if ($category->id == Configuration::get('PS_ROOT_CATEGORY')) {
                $this->errors[] = $this->trans('The root category cannot be modified.', array(), 'Admin.Advparameters.Notification');
            }
            // If no id_category or update failed
            $category->force_id = (bool)$force_ids;
            if (!$res && !$validateOnly) {
                $res = $category->add();
                if (isset($info['id']) && $category->id != $info['id']) {
                    $cat_moved[$info['id']] = $category->id;
                }
            }
        }

        // ValidateOnly mode : stops here
        if ($validateOnly) {
            return;
        }

        //copying images of categories
        if (isset($category->image) && !empty($category->image)) {
            if (!(AdminImportController::copyImg($category->id, null, $category->image, 'categories', !$regenerate))) {
                $this->warnings[] = $category->image.' '.$this->trans('cannot be copied.', array(), 'Admin.Advparameters.Notification');
            }
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = $this->trans(
                '%1$s (ID: %2$s) cannot be %3$s',
                array(
                    (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                    (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID',
                    ($validateOnly?'validated':'saved')
                ),
                'Admin.Advparameters.Notification'
            );
            $error_tmp = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').Db::getInstance()->getMsgError();
            if ($error_tmp != '') {
                $this->errors[] = $error_tmp;
            }
        } else {
            // Associate category to shop
            if ($shop_is_feature_active) {
                Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'category_shop
					WHERE id_category = '.(int)$category->id);

                if (!$shop_is_feature_active) {
                    $info['shop'] = 1;
                } elseif (!isset($info['shop']) || empty($info['shop'])) {
                    $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
                }

                // Get shops for each attributes
                $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

                foreach ($info['shop'] as $shop) {
                    if (!empty($shop) && !is_numeric($shop)) {
                        $category->addShop(Shop::getIdByName($shop));
                    } elseif (!empty($shop)) {
                        $category->addShop($shop);
                    }
                }
            }
        }
    }

    public function productImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false, $moreStep = 0)
    {
        if ($moreStep == 1) {
            return $this->productImportAccessories($offset, $limit, $crossStepsVariables);
        }
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();
        $shop_ids = Shop::getCompleteListOfShopsID();

        $force_ids = Tools::getValue('forceIDs');
        $match_ref = Tools::getValue('match_ref');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();
        if (!$validateOnly) {
            Module::setBatchMode(true);
        }

        $accessories = array();
        if ($crossStepsVariables !== false && array_key_exists('accessories', $crossStepsVariables)) {
            $accessories = $crossStepsVariables['accessories'];
        }

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->productImportOne(
                $info,
                $default_language_id,
                $id_lang,
                $force_ids,
                $regenerate,
                $shop_is_feature_active,
                $shop_ids,
                $match_ref,
                $accessories, // by ref
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);
        if (!$validateOnly) {
            Module::processDeferedFuncCall();
            Module::processDeferedClearCache();
            Tag::updateTagCount();
        }

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['accessories'] = $accessories;
        }
        return $line_count;
    }

    protected function productImportAccessories($offset, $limit, &$crossStepsVariables)
    {
        if ($crossStepsVariables === false || !array_key_exists('accessories', $crossStepsVariables)) {
            return 0;
        }

        $accessories = $crossStepsVariables['accessories'];

        if ($offset == 0) {
            //             AdminImportController::setLocale();
            Module::setBatchMode(true);
        }

        $line_count = 0;
        $i = 0;
        foreach ($accessories as $product_id => $links) {
            // skip elements until reaches offset
            if ($i < $offset) {
                $i++;
                continue;
            }

            if (count($links) > 0) { // We delete and relink only if there is accessories to link...
                // Bulk jobs: for performances, we need to do a minimum amount of SQL queries. No product inflation.
                $unique_ids = Product::getExistingIdsFromIdsOrRefs($links);
                Db::getInstance()->delete('accessory', 'id_product_1 = '.(int)$product_id);
                Product::changeAccessoriesForProduct($unique_ids, $product_id);
            }
            $line_count++;

            // Empty value to reduce array weight (that goes through HTTP requests each time) but do not unset array entry!
            $accessories[$product_id] = 0; // In JSON, 0 is lighter than null or false

            // stop when limit reached
            if ($line_count >= $limit) {
                break;
            }
        }

        if ($line_count < $limit) { // last pass only
            Module::processDeferedFuncCall();
            Module::processDeferedClearCache();
        }

        $crossStepsVariables['accessories'] = $accessories;

        return $line_count;
    }

    protected function productImportOne($info, $default_language_id, $id_lang, $force_ids, $regenerate, $shop_is_feature_active, $shop_ids, $match_ref, &$accessories, $validateOnly = false)
    {
        if (!$force_ids) {
            unset($info['id']);
        }

        $id_product = null;
        // Use product reference as key
        if (!empty($info['id'])) {
            $id_product = (int) $info['id'];
        } else if ($match_ref && isset($info['reference'])) {
            $idProductByRef = (int) Db::getInstance()->getValue('
                                    SELECT p.`id_product`
                                    FROM `' . _DB_PREFIX_ . 'product` p
                                    ' . Shop::addSqlAssociation('product', 'p') . '
                                    WHERE p.`reference` = "' . pSQL($info['reference']) . '"
                                ', false);
            if ($idProductByRef) {
                $id_product = $idProductByRef;
            }
        }

        $product = new Product($id_product);

        $update_advanced_stock_management_value = false;
        if (isset($product->id) && $product->id && Product::existsInDatabase((int)$product->id, 'product')) {
            $product->loadStockData();
            $update_advanced_stock_management_value = true;
            $category_data = Product::getProductCategories((int)$product->id);

            if (is_array($category_data)) {
                foreach ($category_data as $tmp) {
                    if (!isset($product->category) || !$product->category || is_array($product->category)) {
                        $product->category[] = $tmp;
                    }
                }
            }
        }

        AdminImportController::setEntityDefaultValues($product);
        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $product);

        if (!$shop_is_feature_active) {
            $product->shop = (int)Configuration::get('PS_SHOP_DEFAULT');
        } elseif (!isset($product->shop) || empty($product->shop)) {
            $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        if (!$shop_is_feature_active) {
            $product->id_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');
        } else {
            $product->id_shop_default = (int)Context::getContext()->shop->id;
        }

        // link product to shops
        $product->id_shop_list = array();
        foreach (explode($this->multiple_value_separator, $product->shop) as $shop) {
            if (!empty($shop) && !is_numeric($shop)) {
                $product->id_shop_list[] = Shop::getIdByName($shop);
            } elseif (!empty($shop)) {
                $product->id_shop_list[] = $shop;
            }
        }

        if ((int)$product->id_tax_rules_group != 0) {
            if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group))) {
                $address = $this->context->shop->getAddress();
                $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                $product_tax_calculator = $tax_manager->getTaxCalculator();
                $product->tax_rate = $product_tax_calculator->getTotalRate();
            } else {
                $this->addProductWarning(
                    'id_tax_rules_group',
                    $product->id_tax_rules_group,
                    $this->trans('Unknown tax rule group ID. You need to create a group with this ID first.', array(), 'Admin.Advparameters.Notification')
                );
            }
        }
        if (isset($product->manufacturer) && is_numeric($product->manufacturer) && Manufacturer::manufacturerExists((int)$product->manufacturer)) {
            $product->id_manufacturer = (int)$product->manufacturer;
        } elseif (isset($product->manufacturer) && is_string($product->manufacturer) && !empty($product->manufacturer)) {
            if ($manufacturer = Manufacturer::getIdByName($product->manufacturer)) {
                $product->id_manufacturer = (int)$manufacturer;
            } else {
                $manufacturer = new Manufacturer();
                $manufacturer->name = $product->manufacturer;
                $manufacturer->active = true;
                if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $manufacturer->add()) {
                    $product->id_manufacturer = (int)$manufacturer->id;
                    $manufacturer->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                            $manufacturer->name,
                            (isset($manufacturer->id) && !empty($manufacturer->id))? $manufacturer->id : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($product->supplier) && is_numeric($product->supplier) && Supplier::supplierExists((int)$product->supplier)) {
            $product->id_supplier = (int)$product->supplier;
        } elseif (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier)) {
            if ($supplier = Supplier::getIdByName($product->supplier)) {
                $product->id_supplier = (int)$supplier;
            } else {
                $supplier = new Supplier();
                $supplier->name = $product->supplier;
                $supplier->active = true;

                if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly &&  // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $supplier->add()) {
                    $product->id_supplier = (int)$supplier->id;
                    $supplier->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                            $supplier->name,
                            (isset($supplier->id) && !empty($supplier->id))? $supplier->id : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($product->price_tex) && !isset($product->price_tin)) {
            $product->price = $product->price_tex;
        } elseif (isset($product->price_tin) && !isset($product->price_tex)) {
            $product->price = $product->price_tin;
            // If a tax is already included in price, withdraw it from price
            if ($product->tax_rate) {
                $product->price = (float)number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
            }
        } elseif (isset($product->price_tin) && isset($product->price_tex)) {
            $product->price = $product->price_tex;
        }

        if (!Configuration::get('PS_USE_ECOTAX')) {
            $product->ecotax = 0;
        }

        if (isset($product->category) && is_array($product->category) && count($product->category)) {
            $product->id_category = array(); // Reset default values array
            foreach ($product->category as $value) {
                if (is_numeric($value)) {
                    if (Category::categoryExists((int)$value)) {
                        $product->id_category[] = (int)$value;
                    } else {
                        $category_to_create = new Category();
                        $category_to_create->id = (int)$value;
                        $category_to_create->name = AdminImportController::createMultiLangField($value);
                        $category_to_create->active = 1;
                        $category_to_create->id_parent = Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                        $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
                        $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                            !$validateOnly &&  // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                            $category_to_create->add()) {
                            $product->id_category[] = (int)$category_to_create->id;
                        } else {
                            if (!$validateOnly) {
                                $this->errors[] = sprintf(
                                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                                    $category_to_create->name[$default_language_id],
                                    (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
                                );
                            }
                            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                                    Db::getInstance()->getMsgError();
                            }
                        }
                    }
                } elseif (!$validateOnly && is_string($value) && !empty($value)) {
                    $category = Category::searchByPath($default_language_id, trim($value), $this, 'productImportCreateCat');
                    if ($category['id_category']) {
                        $product->id_category[] = (int)$category['id_category'];
                    } else {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array(
                                '%data%' => trim($value),
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            $product->id_category = array_values(array_unique($product->id_category));
        }

        // Will update default category if there is none set here. Home if no category at all.
        if (!isset($product->id_category_default) || !$product->id_category_default) {
            // this if will avoid ereasing default category if category column is not present in the CSV file (or ignored)
            if (isset($product->id_category[0])) {
                $product->id_category_default = (int)$product->id_category[0];
            } else {
                $defaultProductShop = new Shop($product->id_shop_default);
                $product->id_category_default = Category::getRootCategory(null, Validate::isLoadedObject($defaultProductShop)?$defaultProductShop:null)->id;
            }
        }

        $link_rewrite = (is_array($product->link_rewrite) && isset($product->link_rewrite[$id_lang])) ? trim($product->link_rewrite[$id_lang]) : '';
        $valid_link = Validate::isLinkRewrite($link_rewrite);
        if ((isset($product->link_rewrite[$id_lang]) && empty($product->link_rewrite[$id_lang])) || !$valid_link) {
            $link_rewrite = Tools::link_rewrite($product->name[$id_lang]);
            if ($link_rewrite == '') {
                $link_rewrite = 'friendly-url-autogeneration-failed';
            }
        }

        if (!$valid_link) {
            $this->informations[] = $this->trans('Rewrite link for %1$s (ID %2$s): re-written as %3$s.', array(
                '%1$s' => $product->name[$id_lang],
                '%2$s' => (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                '%3$s' => $link_rewrite,
            ), 'Admin.Advparameters.Notification');
        }

        if (!$valid_link || !(is_array($product->link_rewrite) && count($product->link_rewrite))) {
            $product->link_rewrite = AdminImportController::createMultiLangField($link_rewrite);
        } else {
            $product->link_rewrite[(int)$id_lang] = $link_rewrite;
        }

        // replace the value of separator by coma
        if ($this->multiple_value_separator != ',') {
            if (is_array($product->meta_keywords)) {
                foreach ($product->meta_keywords as &$meta_keyword) {
                    if (!empty($meta_keyword)) {
                        $meta_keyword = str_replace($this->multiple_value_separator, ',', $meta_keyword);
                    }
                }
            }
        }

        // Convert comma into dot for all floating values
        foreach (Product::$definition['fields'] as $key => $array) {
            if ($array['type'] == Product::TYPE_FLOAT) {
                $product->{$key} = str_replace(',', '.', $product->{$key});
            }
        }

        // Indexation is already 0 if it's a new product, but not if it's an update
        $product->indexed = 0;
        $productExistsInDatabase = false;

        if ($product->id && Product::existsInDatabase((int)$product->id, 'product')) {
            $productExistsInDatabase = true;
        }

        if (($match_ref && $product->reference && $product->existsRefInDatabase($product->reference)) || $productExistsInDatabase) {
            $product->date_upd = date('Y-m-d H:i:s');
        }

        $res = false;
        $field_error = $product->validateFields(UNFRIENDLY_ERROR, true);
        $lang_field_error = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
        if ($field_error === true && $lang_field_error === true) {
            // check quantity
            if ($product->quantity == null) {
                $product->quantity = 0;
            }

            // If match ref is specified && ref product && ref product already in base, trying to update
            if ($match_ref && $product->reference && $product->existsRefInDatabase($product->reference)) {
                $datas = Db::getInstance()->getRow('
					SELECT product_shop.`date_add`, p.`id_product`
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE p.`reference` = "'.pSQL($product->reference).'"
				', false);
                $product->id = (int)$datas['id_product'];
                $product->date_add = pSQL($datas['date_add']);
                $res = ($validateOnly || $product->update());
            } // Else If id product && id product already in base, trying to update
            elseif ($productExistsInDatabase) {
                $datas = Db::getInstance()->getRow('
					SELECT product_shop.`date_add`
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE p.`id_product` = '.(int)$product->id, false);
                $product->date_add = pSQL($datas['date_add']);
                $res = ($validateOnly || $product->update());
            }
            // If no id_product or update failed
            $product->force_id = (bool)$force_ids;

            if (!$res) {
                if (isset($product->date_add) && $product->date_add != '') {
                    $res = ($validateOnly || $product->add(false));
                } else {
                    $res = ($validateOnly || $product->add());
                }
            }

            if (!$validateOnly) {
                if ($product->getType() == Product::PTYPE_VIRTUAL) {
                    StockAvailable::setProductOutOfStock((int)$product->id, 1);
                } else {
                    StockAvailable::setProductOutOfStock((int)$product->id, (int)$product->out_of_stock);
                }

                if ($product_download_id = ProductDownload::getIdFromIdProduct((int)$product->id)) {
                    $product_download = new ProductDownload($product_download_id);
                    $product_download->delete(true);
                }

                if ($product->getType() == Product::PTYPE_VIRTUAL) {
                    $product_download = new ProductDownload();
                    $product_download->filename = ProductDownload::getNewFilename();
                    Tools::copy($info['file_url'], _PS_DOWNLOAD_DIR_.$product_download->filename);
                    $product_download->id_product = (int)$product->id;
                    $product_download->nb_downloadable = (int)$info['nb_downloadable'];
                    $product_download->date_expiration = $info['date_expiration'];
                    $product_download->nb_days_accessible = (int)$info['nb_days_accessible'];
                    $product_download->display_filename = basename($info['file_url']);
                    $product_download->add();
                }
            }
        }

        $shops = array();
        $product_shop = explode($this->multiple_value_separator, $product->shop);
        foreach ($product_shop as $shop) {
            if (empty($shop)) {
                continue;
            }
            $shop = trim($shop);
            if (!empty($shop) && !is_numeric($shop)) {
                $shop = Shop::getIdByName($shop);
            }

            if (in_array($shop, $shop_ids)) {
                $shops[] = $shop;
            } else {
                $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Shop is not valid', array(), 'Admin.Advparameters.Notification'));
            }
        }
        if (empty($shops)) {
            $shops = Shop::getContextListShopID();
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = sprintf(
                $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
            );
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                Db::getInstance()->getMsgError();
        } else {
            // Product supplier
            if (!$validateOnly && isset($product->id) && $product->id && isset($product->id_supplier) && property_exists($product, 'supplier_reference')) {
                $id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$product->id, 0, (int)$product->id_supplier);
                if ($id_product_supplier) {
                    $product_supplier = new ProductSupplier($id_product_supplier);
                } else {
                    $product_supplier = new ProductSupplier();
                }

                $product_supplier->id_product = (int)$product->id;
                $product_supplier->id_product_attribute = 0;
                $product_supplier->id_supplier = (int)$product->id_supplier;
                $product_supplier->product_supplier_price_te = $product->wholesale_price;
                $product_supplier->product_supplier_reference = $product->supplier_reference;
                $product_supplier->save();
            }

            // SpecificPrice (only the basic reduction feature is supported by the import)
            if (!$shop_is_feature_active) {
                $info['shop'] = 1;
            } elseif (!isset($info['shop']) || empty($info['shop'])) {
                $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
            }

            // Get shops for each attributes
            $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

            $id_shop_list = array();
            foreach ($info['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $id_shop_list[] = (int)Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $id_shop_list[] = $shop;
                }
            }

            if ((isset($info['reduction_price']) && $info['reduction_price'] > 0) || (isset($info['reduction_percent']) && $info['reduction_percent'] > 0)) {
                foreach ($id_shop_list as $id_shop) {
                    $specific_price = SpecificPrice::getSpecificPrice($product->id, $id_shop, 0, 0, 0, 1, 0, 0, 0, 0);

                    if (is_array($specific_price) && isset($specific_price['id_specific_price'])) {
                        $specific_price = new SpecificPrice((int)$specific_price['id_specific_price']);
                    } else {
                        $specific_price = new SpecificPrice();
                    }
                    $specific_price->id_product = (int)$product->id;
                    $specific_price->id_specific_price_rule = 0;
                    $specific_price->id_shop = $id_shop;
                    $specific_price->id_currency = 0;
                    $specific_price->id_country = 0;
                    $specific_price->id_group = 0;
                    $specific_price->price = -1;
                    $specific_price->id_customer = 0;
                    $specific_price->from_quantity = 1;
                    $specific_price->reduction = (isset($info['reduction_price']) && $info['reduction_price']) ? $info['reduction_price'] : $info['reduction_percent'] / 100;
                    $specific_price->reduction_type = (isset($info['reduction_price']) && $info['reduction_price']) ? 'amount' : 'percentage';
                    $specific_price->from = (isset($info['reduction_from']) && Validate::isDate($info['reduction_from'])) ? $info['reduction_from'] : '0000-00-00 00:00:00';
                    $specific_price->to = (isset($info['reduction_to']) && Validate::isDate($info['reduction_to']))  ? $info['reduction_to'] : '0000-00-00 00:00:00';
                    if (!$validateOnly && !$specific_price->save()) {
                        $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Discount is invalid', array(), 'Admin.Advparameters.Notification'));
                    }
                }
            }

            if (!$validateOnly && isset($product->tags) && !empty($product->tags)) {
                if (isset($product->id) && $product->id) {
                    $tags = Tag::getProductTags($product->id);
                    if (is_array($tags) && count($tags)) {
                        if (!empty($product->tags)) {
                            $product->tags = explode($this->multiple_value_separator, $product->tags);
                        }
                        if (is_array($product->tags) && count($product->tags)) {
                            foreach ($product->tags as $key => $tag) {
                                if (!empty($tag)) {
                                    $product->tags[$key] = trim($tag);
                                }
                            }
                            $tags[$id_lang] = $product->tags;
                            $product->tags = $tags;
                        }
                    }
                }
                // Delete tags for this id product, for no duplicating error
                Tag::deleteTagsForProduct($product->id);
                if (!is_array($product->tags) && !empty($product->tags)) {
                    $product->tags = AdminImportController::createMultiLangField($product->tags);
                    foreach ($product->tags as $key => $tags) {
                        $is_tag_added = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
                        if (!$is_tag_added) {
                            $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Tags list is invalid', array(), 'Admin.Advparameters.Notification'));
                            break;
                        }
                    }
                } else {
                    foreach ($product->tags as $key => $tags) {
                        $str = '';
                        foreach ($tags as $one_tag) {
                            $str .= $one_tag.$this->multiple_value_separator;
                        }
                        $str = rtrim($str, $this->multiple_value_separator);

                        $is_tag_added = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
                        if (!$is_tag_added) {
                            $this->addProductWarning(Tools::safeOutput($info['name']), (int)$product->id, $this->trans(
                                'Invalid tag(s) (%s)',
                                array($str),
                                'Admin.Notifications.Error'
                            ));
                            break;
                        }
                    }
                }
            }

            //delete existing images if "delete_existing_images" is set to 1
            if (!$validateOnly && isset($product->delete_existing_images)) {
                if ((bool)$product->delete_existing_images) {
                    $product->deleteImages();
                }
            }

            if (!$validateOnly && isset($product->image) && is_array($product->image) && count($product->image)) {
                $product_has_images = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                foreach ($product->image as $key => $url) {
                    $url = trim($url);
                    $error = false;
                    if (!empty($url)) {
                        $url = str_replace(' ', '%20', $url);

                        $image = new Image();
                        $image->id_product = (int)$product->id;
                        $image->position = Image::getHighestPosition($product->id) + 1;
                        $image->cover = (!$key && !$product_has_images) ? true : false;
                        $alt = $product->image_alt[$key];
                        if (strlen($alt) > 0) {
                            $image->legend = self::createMultiLangField($alt);
                        }
                        // file_exists doesn't work with HTTP protocol
                        if (($field_error = $image->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true && $image->add()) {
                            // associate image to selected shops
                            $image->associateTo($shops);
                            if (!AdminImportController::copyImg($product->id, $image->id, $url, 'products', !$regenerate)) {
                                $image->delete();
                                $this->warnings[] = $this->trans('Error copying image: %url%', array('%url%' => $url), 'Admin.Advparameters.Notification');
                            }
                        } else {
                            $error = true;
                        }
                    } else {
                        $error = true;
                    }

                    if ($error) {
                        $this->warnings[] = $this->trans('Product #%id%: the picture (%url%) cannot be saved.', array('%id%' => $image->id_product, '%url%'  => $url), 'Admin.Advparameters.Notification');
                    }
                }
            }

            if (!$validateOnly && isset($product->id_category) && is_array($product->id_category)) {
                $product->updateCategories(array_map('intval', $product->id_category));
            }

            $product->checkDefaultAttributes();
            if (!$validateOnly && !$product->cache_default_attribute) {
                Product::updateDefaultAttribute($product->id);
            }

            // Features import
            $features = get_object_vars($product);

            if (!$validateOnly && isset($features['features']) && !empty($features['features'])) {
                foreach (explode($this->multiple_value_separator, $features['features']) as $single_feature) {
                    if (empty($single_feature)) {
                        continue;
                    }
                    $tab_feature = explode(':', $single_feature);
                    $feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
                    $feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
                    $position = isset($tab_feature[2]) ? (int)$tab_feature[2] - 1 : false;
                    $custom = isset($tab_feature[3]) ? (int)$tab_feature[3] : false;
                    if (!empty($feature_name) && !empty($feature_value)) {
                        $id_feature = (int)Feature::addFeatureImport($feature_name, $position);
                        $id_product = null;
                        if ($force_ids || $match_ref) {
                            $id_product = (int)$product->id;
                        }
                        $id_feature_value = (int)FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $id_lang, $custom);
                        Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
                    }
                }
            }
            // clean feature positions to avoid conflict
            Feature::cleanPositions();

            // set advanced stock managment
            if (!$validateOnly && isset($product->advanced_stock_management)) {
                if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0) {
                    $this->warnings[] = $this->trans('Advanced stock management has incorrect value. Not set for product %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management == 1) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot enable on product %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                } elseif ($update_advanced_stock_management_value) {
                    $product->setAdvancedStockManagement($product->advanced_stock_management);
                }
                // automaticly disable depends on stock, if a_s_m set to disabled
                if (StockAvailable::dependsOnStock($product->id) == 1 && $product->advanced_stock_management == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, 0);
                }
            }

            // Check if warehouse exists
            if (isset($product->warehouse) && $product->warehouse) {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, warehouse not set on product %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                } elseif (!$validateOnly) {
                    if (Warehouse::exists($product->warehouse)) {
                        // Get already associated warehouses
                        $associated_warehouses_collection = WarehouseProductLocation::getCollection($product->id);
                        // Delete any entry in warehouse for this product
                        foreach ($associated_warehouses_collection as $awc) {
                            $awc->delete();
                        }
                        $warehouse_location_entity = new WarehouseProductLocation();
                        $warehouse_location_entity->id_product = $product->id;
                        $warehouse_location_entity->id_product_attribute = 0;
                        $warehouse_location_entity->id_warehouse = $product->warehouse;
                        if (WarehouseProductLocation::getProductLocation($product->id, 0, $product->warehouse) !== false) {
                            $warehouse_location_entity->update();
                        } else {
                            $warehouse_location_entity->save();
                        }
                        StockAvailable::synchronize($product->id);
                    } else {
                        $this->warnings[] = $this->trans('Warehouse did not exist, cannot set on product  %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                    }
                }
            }

            // stock available
            if (isset($product->depends_on_stock)) {
                if ($product->depends_on_stock != 0 && $product->depends_on_stock != 1) {
                    $this->warnings[] = $this->trans('Incorrect value for "Depends on stock" for product %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                } elseif ((!$product->advanced_stock_management || $product->advanced_stock_management == 0) && $product->depends_on_stock == 1) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot set "Depends on stock" for product %name% ', array('%name%' => $product->name[$default_language_id]), 'Admin.Advparameters.Notification');
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock($product->id, $product->depends_on_stock);
                }

                // This code allows us to set qty and disable depends on stock
                if (!$validateOnly && isset($product->quantity)) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($product->depends_on_stock == 1) {
                        $stock_manager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $product->wholesale_price);
                        if ($price == 0) {
                            $price = 0.000001;
                        }
                        $price = round(floatval($price), 6);
                        $warehouse = new Warehouse($product->warehouse);
                        if ($stock_manager->addProduct((int)$product->id, 0, $warehouse, (int)$product->quantity, 1, $price, true)) {
                            StockAvailable::synchronize((int)$product->id);
                        }
                    } else {
                        if ($shop_is_feature_active) {
                            foreach ($shops as $shop) {
                                StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                            }
                        } else {
                            StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
                        }
                    }
                }
            } elseif (!$validateOnly) {
                // if not depends_on_stock set, use normal qty
                if ($shop_is_feature_active) {
                    foreach ($shops as $shop) {
                        StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                    }
                } else {
                    StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$this->context->shop->id);
                }
            }

            // Accessories linkage
            if (isset($product->accessories) && !$validateOnly && is_array($product->accessories) && count($product->accessories)) {
                $accessories[$product->id] = $product->accessories;
            }
        }
    }

    public function productImportCreateCat($default_language_id, $category_name, $id_parent_category = null)
    {
        $category_to_create = new Category();
        $shop_is_feature_active = Shop::isFeatureActive();
        if (!$shop_is_feature_active) {
            $category_to_create->id_shop_default = 1;
        } else {
            $category_to_create->id_shop_default = (int)Context::getContext()->shop->id;
        }
        $category_to_create->name = AdminImportController::createMultiLangField(trim($category_name));
        $category_to_create->active = 1;
        $category_to_create->id_parent = (int)$id_parent_category ? (int)$id_parent_category : (int)Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
        $category_link_rewrite = Tools::link_rewrite($category_to_create->name[$default_language_id]);
        $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);

        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) !== true ||
            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) !== true ||
            !$category_to_create->add()) {
            $this->errors[] = sprintf(
                $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                $category_to_create->name[$default_language_id],
                (isset($category_to_create->id) && !empty($category_to_create->id))? $category_to_create->id : 'null'
            );
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                    Db::getInstance()->getMsgError();
            }
        }
    }

    public function attributeImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false)
    {
        $default_language = Configuration::get('PS_LANG_DEFAULT');

        $groups = array();
        if ($crossStepsVariables !== false && array_key_exists('groups', $crossStepsVariables)) {
            $groups = $crossStepsVariables['groups'];
        }
        foreach (AttributeGroup::getAttributesGroups($default_language) as $group) {
            $groups[$group['name']] = (int)$group['id_attribute_group'];
        }

        $attributes = array();
        if ($crossStepsVariables !== false && array_key_exists('attributes', $crossStepsVariables)) {
            $attributes = $crossStepsVariables['attributes'];
        }
        foreach (Attribute::getAttributes($default_language) as $attribute) {
            $attributes[$attribute['attribute_group'].'_'.$attribute['name']] = (int)$attribute['id_attribute'];
        }

        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;

            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);
            $info = array_map('trim', $info);

            $this->attributeImportOne(
                $info,
                $default_language,
                $groups, // by ref
                $attributes, // by ref
                $regenerate,
                $shop_is_feature_active,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['groups'] = $groups;
            $crossStepsVariables['attributes'] = $attributes;
        }
        return $line_count;
    }

    protected function attributeImportOne($info, $default_language, &$groups, &$attributes, $regenerate, $shop_is_feature_active, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if (!$shop_is_feature_active) {
            $info['shop'] = 1;
        } elseif (!isset($info['shop']) || empty($info['shop'])) {
            $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        // Get shops for each attributes
        $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

        $id_shop_list = array();
        if (is_array($info['shop']) && count($info['shop'])) {
            foreach ($info['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $id_shop_list[] = Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $id_shop_list[] = $shop;
                }
            }
        }

        if (isset($info['id_product']) && $info['id_product']) {
            $product = new Product((int)$info['id_product'], false, $default_language);
        } elseif (Tools::getValue('match_ref') && isset($info['product_reference']) && $info['product_reference']) {
            $datas = Db::getInstance()->getRow('
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE p.`reference` = "'.pSQL($info['product_reference']).'"
			', false);
            if (isset($datas['id_product']) && $datas['id_product']) {
                $product = new Product((int)$datas['id_product'], false, $default_language);
            } else {
                return;
            }
        } else {
            return;
        }

        $id_image = array();

        if (isset($info['image_url']) && $info['image_url']) {
            $info['image_url'] = explode($this->multiple_value_separator, $info['image_url']);

            if (is_array($info['image_url']) && count($info['image_url'])) {
                foreach ($info['image_url'] as $key => $url) {
                    $url = trim($url);
                    $product_has_images = (bool)Image::getImages($this->context->language->id, $product->id);

                    $image = new Image();
                    $image->id_product = (int)$product->id;
                    $image->position = Image::getHighestPosition($product->id) + 1;
                    $image->cover = (!$product_has_images) ? true : false;

                    if (isset($info['image_alt'])) {
                        $alt = self::split($info['image_alt']);
                        if (isset($alt[$key]) && strlen($alt[$key]) > 0) {
                            $alt = self::createMultiLangField($alt[$key]);
                            $image->legend = $alt;
                        }
                    }

                    $field_error = $image->validateFields(UNFRIENDLY_ERROR, true);
                    $lang_field_error = $image->validateFieldsLang(UNFRIENDLY_ERROR, true);

                    if ($field_error === true &&
                        $lang_field_error === true &&
                        !$validateOnly &&
                        $image->add()) {
                        $image->associateTo($id_shop_list);
                        // FIXME: 2s/image !
                        if (!AdminImportController::copyImg($product->id, $image->id, $url, 'products', !$regenerate)) {
                            $this->warnings[] = $this->trans('Error copying image: %url%', array('%url%' => $url), 'Admin.Advparameters.Notification');
                            $image->delete();
                        } else {
                            $id_image[] = (int)$image->id;
                        }
                        // until here
                    } else {
                        if (!$validateOnly) {
                            $this->warnings[] = $this->trans('%data% cannot be saved',
                                array(
                                    '%data%' => (isset($image->id_product) ? ' ('.$image->id_product.')' : ''),
                                ),
                                'Admin.Advparameters.Notification'
                            );
                        }
                        if ($field_error !== true || $lang_field_error !== true) {
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').mysql_error();
                        }
                    }
                }
            }
        } elseif (isset($info['image_position']) && $info['image_position']) {
            $info['image_position'] = explode($this->multiple_value_separator, $info['image_position']);

            if (is_array($info['image_position']) && count($info['image_position'])) {
                foreach ($info['image_position'] as $position) {
                    // choose images from product by position
                    $images = $product->getImages($default_language);

                    if ($images) {
                        foreach ($images as $row) {
                            if ($row['position'] == (int)$position) {
                                $id_image[] = (int)$row['id_image'];
                                break;
                            }
                        }
                    }
                    if (empty($id_image)) {
                        $this->warnings[] = sprintf(
                            $this->trans('No image was found for combination with id_product = %s and image position = %s.', array(), 'Admin.Advparameters.Notification'),
                            $product->id,
                            (int)$position
                        );
                    }
                }
            }
        }

        $id_attribute_group = 0;
        // groups
        $groups_attributes = array();
        if (isset($info['group'])) {
            foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                if (empty($group)) {
                    continue;
                }
                $tab_group = explode(':', $group);
                $group = trim($tab_group[0]);
                if (!isset($tab_group[1])) {
                    $type = 'select';
                } else {
                    $type = trim($tab_group[1]);
                }

                // sets group
                $groups_attributes[$key]['group'] = $group;

                // if position is filled
                if (isset($tab_group[2])) {
                    $position = trim($tab_group[2]);
                } else {
                    $position = false;
                }

                if (!isset($groups[$group])) {
                    $obj = new AttributeGroup();
                    $obj->is_color_group = false;
                    $obj->group_type = pSQL($type);
                    $obj->name[$default_language] = $group;
                    $obj->public_name[$default_language] = $group;
                    $obj->position = (!$position) ? AttributeGroup::getHigherPosition() + 1 : $position;

                    if (($field_error = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                        ($lang_field_error = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
                        // here, cannot avoid attributeGroup insertion to avoid an error during validation step.
                        //if (!$validateOnly) {
                            $obj->add();
                        $obj->associateTo($id_shop_list);
                        $groups[$group] = $obj->id;
                        //}
                    } else {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
                    }

                    // fills groups attributes
                    $id_attribute_group = $obj->id;
                    $groups_attributes[$key]['id'] = $id_attribute_group;
                } else {
                    // already exists

                    $id_attribute_group = $groups[$group];
                    $groups_attributes[$key]['id'] = $id_attribute_group;
                }
            }
        }

        // inits attribute
        $id_product_attribute = 0;
        $id_product_attribute_update = false;
        $attributes_to_add = array();

        // for each attribute
        if (isset($info['attribute'])) {
            foreach (explode($this->multiple_value_separator, $info['attribute']) as $key => $attribute) {
                if (empty($attribute)) {
                    continue;
                }
                $tab_attribute = explode(':', $attribute);
                $attribute = trim($tab_attribute[0]);
                // if position is filled
                if (isset($tab_attribute[1])) {
                    $position = trim($tab_attribute[1]);
                } else {
                    $position = false;
                }

                if (isset($groups_attributes[$key])) {
                    $group = $groups_attributes[$key]['group'];
                    if (!isset($attributes[$group.'_'.$attribute]) && count($groups_attributes[$key]) == 2) {
                        $id_attribute_group = $groups_attributes[$key]['id'];
                        $obj = new Attribute();
                        // sets the proper id (corresponding to the right key)
                        $obj->id_attribute_group = $groups_attributes[$key]['id'];
                        $obj->name[$default_language] = str_replace('\n', '', str_replace('\r', '', $attribute));
                        $obj->position = (!$position && isset($groups[$group])) ? Attribute::getHigherPosition($groups[$group]) + 1 : $position;

                        if (($field_error = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
                            if (!$validateOnly) {
                                $obj->add();
                                $obj->associateTo($id_shop_list);
                                $attributes[$group.'_'.$attribute] = $obj->id;
                            }
                        } else {
                            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
                        }
                    }

                    $info['minimal_quantity'] = isset($info['minimal_quantity']) && $info['minimal_quantity'] ? (int)$info['minimal_quantity'] : 1;
                    $info['low_stock_threshold'] = empty($info['low_stock_threshold']) && '0' != $info['low_stock_threshold'] ? null : (int)$info['low_stock_threshold'];
                    $info['low_stock_alert'] = !empty($info['low_stock_alert']);

                    $info['wholesale_price'] = str_replace(',', '.', $info['wholesale_price']);
                    $info['price'] = str_replace(',', '.', $info['price']);
                    $info['ecotax'] = str_replace(',', '.', $info['ecotax']);
                    $info['weight'] = str_replace(',', '.', $info['weight']);
                    $info['available_date'] = Validate::isDate($info['available_date']) ? $info['available_date'] : null;

                    if (!Validate::isEan13($info['ean13'])) {
                        $this->warnings[] = $this->trans('EAN13 "%ean13%" has incorrect value for product with id %id%.', array('%ean13%' => $info['ean13'], '%id%' => $product->id), 'Admin.Advparameters.Notification');
                        $info['ean13'] = '';
                    }

                    if ($info['default_on'] && !$validateOnly) {
                        $product->deleteDefaultAttributes();
                    }

                    // if a reference is specified for this product, get the associate id_product_attribute to UPDATE
                    if (isset($info['reference']) && !empty($info['reference'])) {
                        $id_product_attribute = Combination::getIdByReference($product->id, strval($info['reference']));

                        // updates the attribute
                        if ($id_product_attribute && !$validateOnly) {
                            // gets all the combinations of this product
                            $attribute_combinations = $product->getAttributeCombinations($default_language);
                            foreach ($attribute_combinations as $attribute_combination) {
                                if ($id_product_attribute && in_array($id_product_attribute, $attribute_combination)) {
                                    // FIXME: ~3s/declinaison
                                    $product->updateAttribute(
                                        $id_product_attribute,
                                        (float)$info['wholesale_price'],
                                        (float)$info['price'],
                                        (float)$info['weight'],
                                        0,
                                        (Configuration::get('PS_USE_ECOTAX') ? (float)$info['ecotax'] : 0),
                                        $id_image,
                                        (string)$info['reference'],
                                        (string)$info['ean13'],
                                        ((int)$info['default_on'] ? (int)$info['default_on'] : null),
                                        0,
                                        (string)$info['upc'],
                                        (int)$info['minimal_quantity'],
                                        $info['available_date'],
                                        null,
                                        $id_shop_list,
                                        '',
                                        $info['low_stock_threshold'],
                                        $info['low_stock_alert']
                                    );
                                    $id_product_attribute_update = true;
                                    if (isset($info['supplier_reference']) && !empty($info['supplier_reference'])) {
                                        $product->addSupplierReference($product->id_supplier, $id_product_attribute, $info['supplier_reference']);
                                    }
// until here
                                }
                            }
                        }
                    }

                    // if no attribute reference is specified, creates a new one
                    if (!$id_product_attribute && !$validateOnly) {
                        $id_product_attribute = $product->addCombinationEntity(
                            (float)$info['wholesale_price'],
                            (float)$info['price'],
                            (float)$info['weight'],
                            0,
                            (Configuration::get('PS_USE_ECOTAX') ? (float)$info['ecotax'] : 0),
                            (int)$info['quantity'],
                            $id_image,
                            (string)$info['reference'],
                            0,
                            (string)$info['ean13'],
                            ((int)$info['default_on'] ? (int)$info['default_on'] : null),
                            0,
                            (string)$info['upc'],
                            (int)$info['minimal_quantity'],
                            $id_shop_list,
                            $info['available_date'],
                            '',
                            $info['low_stock_threshold'],
                            $info['low_stock_alert']
                        );

                        if (isset($info['supplier_reference']) && !empty($info['supplier_reference'])) {
                            $product->addSupplierReference($product->id_supplier, $id_product_attribute, $info['supplier_reference']);
                        }
                    }

                    // fills our attributes array, in order to add the attributes to the product_attribute afterwards
                    if (isset($attributes[$group.'_'.$attribute])) {
                        $attributes_to_add[] = (int)$attributes[$group.'_'.$attribute];
                    }

                    // after insertion, we clean attribute position and group attribute position
                    if (!$validateOnly) {
                        $obj = new Attribute();
                        $obj->cleanPositions((int)$id_attribute_group, false);
                        AttributeGroup::cleanPositions();
                    }
                }
            }
        }

        $product->checkDefaultAttributes();
        if (!$product->cache_default_attribute && !$validateOnly) {
            Product::updateDefaultAttribute($product->id);
        }
        if ($id_product_attribute) {
            if (!$validateOnly) {
                // now adds the attributes in the attribute_combination table
                if ($id_product_attribute_update) {
                    Db::getInstance()->execute('
						DELETE FROM '._DB_PREFIX_.'product_attribute_combination
						WHERE id_product_attribute = '.(int)$id_product_attribute);
                }

                foreach ($attributes_to_add as $attribute_to_add) {
                    Db::getInstance()->execute('
						INSERT IGNORE INTO '._DB_PREFIX_.'product_attribute_combination (id_attribute, id_product_attribute)
						VALUES ('.(int)$attribute_to_add.','.(int)$id_product_attribute.')', false);
                }
            }

            // set advanced stock managment
            if (isset($info['advanced_stock_management'])) {
                if ($info['advanced_stock_management'] != 1 && $info['advanced_stock_management'] != 0) {
                    $this->warnings[] = $this->trans('Advanced stock management has incorrect value. Not set for product with id %id%.', array('%id%' => $product->id), 'Admin.Advparameters.Notification');
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $info['advanced_stock_management'] == 1) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot enable on product with id %id%.', array('%id%' => $product->id), 'Admin.Advparameters.Notification');
                } elseif (!$validateOnly) {
                    $product->setAdvancedStockManagement($info['advanced_stock_management']);
                }
                // automaticly disable depends on stock, if a_s_m set to disabled
                if (!$validateOnly && StockAvailable::dependsOnStock($product->id) == 1 && $info['advanced_stock_management'] == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, 0, null, $id_product_attribute);
                }
            }

            // Check if warehouse exists
            if (isset($info['warehouse']) && $info['warehouse']) {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, warehouse is not set on product with id %id%.', array('%id%' => $product->id), 'Admin.Advparameters.Notification');
                } else {
                    if (Warehouse::exists($info['warehouse'])) {
                        $warehouse_location_entity = new WarehouseProductLocation();
                        $warehouse_location_entity->id_product = $product->id;
                        $warehouse_location_entity->id_product_attribute = $id_product_attribute;
                        $warehouse_location_entity->id_warehouse = $info['warehouse'];
                        if (!$validateOnly) {
                            if (WarehouseProductLocation::getProductLocation($product->id, $id_product_attribute, $info['warehouse']) !== false) {
                                $warehouse_location_entity->update();
                            } else {
                                $warehouse_location_entity->save();
                            }
                            StockAvailable::synchronize($product->id);
                        }
                    } else {
                        $this->warnings[] = $this->trans('Warehouse did not exist, cannot set on product %name%.', array('%name%' => $product->name[$default_language]), 'Admin.Advparameters.Notification');
                    }
                }
            }

            // stock available
            if (isset($info['depends_on_stock'])) {
                if ($info['depends_on_stock'] != 0 && $info['depends_on_stock'] != 1) {
                    $this->warnings[] = $this->trans('Incorrect value for "Depends on stock" for product %name% ', array('%name%' => $product->name[$default_language]), 'Admin.Notifications.Error');
                } elseif ((!$info['advanced_stock_management'] || $info['advanced_stock_management'] == 0) && $info['depends_on_stock'] == 1) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot set "Depends on stock" for product %name% ', array('%name%' => $product->name[$default_language]), 'Admin.Advparameters.Notification');
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock($product->id, $info['depends_on_stock'], null, $id_product_attribute);
                }

                // This code allows us to set qty and disable depends on stock
                if (isset($info['quantity'])) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($info['depends_on_stock'] == 1) {
                        $stock_manager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $info['wholesale_price']);
                        if ($price == 0) {
                            $price = 0.000001;
                        }
                        $price = round(floatval($price), 6);
                        $warehouse = new Warehouse($info['warehouse']);
                        if (!$validateOnly && $stock_manager->addProduct((int)$product->id, $id_product_attribute, $warehouse, (int)$info['quantity'], 1, $price, true)) {
                            StockAvailable::synchronize((int)$product->id);
                        }
                    } elseif (!$validateOnly) {
                        if ($shop_is_feature_active) {
                            foreach ($id_shop_list as $shop) {
                                StockAvailable::setQuantity((int)$product->id, $id_product_attribute, (int)$info['quantity'], (int)$shop);
                            }
                        } else {
                            StockAvailable::setQuantity((int)$product->id, $id_product_attribute, (int)$info['quantity'], $this->context->shop->id);
                        }
                    }
                }
            } elseif (!$validateOnly) { // if not depends_on_stock set, use normal qty
                if ($shop_is_feature_active) {
                    foreach ($id_shop_list as $shop) {
                        StockAvailable::setQuantity((int)$product->id, $id_product_attribute, (int)$info['quantity'], (int)$shop);
                    }
                } else {
                    StockAvailable::setQuantity((int)$product->id, $id_product_attribute, (int)$info['quantity'], $this->context->shop->id);
                }
            }
        }
    }

    public function customerImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->customerImportOne(
                $info,
                $default_language_id,
                $id_lang,
                $shop_is_feature_active,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function customerImportOne($info, $default_language_id, $id_lang, $shop_is_feature_active, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $customer = new Customer((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id'])) {
                $customer = new Customer((int)$info['id']);
            } else {
                $customer = new Customer();
            }
        }

        $customer_exist = false;
        $autodate = true;

        if (array_key_exists('id', $info) && (int)$info['id'] && Customer::customerIdExistsStatic((int)$info['id']) && Validate::isLoadedObject($customer)) {
            $current_id_customer = (int)$customer->id;
            $current_id_shop = (int)$customer->id_shop;
            $current_id_shop_group = (int)$customer->id_shop_group;
            $customer_exist = true;
            $customer_groups = $customer->getGroups();
            $addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
        }

        // Group Importation
        if (isset($info['group']) && !empty($info['group'])) {
            foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                $group = trim($group);
                if (empty($group)) {
                    continue;
                }
                $id_group = false;
                if (is_numeric($group) && $group) {
                    $my_group = new Group((int)$group);
                    if (Validate::isLoadedObject($my_group)) {
                        $customer_groups[] = (int)$group;
                    }
                    continue;
                }
                $my_group = Group::searchByName($group);
                if (isset($my_group['id_group']) && $my_group['id_group']) {
                    $id_group = (int)$my_group['id_group'];
                }
                if (!$id_group) {
                    $my_group = new Group();
                    $my_group->name = array($id_lang => $group);
                    if ($id_lang != $default_language_id) {
                        $my_group->name = $my_group->name + array($default_language_id => $group);
                    }
                    $my_group->price_display_method = 1;
                    if (!$validateOnly) {
                        $my_group->add();
                        if (Validate::isLoadedObject($my_group)) {
                            $id_group = (int)$my_group->id;
                        }
                    }
                }
                if ($id_group) {
                    $customer_groups[] = (int)$id_group;
                }
            }
        } elseif (empty($info['group']) && isset($customer->id) && $customer->id) {
            $customer_groups = array(0 => Configuration::get('PS_CUSTOMER_GROUP'));
        }

        if (isset($info['date_add']) && !empty($info['date_add'])) {
            $autodate = false;
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $customer);

        if ($customer->passwd) {
            $customer->passwd = $this->get('hashing')->hash($customer->passwd, _COOKIE_KEY_);
        }

        $id_shop_list = explode($this->multiple_value_separator, $customer->id_shop);
        $customers_shop = array();
        $customers_shop['shared'] = array();
        $default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
        if ($shop_is_feature_active && $id_shop_list) {
            foreach ($id_shop_list as $id_shop) {
                if (empty($id_shop)) {
                    continue;
                }
                $shop = new Shop((int)$id_shop);
                $group_shop = $shop->getGroup();
                if ($group_shop->share_customer) {
                    if (!in_array($group_shop->id, $customers_shop['shared'])) {
                        $customers_shop['shared'][(int)$id_shop] = $group_shop->id;
                    }
                } else {
                    $customers_shop[(int)$id_shop] = $group_shop->id;
                }
            }
        } else {
            $default_shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
            $default_shop->getGroup();
            $customers_shop[$default_shop->id] = $default_shop->getGroup()->id;
        }

        //set temporary for validate field
        $customer->id_shop = $default_shop->id;
        $customer->id_shop_group = $default_shop->getGroup()->id;
        if (isset($info['id_default_group']) && !empty($info['id_default_group']) && !is_numeric($info['id_default_group'])) {
            $info['id_default_group'] = trim($info['id_default_group']);
            $my_group = Group::searchByName($info['id_default_group']);
            if (isset($my_group['id_group']) && $my_group['id_group']) {
                $info['id_default_group'] = (int)$my_group['id_group'];
            }
        }
        $my_group = new Group($customer->id_default_group);
        if (!Validate::isLoadedObject($my_group)) {
            $customer->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        }
        $customer_groups[] = (int)$customer->id_default_group;
        $customer_groups = array_flip(array_flip($customer_groups));

        // Bug when updating existing user that were csv-imported before...
        if (isset($customer->date_upd) && $customer->date_upd == '0000-00-00 00:00:00') {
            $customer->date_upd = date('Y-m-d H:i:s');
        }

        $res = false;
        if (($field_error = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            $res = true;
            foreach ($customers_shop as $id_shop => $id_group) {
                $customer->force_id = (bool)$force_ids;
                if ($id_shop == 'shared') {
                    foreach ($id_group as $key => $id) {
                        $customer->id_shop = (int)$key;
                        $customer->id_shop_group = (int)$id;
                        if ($customer_exist && ((int)$current_id_shop_group == (int)$id || in_array($current_id_shop, ShopGroup::getShopsFromGroup($id)))) {
                            $customer->id = (int)$current_id_customer;
                            $res &= ($validateOnly || $customer->update());
                        } else {
                            $res &= ($validateOnly || $customer->add($autodate));
                            if (!$validateOnly && isset($addresses)) {
                                foreach ($addresses as $address) {
                                    $address['id_customer'] = $customer->id;
                                    unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
                                    Db::getInstance()->insert('address', $address, false, false);
                                }
                            }
                        }
                        if ($res && !$validateOnly && isset($customer_groups)) {
                            $customer->updateGroup($customer_groups);
                        }
                    }
                } else {
                    $customer->id_shop = $id_shop;
                    $customer->id_shop_group = $id_group;
                    if ($customer_exist && (int)$id_shop == (int)$current_id_shop) {
                        $customer->id = (int)$current_id_customer;
                        $res &= ($validateOnly || $customer->update());
                    } else {
                        $res &= ($validateOnly || $customer->add($autodate));
                        if (!$validateOnly && isset($addresses)) {
                            foreach ($addresses as $address) {
                                $address['id_customer'] = $customer->id;
                                unset($address['country'], $address['state'], $address['state_iso'], $address['id_address']);
                                Db::getInstance()->insert('address', $address, false, false);
                            }
                        }
                    }
                    if ($res && !$validateOnly && isset($customer_groups)) {
                        $customer->updateGroup($customer_groups);
                    }
                }
            }
        }

        if (isset($customer_groups)) {
            unset($customer_groups);
        }
        if (isset($current_id_customer)) {
            unset($current_id_customer);
        }
        if (isset($current_id_shop)) {
            unset($current_id_shop);
        }
        if (isset($current_id_shop_group)) {
            unset($current_id_shop_group);
        }
        if (isset($addresses)) {
            unset($addresses);
        }

        if (!$res) {
            if ($validateOnly) {
                $this->errors[] = $this->trans(
                    'Email address %1$s (ID: %2$s) cannot be validated.',
                    array(
                        $info['email'],
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                    ),
                    'Admin.Advparameters.Notification'
                );
            } else {
                $this->errors[] = $this->trans(
                    'Email address %1$s (ID: %2$s) cannot be saved.',
                    array(
                        $info['email'],
                        (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                    ),
                    'Admin.Advparameters.Notification'
                );
            }
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                Db::getInstance()->getMsgError();
        }
    }

    public function addressImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->addressImportOne(
                $info,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function addressImportOne($info, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $address = new Address((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Address::addressExists((int)$info['id'])) {
                $address = new Address((int)$info['id']);
            } else {
                $address = new Address();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $address);

        if (isset($address->country) && is_numeric($address->country)) {
            if (Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), (int)$address->country)) {
                $address->id_country = (int)$address->country;
            }
        } elseif (isset($address->country) && is_string($address->country) && !empty($address->country)) {
            if ($id_country = Country::getIdByName(null, $address->country)) {
                $address->id_country = (int)$id_country;
            } else {
                $country = new Country();
                $country->active = 1;
                $country->name = AdminImportController::createMultiLangField($address->country);
                $country->id_zone = 0; // Default zone for country to create
                $country->iso_code = Tools::strtoupper(Tools::substr($address->country, 0, 2)); // Default iso for country to create
                $country->contains_states = 0; // Default value for country to create
                $lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                if (($field_error = $country->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $country->add()) {
                    $address->id_country = (int)$country->id;
                } else {
                    if (!$validateOnly) {
                        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array(
                                '%data%' => $country->name[$default_language_id],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->state) && is_numeric($address->state)) {
            if (State::getNameById((int)$address->state)) {
                $address->id_state = (int)$address->state;
            }
        } elseif (isset($address->state) && is_string($address->state) && !empty($address->state)) {
            if ($id_state = State::getIdByName($address->state)) {
                $address->id_state = (int)$id_state;
            } else {
                $state = new State();
                $state->active = 1;
                $state->name = $address->state;
                $state->id_country = isset($country->id) ? (int)$country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                $state->iso_code = Tools::strtoupper(Tools::substr($address->state, 0, 2)); // Default iso for state to create
                $state->tax_behavior = 0;
                if (($field_error = $state->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $state->add()) {
                    $address->id_state = (int)$state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array(
                                '%data%' => $state->name,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->customer_email) && !empty($address->customer_email)) {
            if (Validate::isEmail($address->customer_email)) {
                // a customer could exists in different shop
                $customer_list = Customer::getCustomersByEmail($address->customer_email);

                if (count($customer_list) == 0) {
                    if ($validateOnly) {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be validated',
                            array(
                                $address->customer_email,
                                Db::getInstance()->getMsgError(),
                                (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be saved',
                            array(
                                $address->customer_email,
                                Db::getInstance()->getMsgError(),
                                (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                $this->errors[] = $this->trans('"%email%" is not a valid email address.', array('%email%' => $address->customer_email), 'Admin.Advparameters.Notification');
                return;
            }
        } elseif (isset($address->id_customer) && !empty($address->id_customer)) {
            if (Customer::customerIdExistsStatic((int)$address->id_customer)) {
                $customer = new Customer((int)$address->id_customer);

                // a customer could exists in different shop
                $customer_list = Customer::getCustomersByEmail($customer->email);

                if (count($customer_list) == 0) {
                    if ($validateOnly) {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be validated',
                            array(
                                $customer->email,
                                Db::getInstance()->getMsgError(),
                                (int)$address->id_customer,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be saved',
                            array(
                                $customer->email,
                                Db::getInstance()->getMsgError(),
                                (int)$address->id_customer,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                if ($validateOnly) {
                    $this->errors[] = $this->trans(
                        'The customer ID #%d does not exist in the database, and therefore cannot be validated.',
                        array(
                            $address->id_customer,
                        ),
                        'Admin.Advparameters.Notification'
                    );
                } else {
                    $this->errors[] = $this->trans(
                        'The customer ID #%d does not exist in the database, and therefore cannot be saved.',
                        array(
                            $address->id_customer,
                        ),
                        'Admin.Advparameters.Notification'
                    );
                }
            }
        } else {
            $customer_list = array();
            $address->id_customer = 0;
        }

        if (isset($address->manufacturer) && is_numeric($address->manufacturer) && Manufacturer::manufacturerExists((int)$address->manufacturer)) {
            $address->id_manufacturer = (int)$address->manufacturer;
        } elseif (isset($address->manufacturer) && is_string($address->manufacturer) && !empty($address->manufacturer)) {
            if ($manufacturerId = Manufacturer::getIdByName($address->manufacturer)) {
                $address->id_manufacturer = $manufacturerId;
            } else {
                $manufacturer = new Manufacturer();
                $manufacturer->name = $address->manufacturer;
                if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $manufacturer->add()) {
                    $address->id_manufacturer = (int)$manufacturer->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                            $manufacturer->name,
                            (isset($manufacturer->id) && !empty($manufacturer->id))? $manufacturer->id : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->supplier) && is_numeric($address->supplier) && Supplier::supplierExists((int)$address->supplier)) {
            $address->id_supplier = (int)$address->supplier;
        } elseif (isset($address->supplier) && is_string($address->supplier) && !empty($address->supplier)) {
            if ($supplierId = Supplier::getIdByName($address->supplier)) {
                $address->id_supplier = $supplierId;
            } else {
                $supplier = new Supplier();
                $supplier->name = $address->supplier;
                if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $supplier->add()) {
                    $address->id_supplier = (int)$supplier->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                            $supplier->name,
                            (isset($supplier->id) && !empty($supplier->id))? $supplier->id : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        $res = false;
        if (($field_error = $address->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $address->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            $address->force_id = (bool)$force_ids;

            if (isset($customer_list) && count($customer_list) > 0) {
                $filter_list = array();
                foreach ($customer_list as $customer) {
                    if (in_array($customer['id_customer'], $filter_list)) {
                        continue;
                    }

                    $filter_list[] = $customer['id_customer'];
                    $address->id_customer = $customer['id_customer'];
                }
            }

            if ($address->id && $address->addressExists($address->id)) {
                $res = ($validateOnly || $address->update());
            }
            if (!$res) {
                $res = ($validateOnly || $address->add());
            }
        }
        if (!$res) {
            if (!$validateOnly) {
                $this->errors[] = sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                    $info['alias'],
                    (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null'
                );
            }
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                    Db::getInstance()->getMsgError();
            }
        }
    }

    public function manufacturerImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $regenerate = Tools::getValue('regenerate');
        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->manufacturerImportOne(
                $info,
                $shop_is_feature_active,
                $regenerate,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function manufacturerImportOne($info, $shop_is_feature_active, $regenerate, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $manufacturer = new Manufacturer((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Manufacturer::existsInDatabase((int)$info['id'], 'manufacturer')) {
                $manufacturer = new Manufacturer((int)$info['id']);
            } else {
                $manufacturer = new Manufacturer();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $manufacturer);

        $res = false;
        if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            if ($manufacturer->id && $manufacturer->manufacturerExists($manufacturer->id)) {
                $res = ($validateOnly || $manufacturer->update());
            }
            $manufacturer->force_id = (bool)$force_ids;
            if (!$res) {
                $res = ($validateOnly || $manufacturer->add());
            }

            //copying images of manufacturer
            if (!$validateOnly && isset($manufacturer->image) && !empty($manufacturer->image)) {
                if (!AdminImportController::copyImg($manufacturer->id, null, $manufacturer->image, 'manufacturers', !$regenerate)) {
                    $this->warnings[] = $manufacturer->image.' '.$this->trans('cannot be copied.', array(), 'Admin.Advparameters.Notification');
                }
            }

            if (!$validateOnly && $res) {
                // Associate supplier to group shop
                if ($shop_is_feature_active && $manufacturer->shop) {
                    Db::getInstance()->execute('
						DELETE FROM '._DB_PREFIX_.'manufacturer_shop
						WHERE id_manufacturer = '.(int)$manufacturer->id);
                    $manufacturer->shop = explode($this->multiple_value_separator, $manufacturer->shop);
                    $shops = array();
                    foreach ($manufacturer->shop as $shop) {
                        if (empty($shop)) {
                            continue;
                        }
                        $shop = trim($shop);
                        if (!is_numeric($shop)) {
                            $shop = ShopGroup::getIdByName($shop);
                        }
                        $shops[] = $shop;
                    }
                    $manufacturer->associateTo($shops);
                }
            }
        }

        if (!$res) {
            if (!$validateOnly) {
                $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                    (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                    (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                );
            }
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                    Db::getInstance()->getMsgError();
            }
        }
    }

    public function supplierImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $regenerate = Tools::getValue('regenerate');
        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            if ($offset > 0) {
                $this->toto = true;
            }

            $this->supplierImportOne(
                $info,
                $shop_is_feature_active,
                $regenerate,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function supplierImportOne($info, $shop_is_feature_active, $regenerate, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $supplier = new Supplier((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Supplier::existsInDatabase((int)$info['id'], 'supplier')) {
                $supplier = new Supplier((int)$info['id']);
            } else {
                $supplier = new Supplier();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supplier);
        if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            $res = false;
            if ($supplier->id && $supplier->supplierExists($supplier->id)) {
                $res = ($validateOnly || $supplier->update());
            }
            $supplier->force_id = (bool)$force_ids;
            if (!$res) {
                $res = ($validateOnly || $supplier->add());
            }

            //copying images of suppliers
            if (!$validateOnly && isset($supplier->image) && !empty($supplier->image)) {
                if (!AdminImportController::copyImg($supplier->id, null, $supplier->image, 'suppliers', !$regenerate)) {
                    $this->warnings[] = $supplier->image.' '.$this->trans('cannot be copied.', array(), 'Admin.Advparameters.Notification');
                }
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                    (isset($info['name']) && !empty($info['name']))? Tools::safeOutput($info['name']) : 'No Name',
                    (isset($info['id']) && !empty($info['id']))? Tools::safeOutput($info['id']) : 'No ID'
                );
            } elseif (!$validateOnly) {
                // Associate supplier to group shop
                if ($shop_is_feature_active && $supplier->shop) {
                    Db::getInstance()->execute('
						DELETE FROM '._DB_PREFIX_.'supplier_shop
						WHERE id_supplier = '.(int)$supplier->id);
                    $supplier->shop = explode($this->multiple_value_separator, $supplier->shop);
                    $shops = array();
                    foreach ($supplier->shop as $shop) {
                        if (empty($shop)) {
                            continue;
                        }
                        $shop = trim($shop);
                        if (!is_numeric($shop)) {
                            $shop = ShopGroup::getIdByName($shop);
                        }
                        $shops[] = $shop;
                    }
                    $supplier->associateTo($shops);
                }
            }
        } else {
            $this->errors[] = $this->trans('Supplier is invalid', array(), 'Admin.Advparameters.Notification').' ('.$supplier->name.')';
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
        }
    }

    public function aliasImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->aliasImportOne(
                $info,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function aliasImportOne($info, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $alias = new Alias((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Alias::existsInDatabase((int)$info['id'], 'alias')) {
                $alias = new Alias((int)$info['id']);
            } else {
                $alias = new Alias();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $alias);

        $res = false;
        if (($field_error = $alias->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $alias->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            if ($alias->id && $alias->aliasExists($alias->id)) {
                $res = ($validateOnly || $alias->update());
            }
            $alias->force_id = (bool)$force_ids;
            if (!$res) {
                $res = ($validateOnly || $alias->add());
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                    $info['name'],
                    (isset($info['id']) ? $info['id'] : 'null')
                );
            }
        } else {
            $this->errors[] = $this->trans('Alias is invalid', array(), 'Admin.Advparameters.Notification').' ('.$alias->name.')';
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
        }
    }

    public function storeContactImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); $current_line++) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', array(), 'Admin.Advparameters.Notification');
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->storeContactImportOne(
                $info,
                Shop::isFeatureActive(),
                $regenerate,
                $force_ids,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $line_count;
    }

    public function storeContactImportOne($info, $shop_is_feature_active, $regenerate, $force_ids, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int)$info['id']) {
            $store = new Store((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Store::existsInDatabase((int)$info['id'], 'store')) {
                $store = new Store((int)$info['id']);
            } else {
                $store = new Store();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $store);

        if (isset($store->image) && !empty($store->image)) {
            if (!(AdminImportController::copyImg($store->id, null, $store->image, 'stores', !$regenerate))) {
                $this->warnings[] = $store->image.' '.$this->trans('cannot be copied.', array(), 'Admin.Advparameters.Notification');
            }
        }

        if (isset($store->hours) && is_array($store->hours)) {
            $newHours = array();
            foreach ($store->hours as $hour) {
                array_push($newHours, array($hour));
            }
            $store->hours = json_encode($newHours);
        }

        if (isset($store->country) && is_numeric($store->country)) {
            if (Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), (int)$store->country)) {
                $store->id_country = (int)$store->country;
            }
        } elseif (isset($store->country) && is_string($store->country) && !empty($store->country)) {
            if ($id_country = Country::getIdByName(null, $store->country)) {
                $store->id_country = (int)$id_country;
            } else {
                $country = new Country();
                $country->active = 1;
                $country->name = AdminImportController::createMultiLangField($store->country);
                $country->id_zone = 0; // Default zone for country to create
                $country->iso_code = Tools::strtoupper(Tools::substr($store->country, 0, 2)); // Default iso for country to create
                $country->contains_states = 0; // Default value for country to create
                $lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                if (($field_error = $country->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $country->add()) {
                    $store->id_country = (int)$country->id;
                } else {
                    if (!$validateOnly) {
                        $default_language_id = (int)Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array(
                                '%data%' => $country->name[$default_language_id]
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($store->state) && is_numeric($store->state)) {
            if (State::getNameById((int)$store->state)) {
                $store->id_state = (int)$store->state;
            }
        } elseif (isset($store->state) && is_string($store->state) && !empty($store->state)) {
            if ($id_state = State::getIdByName($store->state)) {
                $store->id_state = (int)$id_state;
            } else {
                $state = new State();
                $state->active = 1;
                $state->name = $store->state;
                $state->id_country = isset($country->id) ? (int)$country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                $state->iso_code = Tools::strtoupper(Tools::substr($store->state, 0, 2)); // Default iso for state to create
                $state->tax_behavior = 0;
                if (($field_error = $state->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $state->add()) {
                    $store->id_state = (int)$state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array(
                                '%data%' => $state->name),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '').
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        $res = false;
        if (($field_error = $store->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $store->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            if ($store->id && $store->storeExists($store->id)) {
                $res = $validateOnly ? $validateOnly : $store->update();
            }
            $store->force_id = (bool)$force_ids;
            if (!$res) {
                $res = $validateOnly ? $validateOnly : $store->add();
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError().' '.sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', array(), 'Admin.Advparameters.Notification'),
                    $info['name'],
                    (isset($info['id']) ? $info['id'] : 'null')
                );
            }
        } else {
            $this->errors[] = $this->trans('Store is invalid', array(), 'Admin.Advparameters.Notification').' ('.$store->name.')';
            $this->errors[] = ($field_error !== true ? $field_error : '').(isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
        }
    }

    /**
     * @since 1.5.0
     */
    public function supplyOrdersImport($offset = false, $limit = false, $validateOnly = false)
    {
        // opens CSV & sets locale
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');

        // main loop, for each supply orders to import
        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            $this->supplyOrdersImportOne(
                $info,
                $force_ids,
                $current_line,
                $validateOnly
            );
        }
        // closes
        $this->closeCsvFile($handle);

        return $line_count;
    }

    protected function supplyOrdersImportOne($info, $force_ids, $current_line, $validateOnly = false)
    {
        // sets default values if needed
        AdminImportController::setDefaultValues($info);

        // if an id is set, instanciates a supply order with this id if possible
        if (array_key_exists('id', $info) && (int)$info['id'] && SupplyOrder::exists((int)$info['id'])) {
            $supply_order = new SupplyOrder((int)$info['id']);
        } elseif (array_key_exists('reference', $info) && $info['reference'] && SupplyOrder::exists(pSQL($info['reference']))) {
            $supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['reference']));
        } else { // new supply order
            $supply_order = new SupplyOrder();
        }

        // gets parameters
        $id_supplier = (int)$info['id_supplier'];
        $id_lang = (int)$info['id_lang'];
        $id_warehouse = (int)$info['id_warehouse'];
        $id_currency = (int)$info['id_currency'];
        $reference = pSQL($info['reference']);
        $date_delivery_expected = pSQL($info['date_delivery_expected']);
        $discount_rate = (float)$info['discount_rate'];
        $is_template = (bool)$info['is_template'];

        $error = '';
        // checks parameters
        if (!Supplier::supplierExists($id_supplier)) {
            $error = $this->trans('Supplier ID (%id%) is not valid (at line %line%).', array('%id%' => $id_supplier, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (!Language::getLanguage($id_lang)) {
            $error = $this->trans('Lang ID (%id%) is not valid (at line %line%).', array('%id%' => $id_lang, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (!Warehouse::exists($id_warehouse)) {
            $error = $this->trans('Warehouse ID (%id%) is not valid (at line %line%).', array('%id%' => $id_warehouse, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (!Currency::getCurrency($id_currency)) {
            $error = $this->trans('Currency ID (%id%) is not valid (at line %line%).', array('%id%' => $id_currency, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (empty($supply_order->reference) && SupplyOrder::exists($reference)) {
            $error = $this->trans('Reference (%ref%) already exists (at line %line%).', array('%ref%' => $reference, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (!empty($supply_order->reference) && ($supply_order->reference != $reference && SupplyOrder::exists($reference))) {
            $error = $this->trans('Reference (%ref%) already exists (at line %line%).', array('%ref%' => $reference, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }
        if (!Validate::isDateFormat($date_delivery_expected)) {
            $error = $this->trans('Date format (%date%) is not valid (at line %line%). It should be: %date_format%.', array('%date%' => $date_delivery_expected, '%line%' => $current_line + 1, '%date_format%' => $this->trans('YYYY-MM-DD', array(), 'Admin.Advparameters.Notification')), 'Admin.Advparameters.Notification');
        } elseif (new DateTime($date_delivery_expected) <= new DateTime('yesterday')) {
            $error = $this->trans('Date (%date%) cannot be in the past (at line %line%). Format: %date_format%.', array('%date%' => $date_delivery_expected, '%line%' => $current_line + 1, '%date_format%' => $this->trans('YYYY-MM-DD', array(), 'Admin.Advparameters.Notification')), 'Admin.Advparameters.Notification');
        }
        if ($discount_rate < 0 || $discount_rate > 100) {
            $error = $this->trans('Discount rate (%rate%) is not valid (at line %line%). %format%.',
                array('%rate%' => $discount_rate, '%line%' => $current_line + 1, '%format%' => $this->trans('Format: Between 0 and 100', array(), 'Admin.Advparameters.Notification')),
                'Admin.Advparameters.Notification');
        }
        if ($supply_order->id > 0 && !$supply_order->isEditable()) {
            $error = $this->trans('Supply Order (%id%) is not editable (at line %line%).', array('%id%' => $supply_order->id, '%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
        }

        // if no errors, sets supply order
        if (empty($error)) {
            // adds parameters
            $info['id_ref_currency'] = (int)Currency::getDefaultCurrency()->id;
            $info['supplier_name'] = pSQL(Supplier::getNameById($id_supplier));
            if ($supply_order->id > 0) {
                $info['id_supply_order_state'] = (int)$supply_order->id_supply_order_state;
                $info['id'] = (int)$supply_order->id;
            } else {
                $info['id_supply_order_state'] = 1;
            }

            // sets parameters
            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order);

            // updatesd($supply_order);

            $res = false;

            if ((int)$supply_order->id && ($supply_order->exists((int)$supply_order->id) || $supply_order->exists($supply_order->reference))) {
                $res = ($validateOnly || $supply_order->update());
            } else {
                $supply_order->force_id = (bool)$force_ids;
                $res = ($validateOnly || $supply_order->add());
            }

            // errors
            if (!$res) {
                $this->errors[] = $this->trans('Supply Order could not be saved (at line %line%).', array('%line%' => $current_line + 1), 'Admin.Advparameters.Notification');
            }
        } else {
            $this->errors[] = $error;
        }
    }

    public function supplyOrdersDetailsImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false)
    {
        // opens CSV & sets locale
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $products = array();
        $reset = true;
        if ($crossStepsVariables !== false && array_key_exists('products', $crossStepsVariables)) {
            $products = $crossStepsVariables['products'];
        }
        if ($crossStepsVariables !== false && array_key_exists('reset', $crossStepsVariables)) {
            $reset = $crossStepsVariables['reset'];
        }

        $force_ids = Tools::getValue('forceIDs');

        // main loop, for each supply orders details to import
        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            $line_count++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            $this->supplyOrdersDetailsImportOne(
                $info,
                $products, // by ref
                $reset, // by ref
                $force_ids,
                $current_line,
                $validateOnly
            );
        }
        // closes
        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['products'] = $products;
            $crossStepsVariables['reset'] = $reset;
        }
        return $line_count;
    }

    protected function supplyOrdersDetailsImportOne($info, &$products, &$reset, $force_ids, $current_line, $validateOnly = false)
    {
        // sets default values if needed
        AdminImportController::setDefaultValues($info);

        // gets the supply order
        if (array_key_exists('supply_order_reference', $info) && pSQL($info['supply_order_reference']) && SupplyOrder::exists(pSQL($info['supply_order_reference']))) {
            $supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['supply_order_reference']));
        } else {
            $this->errors[] = sprintf($this->trans('Supply Order (%s) could not be loaded (at line %d).', array(), 'Admin.Advparameters.Notification'), $info['supply_order_reference'], $current_line + 1);
        }

        if (empty($this->errors)) {
            // sets parameters
            $id_product = (int)$info['id_product'];
            if (!$info['id_product_attribute']) {
                $info['id_product_attribute'] = 0;
            }
            $id_product_attribute = (int)$info['id_product_attribute'];
            $unit_price_te = (float)$info['unit_price_te'];
            $quantity_expected = (int)$info['quantity_expected'];
            $discount_rate = (float)$info['discount_rate'];
            $tax_rate = (float)$info['tax_rate'];

            // checks if one product/attribute is there only once
            if (isset($products[$id_product][$id_product_attribute])) {
                $this->errors[] = sprintf(
                    $this->trans('Product/Attribute (%d/%d) cannot be added twice (at line %d).', array(), 'Admin.Advparameters.Notification'),
                    $id_product,
                    $id_product_attribute,
                    $current_line + 1
                );
            } else {
                $products[$id_product][$id_product_attribute] = $quantity_expected;
            }

            // checks parameters
            if (false === ($supplier_reference = ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $supply_order->id_supplier))) {
                $this->errors[] = sprintf(
                    $this->trans('Product (%d/%d) is not available for this order (at line %d).', array(), 'Admin.Advparameters.Notification'),
                    $id_product,
                    $id_product_attribute,
                    $current_line + 1
                );
            }
            if ($unit_price_te < 0) {
                $this->errors[] = sprintf($this->trans('Unit Price (tax excl.) (%d) is not valid (at line %d).', array(), 'Admin.Advparameters.Notification'), $unit_price_te, $current_line + 1);
            }
            if ($quantity_expected < 0) {
                $this->errors[] = sprintf($this->trans('Quantity Expected (%d) is not valid (at line %d).', array(), 'Admin.Advparameters.Notification'), $quantity_expected, $current_line + 1);
            }
            if ($discount_rate < 0 || $discount_rate > 100) {
                $this->errors[] = sprintf(
                    $this->trans('Discount rate (%d) is not valid (at line %d). %s.', array(), 'Admin.Advparameters.Notification'),
                    $discount_rate,
                    $current_line + 1,
                    $this->trans('Format: Between 0 and 100', array(), 'Admin.Advparameters.Notification')
                );
            }
            if ($tax_rate < 0 || $tax_rate > 100) {
                $this->errors[] = sprintf(
                    $this->trans('Quantity Expected (%d) is not valid (at line %d).', array(), 'Admin.Advparameters.Notification'),
                    $tax_rate,
                    $current_line + 1,
                    $this->trans('Format: Between 0 and 100', array(), 'Admin.Advparameters.Notification')
                );
            }

            // if no errors, sets supply order details
            if (empty($this->errors)) {
                // resets order if needed
                if (!$validateOnly && $reset) {
                    $supply_order->resetProducts();
                    $reset = false;
                }

                // creates new product
                $supply_order_detail = new SupplyOrderDetail();
                AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supply_order_detail);

                // sets parameters
                $supply_order_detail->id_supply_order = $supply_order->id;
                $currency = new Currency($supply_order->id_ref_currency);
                $supply_order_detail->id_currency = $currency->id;
                $supply_order_detail->exchange_rate = $currency->conversion_rate;
                $supply_order_detail->supplier_reference = $supplier_reference;
                $supply_order_detail->name = Product::getProductName($id_product, $id_product_attribute, $supply_order->id_lang);

                // gets ean13 / ref / upc
                $query = new DbQuery();
                $query->select('
					IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
					IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
					IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc
				');
                $query->from('product', 'p');
                $query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product AND id_product_attribute = '.(int)$id_product_attribute);
                $query->where('p.id_product = '.(int)$id_product);
                $query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                $product_infos = $res['0'];

                $supply_order_detail->reference = $product_infos['reference'];
                $supply_order_detail->ean13 = $product_infos['ean13'];
                $supply_order_detail->upc = $product_infos['upc'];
                $supply_order_detail->force_id = (bool)$force_ids;
                if (!$validateOnly) {
                    $supply_order_detail->add();
                    $supply_order->update();
                }
                unset($supply_order_detail);
            }
        }
    }

    public function utf8EncodeArray($array)
    {
        return (is_array($array) ? array_map('utf8_encode', $array) : utf8_encode($array));
    }

    protected function getNbrColumn($handle, $glue)
    {
        if (!is_resource($handle)) {
            return false;
        }
        $tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue);
        AdminImportController::rewindBomAware($handle);
        return count($tmp);
    }

    protected static function usortFiles($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($b < $a) ? 1 : - 1;
    }

    protected function openCsvFile($offset = false)
    {
        $file = $this->excelToCsvFile(Tools::getValue('csv'));
        $handle = false;
        if (is_file($file) && is_readable($file)) {
            if (!mb_check_encoding(file_get_contents($file), 'UTF-8')) {
                $this->convert = true;
            }
            $handle = fopen($file, 'r');
        }

        if (!$handle) {
            $this->errors[] = $this->trans('Cannot read the .CSV file', array(), 'Admin.Advparameters.Notification');
            return null; // error case
        }

        AdminImportController::rewindBomAware($handle);

        $toSkip = (int)Tools::getValue('skip');
        if ($offset && $offset > 0) {
            $toSkip += $offset;
        }
        for ($i = 0; $i < $toSkip; ++$i) {
            $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator);
            if ($line === false) {
                return false; // reached end of file
            }
        }
        return $handle;
    }

    protected function closeCsvFile($handle)
    {
        fclose($handle);
    }

    protected function excelToCsvFile($filename)
    {
        if (preg_match('#(.*?)\.(csv)#is', $filename)) {
            $dest_file = AdminImportController::getPath(strval(preg_replace('/\.{2,}/', '.', $filename)));
        } else {
            $csv_folder = AdminImportController::getPath();
            $excel_folder = $csv_folder.'csvfromexcel/';
            $info = pathinfo($filename);
            $csv_name = basename($filename, '.'.$info['extension']).'.csv';
            $dest_file = $excel_folder.$csv_name;

            if (!is_dir($excel_folder)) {
                mkdir($excel_folder);
            }

            if (!is_file($dest_file)) {
                $reader_excel = PHPExcel_IOFactory::createReaderForFile($csv_folder.$filename);
                $reader_excel->setReadDataOnly(true);
                $excel_file = $reader_excel->load($csv_folder.$filename);

                $csv_writer = PHPExcel_IOFactory::createWriter($excel_file, 'CSV');

                $csv_writer->setSheetIndex(0);
                $csv_writer->setDelimiter(';');
                $csv_writer->save($dest_file);
            }
        }

        return $dest_file;
    }

    protected function truncateTables($case)
    {
        switch ((int)$case) {
            case $this->entities[$this->trans('Categories', array(), 'Admin.Global')]:
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_lang`
					WHERE id_category NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'category_shop`
					WHERE `id_category` NOT IN ('.(int)Configuration::get('PS_HOME_CATEGORY').
                    ', '.(int)Configuration::get('PS_ROOT_CATEGORY').')');
                Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'category` AUTO_INCREMENT = 3');
                foreach (scandir(_PS_CAT_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->trans('Products', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'feature_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'category_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_tag`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'image_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'specific_price_priority`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_carrier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'cart_product`');
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) { //check if table exist
                    Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'favorite_product`');
                }
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attachment`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_country_tax`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_download`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_group_reduction_cache`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_sale`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'warehouse_product_location`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_available`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'stock_mvt`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customization_field`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_detail`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'pack`');
                Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                if (!file_exists(_PS_PROD_IMG_DIR_)) {
                    mkdir(_PS_PROD_IMG_DIR_);
                }
                break;
            case $this->entities[$this->trans('Combinations', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_impact`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_group_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_image`');
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE id_product_attribute != 0');
                break;
            case $this->entities[$this->trans('Customers', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'customer`');
                break;
            case $this->entities[$this->trans('Addresses', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'address`');
                break;
            case $this->entities[$this->trans('Brands', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'manufacturer_shop`');
                foreach (scandir(_PS_MANU_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_MANU_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->trans('Suppliers', array(), 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supplier_shop`');
                foreach (scandir(_PS_SUPP_IMG_DIR_) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SUPP_IMG_DIR_.$d);
                    }
                }
                break;
            case $this->entities[$this->trans('Alias', array(), 'Admin.Shopparameters.Feature')]:
                Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'alias`');
                break;
        }
        Image::clearTmpDir();
        return true;
    }

    public function clearSmartyCache()
    {
        Tools::enableCache();
        Tools::clearCache($this->context->smarty);
        Tools::restoreCacheSettings();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');
            return;
        }

        if (Tools::isSubmit('import')) {
            $this->importByGroups();
        } elseif ($filename = Tools::getValue('csvfilename')) {
            $filename = urldecode($filename);
            $file = AdminImportController::getPath(basename($filename));
            if (realpath(dirname($file)) != realpath(AdminImportController::getPath())) {
                exit();
            }
            if (!empty($filename)) {
                $b_name = basename($filename);
                if (Tools::getValue('delete') && file_exists($file)) {
                    @unlink($file);
                } elseif (file_exists($file)) {
                    $b_name = explode('.', $b_name);
                    $b_name = strtolower($b_name[count($b_name) - 1]);
                    $mime_types = array('csv' => 'text/csv');

                    if (isset($mime_types[$b_name])) {
                        $mime_type = $mime_types[$b_name];
                    } else {
                        $mime_type = 'application/octet-stream';
                    }

                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: '.$mime_type);
                    header('Content-Length: '.filesize($file));
                    header('Content-Disposition: attachment; filename="'.$filename.'"');
                    $fp = fopen($file, 'rb');
                    while (is_resource($fp) && !feof($fp)) {
                        echo fgets($fp, 16384);
                    }
                    exit;
                }
            }
        }
        Db::getInstance()->enableCache();
        return parent::postProcess();
    }

    public function importByGroups($offset = false, $limit = false, &$results = null, $validateOnly = false, $moreStep = 0)
    {
        // Check if the CSV file exist
        if (Tools::getValue('csv')) {
            $shop_is_feature_active = Shop::isFeatureActive();
            // If i am a superadmin, i can truncate table (ONLY IF OFFSET == 0 or false and NOT FOR VALIDATION MODE!)
            if (!$offset && !$moreStep && !$validateOnly &&(($shop_is_feature_active && $this->context->employee->isSuperAdmin()) || !$shop_is_feature_active) && Tools::getValue('truncate')) {
                $this->truncateTables((int)Tools::getValue('entity'));
            }
            $import_type = false;
            $doneCount = 0;
            // Sometime, import will use registers to memorize data across all elements to import (for trees, or else).
            // Since import is splitted in multiple ajax calls, we must keep these data across all steps of the full import.
            $crossStepsVariables = array();
            if ($crossStepsVars = Tools::getValue('crossStepsVars')) {
                $crossStepsVars = json_decode($crossStepsVars, true);
                if (sizeof($crossStepsVars) > 0) {
                    $crossStepsVariables = $crossStepsVars;
                }
            }
            Db::getInstance()->disableCache();
            $clearCache = false;
            switch ((int)Tools::getValue('entity')) {
                case $this->entities[$import_type = $this->trans('Categories', array(), 'Admin.Global')]:
                    $doneCount += $this->categoryImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    if ($doneCount < $limit && !$validateOnly) {
                        /* Import has finished, we can regenerate the categories nested tree */
                        Category::regenerateEntireNtree();
                    }
                    $clearCache = true;
                    break;
                case $this->entities[$import_type = $this->trans('Products', array(), 'Admin.Global')]:
                    if (!defined('PS_MASS_PRODUCT_CREATION')) {
                        define('PS_MASS_PRODUCT_CREATION', true);
                    }
                    $moreStepLabels = array($this->trans('Linking Accessories...', array(), 'Admin.Advparameters.Notification'));
                    $doneCount += $this->productImport($offset, $limit, $crossStepsVariables, $validateOnly, $moreStep);
                    $clearCache = true;
                    break;
                case $this->entities[$import_type = $this->trans('Customers', array(), 'Admin.Global')]:
                    $doneCount += $this->customerImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[$import_type = $this->trans('Addresses', array(), 'Admin.Global')]:
                    $doneCount += $this->addressImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[$import_type = $this->trans('Combinations', array(), 'Admin.Global')]:
                    $doneCount += $this->attributeImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    $clearCache = true;
                    break;
                case $this->entities[$import_type = $this->trans('Brands', array(), 'Admin.Global')]:
                    $doneCount += $this->manufacturerImport($offset, $limit, $validateOnly);
                    $clearCache = true;
                    break;
                case $this->entities[$import_type = $this->trans('Suppliers', array(), 'Admin.Global')]:
                    $doneCount += $this->supplierImport($offset, $limit, $validateOnly);
                    $clearCache = true;
                    break;
                case $this->entities[$import_type = $this->trans('Alias', array(), 'Admin.Shopparameters.Feature')]:
                    $doneCount += $this->aliasImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[$import_type = $this->trans('Store contacts', array(), 'Admin.Advparameters.Feature')]:
                    $doneCount += $this->storeContactImport($offset, $limit, $validateOnly);
                    $clearCache = true;
                    break;
            }

            // @since 1.5.0
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                switch ((int)Tools::getValue('entity')) {
                    case $this->entities[$import_type = $this->trans('Supply Orders', array(), 'Admin.Advparameters.Feature')]:
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $doneCount += $this->supplyOrdersImport($offset, $limit, $validateOnly);
                        }
                        break;
                    case $this->entities[$import_type = $this->trans('Supply Order Details', array(), 'Admin.Advparameters.Feature')]:
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $doneCount += $this->supplyOrdersDetailsImport($offset, $limit, $crossStepsVariables, $validateOnly);
                        }
                        break;
                }
            }

            if ($results !== null) {
                $results['isFinished'] = ($doneCount < $limit);
                if ($results['isFinished'] && $clearCache && !$validateOnly) {
                    $this->clearSmartyCache();
                }
                $results['doneCount'] = $offset + $doneCount;
                if ($offset === 0) {
                    // compute total count only once, because it takes time
                    $handle = $this->openCsvFile(0);
                    if ($handle) {
                        $count = 0;
                        while (fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) {
                            $count++;
                        }
                        $results['totalCount'] = $count;
                    }
                    $this->closeCsvFile($handle);
                }
                if (!$results['isFinished'] || (!$validateOnly && ($moreStep < count($moreStepLabels)))) {
                    // Since we'll have to POST this array from ajax for the next call, we should care about it size.
                    $nextPostSize = mb_strlen(json_encode($crossStepsVariables));
                    $results['crossStepsVariables'] = $crossStepsVariables;
                    $results['nextPostSize'] = $nextPostSize + (1024*64) ; // 64KB more for the rest of the POST query.
                    $results['postSizeLimit'] = Tools::getMaxUploadSize();
                }
                if ($results['isFinished'] && !$validateOnly && ($moreStep < count($moreStepLabels))) {
                    $results['oneMoreStep'] = $moreStep + 1;
                    $results['moreStepLabel'] = $moreStepLabels[$moreStep];
                }
            }

            if ($import_type !== false) {
                $log_message = sprintf($this->trans('%s import', array(), 'Admin.Advparameters.Notification'), $import_type);
                if ($offset !== false && $limit !== false) {
                    $log_message .= ' '.sprintf($this->trans('(from %s to %s)', array(), 'Admin.Advparameters.Notification'), $offset, $limit);
                }
                if (Tools::getValue('truncate')) {
                    $log_message .= ' '.$this->trans('with truncate', array(), 'Admin.Advparameters.Notification');
                }
                PrestaShopLogger::addLog($log_message, 1, null, $import_type, null, true, (int)$this->context->employee->id);
            }

            Db::getInstance()->enableCache();
        } else {
            $this->errors[] = $this->trans('To proceed, please upload a file first.', array(), 'Admin.Advparameters.Notification');
        }
    }

    public static function setLocale()
    {
        $iso_lang  = trim(Tools::getValue('iso_lang'));
        setlocale(LC_COLLATE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
        setlocale(LC_CTYPE, strtolower($iso_lang).'_'.strtoupper($iso_lang).'.UTF-8');
    }

    protected function addProductWarning($product_name, $product_id = null, $message = '')
    {
        $this->warnings[] = $product_name.(isset($product_id) ? ' (ID '.$product_id.')' : '').' '
            .$message;
    }

    public function ajaxProcessSaveImportMatchs()
    {
        if ($this->access('edit')) {
            $match = implode('|', Tools::getValue('type_value'));
            Db::getInstance()->execute('INSERT IGNORE INTO  `'._DB_PREFIX_.'import_match` (
										`id_import_match` ,
										`name` ,
										`match`,
										`skip`
										)
										VALUES (
										NULL ,
										\''.pSQL(Tools::getValue('newImportMatchs')).'\',
										\''.pSQL($match).'\',
										\''.pSQL(Tools::getValue('skip')).'\'
										)', false);

            die('{"id" : "'.Db::getInstance()->Insert_ID().'"}');
        }
    }

    public function ajaxProcessLoadImportMatchs()
    {
        if ($this->access('edit')) {
            $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '
                .(int)Tools::getValue('idImportMatchs'), true, false);
            die('{"id" : "'.$return[0]['id_import_match'].'", "matchs" : "'.$return[0]['match'].'", "skip" : "'
                .$return[0]['skip'].'"}');
        }
    }

    public function ajaxProcessDeleteImportMatchs()
    {
        if ($this->access('edit')) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '
                .(int)Tools::getValue('idImportMatchs'), false);
            die;
        }
    }

    public static function getPath($file = '')
    {
        return (defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).DIRECTORY_SEPARATOR.'import'
            .DIRECTORY_SEPARATOR.$file;
    }

    public function ajaxProcessImport()
    {
        $offset = (int)Tools::getValue('offset');
        $limit = (int)Tools::getValue('limit');
        $validateOnly = ((int)Tools::getValue('validateOnly') == 1);
        $moreStep = (int)Tools::getValue('moreStep');

        $results = array();
        $this->importByGroups($offset, $limit, $results, $validateOnly, $moreStep);

        // Retrieve errors/warnings if any
        if (count($this->errors) > 0) {
            $results['errors'] = $this->errors;
        }
        if (count($this->warnings) > 0) {
            $results['warnings'] = $this->warnings;
        }
        if (count($this->informations) > 0) {
            $results['informations'] = $this->informations;
        }

        if (!$validateOnly && (bool)$results['isFinished'] && !isset($results['oneMoreStep']) && (bool)Tools::getValue('sendemail')) {
            // Mail::Send() can sometimes throw an error...
            try {
                unset($this->context->cookie->csv_selected); // remove CSV selection file if finished with no error.

                $templateVars = array(
                    '{firstname}' => $this->context->employee->firstname,
                    '{lastname}' => $this->context->employee->lastname,
                    '{filename}' => Tools::getValue('csv')
                );

                $employeeLanguage = new Language((int) $this->context->employee->id_lang);
                // Mail send in last step because in case of failure, does NOT throw an error.
                $mailSuccess = @Mail::Send(
                    (int)$this->context->employee->id_lang,
                    'import',
                    $this->trans(
                        'Import complete',
                        array(),
                        'Emails.Subject',
                        $employeeLanguage->locale
                    ),
                    $templateVars,
                    $this->context->employee->email,
                    $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
                    null,
                    null,
                    null,
                    null,
                    _PS_MAIL_DIR_,
                    false, // do not die in failed! Warn only, it's not an import error, because import finished in fact.
                    (int)$this->context->shop->id
                );
                if (!$mailSuccess) {
                    $results['warnings'][] = $this->trans('The confirmation email couldn\'t be sent, but the import is successful. Yay!', array(), 'Admin.Advparameters.Notification');
                }
            } catch (\Exception $e) {
                $results['warnings'][] = $this->trans('The confirmation email couldn\'t be sent, but the import is successful. Yay!', array(), 'Admin.Advparameters.Notification');
            }
        }

        die(json_encode($results));
    }

    public function initModal()
    {
        parent::initModal();
        $modal_content = $this->context->smarty->fetch('controllers/import/modal_import_progress.tpl');
        $this->modals[] = array(
             'modal_id' => 'importProgress',
             'modal_class' => 'modal-md',
             'modal_title' => $this->trans('Importing your data...', array(), 'Admin.Advparameters.Notification'),
             'modal_content' => $modal_content
         );
    }
}
