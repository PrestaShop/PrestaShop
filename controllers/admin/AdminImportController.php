<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
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
    );

    public $separator;
    public $convert;
    public $multiple_value_separator;

    /**
     * AdminImportControllerCore constructor.
     * - builds entities list
     * - builds available fields list
     * - builds required fields list
     * - builds default values list
     */
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id_product' => array(
                        'label' => $this->trans('Product ID', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'product_reference' => array(
                        'label' => $this->trans('Product Reference', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'group' => array(
                        'label' => $this->trans(
                            'Attribute (Name:Type:Position)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ) . '*',
                    ),
                    'attribute' => array(
                        'label' => $this->trans('Value (Value:Position)', array(), 'Admin.Advparameters.Feature') . '*',
                    ),
                    'supplier_reference' => array(
                        'label' => $this->trans('Supplier reference', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reference' => array('label' => $this->trans('Reference', array(), 'Admin.Global')),
                    'ean13' => array(
                        'label' => $this->trans('EAN13', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'upc' => array(
                        'label' => $this->trans('UPC', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'wholesale_price' => array(
                        'label' => $this->trans('Cost price', array(), 'Admin.Catalog.Feature'),
                    ),
                    'price' => array(
                        'label' => $this->trans('Impact on price', array(), 'Admin.Catalog.Feature'),
                    ),
                    'ecotax' => array(
                        'label' => $this->trans('Ecotax', array(), 'Admin.Catalog.Feature'),
                    ),
                    'quantity' => array('label' => $this->trans('Quantity', array(), 'Admin.Global')),
                    'minimal_quantity' => array(
                        'label' => $this->trans('Minimal quantity', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'low_stock_threshold' => array(
                        'label' => $this->trans('Low stock level', array(), 'Admin.Catalog.Feature')
                    ),
                    'low_stock_alert' => array(
                        'label' => $this->trans(
                            'Send me an email when the quantity is under this level',
                            array(),
                            'Admin.Catalog.Feature'
                        )
                    ),
                    'weight' => array(
                        'label' => $this->trans('Impact on weight', array(), 'Admin.Catalog.Feature'),
                    ),
                    'default_on' => array(
                        'label' => $this->trans('Default (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'available_date' => array(
                        'label' => $this->trans(
                            'Combination availability date',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'image_position' => array(
                        'label' => $this->trans(
                            'Choose among product images by position (1,2,3...)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'image_url' => array(
                        'label' => $this->trans('Image URLs (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'image_alt' => array(
                        'label' => $this->trans('Image alt texts (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->trans('Advanced Stock Management', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Enable Advanced Stock Management on product (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->trans('Depends on stock', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            '0 = Use quantity set in product, 1 = Use quantity from warehouse.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'warehouse' => array(
                        'label' => $this->trans('Warehouse', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'ID of the warehouse to set as storage.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
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
                    'no' => array(
                        'label' => $this->trans(
                            'Ignore this column',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'parent' => array('label' => $this->trans('Parent category', array(), 'Admin.Catalog.Feature')),
                    'is_root_category' => array(
                        'label' => $this->trans('Root category (0/1)', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'A category root is where a category tree can begin. This is used with multistore.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'link_rewrite' => array(
                        'label' => $this->trans('Rewritten URL', array(), 'Admin.Shopparameters.Feature'),
                    ),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'category' => array(
                        'label' => $this->trans('Categories (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'price_tex' => array(
                        'label' => $this->trans('Price tax excluded', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'price_tin' => array(
                        'label' => $this->trans('Price tax included', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id_tax_rules_group' => array(
                        'label' => $this->trans('Tax rule ID', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'wholesale_price' => array('label' => $this->trans('Cost price', array(), 'Admin.Catalog.Feature')),
                    'on_sale' => array(
                        'label' => $this->trans('On sale (0/1)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reduction_price' => array(
                        'label' => $this->trans('Discount amount', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reduction_percent' => array(
                        'label' => $this->trans('Discount percent', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reduction_from' => array(
                        'label' => $this->trans('Discount from (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reduction_to' => array(
                        'label' => $this->trans('Discount to (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'reference' => array(
                        'label' => $this->trans('Reference #', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'supplier_reference' => array(
                        'label' => $this->trans('Supplier reference #', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'supplier' => array('label' => $this->trans('Supplier', array(), 'Admin.Global')),
                    'manufacturer' => array('label' => $this->trans('Brand', array(), 'Admin.Global')),
                    'ean13' => array('label' => $this->trans('EAN13', array(), 'Admin.Advparameters.Feature')),
                    'upc' => array('label' => $this->trans('UPC', array(), 'Admin.Advparameters.Feature')),
                    'ecotax' => array('label' => $this->trans('Ecotax', array(), 'Admin.Catalog.Feature')),
                    'width' => array('label' => $this->trans('Width', array(), 'Admin.Global')),
                    'height' => array('label' => $this->trans('Height', array(), 'Admin.Global')),
                    'depth' => array('label' => $this->trans('Depth', array(), 'Admin.Global')),
                    'weight' => array('label' => $this->trans('Weight', array(), 'Admin.Global')),
                    'quantity' => array('label' => $this->trans('Quantity', array(), 'Admin.Global')),
                    'minimal_quantity' => array(
                        'label' => $this->trans('Minimal quantity', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'low_stock_threshold' => array(
                        'label' => $this->trans('Low stock level', array(), 'Admin.Catalog.Feature')
                    ),
                    'low_stock_alert' => array(
                        'label' => $this->trans(
                            'Send me an email when the quantity is under this level',
                            array(),
                            'Admin.Catalog.Feature'
                        )
                    ),
                    'visibility' => array('label' => $this->trans('Visibility', array(), 'Admin.Catalog.Feature')),
                    'additional_shipping_cost' => array(
                        'label' => $this->trans('Additional shipping cost', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'unity' => array(
                        'label' => $this->trans('Unit for the price per unit', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'unit_price' => array(
                        'label' => $this->trans('Price per unit', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'description_short' => array('label' => $this->trans('Summary', array(), 'Admin.Catalog.Feature')),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'tags' => array('label' => $this->trans('Tags (x,y,z...)', array(), 'Admin.Advparameters.Feature')),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'link_rewrite' => array(
                        'label' => $this->trans('Rewritten URL', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'available_now' => array(
                        'label' => $this->trans('Label when in stock', array(), 'Admin.Catalog.Feature'),
                    ),
                    'available_later' => array(
                        'label' => $this->trans('Label when backorder allowed', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'available_for_order' => array(
                        'label' => $this->trans(
                            'Available for order (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'available_date' => array(
                        'label' => $this->trans('Product availability date', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'date_add' => array(
                        'label' => $this->trans('Product creation date', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'show_price' => array(
                        'label' => $this->trans('Show price (0 = No, 1 = Yes)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'image' => array(
                        'label' => $this->trans('Image URLs (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'image_alt' => array(
                        'label' => $this->trans('Image alt texts (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'delete_existing_images' => array(
                        'label' => $this->trans(
                            'Delete existing images (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'features' => array(
                        'label' => $this->trans(
                            'Feature (Name:Value:Position:Customized)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'online_only' => array(
                        'label' => $this->trans(
                            'Available online only (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'condition' => array('label' => $this->trans('Condition', array(), 'Admin.Catalog.Feature')),
                    'customizable' => array(
                        'label' => $this->trans(
                            'Customizable (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'uploadable_files' => array(
                        'label' => $this->trans(
                            'Uploadable files (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'text_fields' => array(
                        'label' => $this->trans(
                            'Text fields (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'out_of_stock' => array(
                        'label' => $this->trans('Action when out of stock', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'is_virtual' => array(
                        'label' => $this->trans(
                            'Virtual product (0 = No, 1 = Yes)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'file_url' => array('label' => $this->trans('File URL', array(), 'Admin.Advparameters.Feature')),
                    'nb_downloadable' => array(
                        'label' => $this->trans('Number of allowed downloads', array(), 'Admin.Catalog.Feature'),
                        'help' => $this->trans(
                            'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                            array(),
                            'Admin.Catalog.Help'
                        ),
                    ),
                    'date_expiration' => array(
                        'label' => $this->trans('Expiration date (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'nb_days_accessible' => array(
                        'label' => $this->trans('Number of days', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                            array(),
                            'Admin.Catalog.Help'
                        ),
                    ),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'advanced_stock_management' => array(
                        'label' => $this->trans('Advanced Stock Management', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Enable Advanced Stock Management on product (0 = No, 1 = Yes).',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'depends_on_stock' => array(
                        'label' => $this->trans('Depends on stock', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            '0 = Use quantity set in product, 1 = Use quantity from warehouse.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'warehouse' => array(
                        'label' => $this->trans('Warehouse', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'ID of the warehouse to set as storage.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
                    ),
                    'accessories' => array(
                        'label' => $this->trans('Accessories (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active  (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'id_gender' => array(
                        'label' => $this->trans(
                            'Titles ID (Mr = 1, Ms = 2, else 0)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'email' => array('label' => $this->trans('Email', array(), 'Admin.Global') . '*'),
                    'passwd' => array('label' => $this->trans('Password', array(), 'Admin.Global') . '*'),
                    'birthday' => array(
                        'label' => $this->trans('Birth date (yyyy-mm-dd)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'lastname' => array('label' => $this->trans('Last name', array(), 'Admin.Global') . '*'),
                    'firstname' => array('label' => $this->trans('First name', array(), 'Admin.Global') . '*'),
                    'newsletter' => array(
                        'label' => $this->trans('Newsletter (0/1)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'optin' => array(
                        'label' => $this->trans('Partner offers (0/1)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'date_add' => array(
                        'label' => $this->trans(
                            'Registration date (yyyy-mm-dd)',
                            array(),
                            'Admin.Advparameters.Feature'
                        ),
                    ),
                    'group' => array(
                        'label' => $this->trans('Groups (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id_default_group' => array(
                        'label' => $this->trans('Default group ID', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id_shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'alias' => array('label' => $this->trans('Alias', array(), 'Admin.Shopparameters.Feature') . '*'),
                    'active' => array('label' => $this->trans('Active  (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'customer_email' => array(
                        'label' => $this->trans('Customer email', array(), 'Admin.Advparameters.Feature') . '*',
                    ),
                    'id_customer' => array(
                        'label' => $this->trans('Customer ID', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'manufacturer' => array('label' => $this->trans('Brand', array(), 'Admin.Global')),
                    'supplier' => array('label' => $this->trans('Supplier', array(), 'Admin.Global')),
                    'company' => array('label' => $this->trans('Company', array(), 'Admin.Global')),
                    'lastname' => array('label' => $this->trans('Last name', array(), 'Admin.Global') . '*'),
                    'firstname' => array('label' => $this->trans('First name ', array(), 'Admin.Global') . '*'),
                    'address1' => array('label' => $this->trans('Address', array(), 'Admin.Global') . '*'),
                    'address2' => array('label' => $this->trans('Address (2)', array(), 'Admin.Global')),
                    'postcode' => array('label' => $this->trans('Zip/postal code', array(), 'Admin.Global') . '*'),
                    'city' => array('label' => $this->trans('City', array(), 'Admin.Global') . '*'),
                    'country' => array('label' => $this->trans('Country', array(), 'Admin.Global') . '*'),
                    'state' => array('label' => $this->trans('State', array(), 'Admin.Global')),
                    'other' => array('label' => $this->trans('Other', array(), 'Admin.Global')),
                    'phone' => array('label' => $this->trans('Phone', array(), 'Admin.Global')),
                    'phone_mobile' => array('label' => $this->trans('Mobile Phone', array(), 'Admin.Global')),
                    'vat_number' => array(
                        'label' => $this->trans('VAT number', array(), 'Admin.Orderscustomers.Feature'),
                    ),
                    'dni' => array(
                        'label' => $this->trans('Identification number', array(), 'Admin.Orderscustomers.Feature'),
                    ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'description' => array('label' => $this->trans('Description', array(), 'Admin.Global')),
                    'short_description' => array(
                        'label' => $this->trans('Short description', array(), 'Admin.Catalog.Feature'),
                    ),
                    'meta_title' => array('label' => $this->trans('Meta title', array(), 'Admin.Global')),
                    'meta_keywords' => array('label' => $this->trans('Meta keywords', array(), 'Admin.Global')),
                    'meta_description' => array('label' => $this->trans('Meta description', array(), 'Admin.Global')),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of group shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
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
                    'no' => array(
                        'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                    'active' => array('label' => $this->trans('Active (0/1)', array(), 'Admin.Advparameters.Feature')),
                    'name' => array('label' => $this->trans('Name', array(), 'Admin.Global')),
                    'address1' => array('label' => $this->trans('Address', array(), 'Admin.Global').'*'),
                    'address2' => array('label' => $this->trans('Address (2)', array(), 'Admin.Advparameters.Feature')),
                    'postcode' => array('label' => $this->trans('Zip/postal code', array(), 'Admin.Global')),
                    'state' => array('label' => $this->trans('State', array(), 'Admin.Global')),
                    'city' => array('label' => $this->trans('City', array(), 'Admin.Global').'*'),
                    'country' => array('label' => $this->trans('Country', array(), 'Admin.Global').'*'),
                    'latitude' => array(
                        'label' => $this->trans('Latitude', array(), 'Admin.Advparameters.Feature') . '*',
                    ),
                    'longitude' => array(
                        'label' => $this->trans('Longitude', array(), 'Admin.Advparameters.Feature') . '*',
                    ),
                    'phone' => array('label' => $this->trans('Phone', array(), 'Admin.Global')),
                    'fax' => array('label' => $this->trans('Fax', array(), 'Admin.Global')),
                    'email' => array('label' => $this->trans('Email address', array(), 'Admin.Global')),
                    'note' => array('label' => $this->trans('Note', array(), 'Admin.Advparameters.Feature')),
                    'hours' => array(
                        'label' => $this->trans('Hours (x,y,z...)', array(), 'Admin.Advparameters.Feature'),
                    ),
                    'image' => array('label' => $this->trans('Image URL', array(), 'Admin.Advparameters.Feature')),
                    'shop' => array(
                        'label' => $this->trans('ID / Name of shop', array(), 'Admin.Advparameters.Feature'),
                        'help' => $this->trans(
                            'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                            array(),
                            'Admin.Advparameters.Help'
                        ),
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
                        'no' => array(
                            'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'id' => array('label' => $this->trans('ID', array(), 'Admin.Global')),
                        'id_supplier' => array(
                            'label' => $this->trans('Supplier ID *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'id_lang' => array('label' => $this->trans('Lang ID', array(), 'Admin.Advparameters.Feature')),
                        'id_warehouse' => array(
                            'label' => $this->trans('Warehouse ID *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'id_currency' => array(
                            'label' => $this->trans('Currency ID *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'reference' => array(
                            'label' => $this->trans('Supply Order Reference *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'date_delivery_expected' => array(
                            'label' => $this->trans('Delivery Date (Y-M-D)*', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'discount_rate' => array(
                            'label' => $this->trans('Discount rate', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'is_template' => array(
                            'label' => $this->trans('Template', array(), 'Admin.Advparameters.Feature'),
                        ),
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
                        'no' => array(
                            'label' => $this->trans('Ignore this column', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'supply_order_reference' => array(
                            'label' => $this->trans('Supply Order Reference *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'id_product' => array(
                            'label' => $this->trans('Product ID *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'id_product_attribute' => array(
                            'label' => $this->trans('Product Attribute ID', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'unit_price_te' => array(
                            'label' => $this->trans('Unit Price (tax excl.)*', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'quantity_expected' => array(
                            'label' => $this->trans('Quantity Expected *', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'discount_rate' => array(
                            'label' => $this->trans('Discount Rate', array(), 'Admin.Advparameters.Feature'),
                        ),
                        'tax_rate' => array(
                            'label' => $this->trans('Tax Rate', array(), 'Admin.Advparameters.Feature'),
                        ),
                    );
                    // default values
                    self::$default_values = array(
                        'discount_rate' => '0',
                        'tax_rate' => '0',
                    );
                    break;
            }
        }

        $separator = Tools::substr(strval(trim(Tools::getValue('separator'))), 0, 1);
        $this->separator = $separator ? $separator : ';';
        $this->convert = false;
        $separator = Tools::substr(strval(trim(Tools::getValue('multiple_value_separator'))), 0, 1);
        $this->multiple_value_separator = $separator ? $separator : ',';
    }

    /**
     * Sets up back-office theme
     *
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        $boTheme = (Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme)
            ? $this->context->employee->bo_theme
            : 'default';

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $boTheme . DIRECTORY_SEPARATOR . 'template')) {
            $boTheme = 'default';
        }

        // We need to set parent media first, so that jQuery is loaded before the dependant plugins
        parent::setMedia($isNewTheme);

        $boThemeJsPath = __PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $boTheme . '/js/';

        $this->addJs($boThemeJsPath . 'jquery.iframe-transport.js');
        $this->addJs($boThemeJsPath . 'jquery.fileupload.js');
        $this->addJs($boThemeJsPath . 'jquery.fileupload-process.js');
        $this->addJs($boThemeJsPath . 'jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__ . 'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__ . 'js/vendor/ladda.js');
    }

    /**
     * Renders the import file upload form.
     *
     * @return string
     */
    public function renderForm()
    {
        if (!is_dir(AdminImportController::getPath())) {
            $this->errors[] = $this->trans(
                'The import directory doesn\'t exist. Please check your file path.',
                array(),
                'Admin.Advparameters.Notification'
            );

            return false;
        }

        if (!is_writable(AdminImportController::getPath())) {
            $this->displayWarning($this->trans(
                'The import directory must be writable (CHMOD 755 / 777).',
                array(),
                'Admin.Advparameters.Notification'
            ));
        }

        $filesToImport = scandir(AdminImportController::getPath());
        uasort($filesToImport, array('AdminImportController', 'usortFiles'));
        foreach ($filesToImport as $k => $filename) {
            //exclude .  ..  .svn and index.php and all hidden files
            if (preg_match('/^\..*|index\.php/i', $filename)
                || is_dir(AdminImportController::getPath() . $filename)
            ) {
                unset($filesToImport[$k]);
            }
        }
        unset($filename);

        $this->fields_form    = array('');
        $this->toolbar_scroll = false;
        $this->toolbar_btn    = array();

        // adds fancybox
        $this->addJqueryPlugin(array('fancybox'));

        $entitySelected = 0;
        if (isset($this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))])) {
            $entitySelected = $this->entities[$this->l(Tools::ucfirst(Tools::getValue('import_type')))];
            $this->context->cookie->entity_selected = (int)$entitySelected;
        } elseif (isset($this->context->cookie->entity_selected)) {
            $entitySelected = (int)$this->context->cookie->entity_selected;
        }

        $csvSelected = '';
        if (isset($this->context->cookie->csv_selected)
            && @filemtime(AdminImportController::getPath(urldecode($this->context->cookie->csv_selected)))
        ) {
            $csvSelected = urldecode($this->context->cookie->csv_selected);
        } else {
            $this->context->cookie->csv_selected = $csvSelected;
        }

        $idLangSelected = '';
        if (isset($this->context->cookie->iso_lang_selected) && $this->context->cookie->iso_lang_selected) {
            $idLangSelected = (int)Language::getIdByIso(urldecode($this->context->cookie->iso_lang_selected));
        }

        $separatorSelected = $this->separator;
        if (isset($this->context->cookie->separator_selected) && $this->context->cookie->separator_selected) {
            $separatorSelected = urldecode($this->context->cookie->separator_selected);
        }

        $multipleValueSeparatorSelected = $this->multiple_value_separator;
        if (!empty($this->context->cookie->multiple_value_separator_selected)) {
            $multipleValueSeparatorSelected = urldecode($this->context->cookie->multiple_value_separator_selected);
        }

        //get post max size
        $postMaxSize = ini_get('post_max_size');
        $bytes       = (int)trim($postMaxSize);
        $last        = strtolower($postMaxSize[strlen($postMaxSize) - 1]);

        switch ($last) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'g':
                $bytes *= 1024;
                // no break to fall-through
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'm':
                $bytes *= 1024;
                // no break to fall-through
            case 'k':
                $bytes *= 1024;
                break;
        }

        if (!isset($bytes) || $bytes == '') {
            $bytes = 20971520; // 20MB
        }

        $this->tpl_form_vars = array(
            'post_max_size' => (int)$bytes,
            'module_confirmation' => Tools::isSubmit('import') && isset($this->warnings) && !count($this->warnings),
            'path_import' => AdminImportController::getPath(),
            'entities' => $this->entities,
            'entity_selected' => $entitySelected,
            'csv_selected' => $csvSelected,
            'separator_selected' => $separatorSelected,
            'multiple_value_separator_selected' => $multipleValueSeparatorSelected,
            'files_to_import' => $filesToImport,
            'languages' => Language::getLanguages(false),
            'id_language' => ($idLangSelected) ? $idLangSelected : $this->context->language->id,
            'available_fields' => $this->getAvailableFields(),
            'truncateAuthorized' => !(Shop::isFeatureActive()) || $this->context->employee->isSuperAdmin(),
            'PS_ADVANCED_STOCK_MANAGEMENT' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
        );

        return parent::renderForm();
    }

    /**
     * Ajax action. Processes csv file upload.
     */
    public function ajaxProcessuploadCsv()
    {
        $filenamePrefix = date('YmdHis') . '-';

        if (!empty($_FILES['file']['error'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_FILES['file']['error'] = $this->trans(
                        'The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $uploadErrorMessage = $this->trans(
                        'The uploaded file exceeds the post_max_size directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess, for example:',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                    $generatorsPageMessage = $this->trans(
                        '(click to open "Generators" page)',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                    $directiveExample = '<br/><a href="'.$this->context->link->getAdminLink('AdminMeta').'" >'
                        . '<code>php_value post_max_size 20M</code> '
                        . $generatorsPageMessage
                        . '</a>';

                    $_FILES['file']['error'] = $uploadErrorMessage . $directiveExample;
                    break;
                break;
                case UPLOAD_ERR_PARTIAL:
                    $_FILES['file']['error'] = $this->trans(
                        'The uploaded file was only partially uploaded.',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $_FILES['file']['error'] = $this->trans(
                        'No file was uploaded.',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                    break;
            }

            die(json_encode($_FILES));
        }

        if (!preg_match('#([^\.]*?)\.(csv|xls[xt]?|o[dt]s)$#is', $_FILES['file']['name'])) {
            $_FILES['file']['error'] = $this->trans(
                'The extension of your file should be .csv.',
                array(),
                'Admin.Advparameters.Notification'
            );

            die(json_encode($_FILES));
        }

        if (!@filemtime($_FILES['file']['tmp_name'])
            || !@move_uploaded_file(
                $_FILES['file']['tmp_name'],
                AdminImportController::getPath() . $filenamePrefix . str_replace("\0", '', $_FILES['file']['name'])
            )
        ) {
            $_FILES['file']['error'] = $this->trans(
                'An error occurred while uploading / copying the file.',
                array(),
                'Admin.Advparameters.Notification'
            );

            die(json_encode($_FILES));
        }

        @chmod(AdminImportController::getPath() . $filenamePrefix . $_FILES['file']['name'], 0664);
        $_FILES['file']['filename'] = $filenamePrefix . str_replace('\0', '', $_FILES['file']['name']);

        die(json_encode($_FILES));
    }

    /**
     * Prepares view content.
     *
     * @return string The view html content
     */
    public function renderView()
    {
        $this->addJS(_PS_JS_DIR_.'admin/import.js');

        $handle   = $this->openCsvFile();
        $nbColumn = $this->getNbrColumn($handle, $this->separator);
        $nbTable  = ceil($nbColumn / MAX_COLUMNS);

        $res = array();
        foreach ($this->required_fields as $elem) {
            $res[] = '\''.$elem.'\'';
        }

        $data = array();
        for ($i = 0; $i < $nbTable; $i++) {
            $data[$i] = $this->generateContentTable($i, $nbColumn, $handle, $this->separator);
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
            'nb_table' => $nbTable,
            'nb_column' => $nbColumn,
            'res' => implode(',', $res),
            'max_columns' => MAX_COLUMNS,
            'no_pre_select' => array('price_tin', 'feature'),
            'available_fields' => $this->available_fields,
            'data' => $data
        );

        return parent::renderView();
    }

    /**
     * Initializes import toolbar (for import display only)
     */
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

    /**
     * Generates the content preview of a given csv file.
     * Content preview will be an html table.
     *
     * @param int      $tableIndex Number used to identify the html table
     * @param int      $nbColumns  Number of columns to display in the preview
     * @param resource $handle     Csv file handle
     * @param string   $glue       The field delimiter in the csv file
     *
     * @return string The content preview (as html table)
     */
    protected function generateContentTable($tableIndex, $nbColumns, $handle, $glue)
    {
        $html = '<table id="table'.$tableIndex.'" style="display: none;" class="table table-bordered"><thead><tr>';
        // Header
        for ($i = 0; $i < $nbColumns; $i++) {
            if (MAX_COLUMNS * (int)$tableIndex <= $i && (int)$i < MAX_COLUMNS * ((int)$tableIndex + 1)) {
                $html .= '<th>
                            <select id="type_value[' . $i . ']"
                                name="type_value[' . $i . ']"
                                class="type_value">
                                ' . $this->getTypeValuesOptions($i) . '
                            </select>
                        </th>';
            }
        }
        $html .= '</tr></thead><tbody>';

        AdminImportController::setLocale();
        for ($currentLine = 0; $currentLine < 10 && $line = fgetcsv($handle, MAX_LINE_SIZE, $glue); $currentLine++) {
            /* UTF-8 conversion */
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $html .= '<tr id="table_' . $tableIndex . '_line_' . $currentLine . '">';
            foreach ($line as $colIndex => $column) {
                if ((MAX_COLUMNS * (int)$tableIndex <= $colIndex)
                    && ((int)$colIndex < MAX_COLUMNS * ((int)$tableIndex + 1))
                ) {
                    $html .= '<td>' . htmlentities(Tools::substr($column, 0, 200), ENT_QUOTES, 'UTF-8') . '</td>';
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        AdminImportController::rewindBomAware($handle);
        return $html;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (Tools::isSubmit('submitImportFile')) {
            $this->display = 'import';
        }
    }

    /**
     * @inheritdoc
     */
    public function initContent()
    {
        if ($this->display == 'import') {
            if (Tools::getValue('csv')) {
                $this->content .= $this->renderView();
            } else {
                $this->errors[] = $this->trans(
                    'To proceed, please upload a file first.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                $this->content .= $this->renderForm();
            }
        } else {
            $this->content .= $this->renderForm();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    /**
     * Rewinds a file, being aware of the byte order mark (BOM)
     *
     * @param resource $handle Handle of the file to be rewinded
     *
     * @return bool False if $handle is not a resource. True if file was rewinded.
     */
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

        return true;
    }

    /**
     * Casts $field as a boolean
     *
     * @param mixed $field
     *
     * @return bool
     */
    protected static function getBoolean($field)
    {
        return (bool)$field;
    }

    /**
     * "Casts" $field as a price (from string to float)
     *
     * @param string $field
     *
     * @return float
     */
    protected static function getPrice($field)
    {
        $field = ((float)str_replace(',', '.', $field));
        $field = ((float)str_replace('%', '', $field));
        return $field;
    }

    /**
     * @param string $field
     *
     * @return array
     */
    protected static function split($field)
    {
        if (empty($field)) {
            return array();
        }

        $separator = Tools::getValue('multiple_value_separator');
        if (is_null($separator) || trim($separator) == '') {
            $separator = ',';
        }

        $uniqIdPath = false;

        // try data:// protocole. If failed, old school file on filesystem.
        if (($fd = @fopen('data://text/plain;base64,'.base64_encode($field), 'rb')) === false) {
            do {
                $uniqIdPath = _PS_UPLOAD_DIR_.uniqid();
            } while (file_exists($uniqIdPath));
            file_put_contents($uniqIdPath, $field);
            $fd = fopen($uniqIdPath, 'r');
        }

        if ($fd === false) {
            return array();
        }

        $tab = fgetcsv($fd, MAX_LINE_SIZE, $separator);
        fclose($fd);
        if ($uniqIdPath !== false && file_exists($uniqIdPath)) {
            @unlink($uniqIdPath);
        }

        if (empty($tab) || (!is_array($tab))) {
            return array();
        }
        return $tab;
    }

    /**
     * Builds an array indexed on all languages ids. All items' values will be $field.
     *
     * @param mixed $field
     *
     * @return array
     */
    protected static function createMultiLangField($field)
    {
        $res = array();
        foreach (Language::getIDs(false) as $idLang) {
            $res[$idLang] = $field;
        }

        return $res;
    }

    /**
     * Builds options list (html) intended to populate a <select> element.
     * Options values and labels will be built from current configured available fields.
     *
     * @param int $selectedOptionPosition Position of the option to be marked as selected. First position is 1.
     *
     * @return string Options list html (not wrapped with <select> tag)
     */
    protected function getTypeValuesOptions($selectedOptionPosition)
    {
        $i = 0;
        $noPreSelect = array('price_tin', 'feature');

        $options = '';
        foreach ($this->available_fields as $k => $field) {
            $options .= '<option value="'.$k.'"';
            if ($k === 'price_tin') {
                ++$selectedOptionPosition;
            }
            if ($i === ($selectedOptionPosition + 1) && (!in_array($k, $noPreSelect))) {
                $options .= ' selected="selected"';
            }
            $options .= '>'.$field['label'].'</option>';
            ++$i;
        }
        return $options;
    }

    /**
     * Return fields to be displayed as piece of advise
     *
     * @param bool $asArray If set to true, result will be an array. Else, it will be a "\n\r" separated values string
     *
     * @return string|array
     */
    public function getAvailableFields($asArray = false)
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
                $fields[$i - 1] = '<div>'
                    . $this->available_fields[$keys[$i - 1]]['label']
                    . '<br/>&nbsp;&nbsp;<i>'
                    . $this->trans('or', array(), 'Admin.Advparameters.Help')
                    . '</i>&nbsp;&nbsp; '.$field['label']
                    . '</div>';
            } else {
                if (isset($field['help'])) {
                    $html = '&nbsp;<a href="#" class="help-tooltip" data-toggle="tooltip" title="' . $field['help'].'">'
                        . '<i class="icon-info-sign"></i>'
                        . '</a>';
                } else {
                    $html = '';
                }
                $fields[] = '<div>' . $field['label'] . $html . '</div>';
            }
            ++$i;
        }
        if ($asArray) {
            return $fields;
        } else {
            return implode("\n\r", $fields);
        }
    }

    /**
     * Populates self::$column_mask with passed "type_value" request parameter
     * self::$column_mask is then used internally by getMaskedRow()
     */
    protected function receiveTab()
    {
        $typeValue = Tools::getValue('type_value')
            ? Tools::getValue('type_value')
            : array();

        foreach ($typeValue as $nb => $type) {
            if ($type != 'no') {
                self::$column_mask[$type] = $nb;
            }
        }
    }

    /**
     * Mask relevant items of a row
     * Items to be masked are configured in self::$column_mask
     *
     * @param array $row Original row that needs some cleanup
     *
     * @return array Masked row
     */
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

    /**
     * Uses self::$default_values to fill $info with missing values.
     * $info is passed by reference : it's directly modified by this method.
     *
     * @param array $info Array to be filled
     */
    protected static function setDefaultValues(&$info)
    {
        foreach (self::$default_values as $k => $v) {
            if (!isset($info[$k]) || $info[$k] == '') {
                $info[$k] = $v;
            }
        }
    }

    /**
     * Uses self::$default_values to fill $entity with missing values.
     * $entity is passed by reference : it's directly modified by this method.
     *
     * @param $entity
     */
    protected static function setEntityDefaultValues(&$entity)
    {
        $members = get_object_vars($entity);
        foreach (self::$default_values as $k => $v) {
            if ((array_key_exists($k, $members) && $entity->$k === null) || !array_key_exists($k, $members)) {
                $entity->$k = $v;
            }
        }
    }

    /**
     * Fills $entity's $key property with $data value
     * $data will be first filtered against relevant callback from self::$validators
     *
     * @param $data
     * @param $key
     * @param $entity
     *
     * @return bool True if success
     */
    protected static function fillInfo($data, $key, &$entity)
    {
        $data = trim($data);

        // If nothing to fill, just get out ("0" string is NOT nothing).
        if (empty($data) && $data !== '0') {
            return true;
        }

        $validator = !empty(self::$validators[$key]) ? self::$validators[$key] : null;
        // If no validator, or not callable, then just fill without validation / sanitization
        if (!$validator || !is_callable($validator)) {
            $entity->{$key} = $data;

            return true;
        }

        $isoLang = Tools::getValue('iso_lang');

        /*
         * Special case for multi lang field creation.
         * Will fill missing values for all languages.
         * Will force value (even if already set) for passed iso_lang
         */
        if (!empty($validator[1])
            && $validator[1] == 'createMultiLangField'
            && $isoLang
        ) {
            $idLang = Language::getIdByIso($isoLang);
            $valuesByLang = call_user_func($validator, $data);
            foreach ($valuesByLang as $thisIdLang => $value) {
                if (empty($entity->{$key}[$thisIdLang]) || $thisIdLang == $idLang) {
                    $entity->{$key}[$thisIdLang] = $value;
                }
            }

            return true;
        }

        $entity->{$key} = call_user_func($validator, $data);

        return true;
    }

    /**
     * @param array $array The array to be walked
     * @param string $funcName Must be a callable function name
     * @param mixed $userData Additional parameters
     *
     * @return bool True if success
     */
    public static function arrayWalk(&$array, $funcName, &$userData = false)
    {
        if (!is_callable($funcName)) {
            return false;
        }

        foreach ($array as $k => $row) {
            if (!call_user_func_array($funcName, array($row, $k, &$userData))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copy an image located in $url and save it in a path
     * according to $entity->$idEntity .
     * $idImage is used if we need to add a watermark
     *
     * @param int    $idEntity id of product or category (set in entity)
     * @param int    $idImage  (default null) id of the image if watermark enabled.
     * @param string $url      path or url to use
     * @param string $entity   'products' or 'categories'
     * @param bool   $regenerate
     *
     * @return bool
     */
    protected static function copyImg($idEntity, $idImage = null, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpFile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermarkTypes = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $imageObj = new Image($idImage);
                $path = $imageObj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int)$idEntity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_.(int)$idEntity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_.(int)$idEntity;
                break;
            case 'stores':
                $path = _PS_STORE_IMG_DIR_.(int)$idEntity;
                break;
        }

        $url = urldecode(trim($url));
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['path'])) {
            $uri = ltrim($parsedUrl['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parsedUrl['path'] = '/'.implode('/', $parts);
        }

        if (isset($parsedUrl['query'])) {
            $queryParts = array();
            parse_str($parsedUrl['query'], $queryParts);
            $parsedUrl['query'] = http_build_query($queryParts);
        }

        if (!function_exists('http_build_url')) {
            require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');
        }

        $url = http_build_url('', $parsedUrl);

        $origTmpFile = $tmpFile;

        if (Tools::copy($url, $tmpFile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpFile)) {
                @unlink($tmpFile);
                return false;
            }

            $tgtWidth = $tgtHeight = 0;
            $srcWidth = $srcHeight = 0;
            $error = 0;
            ImageManager::resize(
                $tmpFile,
                $path . '.jpg',
                null,
                null,
                'jpg',
                false,
                $error,
                $tgtWidth,
                $tgtHeight,
                5,
                $srcWidth,
                $srcHeight
            );
            $imagesTypes = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previousPath = null;
                $pathData = array();
                $pathData[] = array($tgtWidth, $tgtHeight, $path.'.jpg');
                foreach ($imagesTypes as $imageType) {
                    $tmpFile = self::get_best_path($imageType['width'], $imageType['height'], $pathData);

                    if (ImageManager::resize(
                        $tmpFile,
                        $path.'-'.stripslashes($imageType['name']).'.jpg',
                        $imageType['width'],
                        $imageType['height'],
                        'jpg',
                        false,
                        $error,
                        $tgtWidth,
                        $tgtHeight,
                        5,
                        $srcWidth,
                        $srcHeight
                    )) {
                        // Last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgtWidth <= $srcWidth && $tgtHeight <= $srcHeight) {
                            $pathData[] = array(
                                $tgtWidth,
                                $tgtHeight,
                                $path . '-' . stripslashes($imageType['name']) . '.jpg',
                            );
                        }
                        if ($entity == 'products') {
                            $filename = _PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$idEntity . '.jpg';
                            if (is_file($filename)) {
                                unlink($filename);
                            }
                            $filenameWithContext = _PS_TMP_IMG_DIR_
                                . 'product_mini_'
                                . (int)$idEntity
                                . '_'
                                . (int)Context::getContext()->shop->id
                                . '.jpg';
                            if (is_file($filenameWithContext)) {
                                unlink($filenameWithContext);
                            }
                        }
                    }
                    if (in_array($imageType['id_image_type'], $watermarkTypes)) {
                        Hook::exec('actionWatermark', array('id_image' => $idImage, 'id_product' => $idEntity));
                    }
                }
            }
        } else {
            @unlink($origTmpFile);
            return false;
        }
        unlink($origTmpFile);
        return true;
    }

    /**
     * @deprecated Please use AdminImportControllerCore::getBestPath() instead
     *
     * @param int   $tgtWidth
     * @param int   $tgtHeight
     * @param array $pathData
     *
     * @return string
     */
    protected static function get_best_path($tgtWidth, $tgtHeight, $pathData)
    {
        return self::getBestPath($tgtWidth, $tgtHeight, $pathData);
    }

    /**
     * Get the best image path
     *
     * TODO : better description
     *
     * @param int   $tgtWidth
     * @param int   $tgtHeight
     * @param array $pathData
     *
     * @return string
     */
    protected static function getBestPath($tgtWidth, $tgtHeight, $pathData)
    {
        $pathData = array_reverse($pathData);
        $path     = '';
        foreach ($pathData as $pathInfo) {
            list($width, $height, $path) = $pathInfo;
            if ($width >= $tgtWidth && $height >= $tgtHeight) {
                return $path;
            }
        }
        return $path;
    }

    public function categoryImport(
        $offset = false,
        $limit = false,
        &$crossStepsVariables = false,
        $validateOnly = false
    ) {
        if (!is_array($crossStepsVariables)) {
            $crossStepsVariables = array();
        }
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
        $idLang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($idLang)) {
            $idLang = $defaultLanguageId;
        }
        AdminImportController::setLocale();

        $forceIds = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shopIsFeatureActive = Shop::isFeatureActive();


        $catMoved = array();
        if ($crossStepsVariables !== false && array_key_exists('cat_moved', $crossStepsVariables)) {
            $catMoved = $crossStepsVariables['cat_moved'];
        }

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->categoryImportOne(
                $info,
                $defaultLanguageId,
                $idLang,
                $forceIds,
                $regenerate,
                $shopIsFeatureActive,
                $catMoved, // by ref
                $validateOnly
            );
        }

        if (!$validateOnly) {
            /* Import has finished, we can regenerate the categories nested tree */
            Category::regenerateEntireNtree();
        }
        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVars['cat_moved'] = $catMoved;
        }

        return $lineCount;
    }

    protected function categoryImportOne(
        $info,
        $defaultLanguageId,
        $idLang,
        $forceIds,
        $regenerate,
        $shopIsFeatureActive,
        &$catMoved,
        $validateOnly = false
    ) {
        $baseCategories = array(
            Configuration::get('PS_HOME_CATEGORY'),
            Configuration::get('PS_ROOT_CATEGORY')
        );

        $categoryId = null;
        if (!empty($info['id'])) {
            $categoryId = (int)$info['id'];
        }

        if ($categoryId && in_array($categoryId, $baseCategories)) {
            $this->errors[] = $this->trans(
                'The category ID must be unique. It can\'t be the same as the one for Root or Home category.',
                array(),
                'Admin.Advparameters.Notification'
            );

            return;
        }
        AdminImportController::setDefaultValues($info);

        $category = new Category($categoryId);

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $category);

        // Parent category
        if (isset($category->parent) && is_numeric($category->parent)) {
            // Validation for parenting itself
            if ($validateOnly && $category->parent == $category->id
                || isset($categoryId) && $category->parent == $categoryId
            ) {
                $this->errors[] = $this->trans(
                    'The category ID must be unique. It can\'t be the same as the one for the parent category (ID: %id%).',
                    array('%id%' => (isset($categoryId) && !empty($categoryId))? $categoryId : 'null'),
                    'Admin.Advparameters.Notification'
                );
                return;
            }
            if (isset($catMoved[$category->parent])) {
                $category->parent = $catMoved[$category->parent];
            }
            $category->id_parent = $category->parent;
        } elseif (isset($category->parent) && is_string($category->parent)) {
            // Validation for parenting itself
            if ($validateOnly && isset($category->name) && ($category->parent == $category->name)) {
                $this->errors[] = $this->trans(
                    'A category can\'t be its own parent. You should rename it (current name: %name%).',
                    array('%name%' => $category->parent),
                    'Admin.Advparameters.Notification'
                );
                return;
            }
            $categoryParent = Category::searchByName($idLang, $category->parent, true);
            if ($categoryParent['id_category']) {
                $category->id_parent = (int)$categoryParent['id_category'];
                $category->level_depth = (int)$categoryParent['level_depth'] + 1;
            } else {
                $categoryToCreate = new Category();
                $categoryToCreate->name = AdminImportController::createMultiLangField($category->parent);
                $categoryToCreate->active = 1;
                $categoryLinkRewrite = Tools::link_rewrite($categoryToCreate->name[$idLang]);
                $categoryToCreate->link_rewrite = AdminImportController::createMultiLangField($categoryLinkRewrite);
                // Default parent is home for unknown category to create :
                $categoryToCreate->id_parent = Configuration::get('PS_HOME_CATEGORY');

                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $categoryToCreate->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move the position of this test. Only ->add() should not be triggered is !validateOnly. Previous tests should be always run.
                    && $categoryToCreate->add()
                ) {
                    $category->id_parent = $categoryToCreate->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans('%category% (ID: %id%) cannot be saved', array(
                            '%category%' => $categoryToCreate->name[$idLang],
                            '%id%'       => (!empty($categoryToCreate->id) ? $categoryToCreate->id : 'null'),
                        ), 'Admin.Advparameters.Notification');
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }
        if (isset($category->link_rewrite) && !empty($category->link_rewrite[$defaultLanguageId])) {
            $validLink = Validate::isLinkRewrite($category->link_rewrite[$defaultLanguageId]);
        } else {
            $validLink = false;
        }

        if (!$shopIsFeatureActive) {
            $category->id_shop_default = 1;
        } else {
            $category->id_shop_default = (int)Context::getContext()->shop->id;
        }

        $bak = $category->link_rewrite[$defaultLanguageId];
        if ((isset($category->link_rewrite) && empty($category->link_rewrite[$defaultLanguageId])) || !$validLink) {
            $category->link_rewrite = Tools::link_rewrite($category->name[$defaultLanguageId]);
            if ($category->link_rewrite == '') {
                $category->link_rewrite = 'friendly-url-autogeneration-failed';
                $this->warnings[] = $this->trans(
                    'URL rewriting failed to auto-generate a friendly URL for: %category%',
                    array('%category%' => $category->name[$defaultLanguageId]),
                    'Admin.Advparameters.Notification'
                );
            }
            $category->link_rewrite = AdminImportController::createMultiLangField($category->link_rewrite);
        }

        if (!$validLink) {
            $this->informations[] = $this->trans('Rewrite link for %oldLink% (ID %id%): re-written as %newLink%.', array(
                '%oldLink%' => $bak,
                '%id%'      => $categoryId ? $categoryId : 'null',
                '%newLink%' => $category->link_rewrite[$defaultLanguageId],
            ), 'Admin.Advparameters.Notification');
        }
        $res = false;
        if (($fieldError = $category->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $category->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
            && empty($this->errors)
        ) {
            $categoryAlreadyCreated = Category::searchByNameAndParentCategoryId(
                $idLang,
                $category->name[$idLang],
                $category->id_parent
            );

            // If category already in base, get id category back
            if ($categoryAlreadyCreated['id_category']) {
                $catMoved[$category->id] = (int)$categoryAlreadyCreated['id_category'];
                $category->id            = (int)$categoryAlreadyCreated['id_category'];
                if (Validate::isDate($categoryAlreadyCreated['date_add'])) {
                    $category->date_add = $categoryAlreadyCreated['date_add'];
                }
            }

            if ($category->id && $category->id == $category->id_parent) {
                $this->errors[] = $this->trans('A category cannot be its own parent. The parent category ID is either missing or unknown (ID: %id%).',
                    array('%id%' => !empty($categoryId) ? $categoryId : 'null'),
                    'Admin.Advparameters.Notification'
                );

                return;
            }

            /* No automatic nTree regeneration for import */
            $category->doNotRegenerateNTree = true;

            // If id category AND id category already in base, trying to update
            if ($category->id
                && $category->categoryExists($category->id)
                && !in_array($category->id, $baseCategories)
                && !$validateOnly
            ) {
                $res = $category->update();
            }
            if ($category->id == Configuration::get('PS_ROOT_CATEGORY')) {
                $this->errors[] = $this->trans(
                    'The root category cannot be modified.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
            }
            // If no id_category or update failed
            $category->force_id = (bool)$forceIds;
            if (!$res && !$validateOnly) {
                $res = $category->add();
            }
        }

        // ValidateOnly mode : stops here
        if ($validateOnly) {
            return;
        }

        //copying images of categories
        if (isset($category->image) && !empty($category->image)) {
            if (!(AdminImportController::copyImg($category->id, null, $category->image, 'categories', !$regenerate))) {
                $this->warnings[] = $category->image.' '.$this->trans(
                    'cannot be copied.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
            }
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = $this->trans('%name% (ID: %id%) cannot be %action%', array(
                '%name%'   => !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                '%id%'     => !empty($categoryId) ? Tools::safeOutput($categoryId) : 'No ID',
                '%action%' => $validateOnly ? 'validated' : 'saved',
            ), 'Admin.Advparameters.Notification');
            $errorTmp = ($fieldError !== true ? $fieldError : '')
                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                . Db::getInstance()->getMsgError();
            if ($errorTmp != '') {
                $this->errors[] = $errorTmp;
            }
        } else {
            // Associate category to shop
            if ($shopIsFeatureActive) {
                Db::getInstance()->execute(
                    'DELETE FROM ' . _DB_PREFIX_ . 'category_shop'
                    . 'WHERE id_category = ' . (int)$category->id
                );

                if (!isset($info['shop']) || empty($info['shop'])) {
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

    public function productImport(
        $offset = false,
        $limit = false,
        &$crossStepsVariables = false,
        $validateOnly = false,
        $moreStep = 0
    ) {
        if (!is_array($crossStepsVariables)) {
            $crossStepsVariables = array();
        }

        if ($moreStep == 1) {
            return $this->productImportAccessories($offset, $limit, $crossStepsVariables);
        }

        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
        $idLang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($idLang)) {
            $idLang = $defaultLanguageId;
        }
        AdminImportController::setLocale();
        $shopIds = Shop::getCompleteListOfShopsID();

        $forceIds = Tools::getValue('forceIDs');
        $matchRef = Tools::getValue('match_ref');
        $regenerate = Tools::getValue('regenerate');
        $shopIsFeatureActive = Shop::isFeatureActive();
        if (!$validateOnly) {
            Module::setBatchMode(true);
        }

        $accessories = array();
        if (array_key_exists('accessories', $crossStepsVariables)) {
            $accessories = $crossStepsVariables['accessories'];
        }

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->productImportOne(
                $info,
                $defaultLanguageId,
                $idLang,
                $forceIds,
                $regenerate,
                $shopIsFeatureActive,
                $shopIds,
                $matchRef,
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

        $crossStepsVariables['accessories'] = $accessories;

        return $lineCount;
    }

    protected function productImportAccessories($offset, $limit, &$crossStepsVariables)
    {
        if (!is_array($crossStepsVariables)) {
            $crossStepsVariables = array();
        }

        if (!array_key_exists('accessories', $crossStepsVariables)) {
            return 0;
        }

        $accessories = $crossStepsVariables['accessories'];

        if ($offset == 0) {
            //             AdminImportController::setLocale();
            Module::setBatchMode(true);
        }

        $lineCount = 0;
        $i = 0;
        foreach ($accessories as $productId => $links) {
            // skip elements until reaches offset
            if ($i < $offset) {
                $i++;
                continue;
            }

            if (count($links) > 0) { // We delete and relink only if there is accessories to link...
                // Bulk jobs: for performances, we need to do a minimum amount of SQL queries. No product inflation.
                $uniqueIds = Product::getExistingIdsFromIdsOrRefs($links);
                Db::getInstance()->delete('accessory', 'id_product_1 = '.(int)$productId);
                Product::changeAccessoriesForProduct($uniqueIds, $productId);
            }
            $lineCount++;

            // Empty value to reduce array weight (that goes through HTTP requests each time).
            // But do not unset array entry!
            // In JSON, 0 is lighter than null or false
            $accessories[$productId] = 0;

            // stop when limit reached
            if ($lineCount >= $limit) {
                break;
            }
        }

        if ($lineCount < $limit) { // last pass only
            Module::processDeferedFuncCall();
            Module::processDeferedClearCache();
        }

        $crossStepsVariables['accessories'] = $accessories;

        return $lineCount;
    }

    protected function productImportOne(
        $productData,
        $defaultLanguageId,
        $idLang,
        $forceIds,
        $regenerate,
        $shopIsFeatureActive,
        $shopIds,
        $matchRef,
        &$accessories,
        $validateOnly = false
    ) {
        if (!$forceIds) {
            unset($productData['id']);
        }

        $idProduct = null;
        // Use product reference as key
        if (!empty($productData['id'])) {
            $idProduct = (int)$productData['id'];
        } elseif ($matchRef && isset($productData['reference'])) {
            $idProductByRef = (int)Db::getInstance()->getValue(
                'SELECT p.`id_product` '
                . 'FROM `' . _DB_PREFIX_ . 'product` p '
                . Shop::addSqlAssociation('product', 'p')
                . ' WHERE p.`reference` = "' . pSQL($productData['reference']) . '"',
                false
            );
            if ($idProductByRef) {
                $idProduct = $idProductByRef;
            }
        }

        $product = new Product($idProduct);

        $updateAdvancedStockManagementValue = false;
        if (!empty($product->id) && Product::existsInDatabase((int)$product->id, 'product')) {
            $product->loadStockData();
            $updateAdvancedStockManagementValue = true;
            $categoryData = Product::getProductCategories((int)$product->id);
            if (empty($product->category)) {
                $product->category = array();
            }
            // Product::category can contain several different data types...
            if (is_array($product->category)) {
                foreach ($categoryData as $tmp) {
                    $product->category[] = $tmp;
                }
            }
        }

        AdminImportController::setEntityDefaultValues($product);
        AdminImportController::arrayWalk($productData, array('AdminImportController', 'fillInfo'), $product);

        if (!$shopIsFeatureActive) {
            $product->shop = (int)Configuration::get('PS_SHOP_DEFAULT');
        } elseif (!isset($product->shop) || empty($product->shop)) {
            $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        if (!$shopIsFeatureActive) {
            $product->id_shop_default = (int)Configuration::get('PS_SHOP_DEFAULT');
        } else {
            if (empty($product->shop)) {
                $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
            }
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
                $taxManager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                $productTaxCalculator = $taxManager->getTaxCalculator();
                $product->tax_rate = $productTaxCalculator->getTotalRate();
            } else {
                $this->addProductWarning(
                    'id_tax_rules_group',
                    $product->id_tax_rules_group,
                    $this->trans(
                        'Unknown tax rule group ID. You need to create a group with this ID first.',
                        array(),
                        'Admin.Advparameters.Notification'
                    )
                );
            }
        }
        if (!empty($product->manufacturer)) {
            if (Manufacturer::manufacturerExists((int)$product->manufacturer)) {
                $product->id_manufacturer = (int)$product->manufacturer;
            } elseif (is_string($product->manufacturer)) {
                if ($manufacturer = Manufacturer::getIdByName($product->manufacturer)) {
                    // Found manufacturer by name
                    $product->id_manufacturer = (int)$manufacturer;
                } else {
                    // Manufacturer creation
                    $manufacturer         = new Manufacturer();
                    $manufacturer->name   = $product->manufacturer;
                    $manufacturer->active = true;
                    // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                    if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true
                        && ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                        && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                        && $manufacturer->add()
                    ) {
                        $product->id_manufacturer = (int)$manufacturer->id;
                        $manufacturer->associateTo($product->id_shop_list);
                    } else {
                        if (!$validateOnly) {
                            $this->errors[] = $this->trans('%manufacturer% (ID: %id%) cannot be saved', array(
                                '%manufacturer%' => $manufacturer->name,
                                '%id%'           => !empty($manufacturer->id) ? $manufacturer->id : 'null',
                            ), 'Admin.Advparameters.Notification');
                        }
                        if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                                . Db::getInstance()->getMsgError();
                        }
                    }
                }
            }
        }

        if (isset($product->supplier)
            && is_numeric($product->supplier)
            && Supplier::supplierExists((int)$product->supplier)
        ) {
            $product->id_supplier = (int)$product->supplier;
        } elseif (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier)) {
            if ($supplier = Supplier::getIdByName($product->supplier)) {
                $product->id_supplier = (int)$supplier;
            } else {
                $supplier = new Supplier();
                $supplier->name = $product->supplier;
                $supplier->active = true;

                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly  // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $supplier->add()
                ) {
                    $product->id_supplier = (int)$supplier->id;
                    $supplier->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%supplier% (ID: %id%) cannot be saved',
                            array(
                                '%supplier%' => $supplier->name,
                                '%id%'       => !empty($supplier->id) ? $supplier->id : 'null',
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
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
                        $categoryToCreate = new Category();
                        $categoryToCreate->id = (int)$value;
                        $categoryToCreate->name = AdminImportController::createMultiLangField($value);
                        $categoryToCreate->active = 1;
                        // Default parent is home for unknown category to create
                        $categoryToCreate->id_parent = Configuration::get('PS_HOME_CATEGORY');
                        $categoryLinkRewrite = Tools::link_rewrite($categoryToCreate->name[$defaultLanguageId]);


                        // FIXME needing such a comment about "&& !$validateOnly" position is a code smell.
                        // Refacto needed.
                        if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) === true
                            && ($langFieldError = $categoryToCreate->validateFieldsLang(
                                UNFRIENDLY_ERROR,
                                true
                            )) === true
                            && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                            && $categoryToCreate->add()
                        ) {
                            $categoryToCreate->link_rewrite = AdminImportController::createMultiLangField(
                                $categoryLinkRewrite
                            );
                            $product->id_category[] = (int)$categoryToCreate->id;
                        } else {
                            if (!$validateOnly) {
                                $this->errors[] = $this->trans('%category% (ID: %id%) cannot be saved', array(
                                    '%category%' => $categoryToCreate->name[$defaultLanguageId],
                                    '%id%'       => !empty($categoryToCreate->id) ? $categoryToCreate->id : 'null',
                                ), 'Admin.Advparameters.Notification');
                            }
                            if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                                $this->errors[] = ($fieldError !== true ? $fieldError : '')
                                    . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                                    . Db::getInstance()->getMsgError();
                            }
                        }
                    }
                } elseif (!$validateOnly && is_string($value) && !empty($value)) {
                    $category = Category::searchByPath(
                        $defaultLanguageId,
                        trim($value),
                        $this,
                        'productImportCreateCat'
                    );
                    if ($category['id_category']) {
                        $product->id_category[] = (int)$category['id_category'];
                    } else {
                        $this->errors[] = $this->trans('%data% cannot be saved',
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
            // this will avoid ereasing default category if category column is not present in the CSV file (or ignored)
            if (isset($product->id_category[0])) {
                $product->id_category_default = (int)$product->id_category[0];
            } else {
                $defaultProductShop = new Shop($product->id_shop_default);
                $product->id_category_default = Category::getRootCategory(
                    null,
                    Validate::isLoadedObject($defaultProductShop) ? $defaultProductShop : null
                )->id;
            }
        }

        $linkRewrite = (is_array($product->link_rewrite) && isset($product->link_rewrite[$idLang]))
            ? trim($product->link_rewrite[$idLang])
            : '';
        $validLink = Validate::isLinkRewrite($linkRewrite);
        if ((isset($product->link_rewrite[$idLang]) && empty($product->link_rewrite[$idLang])) || !$validLink) {
            $linkRewrite = Tools::link_rewrite($product->name[$idLang]);
            if ($linkRewrite == '') {
                $linkRewrite = 'friendly-url-autogeneration-failed';
            }
        }

        if (!$validLink) {
            $this->informations[] = $this->trans(
                'Rewrite link for %product% (ID %id%): re-written as %link%.',
                array(
                    '%product%' => $product->name[$idLang],
                    '%id%'      => $product->id ? $product->id : 'null',
                    '%link%'    => $linkRewrite,
                ),
                'Admin.Advparameters.Notification'
            );
        }

        if (!$validLink || !(is_array($product->link_rewrite) && count($product->link_rewrite))) {
            $product->link_rewrite = AdminImportController::createMultiLangField($linkRewrite);
        } else {
            $product->link_rewrite[(int)$idLang] = $linkRewrite;
        }

        // replace the value of separator by coma
        if ($this->multiple_value_separator != ',') {
            if (is_array($product->meta_keywords)) {
                foreach ($product->meta_keywords as &$metaKeyword) {
                    if (!empty($metaKeyword)) {
                        $metaKeyword = str_replace($this->multiple_value_separator, ',', $metaKeyword);
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

        if (($matchRef && $product->reference && $product->existsRefInDatabase($product->reference))
            || $productExistsInDatabase
        ) {
            $product->date_upd = date('Y-m-d H:i:s');
        }

        $res = false;
        $fieldError = $product->validateFields(UNFRIENDLY_ERROR, true);
        $langFieldError = $product->validateFieldsLang(UNFRIENDLY_ERROR, true);
        if ($fieldError === true && $langFieldError === true) {
            // check quantity
            if ($product->quantity == null) {
                $product->quantity = 0;
            }

            // If match ref is specified && ref product && ref product already in base, trying to update
            if ($matchRef && $product->reference && $product->existsRefInDatabase($product->reference)) {
                $data = Db::getInstance()->getRow(
                    'SELECT product_shop.`date_add`, p.`id_product`'
                    . ' FROM `' . _DB_PREFIX_ . 'product` p ' . Shop::addSqlAssociation('product', 'p')
                    . ' WHERE p.`reference` = "' . pSQL($product->reference) . '"',
                    false
                );
                $product->id = (int)$data['id_product'];
                $product->date_add = pSQL($data['date_add']);
                $res = ($validateOnly || $product->update());
            } // Else If id product && id product already in base, trying to update
            elseif ($productExistsInDatabase) {
                $data = Db::getInstance()->getRow(
                    'SELECT product_shop.`date_add`'
                    . ' FROM `' . _DB_PREFIX_ . 'product` p ' . Shop::addSqlAssociation('product', 'p')
                    . ' WHERE p.`id_product` = ' . (int)$product->id,
                    false
                );
                $product->date_add = pSQL($data['date_add']);
                $res = ($validateOnly || $product->update());
            }
            // If no id_product or update failed
            $product->force_id = (bool)$forceIds;

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

                if ($productDownloadId = ProductDownload::getIdFromIdProduct((int)$product->id)) {
                    $productDownload = new ProductDownload($productDownloadId);
                    $productDownload->delete(true);
                }

                if ($product->getType() == Product::PTYPE_VIRTUAL) {
                    $productDownload = new ProductDownload();
                    $productDownload->filename = ProductDownload::getNewFilename();
                    Tools::copy($productData['file_url'], _PS_DOWNLOAD_DIR_.$productDownload->filename);
                    $productDownload->id_product = (int)$product->id;
                    $productDownload->nb_downloadable = (int)$productData['nb_downloadable'];
                    $productDownload->date_expiration = $productData['date_expiration'];
                    $productDownload->nb_days_accessible = (int)$productData['nb_days_accessible'];
                    $productDownload->display_filename = basename($productData['file_url']);
                    $productDownload->add();
                }
            }
        }

        $shops = array();
        $productShop = explode($this->multiple_value_separator, $product->shop);
        foreach ($productShop as $shop) {
            if (empty($shop)) {
                continue;
            }
            $shop = trim($shop);
            if (!empty($shop) && !is_numeric($shop)) {
                $shop = Shop::getIdByName($shop);
            }

            if (in_array($shop, $shopIds)) {
                $shops[] = $shop;
            } else {
                $this->addProductWarning(
                    Tools::safeOutput($productData['name']),
                    $product->id,
                    $this->trans('Shop is not valid', array(), 'Admin.Advparameters.Notification')
                );
            }
        }
        if (empty($shops)) {
            $shops = Shop::getContextListShopID();
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = $this->trans(
                '%data% (ID: %id%) cannot be saved',
                array(
                    '%data%' => !empty($productData['name']) ? Tools::safeOutput($productData['name']) : 'No Name',
                    '%id%'   => !empty($productId) ? Tools::safeOutput($productId) : 'No ID',
                ),
                'Admin.Advparameters.Notification'
            );
            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                . Db::getInstance()->getMsgError();
        } else {
            // Product supplier
            if (!$validateOnly
                && isset($product->id)
                && $product->id
                && isset($product->id_supplier) && property_exists($product, 'supplier_reference')
            ) {
                $idProductSupplier = (int)ProductSupplier::getIdByProductAndSupplier(
                    (int)$product->id,
                    0,
                    (int)$product->id_supplier
                );
                if ($idProductSupplier) {
                    $productSupplier = new ProductSupplier($idProductSupplier);
                } else {
                    $productSupplier = new ProductSupplier();
                }

                $productSupplier->id_product = (int)$product->id;
                $productSupplier->id_product_attribute = 0;
                $productSupplier->id_supplier = (int)$product->id_supplier;
                $productSupplier->product_supplier_price_te = $product->wholesale_price;
                $productSupplier->product_supplier_reference = $product->supplier_reference;
                $productSupplier->save();
            }

            // SpecificPrice (only the basic reduction feature is supported by the import)
            if (!$shopIsFeatureActive) {
                $productData['shop'] = 1;
            } elseif (!isset($productData['shop']) || empty($productData['shop'])) {
                $productData['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
            }

            // Get shops for each attributes
            $productData['shop'] = explode($this->multiple_value_separator, $productData['shop']);

            $idShopList = array();
            foreach ($productData['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $idShopList[] = (int)Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $idShopList[] = $shop;
                }
            }

            if ((isset($productData['reduction_price']) && $productData['reduction_price'] > 0)
                || (isset($productData['reduction_percent']) && $productData['reduction_percent'] > 0)
            ) {
                foreach ($idShopList as $idShop) {
                    $specificPrice = SpecificPrice::getSpecificPrice($product->id, $idShop, 0, 0, 0, 1, 0, 0, 0, 0);

                    if (is_array($specificPrice) && isset($specificPrice['id_specific_price'])) {
                        $specificPrice = new SpecificPrice((int)$specificPrice['id_specific_price']);
                    } else {
                        $specificPrice = new SpecificPrice();
                    }
                    $specificPrice->id_product = (int)$product->id;
                    $specificPrice->id_specific_price_rule = 0;
                    $specificPrice->id_shop = $idShop;
                    $specificPrice->id_currency = 0;
                    $specificPrice->id_country = 0;
                    $specificPrice->id_group = 0;
                    $specificPrice->price = -1;
                    $specificPrice->id_customer = 0;
                    $specificPrice->from_quantity = 1;
                    $specificPrice->reduction = (isset($productData['reduction_price']) && $productData['reduction_price'])
                        ? $productData['reduction_price']
                        : $productData['reduction_percent'] / 100;
                    $specificPrice->reduction_type = (isset($productData['reduction_price']) && $productData['reduction_price'])
                        ? 'amount'
                        : 'percentage';
                    $specificPrice->from = (isset($productData['reduction_from'])
                        && Validate::isDate($productData['reduction_from']))
                        ? $productData['reduction_from']
                        : '0000-00-00 00:00:00';
                    $specificPrice->to = (isset($productData['reduction_to']) && Validate::isDate($productData['reduction_to']))
                        ? $productData['reduction_to']
                        : '0000-00-00 00:00:00';
                    if (!$validateOnly && !$specificPrice->save()) {
                        $this->addProductWarning(
                            Tools::safeOutput($productData['name']),
                            $product->id,
                            $this->trans('Discount is invalid', array(), 'Admin.Advparameters.Notification')
                        );
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
                            $tags[$idLang] = $product->tags;
                            $product->tags = $tags;
                        }
                    }
                }
                // Delete tags for this id product, for no duplicating error
                Tag::deleteTagsForProduct($product->id);
                if (!is_array($product->tags) && !empty($product->tags)) {
                    $product->tags = AdminImportController::createMultiLangField($product->tags);
                    foreach ($product->tags as $key => $tags) {
                        $isTagAdded = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
                        if (!$isTagAdded) {
                            $this->addProductWarning(
                                Tools::safeOutput($productData['name']),
                                $product->id,
                                $this->trans('Tags list is invalid', array(), 'Admin.Advparameters.Notification')
                            );
                            break;
                        }
                    }
                } else {
                    foreach ($product->tags as $key => $tags) {
                        $str = '';
                        foreach ($tags as $oneTag) {
                            $str .= $oneTag.$this->multiple_value_separator;
                        }
                        $str = rtrim($str, $this->multiple_value_separator);

                        $isTagAdded = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
                        if (!$isTagAdded) {
                            $this->addProductWarning(Tools::safeOutput($productData['name']), (int)$product->id, $this->trans('Invalid tag(s) (%tags%)',
                                array('%tags%' => $str),
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
                $productHasImages = (bool)Image::getImages($this->context->language->id, (int)$product->id);
                foreach ($product->image as $key => $url) {
                    $url = trim($url);
                    $error = false;
                    if (!empty($url)) {
                        $url = str_replace(' ', '%20', $url);

                        $image = new Image();
                        $image->id_product = (int)$product->id;
                        $image->position = Image::getHighestPosition($product->id) + 1;
                        $image->cover = (!$key && !$productHasImages) ? true : false;
                        $alt = $product->image_alt[$key];
                        if (strlen($alt) > 0) {
                            $image->legend = self::createMultiLangField($alt);
                        }
                        // file_exists doesn't work with HTTP protocol
                        if (($fieldError = $image->validateFields(UNFRIENDLY_ERROR, true)) === true
                            && ($langFieldError = $image->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                            && $image->add()
                        ) {
                            // associate image to selected shops
                            $image->associateTo($shops);
                            if (!AdminImportController::copyImg(
                                $product->id,
                                $image->id,
                                $url,
                                'products',
                                !$regenerate
                            )) {
                                $image->delete();
                                $this->warnings[] = $this->trans(
                                    'Error copying image: %url%',
                                    array('%url%' => $url),
                                    'Admin.Advparameters.Notification'
                                );
                            }
                        } else {
                            $error = true;
                        }
                    } else {
                        $error = true;
                    }

                    if ($error) {
                        $this->warnings[] = $this->trans(
                            'Product #%id%: the picture (%url%) cannot be saved.',
                            array(
                                '%id%'  => (int)$product->id,
                                '%url%' => $url,
                            ),
                            'Admin.Advparameters.Notification'
                        );
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
                foreach (explode($this->multiple_value_separator, $features['features']) as $singleFeature) {
                    if (empty($singleFeature)) {
                        continue;
                    }
                    $tabFeature   = explode(':', $singleFeature);
                    $featureName  = isset($tabFeature[0]) ? trim($tabFeature[0]) : '';
                    $featureValue = isset($tabFeature[1]) ? trim($tabFeature[1]) : '';
                    $position     = isset($tabFeature[2]) ? (int)$tabFeature[2] - 1 : false;
                    $custom       = isset($tabFeature[3]) ? (int)$tabFeature[3] : false;
                    if (!empty($featureName) && !empty($featureValue)) {
                        $idFeature = (int)Feature::addFeatureImport($featureName, $position);
                        $idProduct = null;
                        if ($forceIds || $matchRef) {
                            $idProduct = (int)$product->id;
                        }
                        $idFeatureValue = (int)FeatureValue::addFeatureValueImport(
                            $idFeature,
                            $featureValue,
                            $idProduct,
                            $idLang,
                            $custom
                        );
                        Product::addFeatureProductImport($product->id, $idFeature, $idFeatureValue);
                    }
                }
            }
            // clean feature positions to avoid conflict
            Feature::cleanPositions();

            // set advanced stock managment
            if (!$validateOnly && isset($product->advanced_stock_management)) {
                if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management has incorrect value. Not set for product %name% ',
                        array('%name%' => $product->name[$defaultLanguageId]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                    && $product->advanced_stock_management == 1
                ) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot enable on product %name% ',
                        array('%name%' => $product->name[$defaultLanguageId]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif ($updateAdvancedStockManagementValue) {
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
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, warehouse not set on product %name% ',
                        array('%name%' => $product->name[$defaultLanguageId]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    if (Warehouse::exists($product->warehouse)) {
                        // Get already associated warehouses
                        $associatedWarehousesCollection = WarehouseProductLocation::getCollection($product->id);
                        // Delete any entry in warehouse for this product
                        foreach ($associatedWarehousesCollection as $awc) {
                            $awc->delete();
                        }
                        $warehouseLocationEntity = new WarehouseProductLocation();
                        $warehouseLocationEntity->id_product = $product->id;
                        $warehouseLocationEntity->id_product_attribute = 0;
                        $warehouseLocationEntity->id_warehouse = $product->warehouse;
                        if (WarehouseProductLocation::getProductLocation(
                            $product->id,
                            0,
                            $product->warehouse
                        ) !== false) {
                            $warehouseLocationEntity->update();
                        } else {
                            $warehouseLocationEntity->save();
                        }
                        StockAvailable::synchronize($product->id);
                    } else {
                        $this->warnings[] = $this->trans(
                            'Warehouse did not exist, cannot set on product %name%.',
                            array('%name%' => $product->name[$defaultLanguageId],),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            // stock available
            if (isset($product->depends_on_stock)) {
                if ($product->depends_on_stock != 0 && $product->depends_on_stock != 1) {
                    $this->warnings[] = $this->trans(
                        'Incorrect value for "Depends on stock" for product %name% ',
                        array('%name%' => $product->name[$defaultLanguageId]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif ((!$product->advanced_stock_management || $product->advanced_stock_management == 0)
                    && $product->depends_on_stock == 1
                ) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot set "Depends on stock" for product %name% ',
                        array('%name%' => $product->name[$defaultLanguageId]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock($product->id, $product->depends_on_stock);
                }

                // This code allows us to set qty and disable depends on stock
                if (!$validateOnly && isset($product->quantity)) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($product->depends_on_stock == 1) {
                        $stockManager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $product->wholesale_price);
                        if ($price == 0) {
                            $price = 0.000001;
                        }
                        $price = round(floatval($price), 6);
                        $warehouse = new Warehouse($product->warehouse);
                        if ($stockManager->addProduct(
                            (int)$product->id,
                            0,
                            $warehouse,
                            (int)$product->quantity,
                            1,
                            $price,
                            true
                        )) {
                            StockAvailable::synchronize((int)$product->id);
                        }
                    } else {
                        if ($shopIsFeatureActive) {
                            foreach ($shops as $shop) {
                                StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                            }
                        } else {
                            StockAvailable::setQuantity(
                                (int)$product->id,
                                0,
                                (int)$product->quantity,
                                (int)$this->context->shop->id
                            );
                        }
                    }
                }
            } elseif (!$validateOnly) {
                // if not depends_on_stock set, use normal qty
                if ($shopIsFeatureActive) {
                    foreach ($shops as $shop) {
                        StockAvailable::setQuantity((int)$product->id, 0, (int)$product->quantity, (int)$shop);
                    }
                } else {
                    StockAvailable::setQuantity(
                        (int)$product->id,
                        0,
                        (int)$product->quantity,
                        (int)$this->context->shop->id
                    );
                }
            }

            // Accessories linkage
            if (isset($product->accessories)
                && !$validateOnly
                && is_array($product->accessories)
                && 0 < count($product->accessories)
            ) {
                $accessories[$product->id] = $product->accessories;
            }
        }
    }

    public function productImportCreateCat($defaultLanguageId, $categoryName, $idParentCategory = null)
    {
        $categoryToCreate = new Category();
        $shopIsFeatureActive = Shop::isFeatureActive();
        if (!$shopIsFeatureActive) {
            $categoryToCreate->id_shop_default = 1;
        } else {
            $categoryToCreate->id_shop_default = (int)Context::getContext()->shop->id;
        }
        $categoryToCreate->name = AdminImportController::createMultiLangField(trim($categoryName));
        $categoryToCreate->active = 1;
        $categoryToCreate->id_parent = (int)$idParentCategory
            ? (int)$idParentCategory
            : (int)Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
        $categoryLinkRewrite = Tools::link_rewrite($categoryToCreate->name[$defaultLanguageId]);
        $categoryToCreate->link_rewrite = AdminImportController::createMultiLangField($categoryLinkRewrite);

        if (($fieldError = $categoryToCreate->validateFields(UNFRIENDLY_ERROR, true)) !== true ||
            ($langFieldError = $categoryToCreate->validateFieldsLang(UNFRIENDLY_ERROR, true)) !== true ||
            !$categoryToCreate->add()) {
            $this->errors[] = $this->trans(
                '%category% (ID: %id%) cannot be saved',
                array(
                    '%category%' => $categoryToCreate->name[$defaultLanguageId],
                    '%id%'       => !empty($categoryToCreate->id) ? $categoryToCreate->id : 'null',
                ),
                'Admin.Advparameters.Notification'
            );
            if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                $this->errors[] = ($fieldError !== true ? $fieldError : '')
                    . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                    . Db::getInstance()->getMsgError();
            }
        }
    }

    public function attributeImport(
        $offset = false,
        $limit = false,
        &$crossStepsVariables = false,
        $validateOnly = false
    ) {
        if (!is_array($crossStepsVariables)) {
            $crossStepsVariables = array();
        }

        $defaultLanguage = Configuration::get('PS_LANG_DEFAULT');

        $groups = array();
        if (array_key_exists('groups', $crossStepsVariables)) {
            $groups = $crossStepsVariables['groups'];
        }
        foreach (AttributeGroup::getAttributesGroups($defaultLanguage) as $group) {
            $groups[$group['name']] = (int)$group['id_attribute_group'];
        }

        $attributes = array();
        if (array_key_exists('attributes', $crossStepsVariables)) {
            $attributes = $crossStepsVariables['attributes'];
        }
        foreach (Attribute::getAttributes($defaultLanguage) as $attribute) {
            $attributes[$attribute['attribute_group'].'_'.$attribute['name']] = (int)$attribute['id_attribute'];
        }

        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $regenerate = Tools::getValue('regenerate');
        $shopIsFeatureActive = Shop::isFeatureActive();

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;

            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );

                continue;
            }

            $info = AdminImportController::getMaskedRow($line);
            $info = array_map('trim', $info);

            $this->attributeImportOne(
                $info,
                $defaultLanguage,
                $groups, // by ref
                $attributes, // by ref
                $regenerate,
                $shopIsFeatureActive,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        $crossStepsVariables['groups'] = $groups;
        $crossStepsVariables['attributes'] = $attributes;

        return $lineCount;
    }

    protected function attributeImportOne(
        $info,
        $defaultLanguage,
        &$groups,
        &$attributes,
        $regenerate,
        $shopIsFeatureActive,
        $validateOnly = false
    ) {
        AdminImportController::setDefaultValues($info);

        if (!$shopIsFeatureActive) {
            $info['shop'] = 1;
        } elseif (!isset($info['shop']) || empty($info['shop'])) {
            $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        // Get shops for each attributes
        $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

        $idShopList = array();
        if (is_array($info['shop']) && count($info['shop'])) {
            foreach ($info['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $idShopList[] = Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $idShopList[] = $shop;
                }
            }
        }

        if (isset($info['id_product']) && $info['id_product']) {
            $product = new Product((int)$info['id_product'], false, $defaultLanguage);
        } elseif (Tools::getValue('match_ref') && isset($info['product_reference']) && $info['product_reference']) {
            $datas = Db::getInstance()->getRow(
                'SELECT p.`id_product`'
                . ' FROM `' . _DB_PREFIX_ . 'product` p ' . Shop::addSqlAssociation('product', 'p')
                . ' WHERE p.`reference` = "' . pSQL($info['product_reference']) . '"',
                false
            );
            if (isset($datas['id_product']) && $datas['id_product']) {
                $product = new Product((int)$datas['id_product'], false, $defaultLanguage);
            } else {
                return;
            }
        } else {
            return;
        }

        $idImage = array();

        if (isset($info['image_url']) && $info['image_url']) {
            $info['image_url'] = explode($this->multiple_value_separator, $info['image_url']);

            if (is_array($info['image_url']) && count($info['image_url'])) {
                foreach ($info['image_url'] as $key => $url) {
                    $url = trim($url);
                    $productHasImages = (bool)Image::getImages($this->context->language->id, $product->id);

                    $image = new Image();
                    $image->id_product = (int)$product->id;
                    $image->position = Image::getHighestPosition($product->id) + 1;
                    $image->cover = (!$productHasImages) ? true : false;

                    if (isset($info['image_alt'])) {
                        $alt = self::split($info['image_alt']);
                        if (isset($alt[$key]) && strlen($alt[$key]) > 0) {
                            $alt = self::createMultiLangField($alt[$key]);
                            $image->legend = $alt;
                        }
                    }

                    $fieldError = $image->validateFields(UNFRIENDLY_ERROR, true);
                    $langFieldError = $image->validateFieldsLang(UNFRIENDLY_ERROR, true);

                    if ($fieldError === true
                        && $langFieldError === true
                        && !$validateOnly
                        && $image->add()
                    ) {
                        $image->associateTo($idShopList);
                        // FIXME: 2s/image !
                        if (!AdminImportController::copyImg($product->id, $image->id, $url, 'products', !$regenerate)) {
                            $this->warnings[] = $this->trans(
                                'Error copying image: %url%',
                                array('%url%' => $url),
                                'Admin.Advparameters.Notification'
                            );
                            $image->delete();
                        } else {
                            $idImage[] = (int)$image->id;
                        }
                        // until here
                    } else {
                        if (!$validateOnly) {
                            $this->warnings[] = $this->trans(
                                '%data% cannot be saved',
                                array('%data%' => (isset($image->id_product) ? ' ('.$image->id_product.')' : '')),
                                'Admin.Advparameters.Notification'
                            );
                        }
                        if ($fieldError !== true || $langFieldError !== true) {
                            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                                . Db::getInstance()->getMsgError();
                        }
                    }
                }
            }
        } elseif (isset($info['image_position']) && $info['image_position']) {
            $info['image_position'] = explode($this->multiple_value_separator, $info['image_position']);

            if (is_array($info['image_position']) && count($info['image_position'])) {
                foreach ($info['image_position'] as $position) {
                    // choose images from product by position
                    $images = $product->getImages($defaultLanguage);

                    if ($images) {
                        foreach ($images as $row) {
                            if ($row['position'] == (int)$position) {
                                $idImage[] = (int)$row['id_image'];
                                break;
                            }
                        }
                    }
                    if (empty($idImage)) {
                        $this->warnings[] = $this->trans(
                            'No image was found for combination with id_product = %id% and image position = %pos%.',
                            array(
                                '%id%'  => $product->id,
                                '%pos%' => (int)$position,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }
        }

        $idAttributeGroup = 0;
        // groups
        $groupsAttributes = array();
        if (isset($info['group'])) {
            foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                if (empty($group)) {
                    continue;
                }
                $tabGroup = explode(':', $group);
                $group = trim($tabGroup[0]);
                if (!isset($tabGroup[1])) {
                    $type = 'select';
                } else {
                    $type = trim($tabGroup[1]);
                }

                // sets group
                $groupsAttributes[$key]['group'] = $group;

                // if position is filled
                if (isset($tabGroup[2])) {
                    $position = trim($tabGroup[2]);
                } else {
                    $position = false;
                }

                if (!isset($groups[$group])) {
                    $obj                                = new AttributeGroup();
                    $obj->is_color_group                = false;
                    $obj->group_type                    = pSQL($type);
                    $obj->name[$defaultLanguage]        = $group;
                    $obj->public_name[$defaultLanguage] = $group;
                    $obj->position                      = (!$position)
                        ? AttributeGroup::getHigherPosition() + 1
                        : $position;

                    if (($fieldError = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true
                        && ($langFieldError = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    ) {
                        // here, cannot avoid attributeGroup insertion to avoid an error during validation step.
                        //if (!$validateOnly) {
                            $obj->add();
                        $obj->associateTo($idShopList);
                        $groups[$group] = $obj->id;
                        //}
                    } else {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '');
                    }

                    // fills groups attributes
                    $idAttributeGroup = $obj->id;
                    $groupsAttributes[$key]['id'] = $idAttributeGroup;
                } else {
                    // already exists

                    $idAttributeGroup = $groups[$group];
                    $groupsAttributes[$key]['id'] = $idAttributeGroup;
                }
            }
        }

        // inits attribute
        $idProductAttribute = 0;
        $idProductAttributeUpdate = false;
        $attributesToAdd = array();

        // for each attribute
        if (isset($info['attribute'])) {
            foreach (explode($this->multiple_value_separator, $info['attribute']) as $key => $attribute) {
                if (empty($attribute)) {
                    continue;
                }
                $tabAttribute = explode(':', $attribute);
                $attribute = trim($tabAttribute[0]);
                // if position is filled
                if (isset($tabAttribute[1])) {
                    $position = trim($tabAttribute[1]);
                } else {
                    $position = false;
                }

                if (isset($groupsAttributes[$key])) {
                    $group = $groupsAttributes[$key]['group'];
                    if (!isset($attributes[$group.'_'.$attribute]) && count($groupsAttributes[$key]) == 2) {
                        $idAttributeGroup = $groupsAttributes[$key]['id'];
                        $obj = new Attribute();
                        // sets the proper id (corresponding to the right key)
                        $obj->id_attribute_group     = $groupsAttributes[$key]['id'];
                        $obj->name[$defaultLanguage] = str_replace('\n', '', str_replace('\r', '', $attribute));
                        $obj->position               = (!$position && isset($groups[$group]))
                            ? Attribute::getHigherPosition($groups[$group]) + 1
                            : $position;

                        if (($fieldError = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true
                            && ($langFieldError = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                        ) {
                            if (!$validateOnly) {
                                $obj->add();
                                $obj->associateTo($idShopList);
                                $attributes[$group.'_'.$attribute] = $obj->id;
                            }
                        } else {
                            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '');
                        }
                    }

                    $info['minimal_quantity'] = isset($info['minimal_quantity']) && $info['minimal_quantity']
                        ? (int)$info['minimal_quantity']
                        : 1;
                    $info['low_stock_threshold'] = empty($info['low_stock_threshold']) && '0' != $info['low_stock_threshold'] ? null : (int)$info['low_stock_threshold'];
                    $info['low_stock_alert'] = !empty($info['low_stock_alert']);

                    $info['wholesale_price'] = str_replace(',', '.', $info['wholesale_price']);
                    $info['price'] = str_replace(',', '.', $info['price']);
                    $info['ecotax'] = str_replace(',', '.', $info['ecotax']);
                    $info['weight'] = str_replace(',', '.', $info['weight']);
                    $info['available_date'] = Validate::isDate($info['available_date'])
                        ? $info['available_date']
                        : null;

                    if (!Validate::isEan13($info['ean13'])) {
                        $this->warnings[] = $this->trans(
                            'EAN13 "%ean%" has incorrect value for product with id %id%.',
                            array(
                                '%ean%' => $info['ean13'],
                                '%id%'  => $product->id,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                        $info['ean13']    = '';
                    }

                    if ($info['default_on'] && !$validateOnly) {
                        $product->deleteDefaultAttributes();
                    }

                    // if a reference is specified for this product, get the associate id_product_attribute to UPDATE
                    if (isset($info['reference']) && !empty($info['reference'])) {
                        $idProductAttribute = Combination::getIdByReference($product->id, strval($info['reference']));

                        // updates the attribute
                        if ($idProductAttribute && !$validateOnly) {
                            // gets all the combinations of this product
                            $attributeCombinations = $product->getAttributeCombinations($defaultLanguage);
                            foreach ($attributeCombinations as $attributeCombination) {
                                if ($idProductAttribute && in_array($idProductAttribute, $attributeCombination)) {
                                    // FIXME: ~3s/declinaison
                                    $product->updateAttribute(
                                        $idProductAttribute,
                                        (float)$info['wholesale_price'],
                                        (float)$info['price'],
                                        (float)$info['weight'],
                                        0,
                                        (Configuration::get('PS_USE_ECOTAX') ? (float)$info['ecotax'] : 0),
                                        $idImage,
                                        (string)$info['reference'],
                                        (string)$info['ean13'],
                                        ((int)$info['default_on'] ? (int)$info['default_on'] : null),
                                        0,
                                        (string)$info['upc'],
                                        (int)$info['minimal_quantity'],
                                        $info['available_date'],
                                        null,
                                        $idShopList,
                                        '',
                                        $info['low_stock_threshold'],
                                        $info['low_stock_alert']
                                    );
                                    $idProductAttributeUpdate = true;
                                    if (isset($info['supplier_reference']) && !empty($info['supplier_reference'])) {
                                        $product->addSupplierReference(
                                            $product->id_supplier,
                                            $idProductAttribute,
                                            $info['supplier_reference']
                                        );
                                    }
// until here
                                }
                            }
                        }
                    }

                    // if no attribute reference is specified, creates a new one
                    if (!$idProductAttribute && !$validateOnly) {
                        $idProductAttribute = $product->addCombinationEntity(
                            (float)$info['wholesale_price'],
                            (float)$info['price'],
                            (float)$info['weight'],
                            0,
                            (Configuration::get('PS_USE_ECOTAX') ? (float)$info['ecotax'] : 0),
                            (int)$info['quantity'],
                            $idImage,
                            (string)$info['reference'],
                            0,
                            (string)$info['ean13'],
                            ((int)$info['default_on'] ? (int)$info['default_on'] : null),
                            0,
                            (string)$info['upc'],
                            (int)$info['minimal_quantity'],
                            $idShopList,
                            $info['available_date'],
                            '',
                            $info['low_stock_threshold'],
                            $info['low_stock_alert']
                        );

                        if (isset($info['supplier_reference']) && !empty($info['supplier_reference'])) {
                            $product->addSupplierReference(
                                $product->id_supplier,
                                $idProductAttribute,
                                $info['supplier_reference']
                            );
                        }
                    }

                    // fills our attributes array, in order to add the attributes to the product_attribute afterwards
                    if (isset($attributes[$group.'_'.$attribute])) {
                        $attributesToAdd[] = (int)$attributes[$group.'_'.$attribute];
                    }

                    // after insertion, we clean attribute position and group attribute position
                    if (!$validateOnly) {
                        $obj = new Attribute();
                        $obj->cleanPositions((int)$idAttributeGroup, false);
                        AttributeGroup::cleanPositions();
                    }
                }
            }
        }

        $product->checkDefaultAttributes();
        if (!$product->cache_default_attribute && !$validateOnly) {
            Product::updateDefaultAttribute($product->id);
        }
        if ($idProductAttribute) {
            if (!$validateOnly) {
                // now adds the attributes in the attribute_combination table
                if ($idProductAttributeUpdate) {
                    Db::getInstance()->execute(
                        'DELETE FROM ' . _DB_PREFIX_ . 'product_attribute_combination'
                        . ' WHERE id_product_attribute = ' . (int)$idProductAttribute
                    );
                }

                foreach ($attributesToAdd as $attributeToAdd) {
                    Db::getInstance()->execute(
                        'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'product_attribute_combination'
                        . ' (id_attribute, id_product_attribute)'
                        . ' VALUES (' . (int)$attributeToAdd . ',' . (int)$idProductAttribute . ')',
                        false
                    );
                }
            }

            // set advanced stock managment
            if (isset($info['advanced_stock_management'])) {
                if ($info['advanced_stock_management'] != 1 && $info['advanced_stock_management'] != 0) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management has incorrect value. Not set for product with id %id%.',
                        array('%id%' => $product->id),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                    && $info['advanced_stock_management'] == 1
                ) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot enable on product with id %id%.',
                        array('%id%' => $product->id),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    $product->setAdvancedStockManagement($info['advanced_stock_management']);
                }
                // automatically disable depends on stock, if a_s_m set to disabled
                if (!$validateOnly
                    && StockAvailable::dependsOnStock($product->id) == 1
                    && $info['advanced_stock_management'] == 0
                ) {
                    StockAvailable::setProductDependsOnStock($product->id, 0, null, $idProductAttribute);
                }
            }

            // Check if warehouse exists
            if (isset($info['warehouse']) && $info['warehouse']) {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, warehouse is not set on product with id %id%.',
                        array('%id%' => $product->id),
                        'Admin.Advparameters.Notification'
                    );
                } else {
                    if (Warehouse::exists($info['warehouse'])) {
                        $warehouseLocationEntity = new WarehouseProductLocation();
                        $warehouseLocationEntity->id_product = $product->id;
                        $warehouseLocationEntity->id_product_attribute = $idProductAttribute;
                        $warehouseLocationEntity->id_warehouse = $info['warehouse'];
                        if (!$validateOnly) {
                            if (WarehouseProductLocation::getProductLocation(
                                $product->id,
                                $idProductAttribute,
                                $info['warehouse']
                            ) !== false) {
                                $warehouseLocationEntity->update();
                            } else {
                                $warehouseLocationEntity->save();
                            }
                            StockAvailable::synchronize($product->id);
                        }
                    } else {
                        $this->warnings[] = $this->trans('Warehouse did not exist, cannot set on product %name%.',
                            array('%name%' => $product->name[$defaultLanguage]),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            // stock available
            if (isset($info['depends_on_stock'])) {
                if ($info['depends_on_stock'] != 0 && $info['depends_on_stock'] != 1) {
                    $this->warnings[] = $this->trans('Incorrect value for "Depends on stock" for product %name% ',
                        array('%name%' => $product->name[$defaultLanguage]),
                        'Admin.Notifications.Error'
                    );
                } elseif ((!$info['advanced_stock_management'] || $info['advanced_stock_management'] == 0)
                    && $info['depends_on_stock'] == 1
                ) {
                    $this->warnings[] = $this->trans('Advanced stock management is not enabled, cannot set "Depends on stock" for product %name% ',
                        array('%name%' => $product->name[$defaultLanguage]),
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock(
                        $product->id,
                        $info['depends_on_stock'],
                        null,
                        $idProductAttribute
                    );
                }

                // This code allows us to set qty and disable depends on stock
                if (isset($info['quantity'])) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($info['depends_on_stock'] == 1) {
                        $stockManager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $info['wholesale_price']);
                        if ($price == 0) {
                            $price = 0.000001;
                        }
                        $price = round(floatval($price), 6);
                        $warehouse = new Warehouse($info['warehouse']);
                        if (!$validateOnly
                            && $stockManager->addProduct(
                                (int)$product->id,
                                $idProductAttribute,
                                $warehouse,
                                (int)$info['quantity'],
                                1,
                                $price,
                                true
                            )
                        ) {
                            StockAvailable::synchronize((int)$product->id);
                        }
                    } elseif (!$validateOnly) {
                        if ($shopIsFeatureActive) {
                            foreach ($idShopList as $shop) {
                                StockAvailable::setQuantity(
                                    (int)$product->id,
                                    $idProductAttribute,
                                    (int)$info['quantity'],
                                    (int)$shop
                                );
                            }
                        } else {
                            StockAvailable::setQuantity(
                                (int)$product->id,
                                $idProductAttribute,
                                (int)$info['quantity'],
                                $this->context->shop->id
                            );
                        }
                    }
                }
            } elseif (!$validateOnly) { // if not depends_on_stock set, use normal qty
                if ($shopIsFeatureActive) {
                    foreach ($idShopList as $shop) {
                        StockAvailable::setQuantity(
                            (int)$product->id,
                            $idProductAttribute,
                            (int)$info['quantity'],
                            (int)$shop
                        );
                    }
                } else {
                    StockAvailable::setQuantity(
                        (int)$product->id,
                        $idProductAttribute,
                        (int)$info['quantity'],
                        $this->context->shop->id
                    );
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

        $defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
        $idLang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($idLang)) {
            $idLang = $defaultLanguageId;
        }
        AdminImportController::setLocale();

        $shopIsFeatureActive = Shop::isFeatureActive();
        $forceIds = Tools::getValue('forceIDs');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->customerImportOne(
                $info,
                $defaultLanguageId,
                $idLang,
                $shopIsFeatureActive,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function customerImportOne(
        $info,
        $defaultLanguageId,
        $idLang,
        $shopIsFeatureActive,
        $forceIds,
        $validateOnly = false
    ) {
        AdminImportController::setDefaultValues($info);

        if ($forceIds && isset($info['id']) && (int)$info['id']) {
            $customer = new Customer((int)$info['id']);
        } else {
            if (array_key_exists('id', $info)
                && (int)$info['id']
                && Customer::customerIdExistsStatic((int)$info['id'])
            ) {
                $customer = new Customer((int)$info['id']);
            } else {
                $customer = new Customer();
            }
        }

        $customerExists = false;
        $currentIdCustomer = null;
        $currentIdShop = null;
        $currentIdShopGroup = null;
        $autodate = true;

        if (array_key_exists('id', $info)
            && (int)$info['id']
            && Customer::customerIdExistsStatic((int)$info['id'])
            && Validate::isLoadedObject($customer)
        ) {
            $currentIdCustomer = (int)$customer->id;
            $currentIdShop = (int)$customer->id_shop;
            $currentIdShopGroup = (int)$customer->id_shop_group;
            $customerExists = true;
            $customerGroups = $customer->getGroups();
            $addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
        }

        // Group Importation
        if (isset($info['group']) && !empty($info['group'])) {
            foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                $group = trim($group);
                if (empty($group)) {
                    continue;
                }
                $idGroup = false;
                if (is_numeric($group) && $group) {
                    $myGroup = new Group((int)$group);
                    if (Validate::isLoadedObject($myGroup)) {
                        $customerGroups[] = (int)$group;
                    }
                    continue;
                }
                $myGroup = Group::searchByName($group);
                if (isset($myGroup['id_group']) && $myGroup['id_group']) {
                    $idGroup = (int)$myGroup['id_group'];
                }
                if (!$idGroup) {
                    $myGroup = new Group();
                    $myGroup->name = array($idLang => $group);
                    if ($idLang != $defaultLanguageId) {
                        $myGroup->name = $myGroup->name + array($defaultLanguageId => $group);
                    }
                    $myGroup->price_display_method = 1;
                    if (!$validateOnly) {
                        $myGroup->add();
                        if (Validate::isLoadedObject($myGroup)) {
                            $idGroup = (int)$myGroup->id;
                        }
                    }
                }
                if ($idGroup) {
                    $customerGroups[] = (int)$idGroup;
                }
            }
        } elseif (empty($info['group']) && isset($customer->id) && $customer->id) {
            $customerGroups = array(0 => Configuration::get('PS_CUSTOMER_GROUP'));
        }

        if (isset($info['date_add']) && !empty($info['date_add'])) {
            $autodate = false;
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $customer);

        if ($customer->passwd) {
            $customer->passwd = $this->get('hashing')->hash($customer->passwd, _COOKIE_KEY_);
        }

        $idShopList = explode($this->multiple_value_separator, $customer->id_shop);
        $customersShop = array();
        $customersShop['shared'] = array();
        $defaultShop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
        if ($shopIsFeatureActive && $idShopList) {
            foreach ($idShopList as $idShop) {
                if (empty($idShop)) {
                    continue;
                }
                $shop = new Shop((int)$idShop);
                $groupShop = $shop->getGroup();
                if ($groupShop->share_customer) {
                    if (!in_array($groupShop->id, $customersShop['shared'])) {
                        $customersShop['shared'][(int)$idShop] = $groupShop->id;
                    }
                } else {
                    $customersShop[(int)$idShop] = $groupShop->id;
                }
            }
        } else {
            $defaultShop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
            $defaultShop->getGroup();
            $customersShop[$defaultShop->id] = $defaultShop->getGroup()->id;
        }

        //set temporary for validate field
        $customer->id_shop = $defaultShop->id;
        $customer->id_shop_group = $defaultShop->getGroup()->id;
        if (isset($info['id_default_group'])
            && !empty($info['id_default_group'])
            && !is_numeric($info['id_default_group'])
        ) {
            $info['id_default_group'] = trim($info['id_default_group']);
            $myGroup = Group::searchByName($info['id_default_group']);
            if (isset($myGroup['id_group']) && $myGroup['id_group']) {
                $info['id_default_group'] = (int)$myGroup['id_group'];
            }
        }
        $myGroup = new Group($customer->id_default_group);
        if (!Validate::isLoadedObject($myGroup)) {
            $customer->id_default_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        }
        $customerGroups[] = (int)$customer->id_default_group;
        $customerGroups = array_flip(array_flip($customerGroups));

        // Bug when updating existing user that were csv-imported before...
        if (isset($customer->date_upd) && $customer->date_upd == '0000-00-00 00:00:00') {
            $customer->date_upd = date('Y-m-d H:i:s');
        }

        $res = false;
        if (($fieldError = $customer->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $customer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
        ) {
            $res = true;
            foreach ($customersShop as $idShop => $idGroup) {
                $customer->force_id = (bool)$forceIds;
                if ($idShop == 'shared') {
                    foreach ($idGroup as $key => $id) {
                        $customer->id_shop = (int)$key;
                        $customer->id_shop_group = (int)$id;
                        if ($customerExists
                            && ((int)$currentIdShopGroup == (int)$id
                                || in_array($currentIdShop, ShopGroup::getShopsFromGroup($id))
                            )
                        ) {
                            $customer->id = (int)$currentIdCustomer;
                            $res &= ($validateOnly || $customer->update());
                        } else {
                            $res &= ($validateOnly || $customer->add($autodate));
                            if (!$validateOnly && isset($addresses)) {
                                foreach ($addresses as $address) {
                                    $address['id_customer'] = $customer->id;
                                    unset(
                                        $address['country'],
                                        $address['state'],
                                        $address['state_iso'],
                                        $address['id_address']
                                    );
                                    Db::getInstance()->insert('address', $address, false, false);
                                }
                            }
                        }
                        if ($res && !$validateOnly && isset($customerGroups)) {
                            $customer->updateGroup($customerGroups);
                        }
                    }
                } else {
                    $customer->id_shop = $idShop;
                    $customer->id_shop_group = $idGroup;
                    if ($customerExists && (int)$idShop == (int)$currentIdShop) {
                        $customer->id = (int)$currentIdCustomer;
                        $res &= ($validateOnly || $customer->update());
                    } else {
                        $res &= ($validateOnly || $customer->add($autodate));
                        if (!$validateOnly && isset($addresses)) {
                            foreach ($addresses as $address) {
                                $address['id_customer'] = $customer->id;
                                unset(
                                    $address['country'],
                                    $address['state'],
                                    $address['state_iso'],
                                    $address['id_address']
                                );
                                Db::getInstance()->insert('address', $address, false, false);
                            }
                        }
                    }
                    if ($res && !$validateOnly && isset($customerGroups)) {
                        $customer->updateGroup($customerGroups);
                    }
                }
            }
        }

        if (isset($customerGroups)) {
            unset($customerGroups);
        }
        if (isset($currentIdCustomer)) {
            unset($currentIdCustomer);
        }
        if (isset($currentIdShop)) {
            unset($currentIdShop);
        }
        if (isset($currentIdShopGroup)) {
            unset($currentIdShopGroup);
        }
        if (isset($addresses)) {
            unset($addresses);
        }

        if (!$res) {
            if ($validateOnly) {
                $this->errors[] = $this->trans(
                    'Email address %email% (ID: %id%) cannot be validated.',
                    array(
                        '%email%' => $info['email'],
                        '%id%'    => !empty($info['id']) ? $info['id'] : 'null',
                    ),
                    'Admin.Advparameters.Notification'
                );
            } else {
                $this->errors[] = $this->trans(
                    'Email address %email% (ID: %id%) cannot be saved.',
                    array(
                        '%email%' => $info['email'],
                        '%id%'    => !empty($info['id']) ? $info['id'] : 'null',
                    ),
                    'Admin.Advparameters.Notification'
                );
            }
            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                . Db::getInstance()->getMsgError();
        }
    }

    public function addressImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $forceIds = Tools::getValue('forceIDs');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->addressImportOne(
                $info,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function addressImportOne($info, $forceIds, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($forceIds && isset($info['id']) && (int)$info['id']) {
            $address = new Address((int)$info['id']);
        } else {
            if (array_key_exists('id', $info) && (int)$info['id'] && Address::addressExists((int)$info['id'])) {
                $address = new Address((int)$info['id']);
            } else {
                $address = new Address();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $address);

        $country = null;
        if (isset($address->country) && is_numeric($address->country)) {
            if (Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), (int)$address->country)) {
                $address->id_country = (int)$address->country;
            }
        } elseif (isset($address->country) && is_string($address->country) && !empty($address->country)) {
            if ($idCountry = Country::getIdByName(null, $address->country)) {
                $address->id_country = (int)$idCountry;
            } else {
                $country = new Country();
                $country->active = 1;
                $country->name = AdminImportController::createMultiLangField($address->country);
                // Default zone for country to create
                $country->id_zone = 0;
                // Default iso for country to create :
                $country->iso_code = Tools::strtoupper(Tools::substr($address->country, 0, 2));
                $country->contains_states = 0; // Default value for country to create
                $langFieldError = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $country->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $country->add()
                ) {
                    $address->id_country = (int)$country->id;
                } else {
                    if (!$validateOnly) {
                        $defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array('%data%' => $country->name[$defaultLanguageId]),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->state) && is_numeric($address->state)) {
            if (State::getNameById((int)$address->state)) {
                $address->id_state = (int)$address->state;
            }
        } elseif (isset($address->state) && is_string($address->state) && !empty($address->state)) {
            if ($idState = State::getIdByName($address->state)) {
                $address->id_state = (int)$idState;
            } else {
                $state = new State();
                $state->active = 1;
                $state->name = $address->state;
                $state->id_country = isset($country->id) ? (int)$country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                // Default iso for state to create :
                $state->iso_code = Tools::strtoupper(Tools::substr($address->state, 0, 2));
                $state->tax_behavior = 0;
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $state->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $state->add()
                ) {
                    $address->id_state = (int)$state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans('%data% cannot be saved',
                            array('%data%' => $state->name),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->customer_email) && !empty($address->customer_email)) {
            if (Validate::isEmail($address->customer_email)) {
                // a customer could exists in different shop
                $customerList = Customer::getCustomersByEmail($address->customer_email);

                if (count($customerList) == 0) {
                    if ($validateOnly) {
                        $this->errors[] = $this->trans(
                            '%email% does not exist in database %errMessage% (ID: %id%), and therefore cannot be validated',
                            array(
                                '%email%'      => $address->customer_email,
                                '%errMessage%' => Db::getInstance()->getMsgError(),
                                '%id%'         => (isset($info['id']) && !empty($info['id']))? $info['id'] : 'null',
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%email% does not exist in database %errMessage% (ID: %id%), and therefore cannot be saved',
                            array(
                                '%email%'      => $address->customer_email,
                                '%errMessage%' => Db::getInstance()->getMsgError(),
                                '%id%'         => (isset($info['id']) && !empty($info['id'])) ? $info['id'] : 'null',
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                $this->errors[] = $this->trans(
                    '"%email%" is not a valid email address.',
                    array('%email%' => $address->customer_email),
                    'Admin.Advparameters.Notification'
                );

                return;
            }
        } elseif (isset($address->id_customer) && !empty($address->id_customer)) {
            if (Customer::customerIdExistsStatic((int)$address->id_customer)) {
                $customer = new Customer((int)$address->id_customer);

                // a customer could exists in different shop
                $customerList = Customer::getCustomersByEmail($customer->email);

                if (count($customerList) == 0) {
                    if ($validateOnly) {
                        $this->errors[] = $this->trans(
                            '%email% does not exist in database %errMessage% (ID: %id%), and therefore cannot be validated',
                            array(
                                '%email%' => $customer->email,
                                '%errMessage%' => Db::getInstance()->getMsgError(),
                                '%id%' => (int)$address->id_customer,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%email% does not exist in database %errMessage% (ID: %id%), and therefore cannot be saved',
                            array(
                                '%email%' => $customer->email,
                                '%errMessage%' => Db::getInstance()->getMsgError(),
                                '%id%' => (int)$address->id_customer,
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                if ($validateOnly) {
                    $this->errors[] = $this->trans(
                        'The customer ID #%id% does not exist in the database, and therefore cannot be validated.',
                        array('%id%' => $address->id_customer),
                        'Admin.Advparameters.Notification'
                    );
                } else {
                    $this->errors[] = $this->trans(
                        'The customer ID #%id% does not exist in the database, and therefore cannot be saved.',
                        array('%id%' => $address->id_customer),
                        'Admin.Advparameters.Notification'
                    );
                }
            }
        } else {
            $customerList = array();
            $address->id_customer = 0;
        }

        if (isset($address->manufacturer)
            && is_numeric($address->manufacturer)
            && Manufacturer::manufacturerExists((int)$address->manufacturer)
        ) {
            $address->id_manufacturer = (int)$address->manufacturer;
        } elseif (isset($address->manufacturer)
            && is_string($address->manufacturer)
            && !empty($address->manufacturer)
        ) {
            if ($manufacturerId = Manufacturer::getIdByName($address->manufacturer)) {
                $address->id_manufacturer = $manufacturerId;
            } else {
                $manufacturer = new Manufacturer();
                $manufacturer->name = $address->manufacturer;
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $manufacturer->add()
                ) {
                    $address->id_manufacturer = (int)$manufacturer->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError() . ' '
                            . $this->trans(
                                '%manufacturer% (ID: %id%) cannot be saved',
                                array(
                                    '%manufacturer%' => $manufacturer->name,
                                    '%id%'           => !empty($manufacturer->id) ? $manufacturer->id : 'null',
                                ),
                                'Admin.Advparameters.Notification'
                            );
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->supplier)
            && is_numeric($address->supplier)
            && Supplier::supplierExists((int)$address->supplier)
        ) {
            $address->id_supplier = (int)$address->supplier;
        } elseif (isset($address->supplier) && is_string($address->supplier) && !empty($address->supplier)) {
            if ($supplierId = Supplier::getIdByName($address->supplier)) {
                $address->id_supplier = $supplierId;
            } else {
                $supplier = new Supplier();
                $supplier->name = $address->supplier;
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $supplier->add()
                ) {
                    $address->id_supplier = (int)$supplier->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError() . ' '
                            . $this->trans(
                                '%supplier% (ID: %id%) cannot be saved',
                                array(
                                    '%supplier%' => $supplier->name,
                                    '%id%'       => (isset($supplier->id) && !empty($supplier->id)) ? $supplier->id : 'null',
                                ),
                                'Admin.Advparameters.Notification'
                            );
                    }
                    if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                        $this->errors[] = ($fieldError !== true ? $fieldError : '')
                            . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        $res = false;
        if (($fieldError = $address->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $address->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
        ) {
            $address->force_id = (bool)$forceIds;

            if (isset($customerList) && count($customerList) > 0) {
                $filterList = array();
                foreach ($customerList as $customer) {
                    if (in_array($customer['id_customer'], $filterList)) {
                        continue;
                    }

                    $filterList[] = $customer['id_customer'];
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
                $this->errors[] = $this->trans('%alias% (ID: %id%) cannot be saved', array(
                    '%alias%' => $info['alias'],
                    '%id%'    => (isset($info['id']) && !empty($info['id'])) ? $info['id'] : 'null',
                ), 'Admin.Advparameters.Notification');
            }
            if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                $this->errors[] = ($fieldError !== true ? $fieldError : '')
                    . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                    . Db::getInstance()->getMsgError();
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

        $shopIsFeatureActive = Shop::isFeatureActive();
        $regenerate = Tools::getValue('regenerate');
        $forceIds = Tools::getValue('forceIDs');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->manufacturerImportOne(
                $info,
                $shopIsFeatureActive,
                $regenerate,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function manufacturerImportOne(
        $info,
        $shopIsFeatureActive,
        $regenerate,
        $forceIds,
        $validateOnly = false
    ) {
        AdminImportController::setDefaultValues($info);

        if ($forceIds && isset($info['id']) && (int)$info['id']) {
            $manufacturer = new Manufacturer((int)$info['id']);
        } else {
            if (array_key_exists('id', $info)
                && (int)$info['id']
                && Manufacturer::existsInDatabase((int)$info['id'], 'manufacturer')
            ) {
                $manufacturer = new Manufacturer((int)$info['id']);
            } else {
                $manufacturer = new Manufacturer();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $manufacturer);

        $res = false;
        if (($fieldError = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
        ) {
            if ($manufacturer->id && $manufacturer->manufacturerExists($manufacturer->id)) {
                $res = ($validateOnly || $manufacturer->update());
            }
            $manufacturer->force_id = (bool)$forceIds;
            if (!$res) {
                $res = ($validateOnly || $manufacturer->add());
            }

            //copying images of manufacturer
            if (!$validateOnly && isset($manufacturer->image) && !empty($manufacturer->image)) {
                if (!AdminImportController::copyImg(
                    $manufacturer->id,
                    null,
                    $manufacturer->image,
                    'manufacturers',
                    !$regenerate
                )) {
                    // TODO : insert translation placeholder here
                    $this->warnings[] = $this->trans(
                        '%image% cannot be copied.',
                        array('%image%' => $manufacturer->image),
                        'Admin.Advparameters.Notification'
                    );
                }
            }

            if (!$validateOnly && $res) {
                // Associate supplier to group shop
                if ($shopIsFeatureActive && $manufacturer->shop) {
                    Db::getInstance()->execute(
                        'DELETE FROM ' . _DB_PREFIX_ . 'manufacturer_shop'
                        . ' WHERE id_manufacturer = ' . (int)$manufacturer->id
                    );
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
                $this->errors[] = Db::getInstance()->getMsgError() . ' '
                    . $this->trans('%name% (ID: %id%) cannot be saved', array(
                        '%name%' => !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                        '%id%'   => !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID',
                    ), 'Admin.Advparameters.Notification');
            }
            if ($fieldError !== true || isset($langFieldError) && $langFieldError !== true) {
                $this->errors[] = ($fieldError !== true ? $fieldError : '')
                    . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '')
                    . Db::getInstance()->getMsgError();
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

        $shopIsFeatureActive = Shop::isFeatureActive();
        $regenerate = Tools::getValue('regenerate');
        $forceIds = Tools::getValue('forceIDs');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );

                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            if ($offset > 0) {
                $this->toto = true;
            }

            $this->supplierImportOne(
                $info,
                $shopIsFeatureActive,
                $regenerate,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function supplierImportOne($info, $shopIsFeatureActive, $regenerate, $forceIds, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($forceIds && isset($info['id']) && (int)$info['id']) {
            $supplier = new Supplier((int)$info['id']);
        } else {
            if (array_key_exists('id', $info)
                && (int)$info['id']
                && Supplier::existsInDatabase((int)$info['id'], 'supplier')
            ) {
                $supplier = new Supplier((int)$info['id']);
            } else {
                $supplier = new Supplier();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supplier);
        if (($fieldError = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
        ) {
            $res = false;
            if ($supplier->id && $supplier->supplierExists($supplier->id)) {
                $res = ($validateOnly || $supplier->update());
            }
            $supplier->force_id = (bool)$forceIds;
            if (!$res) {
                $res = ($validateOnly || $supplier->add());
            }

            //copying images of suppliers
            if (!$validateOnly && isset($supplier->image) && !empty($supplier->image)) {
                if (!AdminImportController::copyImg($supplier->id, null, $supplier->image, 'suppliers', !$regenerate)) {
                    $this->warnings[] = $supplier->image . ' ' . $this->trans(
                        'cannot be copied.',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                }
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError() . ' '
                    . $this->trans('%name% (ID: %id%) cannot be saved', array(
                        '%name%' => !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                        '%id%'   => !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID',
                    ), 'Admin.Advparameters.Notification');
            } elseif (!$validateOnly) {
                // Associate supplier to group shop
                if ($shopIsFeatureActive && $supplier->shop) {
                    Db::getInstance()->execute(
                        'DELETE FROM ' . _DB_PREFIX_ . 'supplier_shop'
                        . ' WHERE id_supplier = ' . (int)$supplier->id
                    );
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
            // TODO : add translation placeholder
            $this->errors[] = $this->trans(
                'Supplier is invalid',
                array(),
                'Admin.Advparameters.Notification'
            ) . ' (' . $supplier->name . ')';
            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '');
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

        $forceIds = Tools::getValue('forceIDs');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->aliasImportOne(
                $info,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function aliasImportOne($info, $forceIds, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        if ($forceIds && isset($info['id']) && (int)$info['id']) {
            $alias = new Alias((int)$info['id']);
        } else {
            if (array_key_exists('id', $info)
                && (int)$info['id']
                && Alias::existsInDatabase((int)$info['id'], 'alias')
            ) {
                $alias = new Alias((int)$info['id']);
            } else {
                $alias = new Alias();
            }
        }

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $alias);

        $res = false;
        if (($fieldError = $alias->validateFields(UNFRIENDLY_ERROR, true)) === true
            && ($langFieldError = $alias->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
        ) {
            if ($alias->id && $alias->aliasExists($alias->id)) {
                $res = ($validateOnly || $alias->update());
            }
            $alias->force_id = (bool)$forceIds;
            if (!$res) {
                $res = ($validateOnly || $alias->add());
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError() . ' '
                    . $this->trans('%name% (ID: %id%) cannot be saved', array(
                        '%name%' => $info['name'],
                        '%id%'   => (isset($info['id']) ? $info['id'] : 'null'),
                    ), 'Admin.Advparameters.Notification');
            }
        } else {
            // TODO : add translation placeholder
            $this->errors[] = $this->trans(
                'Alias is invalid',
                array(),
                'Admin.Advparameters.Notification'
            ) .' (' . $alias->name . ')';
            $this->errors[] = ($fieldError !== true ? $fieldError : '')
                . (isset($langFieldError) && $langFieldError !== true ? $langFieldError : '');
        }
    }

    public function storeContactImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $forceIds = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');

        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans(
                    'There is an empty row in the file that won\'t be imported.',
                    array(),
                    'Admin.Advparameters.Notification'
                );
                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

            $this->storeContactImportOne(
                $info,
                Shop::isFeatureActive(),
                $regenerate,
                $forceIds,
                $validateOnly
            );
        }
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    /**
     * Import a store.
     * If passed data contains a known store id, this store will be updated.
     * Can also be used for store data validation only.
     *
     * @param array $info         Store data
     * @param mixed $notUsed      Not used anymore.
     * @param bool  $regenerate   If images should be regenerated
     * @param bool  $forceIds     If passed store id should be used for store creation
     * @param bool  $validateOnly If set to true, store will not be updated nor created with $info data
     */
    public function storeContactImportOne($info, $notUsed, $regenerate, $forceIds, $validateOnly = false)
    {
        AdminImportController::setDefaultValues($info);

        $storeId = isset($info['id']) ? (int)$info['id'] : null;
        $store   = new Store($storeId);

        $store->force_id = (bool)$forceIds;

        AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $store);

        if (!empty($store->image)) {
            $imgWasCopied = AdminImportController::copyImg($store->id, null, $store->image, 'stores', !$regenerate);
            if (!$imgWasCopied) {
                $this->warnings[] = $this->trans('%image% cannot be copied.',
                    array('%image%' => $store->image),
                    'Admin.Advparameters.Notification'
                );
            }
        }

        /*
         * New structure for store hours :
         * [
         *   ['hour string'],
         *   ['another hour string'],
         * ]
         */
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
            if ($idCountry = Country::getIdByName(null, $store->country)) {
                $store->id_country = (int)$idCountry;
            } else {
                $country = new Country();
                $country->active = 1;
                $country->name = AdminImportController::createMultiLangField($store->country);
                // Default zone for country to create
                $country->id_zone = 0;
                // Default iso for country to create :
                $country->iso_code = Tools::strtoupper(Tools::substr($store->country, 0, 2));
                $country->contains_states = 0; // Default value for country to create
                $country->need_identification_number = 0; // Default value for country to create
                $langFieldsValidationResult = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldsValidationResult = $country->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldsValidationResult = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $country->add()
                ) {
                    $store->id_country = (int)$country->id;
                } else {
                    if (!$validateOnly) {
                        $defaultLanguageId = (int)Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array('%data%' => $country->name[$defaultLanguageId]),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($fieldsValidationResult !== true
                        || (isset($langFieldsValidationResult) && $langFieldsValidationResult !== true)
                    ) {
                        $this->errors[] = ($fieldsValidationResult !== true ? $fieldsValidationResult : '')
                            . (isset($langFieldsValidationResult) && $langFieldsValidationResult !== true
                                ? $langFieldsValidationResult
                                : ''
                            )
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($store->state) && is_numeric($store->state)) {
            if (State::getNameById((int)$store->state)) {
                $store->id_state = (int)$store->state;
            }
        } elseif (isset($store->state) && is_string($store->state) && !empty($store->state)) {
            if ($idState = State::getIdByName($store->state)) {
                $store->id_state = (int)$idState;
            } else {
                $state = new State();
                $state->active = 1;
                $state->name = $store->state;
                $state->id_country = isset($country->id) ? (int)$country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                // Default iso for state to create :
                $state->iso_code = Tools::strtoupper(Tools::substr($store->state, 0, 2));
                $state->tax_behavior = 0;
                // FIXME needing such a comment about "&& !$validateOnly" position is a code smell. Refacto needed.
                if (($fieldsValidationResult = $state->validateFields(UNFRIENDLY_ERROR, true)) === true
                    && ($langFieldsValidationResult = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true
                    && !$validateOnly // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    && $state->add()
                ) {
                    $store->id_state = (int)$state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            array('%data%' => $state->name),
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($fieldsValidationResult !== true
                        || isset($langFieldsValidationResult) && $langFieldsValidationResult !== true
                    ) {
                        $this->errors[] = ($fieldsValidationResult !== true ? $fieldsValidationResult : '')
                            . (isset($langFieldsValidationResult) && $langFieldsValidationResult !== true
                                ? $langFieldsValidationResult
                                : ''
                            )
                            . Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        $fieldsValidationResult     = $store->validateFields(UNFRIENDLY_ERROR, true);
        $langFieldsValidationResult = $store->validateFieldsLang(UNFRIENDLY_ERROR, true);

        // If errors, log them and stop execution
        if (true !== $fieldsValidationResult || true !== $langFieldsValidationResult) {
            $errorGenericMessage = $this->trans('Store is invalid', array(), 'Admin.Advparameters.Notification');
            $this->errors[]      = $errorGenericMessage . ' (' . $store->name . ')';

            $errorDetails = '';
            if ($fieldsValidationResult !== true) {
                $errorDetails .= $fieldsValidationResult;
            }
            if ($langFieldsValidationResult !== true) {
                $errorDetails .= $langFieldsValidationResult;
            }
            $this->errors[] = $errorDetails;

            return;
        }

        if ($validateOnly) {
            return;
        }

        $res = false;
        if ($store->storeExists($store->id)) {
            $res = $store->update();
        }

        // If store doesn't exist or update failed
        if (!$res) {
            $res = $store->add();
        }

        // If nothing worked, log an error
        if (!$res) {
            $this->errors[] = Db::getInstance()->getMsgError() . ' '
                . $this->trans('%name% (ID: %id%) cannot be saved', array(
                    '%name%' => $info['name'],
                    '%id%'   => $store->id ? $store->id : 'null',
                ), 'Admin.Advparameters.Notification');
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

        $forceIds = Tools::getValue('forceIDs');

        // main loop, for each supply orders to import
        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            $this->supplyOrdersImportOne(
                $info,
                $forceIds,
                $lineCount - 1,
                $validateOnly
            );
        }
        // closes
        $this->closeCsvFile($handle);

        return $lineCount;
    }

    protected function supplyOrdersImportOne($info, $forceIds, $currentLine, $validateOnly = false)
    {
        // sets default values if needed
        AdminImportController::setDefaultValues($info);

        // if an id is set, instanciates a supply order with this id if possible
        if (array_key_exists('id', $info) && (int)$info['id'] && SupplyOrder::exists((int)$info['id'])) {
            $supplyOrder = new SupplyOrder((int)$info['id']);
        } elseif (array_key_exists('reference', $info)
            && $info['reference']
            && SupplyOrder::exists(pSQL($info['reference']))
        ) {
            $supplyOrder = SupplyOrder::getSupplyOrderByReference(pSQL($info['reference']));
        } else { // new supply order
            $supplyOrder = new SupplyOrder();
        }

        // gets parameters
        $idSupplier = (int)$info['id_supplier'];
        $idLang = (int)$info['id_lang'];
        $idWarehouse = (int)$info['id_warehouse'];
        $idCurrency = (int)$info['id_currency'];
        $reference = pSQL($info['reference']);
        $dateDeliveryExpected = pSQL($info['date_delivery_expected']);
        $discountRate = (float)$info['discount_rate'];

        $error = '';
        // checks parameters
        if (!Supplier::supplierExists($idSupplier)) {
            $error = $this->trans(
                'Supplier ID (%id%) is not valid (at line %line%).',
                array(
                    '%id%'   => $idSupplier,
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (!Language::getLanguage($idLang)) {
            $error = $this->trans(
                'Lang ID (%id%) is not valid (at line %line%).',
                array(
                    '%id%'   => $idLang,
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (!Warehouse::exists($idWarehouse)) {
            $error = $this->trans(
                'Warehouse ID (%id%) is not valid (at line %line%).',
                array(
                    '%id%'   => $idWarehouse,
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (!Currency::getCurrency($idCurrency)) {
            $error = $this->trans(
                'Currency ID (%id%) is not valid (at line %line%).',
                array(
                    '%id%'   => $idCurrency,
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (empty($supplyOrder->reference) && SupplyOrder::exists($reference)) {
            $error = $this->trans(
                'Reference (%reference%) already exists (at line %line%).',
                array(
                    '%reference%' => $reference,
                    '%line%'      => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (!empty($supplyOrder->reference)
            && $supplyOrder->reference != $reference
            && SupplyOrder::exists($reference)
        ) {
            $error = $this->trans(
                'Reference (%reference%) already exists (at line %line%).',
                array(
                    '%reference%' => $reference,
                    '%line%'      => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if (!Validate::isDateFormat($dateDeliveryExpected)) {
            $error = $this->trans(
                'Date format (%date%) is not valid (at line %line%). It should be: %otherFormat%.',
                array(
                    '%date%'        => $dateDeliveryExpected,
                    '%line%'        => $currentLine + 1,
                    '%otherFormat%' => $this->trans('YYYY-MM-DD', array(), 'Admin.Advparameters.Notification'),
                ),
                'Admin.Advparameters.Notification'
            );
        } elseif (new DateTime($dateDeliveryExpected) <= new DateTime('yesterday')) {
            $error = $this->trans(
                'Date (%date%) cannot be in the past (at line %line%). Format: %format%.',
                array(
                    '%date%'   => $dateDeliveryExpected,
                    '%line%'   => $currentLine + 1,
                    '%format%' => $this->trans('YYYY-MM-DD', array(), 'Admin.Advparameters.Notification'),
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if ($discountRate < 0 || $discountRate > 100) {
            $error = $this->trans(
                'Discount rate (%rate%) is not valid (at line %line%). %reason%.',
                array(
                    '%rate%'   => $discountRate,
                    '%line%'   => $currentLine + 1,
                    '%reason%' => $this->trans(
                        'Format: Between 0 and 100',
                        array(),
                        'Admin.Advparameters.Notification'
                    ),
                ),
                'Admin.Advparameters.Notification'
            );
        }
        if ($supplyOrder->id > 0 && !$supplyOrder->isEditable()) {
            $error = $this->trans(
                'Supply Order (%id%) is not editable (at line %line%).',
                array(
                    '%id%'   => $supplyOrder->id,
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }

        // if no errors, sets supply order
        if (empty($error)) {
            // adds parameters
            $info['id_ref_currency'] = (int)Currency::getDefaultCurrency()->id;
            $info['supplier_name'] = pSQL(Supplier::getNameById($idSupplier));
            if ($supplyOrder->id > 0) {
                $info['id_supply_order_state'] = (int)$supplyOrder->id_supply_order_state;
                $info['id'] = (int)$supplyOrder->id;
            } else {
                $info['id_supply_order_state'] = 1;
            }

            // sets parameters
            AdminImportController::arrayWalk($info, array('AdminImportController', 'fillInfo'), $supplyOrder);

            if ((int)$supplyOrder->id
                && ($supplyOrder->exists((int)$supplyOrder->id) || $supplyOrder->exists($supplyOrder->reference))
            ) {
                $res = ($validateOnly || $supplyOrder->update());
            } else {
                $supplyOrder->force_id = (bool)$forceIds;
                $res = ($validateOnly || $supplyOrder->add());
            }

            // errors
            if (!$res) {
                $this->errors[] = $this->trans('Supply Order could not be saved (at line %line%).',
                    array('%line%' => $currentLine + 1),
                    'Admin.Advparameters.Notification'
                );
            }
        } else {
            $this->errors[] = $error;
        }
    }

    public function supplyOrdersDetailsImport(
        $offset = false,
        $limit = false,
        &$crossStepsVariables = false,
        $validateOnly = false
    ) {
        if (!is_array($crossStepsVariables)) {
            $crossStepsVariables = array();
        }

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

        $forceIds = Tools::getValue('forceIDs');

        // main loop, for each supply orders details to import
        $lineCount = 0;
        while ((!$limit || $lineCount < $limit)
            && $line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)
        ) {
            $lineCount++;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $info = AdminImportController::getMaskedRow($line);

            $this->supplyOrdersDetailsImportOne(
                $info,
                $products, // by ref
                $reset, // by ref
                $forceIds,
                $lineCount - 1,
                $validateOnly
            );
        }
        // closes
        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['products'] = $products;
            $crossStepsVariables['reset'] = $reset;
        }
        return $lineCount;
    }

    protected function supplyOrdersDetailsImportOne(
        $info,
        &$products,
        &$reset,
        $forceIds,
        $currentLine,
        $validateOnly = false
    ) {
        // sets default values if needed
        AdminImportController::setDefaultValues($info);

        // gets the supply order
        if (array_key_exists('supply_order_reference', $info)
            && pSQL($info['supply_order_reference'])
            && SupplyOrder::exists(pSQL($info['supply_order_reference']))
        ) {
            $supplyOrder = SupplyOrder::getSupplyOrderByReference(pSQL($info['supply_order_reference']));
        } else {
            $this->errors[] = $this->trans(
                'Supply Order (%ref%) could not be loaded (at line %line%).',
                array(
                    '%ref%'  => $info['supply_order_reference'],
                    '%line%' => $currentLine + 1,
                ),
                'Admin.Advparameters.Notification'
            );
        }

        if (empty($this->errors)) {
            // sets parameters
            $idProduct = (int)$info['id_product'];
            if (!$info['id_product_attribute']) {
                $info['id_product_attribute'] = 0;
            }
            $idProductAttribute = (int)$info['id_product_attribute'];
            $unitPriceTe = (float)$info['unit_price_te'];
            $quantityExpected = (int)$info['quantity_expected'];
            $discountRate = (float)$info['discount_rate'];
            $taxRate = (float)$info['tax_rate'];

            // checks if one product/attribute is there only once
            if (isset($products[$idProduct][$idProductAttribute])) {
                $this->errors[] =
                    $this->trans(
                        'Product/Attribute (%productId%/%attributeId%) cannot be added twice (at line %line%).',
                        array(
                            '%productId%'   => $idProduct,
                            '%attributeId%' => $idProductAttribute,
                            '%line%'        => $currentLine + 1,
                        ),
                        'Admin.Advparameters.Notification'
                    );
            } else {
                $products[$idProduct][$idProductAttribute] = $quantityExpected;
            }

            // checks parameters
            if (false === ($supplierReference = ProductSupplier::getProductSupplierReference($idProduct,
                $idProductAttribute,
                $supplyOrder->id_supplier
            ))) {
                $this->errors[] =
                    $this->trans(
                        'Product (%productId%/%attributeId%) is not available for this order (at line %line%).',
                        array(
                            '%productId%'   => $idProduct,
                            '%attributeId%' => $idProductAttribute,
                            '%line%'        => $currentLine + 1,
                        ),
                        'Admin.Advparameters.Notification'
                    );
            }
            if ($unitPriceTe < 0) {
                $this->errors[] = $this->trans(
                    'Unit Price (tax excl.) (%price%) is not valid (at line %line%).',
                    array(
                        '%price%' => $unitPriceTe,
                        '%line%'  => $currentLine + 1,
                    ),
                    'Admin.Advparameters.Notification'
                );
            }
            if ($quantityExpected < 0) {
                $this->errors[] = $this->trans(
                    'Quantity Expected (%qty%) is not valid (at line %line%).',
                    array(
                        '%qty%'  => $quantityExpected,
                        '%line%' => $currentLine + 1,
                    ),
                    'Admin.Advparameters.Notification'
                );
            }
            if ($discountRate < 0 || $discountRate > 100) {
                $this->errors[] = $this->trans(
                    'Discount rate (%rate%) is not valid (at line %line%). %reason%.',
                    array(
                        '%rate%'   => $discountRate,
                        '%line%'   => $currentLine + 1,
                        '%reason%' => $this->trans(
                            'Format: Between 0 and 100',
                            array(),
                            'Admin.Advparameters.Notification'
                        ),
                    ),
                    'Admin.Advparameters.Notification'
                );
            }
            if ($taxRate < 0 || $taxRate > 100) {
                $this->errors[] = $this->trans(
                    'Tax rate (%rate%) is not valid (at line %line%).',
                    array(
                        '%rate%' => $taxRate,
                        '%line%' => $currentLine + 1,
                        $this->trans('Format: Between 0 and 100', array(), 'Admin.Advparameters.Notification'),
                    ),
                    'Admin.Advparameters.Notification'
                );
            }

            // if no errors, sets supply order details
            if (empty($this->errors)) {
                // resets order if needed
                if (!$validateOnly && $reset) {
                    $supplyOrder->resetProducts();
                    $reset = false;
                }

                // creates new product
                $supplyOrderDetail = new SupplyOrderDetail();
                AdminImportController::arrayWalk(
                    $info,
                    array('AdminImportController', 'fillInfo'),
                    $supplyOrderDetail
                );

                // sets parameters
                $supplyOrderDetail->id_supply_order = $supplyOrder->id;
                $currency = new Currency($supplyOrder->id_ref_currency);
                $supplyOrderDetail->id_currency = $currency->id;
                $supplyOrderDetail->exchange_rate = $currency->conversion_rate;
                $supplyOrderDetail->supplier_reference = $supplierReference;
                $supplyOrderDetail->name = Product::getProductName(
                    $idProduct,
                    $idProductAttribute,
                    $supplyOrder->id_lang
                );

                // gets ean13 / ref / upc
                $query = new DbQuery();
                $query->select('
                    IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
                    IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
                    IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc
                ');
                $query->from('product', 'p');
                $query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product AND id_product_attribute = '
                    . (int)$idProductAttribute);
                $query->where('p.id_product = '.(int)$idProduct);
                $query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                $productData = $res['0'];

                $supplyOrderDetail->reference = $productData['reference'];
                $supplyOrderDetail->ean13 = $productData['ean13'];
                $supplyOrderDetail->upc = $productData['upc'];
                $supplyOrderDetail->force_id = (bool)$forceIds;
                if (!$validateOnly) {
                    $supplyOrderDetail->add();
                    $supplyOrder->update();
                }
                unset($supplyOrderDetail);
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
            $destFile = AdminImportController::getPath(strval(preg_replace('/\.{2,}/', '.', $filename)));
        } else {
            $csvFolder   = AdminImportController::getPath();
            $excelFolder = $csvFolder . 'csvfromexcel/';
            $info        = pathinfo($filename);
            $csvName     = basename($filename, '.' . $info['extension']) . '.csv';
            $destFile    = $excelFolder . $csvName;

            if (!is_dir($excelFolder)) {
                mkdir($excelFolder);
            }

            if (!is_file($destFile)) {
                $excelReader = PHPExcel_IOFactory::createReaderForFile($csvFolder.$filename);
                $excelReader->setReadDataOnly(true);
                $excelFile = $excelReader->load($csvFolder.$filename);

                $csvWriter = PHPExcel_IOFactory::createWriter($excelFile, 'CSV');

                $csvWriter->setSheetIndex(0);
                $csvWriter->setDelimiter(';');
                $csvWriter->save($destFile);
            }
        }

        return $destFile;
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
                //check if table exists before truncate
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'favorite_product\' '))) {
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
                Db::getInstance()->execute('DELETE FROM `' ._DB_PREFIX_.'stock_available`'
                    . ' WHERE id_product_attribute != 0');
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
            $this->errors[] = $this->trans(
                'This functionality has been disabled.',
                array(),
                'Admin.Notifications.Error'
            );

            return false;
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
                $basename = basename($filename);
                if (Tools::getValue('delete') && file_exists($file)) {
                    @unlink($file);
                } elseif (file_exists($file)) {
                    $basename = explode('.', $basename);
                    $basename = strtolower($basename[count($basename) - 1]);
                    $mimeTypes = array('csv' => 'text/csv');

                    if (isset($mimeTypes[$basename])) {
                        $mimeType = $mimeTypes[$basename];
                    } else {
                        $mimeType = 'application/octet-stream';
                    }

                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: ' . $mimeType);
                    header('Content-Length: ' . filesize($file));
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
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

    public function importByGroups(
        $offset = false,
        $limit = false,
        &$results = null,
        $validateOnly = false,
        $moreStep = 0
    ) {
        // Check if the CSV file exist
        if (Tools::getValue('csv')) {
            $shopIsFeatureActive = Shop::isFeatureActive();
            // If i am a superadmin, i can truncate table (ONLY IF OFFSET == 0 or false and NOT FOR VALIDATION MODE!)
            if (!$offset
                && !$moreStep
                && !$validateOnly
                &&(($shopIsFeatureActive && $this->context->employee->isSuperAdmin()) || !$shopIsFeatureActive)
                && Tools::getValue('truncate')
            ) {
                $this->truncateTables((int)Tools::getValue('entity'));
            }
            $importType = false;
            $doneCount = 0;
            /* Sometime, import will use registers to memorize data across all elements to import (for trees, or else).
             * Since import is splitted in multiple ajax calls, we must keep these data across all steps of the full
             * import.
             */
            $crossStepsVariables = array();
            if ($crossStepsVars = Tools::getValue('crossStepsVars')) {
                $crossStepsVars = json_decode($crossStepsVars, true);
                if (sizeof($crossStepsVars) > 0) {
                    $crossStepsVariables = $crossStepsVars;
                }
            }
            Db::getInstance()->disableCache();
            switch ((int)Tools::getValue('entity')) {
                case $this->entities[$importType = $this->trans('Categories', array(), 'Admin.Global')]:
                    $doneCount += $this->categoryImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    $this->clearSmartyCache();
                    break;
                case $this->entities[$importType = $this->trans('Products', array(), 'Admin.Global')]:
                    if (!defined('PS_MASS_PRODUCT_CREATION')) {
                        define('PS_MASS_PRODUCT_CREATION', true);
                    }
                    $moreStepLabels = array($this->trans(
                        'Linking Accessories...',
                        array(),
                        'Admin.Advparameters.Notification'
                    ));
                    $doneCount += $this->productImport($offset, $limit, $crossStepsVariables, $validateOnly, $moreStep);
                    $this->clearSmartyCache();
                    break;
                case $this->entities[$importType = $this->trans('Customers', array(), 'Admin.Global')]:
                    $doneCount += $this->customerImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[$importType = $this->trans('Addresses', array(), 'Admin.Global')]:
                    $doneCount += $this->addressImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[$importType = $this->trans('Combinations', array(), 'Admin.Global')]:
                    $doneCount += $this->attributeImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    $this->clearSmartyCache();
                    break;
                case $this->entities[$importType = $this->trans('Brands', array(), 'Admin.Global')]:
                    $doneCount += $this->manufacturerImport($offset, $limit, $validateOnly);
                    $this->clearSmartyCache();
                    break;
                case $this->entities[$importType = $this->trans('Suppliers', array(), 'Admin.Global')]:
                    $doneCount += $this->supplierImport($offset, $limit, $validateOnly);
                    $this->clearSmartyCache();
                    break;
                case $this->entities[$importType = $this->trans('Alias', array(), 'Admin.Shopparameters.Feature')]:
                    $doneCount += $this->aliasImport($offset, $limit, $validateOnly);
                    break;
                case $this->entities[
                    $importType = $this->trans('Store contacts', array(), 'Admin.Advparameters.Feature')
                ]:
                    $doneCount += $this->storeContactImport($offset, $limit, $validateOnly);
                    $this->clearSmartyCache();
                    break;
            }

            // @since 1.5.0
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                switch ((int)Tools::getValue('entity')) {
                    case $this->entities[
                        $importType = $this->trans('Supply Orders', array(), 'Admin.Advparameters.Feature')
                    ]:
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $doneCount += $this->supplyOrdersImport($offset, $limit, $validateOnly);
                        }
                        break;
                    case $this->entities[
                        $importType = $this->trans('Supply Order Details', array(), 'Admin.Advparameters.Feature')
                    ]:
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $doneCount += $this->supplyOrdersDetailsImport(
                                $offset,
                                $limit,
                                $crossStepsVariables,
                                $validateOnly
                            );
                        }
                        break;
                }
            }

            if ($results !== null) {
                $results['isFinished'] = ($doneCount < $limit);
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

            if ($importType !== false) {
                $logMessage = $this->trans(
                    '%type% import',
                    array('%type%' => $importType),
                    'Admin.Advparameters.Notification'
                );
                if ($offset !== false && $limit !== false) {
                    $logMessage .= ' ' . $this->trans(
                        '(from %offset% to %limit%)',
                        array(
                            '%offset%' => $offset,
                            '%limit%' => $limit,
                        ),
                        'Admin.Advparameters.Notification'
                    );
                }
                if (Tools::getValue('truncate')) {
                    $logMessage .= ' ' . $this->trans('with truncate', array(), 'Admin.Advparameters.Notification');
                }
                PrestaShopLogger::addLog(
                    $logMessage,
                    1,
                    null,
                    $importType,
                    null,
                    true,
                    (int)$this->context->employee->id
                );
            }

            Db::getInstance()->enableCache();
        } else {
            $this->errors[] = $this->trans(
                'To proceed, please upload a file first.',
                array(),
                'Admin.Advparameters.Notification'
            );
        }
    }

    public static function setLocale()
    {
        $isoLang  = trim(Tools::getValue('iso_lang'));
        setlocale(LC_COLLATE, strtolower($isoLang).'_'.strtoupper($isoLang).'.UTF-8');
        setlocale(LC_CTYPE, strtolower($isoLang).'_'.strtoupper($isoLang).'.UTF-8');
    }

    protected function addProductWarning($productName, $productId = null, $message = '')
    {
        $this->warnings[] = $productName.(isset($productId) ? ' (ID '.$productId.')' : '').' '
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
            $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE '
                . '`id_import_match` = ' . (int)Tools::getValue('idImportMatchs'), true, false);
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

        if (!$validateOnly
            && (bool)$results['isFinished']
            && !isset($results['oneMoreStep'])
            && (bool)Tools::getValue('sendemail')
        ) {
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
                    $this->trans('Import complete',
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
                    false, // do not die in failed! Warn only, it's not an import error because import finished in fact.
                    (int)$this->context->shop->id
                );
                if (!$mailSuccess) {
                    $results['warnings'][] = $this->trans(
                        'The confirmation email couldn\'t be sent, but the import is successful. Yay!',
                        array(),
                        'Admin.Advparameters.Notification'
                    );
                }
            } catch (\Exception $e) {
                $results['warnings'][] = $this->trans(
                    'The confirmation email couldn\'t be sent, but the import is successful. Yay!',
                    array(),
                    'Admin.Advparameters.Notification'
                );
            }
        }

        die(json_encode($results));
    }

    public function initModal()
    {
        parent::initModal();
        $modalContent = $this->context->smarty->fetch('controllers/import/modal_import_progress.tpl');
        $this->modals[] = array(
             'modal_id' => 'importProgress',
             'modal_class' => 'modal-md',
             'modal_title' => $this->trans('Importing your data...', array(), 'Admin.Advparameters.Notification'),
             'modal_content' => $modalContent,
         );
    }
}
