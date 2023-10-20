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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

@ini_set('max_execution_time', '0');
/* No max line limit since the lines can be more than 4096. Performance impact is not significant. */
define('MAX_LINE_SIZE', 0);

/* Used for validatefields diying without user friendly error or not */
define('UNFRIENDLY_ERROR', false);

/* this value set the number of columns visible on each page */
define('MAX_COLUMNS', 6);

/* correct Mac error on eof */
@ini_set('auto_detect_line_endings', '1');

class AdminImportControllerCore extends AdminController
{
    public static $column_mask;

    public $entities = [];

    public $available_fields = [];

    /** @var array|string[] */
    public $required_fields = [];

    public static $default_values = [];

    public static $validators = [
        'active' => ['AdminImportController', 'getBoolean'],
        'tax_rate' => ['AdminImportController', 'getPrice'],
        /* Tax excluded */
        'price_tex' => ['AdminImportController', 'getPrice'],
        /* Tax included */
        'price_tin' => ['AdminImportController', 'getPrice'],
        'reduction_price' => ['AdminImportController', 'getPrice'],
        'reduction_percent' => ['AdminImportController', 'getPrice'],
        'wholesale_price' => ['AdminImportController', 'getPrice'],
        'ecotax' => ['AdminImportController', 'getPrice'],
        'name' => ['AdminImportController', 'createMultiLangField'],
        'description' => ['AdminImportController', 'createMultiLangField'],
        'description_short' => ['AdminImportController', 'createMultiLangField'],
        'meta_title' => ['AdminImportController', 'createMultiLangField'],
        'meta_keywords' => ['AdminImportController', 'createMultiLangField'],
        'meta_description' => ['AdminImportController', 'createMultiLangField'],
        'link_rewrite' => ['AdminImportController', 'createMultiLangField'],
        'available_now' => ['AdminImportController', 'createMultiLangField'],
        'available_later' => ['AdminImportController', 'createMultiLangField'],
        'category' => ['AdminImportController', 'split'],
        'online_only' => ['AdminImportController', 'getBoolean'],
        'accessories' => ['AdminImportController', 'split'],
        'image_alt' => ['AdminImportController', 'split'],
        'delivery_in_stock' => ['AdminImportController', 'createMultiLangField'],
        'delivery_out_stock' => ['AdminImportController', 'createMultiLangField'],
    ];

    public $separator;
    public $convert;
    public $multiple_value_separator;

    /**
     * This flag shows if import was executed in current request.
     * Used for symfony migration purposes.
     *
     * @var bool
     */
    private $importExecuted = false;

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        $this->entities = [
            $this->trans('Categories', [], 'Admin.Global'),
            $this->trans('Products', [], 'Admin.Global'),
            $this->trans('Combinations', [], 'Admin.Global'),
            $this->trans('Customers', [], 'Admin.Global'),
            $this->trans('Addresses', [], 'Admin.Global'),
            $this->trans('Brands', [], 'Admin.Global'),
            $this->trans('Suppliers', [], 'Admin.Global'),
            $this->trans('Alias', [], 'Admin.Shopparameters.Feature'),
            $this->trans('Store contacts', [], 'Admin.Advparameters.Feature'),
        ];

        // @since 1.5.0
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->entities = array_merge(
                $this->entities,
                [
                    $this->trans('Supply Orders', [], 'Admin.Advparameters.Feature'),
                    $this->trans('Supply Order Details', [], 'Admin.Advparameters.Feature'),
                ]
            );
        }

        $this->entities = array_flip($this->entities);

        switch ((int) Tools::getValue('entity')) {
            case $this->entities[$this->trans('Combinations', [], 'Admin.Global')]:
                $this->required_fields = [
                    'group',
                    'attribute',
                ];

                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id_product' => ['label' => $this->trans('Product ID', [], 'Admin.Advparameters.Feature')],
                    'product_reference' => ['label' => $this->trans('Product Reference', [], 'Admin.Advparameters.Feature')],
                    'group' => [
                        'label' => $this->trans('Attribute (Name:Type:Position)', [], 'Admin.Advparameters.Feature') . '*',
                    ],
                    'attribute' => [
                        'label' => $this->trans('Value (Value:Position)', [], 'Admin.Advparameters.Feature') . '*',
                    ],
                    'supplier_reference' => ['label' => $this->trans('Supplier reference', [], 'Admin.Advparameters.Feature')],
                    'reference' => ['label' => $this->trans('Reference', [], 'Admin.Global')],
                    'ean13' => ['label' => $this->trans('EAN-13', [], 'Admin.Advparameters.Feature')],
                    'upc' => ['label' => $this->trans('UPC', [], 'Admin.Advparameters.Feature')],
                    'mpn' => ['label' => $this->trans('MPN', [], 'Admin.Catalog.Feature')],
                    'wholesale_price' => ['label' => $this->trans('Cost price', [], 'Admin.Catalog.Feature')],
                    'price' => ['label' => $this->trans('Impact on price', [], 'Admin.Catalog.Feature')],
                    'ecotax' => ['label' => $this->trans('Ecotax', [], 'Admin.Catalog.Feature')],
                    'quantity' => ['label' => $this->trans('Quantity', [], 'Admin.Global')],
                    'minimal_quantity' => ['label' => $this->trans('Minimal quantity', [], 'Admin.Advparameters.Feature')],
                    'low_stock_threshold' => ['label' => $this->trans('Low stock level', [], 'Admin.Catalog.Feature')],
                    'low_stock_alert' => ['label' => $this->trans('Receive a low stock alert by email', [], 'Admin.Catalog.Feature')],
                    'weight' => ['label' => $this->trans('Impact on weight', [], 'Admin.Catalog.Feature')],
                    'default_on' => ['label' => $this->trans('Default (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'available_date' => ['label' => $this->trans('Combination availability date', [], 'Admin.Advparameters.Feature')],
                    'image_position' => [
                        'label' => $this->trans('Choose among product images by position (1,2,3...)', [], 'Admin.Advparameters.Feature'),
                    ],
                    'image_url' => ['label' => $this->trans('Image URLs (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'image_alt' => ['label' => $this->trans('Image alt texts (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'shop' => [
                        'label' => $this->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                    'advanced_stock_management' => [
                        'label' => $this->trans('Advanced Stock Management', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Enable advanced stock management on product (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Help'),
                    ],
                    'depends_on_stock' => [
                        'label' => $this->trans('Depends on stock', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('0 = Use quantity set in product, 1 = Use quantity from warehouse.', [], 'Admin.Advparameters.Help'),
                    ],
                    'warehouse' => [
                        'label' => $this->trans('Warehouse', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('ID of the warehouse to set as storage.', [], 'Admin.Advparameters.Help'),
                    ],
                ];

                self::$default_values = [
                    'reference' => '',
                    'supplier_reference' => '',
                    'ean13' => '',
                    'upc' => '',
                    'mpn' => '',
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
                    'available_date' => date('Y-m-d'),
                ];

                break;

            case $this->entities[$this->trans('Categories', [], 'Admin.Global')]:
                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'active' => ['label' => $this->trans('Active (0/1)', [], 'Admin.Advparameters.Feature')],
                    'name' => ['label' => $this->trans('Name', [], 'Admin.Global')],
                    'parent' => ['label' => $this->trans('Parent category', [], 'Admin.Catalog.Feature')],
                    'is_root_category' => [
                        'label' => $this->trans('Root category (0/1)', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('A category root is where a category tree can begin. This is used with multistore.', [], 'Admin.Advparameters.Help'),
                    ],
                    'description' => ['label' => $this->trans('Description', [], 'Admin.Global')],
                    'meta_title' => ['label' => $this->trans('Meta title', [], 'Admin.Global')],
                    'meta_keywords' => ['label' => $this->trans('Meta keywords', [], 'Admin.Global')],
                    'meta_description' => ['label' => $this->trans('Meta description', [], 'Admin.Global')],
                    'link_rewrite' => ['label' => $this->trans('Rewritten URL', [], 'Admin.Shopparameters.Feature')],
                    'image' => ['label' => $this->trans('Image URL', [], 'Admin.Advparameters.Feature')],
                    'shop' => [
                        'label' => $this->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                ];

                self::$default_values = [
                    'active' => '1',
                    'parent' => Configuration::get('PS_HOME_CATEGORY'),
                    'link_rewrite' => '',
                ];

                break;

            case $this->entities[$this->trans('Products', [], 'Admin.Global')]:
                self::$validators['image'] = [
                    'AdminImportController',
                    'split',
                ];

                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'active' => ['label' => $this->trans('Active (0/1)', [], 'Admin.Advparameters.Feature')],
                    'name' => ['label' => $this->trans('Name', [], 'Admin.Global')],
                    'category' => ['label' => $this->trans('Categories (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'price_tex' => ['label' => $this->trans('Price tax excluded', [], 'Admin.Advparameters.Feature')],
                    'price_tin' => ['label' => $this->trans('Price tax included', [], 'Admin.Advparameters.Feature')],
                    'id_tax_rules_group' => ['label' => $this->trans('Tax rule ID', [], 'Admin.Advparameters.Feature')],
                    'wholesale_price' => ['label' => $this->trans('Cost price', [], 'Admin.Catalog.Feature')],
                    'on_sale' => ['label' => $this->trans('On sale (0/1)', [], 'Admin.Advparameters.Feature')],
                    'reduction_price' => ['label' => $this->trans('Discount amount', [], 'Admin.Advparameters.Feature')],
                    'reduction_percent' => ['label' => $this->trans('Discount percent', [], 'Admin.Advparameters.Feature')],
                    'reduction_from' => ['label' => $this->trans('Discount from (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')],
                    'reduction_to' => ['label' => $this->trans('Discount to (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')],
                    'reference' => ['label' => $this->trans('Reference #', [], 'Admin.Advparameters.Feature')],
                    'supplier_reference' => ['label' => $this->trans('Supplier reference #', [], 'Admin.Advparameters.Feature')],
                    'supplier' => ['label' => $this->trans('Supplier', [], 'Admin.Global')],
                    'manufacturer' => ['label' => $this->trans('Brand', [], 'Admin.Global')],
                    'ean13' => ['label' => $this->trans('EAN-13', [], 'Admin.Advparameters.Feature')],
                    'upc' => ['label' => $this->trans('UPC', [], 'Admin.Advparameters.Feature')],
                    'mpn' => ['label' => $this->trans('MPN', [], 'Admin.Catalog.Feature')],
                    'ecotax' => ['label' => $this->trans('Ecotax', [], 'Admin.Catalog.Feature')],
                    'width' => ['label' => $this->trans('Width', [], 'Admin.Global')],
                    'height' => ['label' => $this->trans('Height', [], 'Admin.Global')],
                    'depth' => ['label' => $this->trans('Depth', [], 'Admin.Global')],
                    'weight' => ['label' => $this->trans('Weight', [], 'Admin.Global')],
                    'delivery_in_stock' => [
                        'label' => $this->trans(
                            'Delivery time of in-stock products:',
                            [],
                            'Admin.Catalog.Feature'
                        ),
                    ],
                    'delivery_out_stock' => [
                        'label' => $this->trans(
                            'Delivery time of out-of-stock products with allowed orders:',
                            [],
                            'Admin.Advparameters.Feature'
                        ),
                    ],
                    'quantity' => ['label' => $this->trans('Quantity', [], 'Admin.Global')],
                    'minimal_quantity' => ['label' => $this->trans('Minimal quantity', [], 'Admin.Advparameters.Feature')],
                    'low_stock_threshold' => ['label' => $this->trans('Low stock level', [], 'Admin.Catalog.Feature')],
                    'low_stock_alert' => ['label' => $this->trans('Receive a low stock alert by email', [], 'Admin.Catalog.Feature')],
                    'visibility' => ['label' => $this->trans('Visibility', [], 'Admin.Catalog.Feature')],
                    'additional_shipping_cost' => ['label' => $this->trans('Additional shipping cost', [], 'Admin.Advparameters.Feature')],
                    'unity' => ['label' => $this->trans('Unit for the price per unit', [], 'Admin.Advparameters.Feature')],
                    'unit_price' => ['label' => $this->trans('Price per unit', [], 'Admin.Advparameters.Feature')],
                    'description_short' => ['label' => $this->trans('Summary', [], 'Admin.Catalog.Feature')],
                    'description' => ['label' => $this->trans('Description', [], 'Admin.Global')],
                    'tags' => ['label' => $this->trans('Tags (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'meta_title' => ['label' => $this->trans('Meta title', [], 'Admin.Global')],
                    'meta_keywords' => ['label' => $this->trans('Meta keywords', [], 'Admin.Global')],
                    'meta_description' => ['label' => $this->trans('Meta description', [], 'Admin.Global')],
                    'link_rewrite' => ['label' => $this->trans('Rewritten URL', [], 'Admin.Advparameters.Feature')],
                    'available_now' => ['label' => $this->trans('Label when in stock', [], 'Admin.Catalog.Feature')],
                    'available_later' => ['label' => $this->trans('Label when backorder allowed', [], 'Admin.Advparameters.Feature')],
                    'available_for_order' => ['label' => $this->trans('Available for order (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'available_date' => ['label' => $this->trans('Product availability date', [], 'Admin.Advparameters.Feature')],
                    'date_add' => ['label' => $this->trans('Product creation date', [], 'Admin.Advparameters.Feature')],
                    'show_price' => ['label' => $this->trans('Show price (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'image' => ['label' => $this->trans('Image URLs (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'image_alt' => ['label' => $this->trans('Image alt texts (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'delete_existing_images' => [
                        'label' => $this->trans('Delete existing images (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature'),
                    ],
                    'features' => ['label' => $this->trans('Feature (Name:Value:Position:Customized)', [], 'Admin.Advparameters.Feature')],
                    'online_only' => ['label' => $this->trans('Available online only (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'condition' => ['label' => $this->trans('Condition', [], 'Admin.Catalog.Feature')],
                    'customizable' => ['label' => $this->trans('Customizable (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'uploadable_files' => ['label' => $this->trans('Uploadable files (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'text_fields' => ['label' => $this->trans('Text fields (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'out_of_stock' => ['label' => $this->trans('Action when out of stock', [], 'Admin.Advparameters.Feature')],
                    'is_virtual' => ['label' => $this->trans('Virtual product (0 = No, 1 = Yes)', [], 'Admin.Advparameters.Feature')],
                    'file_url' => ['label' => $this->trans('File URL', [], 'Admin.Advparameters.Feature')],
                    'nb_downloadable' => [
                        'label' => $this->trans('Number of allowed downloads', [], 'Admin.Catalog.Feature'),
                        'help' => $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', [], 'Admin.Catalog.Help'),
                    ],
                    'date_expiration' => ['label' => $this->trans('Expiration date (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')],
                    'nb_days_accessible' => [
                        'label' => $this->trans('Number of days', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Number of days this file can be accessed by customers. Set to zero for unlimited access.', [], 'Admin.Catalog.Help'),
                    ],
                    'shop' => [
                        'label' => $this->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                    'advanced_stock_management' => [
                        'label' => $this->trans('Advanced Stock Management', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Enable advanced stock management on product (0 = No, 1 = Yes).', [], 'Admin.Advparameters.Help'),
                    ],
                    'depends_on_stock' => [
                        'label' => $this->trans('Depends on stock', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('0 = Use quantity set in product, 1 = Use quantity from warehouse.', [], 'Admin.Advparameters.Help'),
                    ],
                    'warehouse' => [
                        'label' => $this->trans('Warehouse', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('ID of the warehouse to set as storage.', [], 'Admin.Advparameters.Help'),
                    ],
                    'accessories' => ['label' => $this->trans('Accessories (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                ];

                self::$default_values = [
                    'id_category' => [(int) Configuration::get('PS_HOME_CATEGORY')],
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
                    'description_short' => [(int) Configuration::get('PS_LANG_DEFAULT') => ''],
                    'link_rewrite' => [(int) Configuration::get('PS_LANG_DEFAULT') => ''],
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
                ];

                break;

            case $this->entities[$this->trans('Customers', [], 'Admin.Global')]:
                //Overwrite required_fields AS only email is required whereas other entities
                $this->required_fields = ['email', 'passwd', 'lastname', 'firstname'];

                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'active' => ['label' => $this->trans('Active  (0/1)', [], 'Admin.Advparameters.Feature')],
                    'id_gender' => ['label' => $this->trans('Titles ID (Mr = 1, Ms = 2, else 0)', [], 'Admin.Advparameters.Feature')],
                    'email' => ['label' => $this->trans('Email', [], 'Admin.Global') . '*'],
                    'passwd' => ['label' => $this->trans('Password', [], 'Admin.Global') . '*'],
                    'birthday' => ['label' => $this->trans('Birth date (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')],
                    'lastname' => ['label' => $this->trans('Last name', [], 'Admin.Global') . '*'],
                    'firstname' => ['label' => $this->trans('First name', [], 'Admin.Global') . '*'],
                    'newsletter' => ['label' => $this->trans('Newsletter (0/1)', [], 'Admin.Advparameters.Feature')],
                    'optin' => ['label' => $this->trans('Partner offers (0/1)', [], 'Admin.Advparameters.Feature')],
                    'date_add' => ['label' => $this->trans('Registration date (yyyy-mm-dd)', [], 'Admin.Advparameters.Feature')],
                    'group' => ['label' => $this->trans('Groups (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'id_default_group' => ['label' => $this->trans('Default group ID', [], 'Admin.Advparameters.Feature')],
                    'id_shop' => [
                        'label' => $this->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                ];

                self::$default_values = [
                    'active' => '1',
                    'date_upd' => date('Y-m-d H:i:s'),
                    'id_shop' => Configuration::get('PS_SHOP_DEFAULT'),
                ];

                break;

            case $this->entities[$this->trans('Addresses', [], 'Admin.Global')]:
                //Overwrite required_fields
                $this->required_fields = [
                    'alias',
                    'lastname',
                    'firstname',
                    'address1',
                    'postcode',
                    'country',
                    'customer_email',
                    'city',
                ];

                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'alias' => ['label' => $this->trans('Alias', [], 'Admin.Shopparameters.Feature') . '*'],
                    'active' => ['label' => $this->trans('Active  (0/1)', [], 'Admin.Advparameters.Feature')],
                    'customer_email' => ['label' => $this->trans('Customer email', [], 'Admin.Advparameters.Feature') . '*'],
                    'id_customer' => ['label' => $this->trans('Customer ID', [], 'Admin.Advparameters.Feature')],
                    'manufacturer' => ['label' => $this->trans('Brand', [], 'Admin.Global')],
                    'supplier' => ['label' => $this->trans('Supplier', [], 'Admin.Global')],
                    'company' => ['label' => $this->trans('Company', [], 'Admin.Global')],
                    'lastname' => ['label' => $this->trans('Last name', [], 'Admin.Global') . '*'],
                    'firstname' => ['label' => $this->trans('First name', [], 'Admin.Global') . '*'],
                    'address1' => ['label' => $this->trans('Address', [], 'Admin.Global') . '*'],
                    'address2' => ['label' => $this->trans('Address (2)', [], 'Admin.Global')],
                    'postcode' => ['label' => $this->trans('Zip/Postal code', [], 'Admin.Global') . '*'],
                    'city' => ['label' => $this->trans('City', [], 'Admin.Global') . '*'],
                    'country' => ['label' => $this->trans('Country', [], 'Admin.Global') . '*'],
                    'state' => ['label' => $this->trans('State', [], 'Admin.Global')],
                    'other' => ['label' => $this->trans('Other', [], 'Admin.Global')],
                    'phone' => ['label' => $this->trans('Phone', [], 'Admin.Global')],
                    'phone_mobile' => ['label' => $this->trans('Mobile phone', [], 'Admin.Global')],
                    'vat_number' => ['label' => $this->trans('VAT number', [], 'Admin.Orderscustomers.Feature')],
                    'dni' => ['label' => $this->trans('Identification number', [], 'Admin.Orderscustomers.Feature')],
                ];

                self::$default_values = [
                    'alias' => 'Alias',
                    'postcode' => 'X',
                ];

                break;
            case $this->entities[$this->trans('Brands', [], 'Admin.Global')]:
            case $this->entities[$this->trans('Suppliers', [], 'Admin.Global')]:
                //Overwrite validators AS name is not MultiLangField
                self::$validators = [
                    'description' => ['AdminImportController', 'createMultiLangField'],
                    'short_description' => ['AdminImportController', 'createMultiLangField'],
                    'meta_title' => ['AdminImportController', 'createMultiLangField'],
                    'meta_keywords' => ['AdminImportController', 'createMultiLangField'],
                    'meta_description' => ['AdminImportController', 'createMultiLangField'],
                ];

                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'active' => ['label' => $this->trans('Active (0/1)', [], 'Admin.Advparameters.Feature')],
                    'name' => ['label' => $this->trans('Name', [], 'Admin.Global')],
                    'description' => ['label' => $this->trans('Description', [], 'Admin.Global')],
                    'short_description' => ['label' => $this->trans('Short description', [], 'Admin.Catalog.Feature')],
                    'meta_title' => ['label' => $this->trans('Meta title', [], 'Admin.Global')],
                    'meta_keywords' => ['label' => $this->trans('Meta keywords', [], 'Admin.Global')],
                    'meta_description' => ['label' => $this->trans('Meta description', [], 'Admin.Global')],
                    'image' => ['label' => $this->trans('Image URL', [], 'Admin.Advparameters.Feature')],
                    'shop' => [
                        'label' => $this->trans('ID / Name of group shop', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                ];

                if ($this->entities[$this->trans('Suppliers', [], 'Admin.Global')]) {
                    unset($this->available_fields['short_description'], self::$validators['short_description']);
                }

                self::$default_values = [
                    'shop' => Shop::getGroupFromShop((int) Configuration::get('PS_SHOP_DEFAULT')),
                ];

                break;
            case $this->entities[$this->trans('Alias', [], 'Admin.Shopparameters.Feature')]:
                //Overwrite required_fields
                $this->required_fields = [
                    'alias',
                    'search',
                ];
                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'alias' => ['label' => $this->trans('Alias', [], 'Admin.Shopparameters.Feature') . '*'],
                    'search' => ['label' => $this->trans('Search', [], 'Admin.Shopparameters.Feature') . '*'],
                    'active' => ['label' => $this->trans('Active', [], 'Admin.Global')],
                ];
                self::$default_values = [
                    'active' => '1',
                ];

                break;
            case $this->entities[$this->trans('Store contacts', [], 'Admin.Advparameters.Feature')]:
                self::$validators['hours'] = ['AdminImportController', 'split'];
                self::$validators['address1'] = ['AdminImportController', 'createMultiLangField'];
                self::$validators['address2'] = ['AdminImportController', 'createMultiLangField'];

                $this->required_fields = [
                    'address1',
                    'city',
                    'country',
                    'latitude',
                    'longitude',
                ];
                $this->available_fields = [
                    'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                    'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                    'active' => ['label' => $this->trans('Active (0/1)', [], 'Admin.Advparameters.Feature')],
                    'name' => ['label' => $this->trans('Name', [], 'Admin.Global')],
                    'address1' => ['label' => $this->trans('Address', [], 'Admin.Global') . '*'],
                    'address2' => ['label' => $this->trans('Address (2)', [], 'Admin.Advparameters.Feature')],
                    'postcode' => ['label' => $this->trans('Zip/Postal code', [], 'Admin.Global')],
                    'state' => ['label' => $this->trans('State', [], 'Admin.Global')],
                    'city' => ['label' => $this->trans('City', [], 'Admin.Global') . '*'],
                    'country' => ['label' => $this->trans('Country', [], 'Admin.Global') . '*'],
                    'latitude' => ['label' => $this->trans('Latitude', [], 'Admin.Advparameters.Feature') . '*'],
                    'longitude' => ['label' => $this->trans('Longitude', [], 'Admin.Advparameters.Feature') . '*'],
                    'phone' => ['label' => $this->trans('Phone', [], 'Admin.Global')],
                    'fax' => ['label' => $this->trans('Fax', [], 'Admin.Global')],
                    'email' => ['label' => $this->trans('Email address', [], 'Admin.Global')],
                    'note' => ['label' => $this->trans('Note', [], 'Admin.Advparameters.Feature')],
                    'hours' => ['label' => $this->trans('Hours (x,y,z...)', [], 'Admin.Advparameters.Feature')],
                    'image' => ['label' => $this->trans('Image URL', [], 'Admin.Advparameters.Feature')],
                    'shop' => [
                        'label' => $this->trans('ID / Name of the store', [], 'Admin.Advparameters.Feature'),
                        'help' => $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', [], 'Admin.Advparameters.Help'),
                    ],
                ];
                self::$default_values = [
                    'active' => '1',
                ];

                break;
        }

        // @since 1.5.0
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            switch ((int) Tools::getValue('entity')) {
                case $this->entities[$this->trans('Supply Orders', [], 'Admin.Advparameters.Feature')]:
                    // required fields
                    $this->required_fields = [
                        'id_supplier',
                        'id_warehouse',
                        'reference',
                        'date_delivery_expected',
                    ];
                    // available fields
                    $this->available_fields = [
                        'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                        'id' => ['label' => $this->trans('ID', [], 'Admin.Global')],
                        'id_supplier' => ['label' => $this->trans('Supplier ID *', [], 'Admin.Advparameters.Feature')],
                        'id_lang' => ['label' => $this->trans('Lang ID', [], 'Admin.Advparameters.Feature')],
                        'id_warehouse' => ['label' => $this->trans('Warehouse ID *', [], 'Admin.Advparameters.Feature')],
                        'id_currency' => ['label' => $this->trans('Currency ID *', [], 'Admin.Advparameters.Feature')],
                        'reference' => ['label' => $this->trans('Supply Order Reference *', [], 'Admin.Advparameters.Feature')],
                        'date_delivery_expected' => ['label' => $this->trans('Delivery Date (Y-M-D)*', [], 'Admin.Advparameters.Feature')],
                        'discount_rate' => ['label' => $this->trans('Discount rate', [], 'Admin.Advparameters.Feature')],
                        'is_template' => ['label' => $this->trans('Template', [], 'Admin.Advparameters.Feature')],
                    ];
                    // default values
                    self::$default_values = [
                        'id_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
                        'id_currency' => Currency::getDefaultCurrency()->id,
                        'discount_rate' => '0',
                        'is_template' => '0',
                    ];

                    break;
                case $this->entities[$this->trans('Supply Order Details', [], 'Admin.Advparameters.Feature')]:
                    // required fields
                    $this->required_fields = [
                        'supply_order_reference',
                        'id_product',
                        'unit_price_te',
                        'quantity_expected',
                    ];
                    // available fields
                    $this->available_fields = [
                        'no' => ['label' => $this->trans('Ignore this column', [], 'Admin.Advparameters.Feature')],
                        'supply_order_reference' => ['label' => $this->trans('Supply Order Reference *', [], 'Admin.Advparameters.Feature')],
                        'id_product' => ['label' => $this->trans('Product ID *', [], 'Admin.Advparameters.Feature')],
                        'id_product_attribute' => ['label' => $this->trans('Product Attribute ID', [], 'Admin.Advparameters.Feature')],
                        'unit_price_te' => ['label' => $this->trans('Unit Price (tax excl.)*', [], 'Admin.Advparameters.Feature')],
                        'quantity_expected' => ['label' => $this->trans('Quantity Expected *', [], 'Admin.Advparameters.Feature')],
                        'discount_rate' => ['label' => $this->trans('Discount Rate', [], 'Admin.Advparameters.Feature')],
                        'tax_rate' => ['label' => $this->trans('Tax Rate', [], 'Admin.Advparameters.Feature')],
                    ];
                    // default values
                    self::$default_values = [
                        'discount_rate' => '0',
                        'tax_rate' => '0',
                    ];

                    break;
            }
        }

        $this->separator = ($separator = Tools::substr((string) (trim(Tools::getValue('separator'))), 0, 1)) ? $separator : ';';
        $this->convert = false;
        $this->multiple_value_separator = ($separator = Tools::substr((string) (trim(Tools::getValue('multiple_value_separator'))), 0, 1)) ? $separator : ',';
    }

    public function setMedia($isNewTheme = false)
    {
        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR
            . 'template')) {
            $bo_theme = 'default';
        }

        // We need to set parent media first, so that jQuery is loaded before the dependant plugins
        parent::setMedia($isNewTheme);

        $this->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $bo_theme . '/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $bo_theme . '/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $bo_theme . '/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $bo_theme . '/js/jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__ . 'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__ . 'js/vendor/ladda.js');
    }

    /**
     * @return bool|string
     *
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function renderForm()
    {
        // If import was executed - collect errors or success message
        // and send them to the migrated controller.
        if ($this->importExecuted) {
            $session = $this->getSession();

            if ($this->errors) {
                foreach ($this->errors as $error) {
                    $session->getFlashBag()->add('error', $error);
                }
            } else {
                foreach ($this->warnings as $warning) {
                    $session->getFlashBag()->add('warning', $warning);
                }

                $session->getFlashBag()->add(
                    'success',
                    $this->trans(
                        'Your file has been successfully imported into your shop. Don\'t forget to re-build the products\' search index.',
                        [],
                        'Admin.Advparameters.Notification'
                    )
                );
            }
        }

        $request = $this->getSymfonyRequest();

        if ($request && $request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_GET)) {
            // Import form is reworked in Symfony.
            // If user tries to access legacy form directly,
            // we redirect him to new form.
            $symfonyImportForm = $this->context->link->getAdminLink('AdminImport');
            Tools::redirectAdmin($symfonyImportForm);
        }

        if (!is_dir(AdminImportController::getPath())) {
            return !($this->errors[] = $this->trans('The import directory doesn\'t exist. Please check your file path.', [], 'Admin.Advparameters.Notification'));
        }

        if (!is_writable(AdminImportController::getPath())) {
            $this->displayWarning($this->trans('The import directory must be writable (CHMOD 755 / 777).', [], 'Admin.Advparameters.Notification'));
        }

        $files_to_import = scandir(AdminImportController::getPath(), SCANDIR_SORT_NONE);
        uasort($files_to_import, ['AdminImportController', 'usortFiles']);
        foreach ($files_to_import as $k => &$filename) {
            //exclude .  ..  .svn and index.php and all hidden files
            if (preg_match('/^\..*|index\.php/i', $filename) || is_dir(AdminImportController::getPath() . $filename)) {
                unset($files_to_import[$k]);
            }
        }
        unset($filename);

        $this->fields_form = [''];

        $this->toolbar_scroll = false;
        $this->toolbar_btn = [];

        // adds fancybox
        $this->addJqueryPlugin(['fancybox']);

        $entity_selected = 0;
        if (isset($this->entities[$this->trans(Tools::ucfirst(Tools::getValue('import_type')))])) {
            $entity_selected = $this->entities[$this->trans(Tools::ucfirst(Tools::getValue('import_type')))];
            $this->context->cookie->entity_selected = (int) $entity_selected;
        } elseif (isset($this->context->cookie->entity_selected)) {
            $entity_selected = (int) $this->context->cookie->entity_selected;
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
            $id_lang_selected = (int) Language::getIdByIso(urldecode($this->context->cookie->iso_lang_selected));
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
        $bytes = (int) trim($post_max_size);
        $last = strtolower($post_max_size[strlen($post_max_size) - 1]);

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

        if ($bytes == '') {
            $bytes = 20971520;
        } // 20Mb

        $this->tpl_form_vars = [
            'post_max_size' => (int) $bytes,
            'module_confirmation' => Tools::isSubmit('import') && !count($this->warnings),
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
        ];

        return parent::renderForm();
    }

    public function ajaxProcessuploadCsv()
    {
        $filename_prefix = date('YmdHis') . '-';

        if (isset($_FILES['file']) && !empty($_FILES['file']['error'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $_FILES['file']['error'] = $this->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.', [], 'Admin.Advparameters.Notification');

                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_FILES['file']['error'] = $this->trans('The uploaded file exceeds the post_max_size directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess, for example:', [], 'Admin.Advparameters.Notification')
                    . '<br/><a href="' . $this->context->link->getAdminLink('AdminMeta') . '" >
					<code>php_value post_max_size 20M</code> ' .
                    $this->trans('(click to open "Generators" page)', [], 'Admin.Advparameters.Notification') . '</a>';

                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_FILES['file']['error'] = $this->trans('The uploaded file was only partially uploaded.', [], 'Admin.Advparameters.Notification');

                    break;
                case UPLOAD_ERR_NO_FILE:
                    $_FILES['file']['error'] = $this->trans('No file was uploaded.', [], 'Admin.Advparameters.Notification');

                    break;
            }
        } elseif (!preg_match('#([^\.]*?)\.(csv|xls[xt]?|o[dt]s)$#is', $_FILES['file']['name'])) {
            $_FILES['file']['error'] = $this->trans('The extension of your file should be ".csv".', [], 'Admin.Advparameters.Notification');
        } elseif (!@filemtime($_FILES['file']['tmp_name']) ||
            !@move_uploaded_file($_FILES['file']['tmp_name'], AdminImportController::getPath() . $filename_prefix . str_replace("\0", '', $_FILES['file']['name']))) {
            $_FILES['file']['error'] = $this->trans('An error occurred while uploading / copying the file.', [], 'Admin.Advparameters.Notification');
        } else {
            @chmod(AdminImportController::getPath() . $filename_prefix . $_FILES['file']['name'], 0664);
            $_FILES['file']['filename'] = $filename_prefix . str_replace('\0', '', $_FILES['file']['name']);
        }

        die(json_encode($_FILES));
    }

    public function renderView()
    {
        $this->addJS(_PS_JS_DIR_ . 'admin/import.js');

        $handle = $this->openCsvFile();
        $nb_column = $this->getNbrColumn($handle, $this->separator);
        $nb_table = ceil($nb_column / MAX_COLUMNS);

        $res = [];
        foreach ($this->required_fields as $elem) {
            $res[] = '\'' . $elem . '\'';
        }

        $data = [];
        for ($i = 0; $i < $nb_table; ++$i) {
            $data[$i] = $this->generateContentTable($i, $nb_column, $handle, $this->separator);
        }

        $this->context->cookie->entity_selected = (int) Tools::getValue('entity');
        $this->context->cookie->iso_lang_selected = urlencode(Tools::getValue('iso_lang'));
        $this->context->cookie->separator_selected = urlencode($this->separator);
        $this->context->cookie->multiple_value_separator_selected = urlencode($this->multiple_value_separator);
        $this->context->cookie->csv_selected = urlencode(Tools::getValue('csv'));

        $this->tpl_view_vars = [
            'import_matchs' => Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'import_match', true, false),
            'fields_value' => [
                'csv' => Tools::getValue('csv'),
                'entity' => (int) Tools::getValue('entity'),
                'iso_lang' => Tools::getValue('iso_lang'),
                'truncate' => Tools::getValue('truncate'),
                'forceIDs' => Tools::getValue('forceIDs'),
                'regenerate' => Tools::getValue('regenerate'),
                'sendemail' => Tools::getValue('sendemail'),
                'match_ref' => Tools::getValue('match_ref'),
                'separator' => $this->separator,
                'multiple_value_separator' => $this->multiple_value_separator,
            ],
            'nb_table' => $nb_table,
            'nb_column' => $nb_column,
            'res' => implode(',', $res),
            'max_columns' => MAX_COLUMNS,
            'no_pre_select' => ['price_tin', 'feature'],
            'available_fields' => $this->available_fields,
            'data' => $data,
        ];

        return parent::renderView();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            case 'import':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }

                $this->toolbar_btn['cancel'] = [
                    'href' => $back,
                    'desc' => $this->trans('Cancel', [], 'Admin.Actions'),
                ];
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save-import'] = [
                    'href' => '#',
                    'desc' => $this->trans('Import .CSV data', [], 'Admin.Advparameters.Feature'),
                ];

                break;
        }
    }

    protected function generateContentTable($current_table, $nb_column, $handle, $glue)
    {
        $html = '<table id="table' . $current_table . '" style="display: none;" class="table table-bordered"><thead><tr>';
        // Header
        for ($i = 0; $i < $nb_column; ++$i) {
            if (MAX_COLUMNS * (int) $current_table <= $i && (int) $i < MAX_COLUMNS * ((int) $current_table + 1)) {
                $html .= '<th>
							<select id="type_value[' . $i . ']"
								name="type_value[' . $i . ']"
								class="type_value chosen">
								' . $this->getTypeValuesOptions($i) . '
							</select>
						</th>';
            }
        }
        $html .= '</tr></thead><tbody>';

        AdminImportController::setLocale();
        for ($current_line = 0; $current_line < 10 && $line = fgetcsv($handle, MAX_LINE_SIZE, $glue); ++$current_line) {
            /* UTF-8 conversion */
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }
            $html .= '<tr id="table_' . $current_table . '_line_' . $current_line . '">';
            foreach ($line as $nb_c => $column) {
                if ((MAX_COLUMNS * (int) $current_table <= $nb_c) && ((int) $nb_c < MAX_COLUMNS * ((int) $current_table + 1))) {
                    $html .= '<td>' . htmlentities(Tools::substr($column, 0, 200), ENT_QUOTES, 'UTF-8') . '</td>';
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
                $this->errors[] = $this->trans('To proceed, please upload a file first.', [], 'Admin.Advparameters.Notification');
                $this->content .= $this->renderForm();
            }
        } else {
            $this->content .= $this->renderForm();
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
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
        return (bool) $field;
    }

    protected static function getPrice($field)
    {
        return (float) str_replace(
            [',', '%'],
            ['.', ''],
            $field
        );
    }

    protected static function split($field)
    {
        if (empty($field)) {
            return [];
        }

        $separator = Tools::getValue('multiple_value_separator');
        if (null === $separator || trim($separator) == '') {
            $separator = ',';
        }

        $tab = '';
        $uniqid_path = false;

        // try data:// protocole. If failed, old school file on filesystem.
        if (($fd = @fopen('data://text/plain;base64,' . base64_encode($field), 'rb')) === false) {
            do {
                $uniqid_path = _PS_UPLOAD_DIR_ . uniqid();
            } while (file_exists($uniqid_path));
            file_put_contents($uniqid_path, $field);
            $fd = fopen($uniqid_path, 'rb');
        }

        if ($fd === false) {
            return [];
        }

        $tab = fgetcsv($fd, MAX_LINE_SIZE, $separator);
        fclose($fd);
        if ($uniqid_path !== false && file_exists($uniqid_path)) {
            @unlink($uniqid_path);
        }

        if (empty($tab) || (!is_array($tab))) {
            return [];
        }

        return $tab;
    }

    protected static function createMultiLangField($field)
    {
        $res = [];
        foreach (Language::getIDs(false) as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }

    protected function getTypeValuesOptions($nb_c)
    {
        $i = 0;
        $no_pre_select = ['price_tin', 'feature'];

        $options = '';
        foreach ($this->available_fields as $k => $field) {
            $options .= '<option value="' . $k . '"';
            if ($k === 'price_tin') {
                ++$nb_c;
            }
            if ($i === ($nb_c + 1) && (!in_array($k, $no_pre_select))) {
                $options .= ' selected="selected"';
            }
            $options .= '>' . $field['label'] . '</option>';
            ++$i;
        }

        return $options;
    }

    /**
     * Return fields to be display AS piece of advise
     *
     * @param bool $in_array
     *
     * @return string|array
     */
    public function getAvailableFields($in_array = false)
    {
        $i = 0;
        $fields = [];
        $keys = array_keys($this->available_fields);
        array_shift($keys);
        foreach ($this->available_fields as $k => $field) {
            if ($k === 'no') {
                continue;
            }
            if ($k === 'price_tin') { // Special case for Product : either one or the other. Not both.
                $fields[$i - 1] = '<div>' . $this->available_fields[$keys[$i - 1]]['label'] . '<br/>&nbsp;&nbsp;<i>' . $this->trans('or', [], 'Admin.Advparameters.Help') . '</i>&nbsp;&nbsp; ' . $field['label'] . '</div>';
            } else {
                if (isset($field['help'])) {
                    $html = '&nbsp;<span class="help-box" data-toggle="popover" data-content="' . $field['help'] . '"></span>';
                } else {
                    $html = '';
                }
                $fields[] = '<div>' . $field['label'] . $html . '</div>';
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
        $type_value = Tools::getValue('type_value') ? Tools::getValue('type_value') : [];
        foreach ($type_value as $nb => $type) {
            if ($type != 'no') {
                self::$column_mask[$type] = $nb;
            }
        }
    }

    public static function getMaskedRow($row)
    {
        $res = [];
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
     * @param array $array
     * @param callable $funcname
     *
     * @return bool
     */
    public static function arrayWalk(&$array, $funcname, &$user_data = false)
    {
        if (!is_callable($funcname)) {
            return false;
        }

        foreach ($array as $k => $row) {
            if (!call_user_func_array($funcname, [$row, $k, &$user_data])) {
                return false;
            }
        }

        return true;
    }

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark.
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     *
     * @return bool
     */
    protected static function copyImg($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)
    {
        return ImageManager::copyImg($id_entity, $id_image, $url, $entity, $regenerate);
    }

    protected static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        return ImageManager::get_best_path($tgt_width, $tgt_height, $path_infos);
    }

    public function categoryImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false)
    {
        $this->receiveTab();
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');
        $regenerate = Tools::getValue('regenerate');
        $shop_is_feature_active = Shop::isFeatureActive();

        $cat_moved = [];
        if ($crossStepsVariables !== false && array_key_exists('cat_moved', $crossStepsVariables)) {
            $cat_moved = $crossStepsVariables['cat_moved'];
        }

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

                continue;
            }

            $info = AdminImportController::getMaskedRow($line);
            try {
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
            } catch (Exception $exc) {
                $this->errors[] = $exc->getMessage();
            }
        }

        $this->closeCsvFile($handle);

        if ($crossStepsVariables !== false) {
            $crossStepsVariables['cat_moved'] = $cat_moved;
        }

        return $line_count;
    }

    protected function categoryImportOne($info, $default_language_id, $id_lang, $force_ids, $regenerate, $shop_is_feature_active, &$cat_moved, $validateOnly = false)
    {
        $tab_categ = [Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')];
        if (isset($info['id']) && in_array((int) $info['id'], $tab_categ)) {
            $this->errors[] = $this->trans('The category ID must be unique. It can\'t be the same as the one for Root or Home category.', [], 'Admin.Advparameters.Notification');

            return;
        }
        AdminImportController::setDefaultValues($info);

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $category = new Category((int) $info['id']);
        } else {
            if (isset($info['id']) && (int) $info['id'] && Category::existsInDatabase((int) $info['id'], 'category')) {
                $category = new Category((int) $info['id']);
            } else {
                $category = new Category();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $category);

        /** @var Category $category */
        // Parent category
        if (isset($category->parent) && is_numeric($category->parent)) {
            // Validation for parenting itself
            if ($validateOnly && ($category->parent == $category->id) || (isset($info['id']) && $category->parent == (int) $info['id'])) {
                $this->errors[] = $this->trans(
                    'The category ID must be unique. It can\'t be the same as the one for the parent category (ID: %1$s).',
                    [
                        !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                    ],
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
                    [Tools::htmlentitiesUTF8($category->parent)],
                    'Admin.Advparameters.Notification'
                );

                return;
            }
            $category_parent = Category::searchByName($id_lang, $category->parent, true);
            if ($category_parent['id_category']) {
                $category->id_parent = (int) $category_parent['id_category'];
                $category->level_depth = (int) $category_parent['level_depth'] + 1;
            } else {
                $category_to_create = new Category();
                $category_to_create->name = AdminImportController::createMultiLangField($category->parent);
                $category_to_create->active = true;
                $category_link_rewrite = Tools::str2url($category_to_create->name[$id_lang]);
                $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                $category_to_create->id_parent = (int) Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create

                if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move the position of this test. Only ->add() should not be triggered is !validateOnly. Previous tests should be always run.
                    $category_to_create->add()) {
                    $category->id_parent = $category_to_create->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%category_name% (ID: %id%) cannot be saved',
                            [
                                '%category_name%' => Tools::htmlentitiesUTF8($category_to_create->name[$id_lang]),
                                '%id%' => !empty($category_to_create->id) ? Tools::htmlentitiesUTF8($category_to_create->id) : 'null',
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
            $category->id_shop_default = (int) Context::getContext()->shop->id;
        }

        $bak = $category->link_rewrite[$default_language_id];
        if ((isset($category->link_rewrite) && empty($category->link_rewrite[$default_language_id])) || !$valid_link) {
            $category->link_rewrite = Tools::str2url($category->name[$default_language_id]);
            if ($category->link_rewrite == '') {
                $category->link_rewrite = 'friendly-url-autogeneration-failed';
                $this->warnings[] = $this->trans(
                    'URL rewriting failed to auto-generate a friendly URL for: %category_name%',
                    [
                        '%category_name%' => Tools::htmlentitiesUTF8($category->name[$default_language_id]),
                    ],
                    'Admin.Advparameters.Notification'
                );
            }
            $category->link_rewrite = AdminImportController::createMultiLangField($category->link_rewrite);
        }

        if (!$valid_link) {
            $this->informations[] = $this->trans(
                'Rewrite link for %1$s (ID %2$s): re-written as %3$s.',
                [
                    '%1$s' => Tools::htmlentitiesUTF8($bak),
                    '%2$s' => !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                    '%3$s' => Tools::htmlentitiesUTF8($category->link_rewrite[$default_language_id]),
                ],
                'Admin.Advparameters.Notification'
            );
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
                $cat_moved[$category->id] = (int) $category_already_created['id_category'];
                $category->id = (int) $category_already_created['id_category'];
                if (Validate::isDate($category_already_created['date_add'])) {
                    $category->date_add = $category_already_created['date_add'];
                }
            }

            if ($category->id && $category->id == $category->id_parent) {
                $this->errors[] = sprintf(
                    $this->trans(
                        'A category cannot be its own parent. The parent category ID is either missing or unknown (ID: %1$s).',
                        [],
                        'Admin.Advparameters.Notification'
                    ),
                    !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null'
                );

                return;
            }

            /* No automatic nTree regeneration for import */
            $category->doNotRegenerateNTree = true;

            // If id category AND id category already in base, trying to update
            $categories_home_root = [Configuration::get('PS_ROOT_CATEGORY'), Configuration::get('PS_HOME_CATEGORY')];
            if ($category->id &&
                $category->categoryExists($category->id) &&
                !in_array($category->id, $categories_home_root) &&
                !$validateOnly) {
                $res = $category->update();
            }
            if ($category->id == Configuration::get('PS_ROOT_CATEGORY')) {
                $this->errors[] = $this->trans('The root category cannot be modified.', [], 'Admin.Advparameters.Notification');
            }
            // If no id_category or update failed
            $category->force_id = (bool) $force_ids;
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
                $this->warnings[] = $category->image . ' ' . $this->trans('cannot be copied.', [], 'Admin.Advparameters.Notification');
            }
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = $this->trans(
                '%1$s (ID: %2$s) cannot be %3$s',
                [
                    !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                    !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID',
                    'saved',
                ],
                'Admin.Advparameters.Notification'
            );
            $error_tmp = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') . Db::getInstance()->getMsgError();
            if ($error_tmp != '') {
                $this->errors[] = $error_tmp;
            }
        } else {
            // Associate category to shop
            if ($shop_is_feature_active) {
                Db::getInstance()->execute('
					DELETE FROM ' . _DB_PREFIX_ . 'category_shop
					WHERE id_category = ' . (int) $category->id);

                if (!isset($info['shop']) || empty($info['shop'])) {
                    $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
                }

                // Get shops for each attributes
                $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

                foreach ($info['shop'] as $shop) {
                    if (!empty($shop) && !is_numeric($shop)) {
                        $category->addShop(Shop::getIdByName($shop));
                    } elseif (!empty($shop)) {
                        $category->addShop((int) $shop);
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

        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
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

        $accessories = [];
        if ($crossStepsVariables !== false && array_key_exists('accessories', $crossStepsVariables)) {
            $accessories = $crossStepsVariables['accessories'];
        }

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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
                ++$i;

                continue;
            }

            if (count($links) > 0) { // We delete and relink only if there is accessories to link...
                // Bulk jobs: for performances, we need to do a minimum amount of SQL queries. No product inflation.
                $unique_ids = Product::getExistingIdsFromIdsOrRefs($links);
                Db::getInstance()->delete('accessory', 'id_product_1 = ' . (int) $product_id);
                Product::changeAccessoriesForProduct($unique_ids, $product_id);
            }
            ++$line_count;

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
        } elseif ($match_ref && isset($info['reference'])) {
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
        if (isset($product->id) && $product->id && Product::existsInDatabase((int) $product->id, 'product')) {
            $product->loadStockData();
            $update_advanced_stock_management_value = true;
            $category_data = Product::getProductCategories((int) $product->id);

            if (is_array($category_data)) {
                foreach ($category_data as $tmp) {
                    if ($product->category && is_array($product->category)) {
                        continue;
                    }
                    $product->category[] = $tmp;
                }
            }
        }

        AdminImportController::setEntityDefaultValues($product);
        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $product);

        /** @var Product|null $product */
        if (!$product) {
            return;
        }

        if (!$shop_is_feature_active) {
            $product->shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        } elseif (!isset($product->shop) || empty($product->shop)) {
            $product->shop = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        if (!$shop_is_feature_active) {
            $product->id_shop_default = (int) Configuration::get('PS_SHOP_DEFAULT');
        } else {
            $product->id_shop_default = (int) Context::getContext()->shop->id;
        }

        // link product to shops
        foreach (explode($this->multiple_value_separator, $product->shop) as $shop) {
            if (!empty($shop) && !is_numeric($shop)) {
                $product->id_shop_list[] = Shop::getIdByName($shop);
            } elseif (!empty($shop)) {
                $product->id_shop_list[] = $shop;
            }
        }

        if ((int) $product->id_tax_rules_group != 0) {
            if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group))) {
                $address = $this->context->shop->getAddress();
                $tax_manager = TaxManagerFactory::getManager($address, $product->id_tax_rules_group);
                $product_tax_calculator = $tax_manager->getTaxCalculator();
                $product->tax_rate = $product_tax_calculator->getTotalRate();
            } else {
                $this->addProductWarning(
                    'id_tax_rules_group',
                    $product->id_tax_rules_group,
                    $this->trans('Unknown tax rule group ID. You need to create a group with this ID first.', [], 'Admin.Advparameters.Notification')
                );
            }
        }
        if (isset($product->manufacturer) && is_numeric($product->manufacturer) && Manufacturer::manufacturerExists((int) $product->manufacturer)) {
            $product->id_manufacturer = (int) $product->manufacturer;
        } elseif (isset($product->manufacturer) && is_string($product->manufacturer) && !empty($product->manufacturer)) {
            if ($manufacturer = Manufacturer::getIdByName($product->manufacturer)) {
                $product->id_manufacturer = (int) $manufacturer;
            } else {
                $manufacturer = new Manufacturer();
                $manufacturer->name = $product->manufacturer;
                $manufacturer->active = true;
                if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $manufacturer->add()) {
                    $product->id_manufacturer = (int) $manufacturer->id;
                    $manufacturer->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                            Tools::htmlentitiesUTF8($manufacturer->name),
                            !empty($manufacturer->id) ? $manufacturer->id : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($product->supplier) && is_numeric($product->supplier) && Supplier::supplierExists((int) $product->supplier)) {
            $product->id_supplier = (int) $product->supplier;
        } elseif (isset($product->supplier) && is_string($product->supplier) && !empty($product->supplier)) {
            if ($supplier = Supplier::getIdByName($product->supplier)) {
                $product->id_supplier = (int) $supplier;
            } else {
                $supplier = new Supplier();
                $supplier->name = $product->supplier;
                $supplier->active = true;

                if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly &&  // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $supplier->add()) {
                    $product->id_supplier = (int) $supplier->id;
                    $supplier->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                            Tools::htmlentitiesUTF8($supplier->name),
                            !empty($supplier->id) ? Tools::htmlentitiesUTF8($supplier->id) : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
                $product->price = (float) number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
            }
        } elseif (isset($product->price_tin, $product->price_tex)) {
            $product->price = $product->price_tex;
        }

        if (!Configuration::get('PS_USE_ECOTAX')) {
            $product->ecotax = 0;
        }

        if (!empty($product->category) && is_array($product->category)) {
            $product->id_category = []; // Reset default values array
            foreach ($product->category as $value) {
                if (is_numeric($value)) {
                    if (Category::categoryExists((int) $value)) {
                        $product->id_category[] = (int) $value;
                    } else {
                        $category_to_create = new Category();
                        $category_to_create->id = (int) $value;
                        $category_to_create->name = AdminImportController::createMultiLangField($value);
                        $category_to_create->active = true;
                        $category_to_create->id_parent = (int) Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
                        $category_link_rewrite = Tools::str2url($category_to_create->name[$default_language_id]);
                        $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);
                        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                            !$validateOnly &&  // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                            $category_to_create->add()) {
                            $product->id_category[] = (int) $category_to_create->id;
                        } else {
                            if (!$validateOnly) {
                                $this->errors[] = sprintf(
                                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                                    Tools::htmlentitiesUTF8($category_to_create->name[$default_language_id]),
                                    !empty($category_to_create->id) ? Tools::htmlentitiesUTF8($category_to_create->id) : 'null'
                                );
                            }
                            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                                $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                                    Db::getInstance()->getMsgError();
                            }
                        }
                    }
                } elseif (!$validateOnly && is_string($value) && !empty($value)) {
                    $category = Category::searchByPath($default_language_id, trim($value), $this, 'productImportCreateCat');
                    if ($category['id_category']) {
                        $product->id_category[] = (int) $category['id_category'];
                    } else {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            [
                                '%data%' => Tools::htmlentitiesUTF8(trim($value)),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            $product->id_category = array_values(array_unique($product->id_category));
        }

        // Category default now takes the value of the first new category during import
        if (isset($product->id_category[0])) {
            if (empty($product->id_category_default) || !in_array($product->id_category_default, $product->id_category)) {
                $product->id_category_default = (int) $product->id_category[0];
            }
        } else {
            if (empty($product->id_category_default)) {
                $defaultProductShop = new Shop($product->id_shop_default);
                $product->id_category_default = Category::getRootCategory(null, Validate::isLoadedObject($defaultProductShop) ? $defaultProductShop : null)->id;
            }
        }

        $link_rewrite = (is_array($product->link_rewrite) && isset($product->link_rewrite[$id_lang])) ? trim($product->link_rewrite[$id_lang]) : '';
        $valid_link = Validate::isLinkRewrite($link_rewrite);
        if ((isset($product->link_rewrite[$id_lang]) && empty($product->link_rewrite[$id_lang])) || !$valid_link) {
            $link_rewrite = Tools::str2url($product->name[$id_lang]);
            if ($link_rewrite == '') {
                $link_rewrite = 'friendly-url-autogeneration-failed';
            }
        }

        if (!$valid_link) {
            $this->informations[] = $this->trans(
                'Rewrite link for %1$s (ID %2$s): re-written as %3$s.',
                [
                    '%1$s' => Tools::htmlentitiesUTF8($product->name[$id_lang]),
                    '%2$s' => !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                    '%3$s' => Tools::htmlentitiesUTF8($link_rewrite),
                ],
                'Admin.Advparameters.Notification'
            );
        }

        if (!$valid_link || !(is_array($product->link_rewrite) && count($product->link_rewrite))) {
            $product->link_rewrite = AdminImportController::createMultiLangField($link_rewrite);
        } else {
            $product->link_rewrite[(int) $id_lang] = $link_rewrite;
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
        $product->indexed = false;
        $productExistsInDatabase = false;

        if ($product->id && Product::existsInDatabase((int) $product->id, 'product')) {
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
					FROM `' . _DB_PREFIX_ . 'product` p
					' . Shop::addSqlAssociation('product', 'p') . '
					WHERE p.`reference` = "' . pSQL($product->reference) . '"
				', false);
                $product->id = (int) $datas['id_product'];
                $product->date_add = pSQL($datas['date_add']);
                $res = ($validateOnly || $product->update());
            } // Else If id product && id product already in base, trying to update
            elseif ($productExistsInDatabase) {
                $datas = Db::getInstance()->getRow('
					SELECT product_shop.`date_add`
					FROM `' . _DB_PREFIX_ . 'product` p
					' . Shop::addSqlAssociation('product', 'p') . '
					WHERE p.`id_product` = ' . (int) $product->id, false);
                $product->date_add = pSQL($datas['date_add']);
                $res = ($validateOnly || $product->update());
            }
            // If no id_product or update failed
            $product->force_id = (bool) $force_ids;

            if (!$res) {
                if ($product->date_add != '') {
                    $res = ($validateOnly || $product->add(false));
                } else {
                    $res = ($validateOnly || $product->add());
                }
            }

            if (!$validateOnly) {
                if ($product->getType() == Product::PTYPE_VIRTUAL) {
                    StockAvailable::setProductOutOfStock((int) $product->id, 1);
                } else {
                    StockAvailable::setProductOutOfStock((int) $product->id, (int) $product->out_of_stock);
                }

                if ($product_download_id = ProductDownload::getIdFromIdProduct((int) $product->id)) {
                    $product_download = new ProductDownload($product_download_id);
                    $product_download->delete(true);
                }

                if ($product->getType() == Product::PTYPE_VIRTUAL) {
                    $product_download = new ProductDownload();
                    $product_download->filename = ProductDownload::getNewFilename();
                    Tools::copy($info['file_url'], _PS_DOWNLOAD_DIR_ . $product_download->filename);
                    $product_download->id_product = (int) $product->id;
                    $product_download->nb_downloadable = (int) $info['nb_downloadable'];
                    $product_download->date_expiration = $info['date_expiration'];
                    $product_download->nb_days_accessible = (int) $info['nb_days_accessible'];
                    $product_download->display_filename = basename($info['file_url']);
                    $product_download->add();
                }
            }
        }

        $shops = [];
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
                $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Shop is not valid', [], 'Admin.Advparameters.Notification'));
            }
        }
        if (empty($shops)) {
            $shops = Shop::getContextListShopID();
        }
        // If both failed, mysql error
        if (!$res) {
            $this->errors[] = sprintf(
                $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID'
            );
            $this->errors[] = ($field_error !== true ? $field_error : '') . ($lang_field_error !== true ? $lang_field_error : '') .
                Db::getInstance()->getMsgError();
        } else {
            // Product supplier
            if (!$validateOnly && !empty($product->id) && property_exists($product, 'supplier_reference') && !empty($product->id_supplier)) {
                $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $product->id, 0, (int) $product->id_supplier);
                if ($id_product_supplier) {
                    $product_supplier = new ProductSupplier($id_product_supplier);
                } else {
                    $product_supplier = new ProductSupplier();
                }
                $product_supplier->id_product = (int) $product->id;
                $product_supplier->id_product_attribute = 0;
                $product_supplier->id_supplier = (int) $product->id_supplier;
                $product_supplier->product_supplier_price_te = $product->wholesale_price;
                $product_supplier->product_supplier_reference = $product->supplier_reference;
                $product_supplier->id_currency = Currency::getDefaultCurrency()->id;
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

            $id_shop_list = [];
            foreach ($info['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $id_shop_list[] = (int) Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $id_shop_list[] = $shop;
                }
            }

            if ((isset($info['reduction_price']) && $info['reduction_price'] > 0) || (isset($info['reduction_percent']) && $info['reduction_percent'] > 0)) {
                foreach ($id_shop_list as $id_shop) {
                    $specific_price = SpecificPrice::getSpecificPrice($product->id, $id_shop, 0, 0, 0, 1, 0, 0, 0, 0);

                    if (is_array($specific_price) && isset($specific_price['id_specific_price'])) {
                        $specific_price = new SpecificPrice((int) $specific_price['id_specific_price']);
                    } else {
                        $specific_price = new SpecificPrice();
                    }
                    $specific_price->id_product = (int) $product->id;
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
                    $specific_price->to = (isset($info['reduction_to']) && Validate::isDate($info['reduction_to'])) ? $info['reduction_to'] : '0000-00-00 00:00:00';
                    if (!$validateOnly && !$specific_price->save()) {
                        $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Discount is invalid', [], 'Admin.Advparameters.Notification'));
                    }
                }
            }

            if (!$validateOnly && !empty($product->tags)) {
                if (isset($product->id) && $product->id) {
                    $tags = Tag::getProductTags($product->id);
                    if (is_array($tags) && count($tags)) {
                        /** @phpstan-ignore-next-line $product->tags is filled with a string at line 1986 */
                        $productTags = explode($this->multiple_value_separator, $product->tags);
                        foreach ($productTags as $key => $tag) {
                            if (!empty($tag)) {
                                $productTags[$key] = trim($tag);
                            }
                        }
                        $tags[$id_lang] = $productTags;
                        $product->tags = $tags;
                    }
                }
                // Delete tags for this id product, for no duplicating error
                Tag::deleteTagsForProduct($product->id);
                if (!is_array($product->tags) && !empty($product->tags)) {
                    $product->tags = AdminImportController::createMultiLangField($product->tags);
                    foreach ($product->tags as $key => $tags) {
                        $is_tag_added = Tag::addTags($key, $product->id, $tags, $this->multiple_value_separator);
                        if (!$is_tag_added) {
                            $this->addProductWarning(Tools::safeOutput($info['name']), $product->id, $this->trans('Tags list is invalid', [], 'Admin.Advparameters.Notification'));

                            break;
                        }
                    }
                } else {
                    foreach ($product->tags as $key => $tags) {
                        $str = '';
                        foreach ($tags as $one_tag) {
                            $str .= $one_tag . $this->multiple_value_separator;
                        }
                        $str = rtrim($str, $this->multiple_value_separator);

                        $is_tag_added = Tag::addTags($key, $product->id, $str, $this->multiple_value_separator);
                        if (!$is_tag_added) {
                            $this->addProductWarning(Tools::safeOutput($info['name']), (int) $product->id, $this->trans(
                                'Invalid tag(s) (%s)',
                                [$str],
                                'Admin.Notifications.Error'
                            ));

                            break;
                        }
                    }
                }
            }

            //delete existing images if "delete_existing_images" is set to 1
            if (!$validateOnly && isset($product->delete_existing_images)) {
                if ((bool) $product->delete_existing_images) {
                    $product->deleteImages();
                }
            }

            if (!$validateOnly && isset($product->image) && is_array($product->image) && count($product->image)) {
                $product_has_images = (bool) Image::getImages($this->context->language->id, (int) $product->id);
                foreach ($product->image as $key => $url) {
                    $url = trim($url);
                    $error = false;
                    if (!empty($url)) {
                        $url = str_replace(' ', '%20', $url);

                        $image = new Image();
                        $image->id_product = (int) $product->id;
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
                                $this->warnings[] = $this->trans('Error copying image: %url%', ['%url%' => $url], 'Admin.Advparameters.Notification');
                            }
                        } else {
                            $error = true;
                        }
                    } else {
                        $error = true;
                    }

                    if ($error) {
                        $this->warnings[] = $this->trans(
                            'Product #%id%: the picture (%url%) cannot be saved.', [
                                '%id%' => Tools::htmlentitiesUTF8(isset($image) ? $image->id_product : ''),
                                '%url%' => Tools::htmlentitiesUTF8($url),
                            ],
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
                foreach (explode($this->multiple_value_separator, $features['features']) as $single_feature) {
                    if (empty($single_feature)) {
                        continue;
                    }
                    $tab_feature = explode(':', $single_feature);
                    $feature_name = isset($tab_feature[0]) ? trim($tab_feature[0]) : '';
                    $feature_value = isset($tab_feature[1]) ? trim($tab_feature[1]) : '';
                    $position = isset($tab_feature[2]) ? (int) $tab_feature[2] - 1 : false;
                    $custom = isset($tab_feature[3]) ? (int) $tab_feature[3] : false;
                    if (!empty($feature_name) && !empty($feature_value)) {
                        $id_feature = (int) Feature::addFeatureImport($feature_name, $position);
                        $id_product = null;
                        if ($force_ids || $match_ref) {
                            $id_product = (int) $product->id;
                        }
                        $id_feature_value = (int) FeatureValue::addFeatureValueImport($id_feature, $feature_value, $id_product, $id_lang, $custom);
                        Product::addFeatureProductImport($product->id, $id_feature, $id_feature_value);
                    }
                }
            }
            // clean feature positions to avoid conflict
            Feature::cleanPositions();

            // set advanced stock managment
            if (!$validateOnly) {
                /* @phpstan-ignore-next-line Data from the property `advanced_stock_management` come from the database */
                if ($product->advanced_stock_management != 1 && $product->advanced_stock_management != 0) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management has incorrect value. Not set for product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management == 1) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot enable on product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif ($update_advanced_stock_management_value) {
                    $product->setAdvancedStockManagement($product->advanced_stock_management);
                }
                // automaticly disable depends on stock, if a_s_m set to disabled
                if (StockAvailable::dependsOnStock($product->id) == 1 && $product->advanced_stock_management == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, false);
                }
            }

            // Check if warehouse exists
            if (isset($product->warehouse) && $product->warehouse) {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, warehouse not set on product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
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
                        $this->warnings[] = $this->trans(
                            'Warehouse did not exist, cannot set on product %name%.',
                            [
                                '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            // stock available
            if (isset($product->depends_on_stock)) {
                /* @phpstan-ignore-next-line Data from the property `depends_on_stock` come from the database */
                if ($product->depends_on_stock != 0 && $product->depends_on_stock != 1) {
                    $this->warnings[] = $this->trans(
                        'Incorrect value for "Depends on stock" for product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                /* @phpstan-ignore-next-line Data from properties `advanced_stock_management` & `depends_on_stock` come from the database */
                } elseif ((!$product->advanced_stock_management || $product->advanced_stock_management == 0) && $product->depends_on_stock == 1) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot set "Depends on stock" for product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language_id]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock($product->id, $product->depends_on_stock);
                }

                // This code allows us to set qty and disable depends on stock
                if (!$validateOnly) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($product->depends_on_stock == 1) {
                        $stock_manager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', (string) $product->wholesale_price);
                        if ($price == '0') {
                            $price = 0.000001;
                        }
                        $price = round((float) $price, 6);
                        $warehouse = new Warehouse($product->warehouse);
                        if ($stock_manager->addProduct((int) $product->id, 0, $warehouse, (int) $product->quantity, 1, $price, true)) {
                            StockAvailable::synchronize((int) $product->id);
                        }
                    } else {
                        if ($shop_is_feature_active) {
                            foreach ($shops as $shop) {
                                StockAvailable::setQuantity((int) $product->id, 0, (int) $product->quantity, (int) $shop);
                            }
                        } else {
                            StockAvailable::setQuantity((int) $product->id, 0, (int) $product->quantity, (int) $this->context->shop->id);
                        }
                    }
                }
            } elseif (!$validateOnly) {
                // if not depends_on_stock set, use normal qty
                if ($shop_is_feature_active) {
                    foreach ($shops as $shop) {
                        StockAvailable::setQuantity((int) $product->id, 0, (int) $product->quantity, (int) $shop);
                    }
                } else {
                    StockAvailable::setQuantity((int) $product->id, 0, (int) $product->quantity, (int) $this->context->shop->id);
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
            $category_to_create->id_shop_default = (int) Context::getContext()->shop->id;
        }
        $category_to_create->name = AdminImportController::createMultiLangField(trim($category_name));
        $category_to_create->active = true;
        $category_to_create->id_parent = (int) $id_parent_category ? (int) $id_parent_category : (int) Configuration::get('PS_HOME_CATEGORY'); // Default parent is home for unknown category to create
        $category_link_rewrite = Tools::str2url($category_to_create->name[$default_language_id]);
        $category_to_create->link_rewrite = AdminImportController::createMultiLangField($category_link_rewrite);

        if (($field_error = $category_to_create->validateFields(UNFRIENDLY_ERROR, true)) !== true ||
            ($lang_field_error = $category_to_create->validateFieldsLang(UNFRIENDLY_ERROR, true)) !== true ||
            !$category_to_create->add()) {
            $this->errors[] = sprintf(
                $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                Tools::htmlentitiesUTF8($category_to_create->name[$default_language_id]),
                !empty($category_to_create->id) ? Tools::htmlentitiesUTF8($category_to_create->id) : 'null'
            );
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                    Db::getInstance()->getMsgError();
            }
        }
    }

    public function attributeImport($offset = false, $limit = false, &$crossStepsVariables = false, $validateOnly = false)
    {
        $default_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $groups = [];
        if ($crossStepsVariables !== false && array_key_exists('groups', $crossStepsVariables)) {
            $groups = $crossStepsVariables['groups'];
        }
        foreach (AttributeGroup::getAttributesGroups($default_language) as $group) {
            $groups[$group['name']] = (int) $group['id_attribute_group'];
        }

        $attributes = [];
        if ($crossStepsVariables !== false && array_key_exists('attributes', $crossStepsVariables)) {
            $attributes = $crossStepsVariables['attributes'];
        }
        foreach (ProductAttribute::getAttributes($default_language) as $attribute) {
            $attributes[$attribute['attribute_group'] . '_' . $attribute['name']] = (int) $attribute['id_attribute'];
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
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;

            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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
        } elseif (empty($info['shop'])) {
            $info['shop'] = implode($this->multiple_value_separator, Shop::getContextListShopID());
        }

        // Get shops for each attributes
        $info['shop'] = explode($this->multiple_value_separator, $info['shop']);

        $id_shop_list = [];
        if (is_array($info['shop'])) {
            foreach ($info['shop'] as $shop) {
                if (!empty($shop) && !is_numeric($shop)) {
                    $id_shop_list[] = Shop::getIdByName($shop);
                } elseif (!empty($shop)) {
                    $id_shop_list[] = $shop;
                }
            }
        }

        if (isset($info['id_product']) && $info['id_product']) {
            $product = new Product((int) $info['id_product'], false, $default_language);
        } elseif (Tools::getValue('match_ref') && isset($info['product_reference']) && $info['product_reference']) {
            $datas = Db::getInstance()->getRow('
				SELECT p.`id_product`
				FROM `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				WHERE p.`reference` = "' . pSQL($info['product_reference']) . '"
			', false);
            if (isset($datas['id_product']) && $datas['id_product']) {
                $product = new Product((int) $datas['id_product'], false, $default_language);
            } else {
                return;
            }
        } else {
            return;
        }

        $id_image = [];

        if (isset($info['image_url']) && $info['image_url']) {
            $info['image_url'] = explode($this->multiple_value_separator, $info['image_url']);

            if (is_array($info['image_url'])) {
                foreach ($info['image_url'] as $key => $url) {
                    $url = trim($url);
                    $product_has_images = (bool) Image::getImages($this->context->language->id, $product->id);

                    $image = new Image();
                    $image->id_product = (int) $product->id;
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
                            $this->warnings[] = $this->trans(
                                'Error copying image: %url%',
                                ['%url%' => Tools::htmlentitiesUTF8($url)],
                                'Admin.Advparameters.Notification'
                            );
                            $image->delete();
                        } else {
                            $id_image[] = (int) $image->id;
                        }
                        // until here
                    } else {
                        if (!$validateOnly) {
                            $this->warnings[] = $this->trans(
                                '%data% cannot be saved',
                                [
                                    '%data%' => ' (' . Tools::htmlentitiesUTF8($image->id_product) . ')',
                                ],
                                'Admin.Advparameters.Notification'
                            );
                        }
                        if ($field_error !== true || $lang_field_error !== true) {
                            $this->errors[] = ($field_error !== true ? $field_error : '')
                                . ($lang_field_error !== true ? $lang_field_error : '');
                        }
                    }
                }
            }
        } elseif (isset($info['image_position']) && $info['image_position']) {
            $info['image_position'] = explode($this->multiple_value_separator, $info['image_position']);

            if (is_array($info['image_position'])) {
                foreach ($info['image_position'] as $position) {
                    // choose images from product by position
                    $images = $product->getImages($default_language);

                    if ($images) {
                        foreach ($images as $row) {
                            if ($row['position'] == (int) $position) {
                                $id_image[] = (int) $row['id_image'];

                                break;
                            }
                        }
                    }
                    if (empty($id_image)) {
                        $this->warnings[] = sprintf(
                            $this->trans('No image was found for combination with id_product = %s and image position = %s.', [], 'Admin.Advparameters.Notification'),
                            Tools::htmlentitiesUTF8($product->id),
                            (int) $position
                        );
                    }
                }
            }
        }

        $id_attribute_group = 0;
        // groups
        $groups_attributes = [];
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
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
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
        $attributes_to_add = [];

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
                    if (!isset($attributes[$group . '_' . $attribute])) {
                        $id_attribute_group = $groups_attributes[$key]['id'];
                        $obj = new ProductAttribute();
                        // sets the proper id (corresponding to the right key)
                        $obj->id_attribute_group = $groups_attributes[$key]['id'];
                        $obj->name[$default_language] = str_replace('\n', '', str_replace('\r', '', $attribute));
                        $obj->position = (!$position && isset($groups[$group])) ? ProductAttribute::getHigherPosition($groups[$group]) + 1 : $position;

                        if (($field_error = $obj->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                            ($lang_field_error = $obj->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
                            if (!$validateOnly) {
                                $obj->add();
                                $obj->associateTo($id_shop_list);
                                $attributes[$group . '_' . $attribute] = $obj->id;
                            }
                        } else {
                            $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
                        }
                    }

                    $info['minimal_quantity'] = isset($info['minimal_quantity']) && $info['minimal_quantity'] ? (int) $info['minimal_quantity'] : 1;
                    $info['low_stock_threshold'] = empty($info['low_stock_threshold']) && '0' != $info['low_stock_threshold'] ? null : (int) $info['low_stock_threshold'];
                    $info['low_stock_alert'] = !empty($info['low_stock_alert']);

                    $info['wholesale_price'] = str_replace(',', '.', $info['wholesale_price']);
                    $info['price'] = str_replace(',', '.', $info['price']);
                    $info['ecotax'] = str_replace(',', '.', $info['ecotax']);
                    $info['weight'] = str_replace(',', '.', $info['weight']);
                    $info['available_date'] = Validate::isDate($info['available_date']) ? $info['available_date'] : null;

                    if (!Validate::isEan13($info['ean13'])) {
                        $this->warnings[] = $this->trans(
                            'EAN-13 "%ean13%" has incorrect value for product with ID %id%.',
                            [
                                '%ean13%' => Tools::htmlentitiesUTF8($info['ean13']),
                                '%id%' => Tools::htmlentitiesUTF8($product->id),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                        $info['ean13'] = '';
                    }

                    if ($info['default_on'] && !$validateOnly) {
                        $product->deleteDefaultAttributes();
                    }

                    // if a reference is specified for this product, get the associate id_product_attribute to UPDATE
                    if (isset($info['reference']) && !empty($info['reference'])) {
                        $id_product_attribute = Combination::getIdByReference($product->id, (string) ($info['reference']));

                        // updates the attribute
                        if ($id_product_attribute && !$validateOnly) {
                            // gets all the combinations of this product
                            $attribute_combinations = $product->getAttributeCombinations($default_language);
                            foreach ($attribute_combinations as $attribute_combination) {
                                if (in_array($id_product_attribute, $attribute_combination)) {
                                    // FIXME: ~3s/declinaison
                                    $product->updateAttribute(
                                        $id_product_attribute,
                                        (float) $info['wholesale_price'],
                                        (float) $info['price'],
                                        (float) $info['weight'],
                                        0,
                                        (Configuration::get('PS_USE_ECOTAX') ? (float) $info['ecotax'] : 0),
                                        $id_image,
                                        (string) $info['reference'],
                                        (string) $info['ean13'],
                                        ((bool) $info['default_on'] ? (bool) $info['default_on'] : null),
                                        '',
                                        (string) $info['upc'],
                                        (int) $info['minimal_quantity'],
                                        $info['available_date'],
                                        false,
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
                            (float) $info['wholesale_price'],
                            (float) $info['price'],
                            (float) $info['weight'],
                            0,
                            (Configuration::get('PS_USE_ECOTAX') ? (float) $info['ecotax'] : 0),
                            (int) $info['quantity'],
                            $id_image,
                            (string) $info['reference'],
                            0,
                            (string) $info['ean13'],
                            ((bool) $info['default_on'] ? (bool) $info['default_on'] : null),
                            '',
                            (string) $info['upc'],
                            (int) $info['minimal_quantity'],
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
                    if (isset($attributes[$group . '_' . $attribute])) {
                        $attributes_to_add[] = (int) $attributes[$group . '_' . $attribute];
                    }

                    // after insertion, we clean attribute position and group attribute position
                    if (!$validateOnly) {
                        $obj = new ProductAttribute();
                        $obj->cleanPositions((int) $id_attribute_group, false);
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
						DELETE FROM ' . _DB_PREFIX_ . 'product_attribute_combination
						WHERE id_product_attribute = ' . (int) $id_product_attribute);
                }

                foreach ($attributes_to_add as $attribute_to_add) {
                    Db::getInstance()->execute('
						INSERT IGNORE INTO ' . _DB_PREFIX_ . 'product_attribute_combination (id_attribute, id_product_attribute)
						VALUES (' . (int) $attribute_to_add . ',' . (int) $id_product_attribute . ')', false);
                }
            }

            // set advanced stock managment
            if (isset($info['advanced_stock_management'])) {
                if ($info['advanced_stock_management'] != 1 && $info['advanced_stock_management'] != 0) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management has incorrect value. Not set for product with id %id%.',
                        [
                            '%id%' => Tools::htmlentitiesUTF8($product->id),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $info['advanced_stock_management'] == 1) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot enable on product with id %id%.',
                        [
                            '%id%' => Tools::htmlentitiesUTF8($product->id),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    $product->setAdvancedStockManagement($info['advanced_stock_management']);
                }
                // automaticly disable depends on stock, if a_s_m set to disabled
                if (!$validateOnly && StockAvailable::dependsOnStock($product->id) == 1 && $info['advanced_stock_management'] == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, false, null, $id_product_attribute);
                }
            }

            // Check if warehouse exists
            if (isset($info['warehouse']) && $info['warehouse']) {
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, warehouse is not set on product with id %id%.',
                        ['%id%' => Tools::htmlentitiesUTF8($product->id)],
                        'Admin.Advparameters.Notification'
                    );
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
                        $this->warnings[] = $this->trans(
                            'Warehouse did not exist, cannot set on product %name%.',
                            [
                                '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language]),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            }

            // stock available
            if (isset($info['depends_on_stock'])) {
                if ($info['depends_on_stock'] != 0 && $info['depends_on_stock'] != 1) {
                    $this->warnings[] = $this->trans(
                        'Incorrect value for "Depends on stock" for product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language]),
                        ],
                        'Admin.Notifications.Error'
                    );
                } elseif ((!$info['advanced_stock_management'] || $info['advanced_stock_management'] == 0) && $info['depends_on_stock'] == 1) {
                    $this->warnings[] = $this->trans(
                        'Advanced stock management is not enabled, cannot set "Depends on stock" for product %name%',
                        [
                            '%name%' => Tools::htmlentitiesUTF8($product->name[$default_language]),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } elseif (!$validateOnly) {
                    StockAvailable::setProductDependsOnStock($product->id, $info['depends_on_stock'], null, $id_product_attribute);
                }

                // This code allows us to set qty and disable depends on stock
                if (isset($info['quantity'])) {
                    // if depends on stock and quantity, add quantity to stock
                    if ($info['depends_on_stock'] == 1) {
                        $stock_manager = StockManagerFactory::getManager();
                        $price = str_replace(',', '.', $info['wholesale_price']);
                        if ($price == '0') {
                            $price = 0.000001;
                        }
                        $price = round((float) $price, 6);
                        $warehouse = new Warehouse($info['warehouse']);
                        if (!$validateOnly && $stock_manager->addProduct((int) $product->id, $id_product_attribute, $warehouse, (int) $info['quantity'], 1, $price, true)) {
                            StockAvailable::synchronize((int) $product->id);
                        }
                    } elseif (!$validateOnly) {
                        if ($shop_is_feature_active) {
                            foreach ($id_shop_list as $shop) {
                                StockAvailable::setQuantity((int) $product->id, $id_product_attribute, (int) $info['quantity'], (int) $shop);
                            }
                        } else {
                            StockAvailable::setQuantity((int) $product->id, $id_product_attribute, (int) $info['quantity'], $this->context->shop->id);
                        }
                    }
                }
            } elseif (!$validateOnly) { // if not depends_on_stock set, use normal qty
                if ($shop_is_feature_active) {
                    foreach ($id_shop_list as $shop) {
                        StockAvailable::setQuantity((int) $product->id, $id_product_attribute, (int) $info['quantity'], (int) $shop);
                    }
                } else {
                    StockAvailable::setQuantity((int) $product->id, $id_product_attribute, (int) $info['quantity'], $this->context->shop->id);
                }
            }

            // assign combination id to already associated product suppliers
            $productSuppliers = ProductSupplier::getSupplierCollection($product->id);
            /** @var ProductSupplier $productSupplier */
            foreach ($productSuppliers as $productSupplier) {
                // skip if related combination supplier already exists
                if ((int) $productSupplier->id_product_attribute === (int) $id_product_attribute) {
                    continue;
                }

                $combinationSupplier = clone $productSupplier;
                $combinationSupplier->id = null;
                $combinationSupplier->id_product_attribute = $id_product_attribute;
                $combinationSupplier->add();
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

        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
        $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
        if (!Validate::isUnsignedId($id_lang)) {
            $id_lang = $default_language_id;
        }
        AdminImportController::setLocale();

        $shop_is_feature_active = Shop::isFeatureActive();
        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $customer = new Customer((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Customer::customerIdExistsStatic((int) $info['id'])) {
                $customer = new Customer((int) $info['id']);
            } else {
                $customer = new Customer();
            }
        }

        $customer_exist = false;
        $autodate = true;

        if (array_key_exists('id', $info) && (int) $info['id'] && Customer::customerIdExistsStatic((int) $info['id']) && Validate::isLoadedObject($customer)) {
            $current_id_customer = (int) $customer->id;
            $current_id_shop = (int) $customer->id_shop;
            $current_id_shop_group = (int) $customer->id_shop_group;
            $customer_exist = true;
            $customer_groups = $customer->getGroups();
            $addresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
        }

        // Group Importation
        if (isset($info['group']) && !empty($info['group'])) {
            foreach (explode($this->multiple_value_separator, $info['group']) as $key => $group) {
                $group = trim($group);
                if (empty($group)) {
                    continue;
                }
                $id_group = false;
                if (is_numeric($group)) {
                    $my_group = new Group((int) $group);
                    if (Validate::isLoadedObject($my_group)) {
                        $customer_groups[] = (int) $group;
                    }

                    continue;
                }
                $my_group = Group::searchByName($group);
                if (isset($my_group['id_group']) && $my_group['id_group']) {
                    $id_group = (int) $my_group['id_group'];
                }
                if (!$id_group) {
                    $my_group = new Group();
                    $my_group->name = [$id_lang => $group];
                    if ($id_lang != $default_language_id) {
                        $my_group->name = $my_group->name + [$default_language_id => $group];
                    }
                    $my_group->price_display_method = 1;
                    if (!$validateOnly) {
                        $my_group->add();
                        if (Validate::isLoadedObject($my_group)) {
                            $id_group = (int) $my_group->id;
                        }
                    }
                }
                if ($id_group) {
                    $customer_groups[] = (int) $id_group;
                }
            }
        } elseif (!empty($customer->id)) {
            $customer_groups = [0 => Configuration::get('PS_CUSTOMER_GROUP')];
        }

        if (isset($info['date_add']) && !empty($info['date_add'])) {
            $autodate = false;
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $customer);

        if ($customer->passwd) {
            $customer->passwd = $this->get('hashing')->hash($customer->passwd, _COOKIE_KEY_);
        }

        $id_shop_list = explode($this->multiple_value_separator, $customer->id_shop);
        $customers_shop = [];
        $customers_shop['shared'] = [];
        $default_shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        if ($shop_is_feature_active && is_array($id_shop_list)) {
            foreach ($id_shop_list as $id_shop) {
                if (empty($id_shop)) {
                    continue;
                }
                $shop = new Shop((int) $id_shop);
                $group_shop = $shop->getGroup();
                if ($group_shop->share_customer) {
                    if (!in_array($group_shop->id, $customers_shop['shared'])) {
                        $customers_shop['shared'][(int) $id_shop] = $group_shop->id;
                    }
                } else {
                    $customers_shop[(int) $id_shop] = $group_shop->id;
                }
            }
        } else {
            $default_shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
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
                $info['id_default_group'] = (int) $my_group['id_group'];
            }
        }
        $my_group = new Group($customer->id_default_group);
        if (!Validate::isLoadedObject($my_group)) {
            $customer->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
        }
        $customer_groups[] = (int) $customer->id_default_group;
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
                $customer->force_id = (bool) $force_ids;
                if ($id_shop == 'shared') {
                    foreach ($id_group as $key => $id) {
                        $customer->id_shop = (int) $key;
                        $customer->id_shop_group = (int) $id;
                        if ($customer_exist
                            && isset($current_id_customer) // @phpstan-ignore-line
                            && (
                                (isset($current_id_shop_group) && (int) $current_id_shop_group == (int) $id) // @phpstan-ignore-line
                                || (isset($current_id_shop) && in_array($current_id_shop, ShopGroup::getShopsFromGroup($id))) // @phpstan-ignore-line
                            )
                        ) {
                            $customer->id = (int) $current_id_customer;
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
                        if ($res && !$validateOnly) {
                            $customer->updateGroup($customer_groups);
                        }
                    }
                } else {
                    $customer->id_shop = $id_shop;
                    $customer->id_shop_group = $id_group;
                    if ($customer_exist && isset($current_id_customer, $current_id_shop) && (int) $id_shop == (int) $current_id_shop) { // @phpstan-ignore-line
                        $customer->id = (int) $current_id_customer;
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
                    if ($res && !$validateOnly) {
                        $customer->updateGroup($customer_groups);
                    }
                }
            }
        }

        unset($customer_groups);
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
                    [
                        Tools::htmlentitiesUTF8($info['email']),
                        !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                    ],
                    'Admin.Advparameters.Notification'
                );
            } else {
                $this->errors[] = $this->trans(
                    'Email address %1$s (ID: %2$s) cannot be saved.',
                    [
                        Tools::htmlentitiesUTF8($info['email']),
                        !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                    ],
                    'Admin.Advparameters.Notification'
                );
            }
            $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                Db::getInstance()->getMsgError();
        }
    }

    public function addressImport($offset = false, $limit = false, $validateOnly = false)
    {
        $this->receiveTab();
        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
        $handle = $this->openCsvFile($offset);
        if (!$handle) {
            return false;
        }

        AdminImportController::setLocale();

        $force_ids = Tools::getValue('forceIDs');

        $line_count = 0;
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $address = new Address((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Address::addressExists((int) $info['id'])) {
                $address = new Address((int) $info['id']);
            } else {
                $address = new Address();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $address);

        /** @var Address $address */
        if (!empty($address->country) && is_numeric($address->country)) {
            if (Country::getNameById((int) Configuration::get('PS_LANG_DEFAULT'), (int) $address->country)) {
                $address->id_country = (int) $address->country;
            }
        } elseif (!empty($address->country) && is_string($address->country)) {
            if ($id_country = Country::getIdByName(null, $address->country)) {
                $address->id_country = (int) $id_country;
            } else {
                $country = new Country();
                $country->active = true;
                $country->name = AdminImportController::createMultiLangField($address->country);
                $country->id_zone = 0; // Default zone for country to create
                $country->iso_code = Tools::strtoupper(Tools::substr($address->country, 0, 2)); // Default iso for country to create
                $country->contains_states = false; // Default value for country to create
                $lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                if (($field_error = $country->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $country->add()) {
                    $address->id_country = (int) $country->id;
                } else {
                    if (!$validateOnly) {
                        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            [
                                '%data%' => Tools::htmlentitiesUTF8($country->name[$default_language_id]),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . ($lang_field_error !== true ? $lang_field_error : '') .
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->state) && is_numeric($address->state)) {
            if (State::getNameById((int) $address->state)) {
                $address->id_state = (int) $address->state;
            }
        } elseif (isset($address->state) && is_string($address->state) && !empty($address->state)) {
            if ($id_state = State::getIdByName($address->state)) {
                $address->id_state = (int) $id_state;
            } else {
                $state = new State();
                $state->active = true;
                $state->name = $address->state;
                $state->id_country = isset($country->id) ? (int) $country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                $state->iso_code = Tools::strtoupper(Tools::substr($address->state, 0, 2)); // Default iso for state to create
                $state->tax_behavior = 0;
                if (($field_error = $state->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $state->add()) {
                    $address->id_state = (int) $state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            [
                                '%data%' => Tools::htmlentitiesUTF8($state->name),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
                            [
                                Tools::htmlentitiesUTF8($address->customer_email),
                                Tools::htmlentitiesUTF8(Db::getInstance()->getMsgError()),
                                !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be saved',
                            [
                                Tools::htmlentitiesUTF8($address->customer_email),
                                Tools::htmlentitiesUTF8(Db::getInstance()->getMsgError()),
                                !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null',
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                $this->errors[] = $this->trans('"%email%" is not a valid email address.', ['%email%' => Tools::htmlentitiesUTF8($address->customer_email)], 'Admin.Advparameters.Notification');

                return;
            }
        } elseif (!empty($address->id_customer)) {
            if (Customer::customerIdExistsStatic((int) $address->id_customer)) {
                $customer = new Customer((int) $address->id_customer);

                // a customer could exists in different shop
                $customer_list = Customer::getCustomersByEmail($customer->email);

                if (count($customer_list) == 0) {
                    if ($validateOnly) {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be validated',
                            [
                                Tools::htmlentitiesUTF8($customer->email),
                                Tools::htmlentitiesUTF8(Db::getInstance()->getMsgError()),
                                (int) $address->id_customer,
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $this->errors[] = $this->trans(
                            '%1$s does not exist in database %2$s (ID: %3$s), and therefore cannot be saved',
                            [
                                Tools::htmlentitiesUTF8($customer->email),
                                Tools::htmlentitiesUTF8(Db::getInstance()->getMsgError()),
                                (int) $address->id_customer,
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                }
            } else {
                if ($validateOnly) {
                    $this->errors[] = $this->trans(
                        'The customer ID #%d does not exist in the database, and therefore cannot be validated.',
                        [
                            Tools::htmlentitiesUTF8($address->id_customer),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                } else {
                    $this->errors[] = $this->trans(
                        'The customer ID #%d does not exist in the database, and therefore cannot be saved.',
                        [
                            Tools::htmlentitiesUTF8($address->id_customer),
                        ],
                        'Admin.Advparameters.Notification'
                    );
                }
            }
        } else {
            $customer_list = [];
            $address->id_customer = 0;
        }

        if (isset($address->manufacturer) && is_numeric($address->manufacturer) && Manufacturer::manufacturerExists((int) $address->manufacturer)) {
            $address->id_manufacturer = (int) $address->manufacturer;
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
                    $address->id_manufacturer = (int) $manufacturer->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                            Tools::htmlentitiesUTF8($manufacturer->name),
                            !empty($manufacturer->id) ? Tools::htmlentitiesUTF8($manufacturer->id) : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($address->supplier) && is_numeric($address->supplier) && Supplier::supplierExists((int) $address->supplier)) {
            $address->id_supplier = (int) $address->supplier;
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
                    $address->id_supplier = (int) $supplier->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                            $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                            Tools::htmlentitiesUTF8($supplier->name),
                            !empty($supplier->id) ? Tools::htmlentitiesUTF8($supplier->id) : 'null'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        $res = false;
        if (($field_error = $address->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $address->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            $address->force_id = (bool) $force_ids;

            if (isset($customer_list) && count($customer_list) > 0) {
                $filter_list = [];
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
                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    Tools::htmlentitiesUTF8($info['alias']),
                    !empty($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null'
                );
            }
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $manufacturer = new Manufacturer((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Manufacturer::existsInDatabase((int) $info['id'], 'manufacturer')) {
                $manufacturer = new Manufacturer((int) $info['id']);
            } else {
                $manufacturer = new Manufacturer();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $manufacturer);

        /** @var Manufacturer $manufacturer */
        $res = false;
        if (($field_error = $manufacturer->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $manufacturer->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            if ($manufacturer->id && $manufacturer->manufacturerExists($manufacturer->id)) {
                $res = ($validateOnly || $manufacturer->update());
            }
            $manufacturer->force_id = (bool) $force_ids;
            if (!$res) {
                $res = ($validateOnly || $manufacturer->add());
            }

            //copying images of manufacturer
            if (!$validateOnly && isset($manufacturer->image) && !empty($manufacturer->image)) {
                if (!AdminImportController::copyImg($manufacturer->id, null, $manufacturer->image, 'manufacturers', !$regenerate)) {
                    $this->warnings[] = $manufacturer->image . ' ' . $this->trans('cannot be copied.', [], 'Admin.Advparameters.Notification');
                }
            }

            if (!$validateOnly && $res) {
                // Associate supplier to group shop
                if ($shop_is_feature_active && $manufacturer->shop) {
                    Db::getInstance()->execute('
						DELETE FROM ' . _DB_PREFIX_ . 'manufacturer_shop
						WHERE id_manufacturer = ' . (int) $manufacturer->id);
                    $manufacturer->shop = explode($this->multiple_value_separator, $manufacturer->shop);
                    $shops = [];
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
                $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                    !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID'
                );
            }
            if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

                continue;
            }

            $info = AdminImportController::getMaskedRow($line);

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $supplier = new Supplier((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Supplier::existsInDatabase((int) $info['id'], 'supplier')) {
                $supplier = new Supplier((int) $info['id']);
            } else {
                $supplier = new Supplier();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $supplier);

        /** @var Supplier $supplier */
        if (($field_error = $supplier->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $supplier->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            $res = false;
            if ($supplier->id && $supplier->supplierExists($supplier->id)) {
                $res = ($validateOnly || $supplier->update());
            }
            $supplier->force_id = (bool) $force_ids;
            if (!$res) {
                $res = ($validateOnly || $supplier->add());
            }

            //copying images of suppliers
            if (!$validateOnly && isset($supplier->image) && !empty($supplier->image)) {
                if (!AdminImportController::copyImg($supplier->id, null, $supplier->image, 'suppliers', !$regenerate)) {
                    $this->warnings[] = $supplier->image . ' ' . $this->trans('cannot be copied.', [], 'Admin.Advparameters.Notification');
                }
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    !empty($info['name']) ? Tools::safeOutput($info['name']) : 'No Name',
                    !empty($info['id']) ? Tools::safeOutput($info['id']) : 'No ID'
                );
            } elseif (!$validateOnly) {
                // Associate supplier to group shop
                if ($shop_is_feature_active && $supplier->shop) {
                    Db::getInstance()->execute('
						DELETE FROM ' . _DB_PREFIX_ . 'supplier_shop
						WHERE id_supplier = ' . (int) $supplier->id);
                    $supplier->shop = explode($this->multiple_value_separator, $supplier->shop);
                    $shops = [];
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
            $this->errors[] = $this->trans('Supplier is invalid', [], 'Admin.Advparameters.Notification') . ' (' . Tools::htmlentitiesUTF8($supplier->name) . ')';
            $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
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
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $alias = new Alias((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Alias::existsInDatabase((int) $info['id'], 'alias')) {
                $alias = new Alias((int) $info['id']);
            } else {
                $alias = new Alias();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $alias);

        /** @var Alias $alias */
        $res = false;
        if (($field_error = $alias->validateFields(UNFRIENDLY_ERROR, true)) === true &&
            ($lang_field_error = $alias->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true) {
            if ($alias->id && $alias->aliasExists($alias->id)) {
                $res = ($validateOnly || $alias->update());
            }
            $alias->force_id = (bool) $force_ids;
            if (!$res) {
                $res = ($validateOnly || $alias->add());
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    Tools::htmlentitiesUTF8($info['name']),
                    (isset($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null')
                );
            }
        } else {
            $this->errors[] = $this->trans('Alias is invalid', [], 'Admin.Advparameters.Notification') . ' (' . Tools::htmlentitiesUTF8($alias->name) . ')';
            $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
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
        for ($current_line = 0; ($line = fgetcsv($handle, MAX_LINE_SIZE, $this->separator)) && (!$limit || $current_line < $limit); ++$current_line) {
            ++$line_count;
            if ($this->convert) {
                $line = $this->utf8EncodeArray($line);
            }

            if (count($line) == 1 && $line[0] == null) {
                $this->warnings[] = $this->trans('There is an empty row in the file that won\'t be imported.', [], 'Admin.Advparameters.Notification');

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

        if ($force_ids && isset($info['id']) && (int) $info['id']) {
            $store = new Store((int) $info['id']);
        } else {
            if (array_key_exists('id', $info) && (int) $info['id'] && Store::existsInDatabase((int) $info['id'], 'store')) {
                $store = new Store((int) $info['id']);
            } else {
                $store = new Store();
            }
        }

        AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $store);

        /** @var Store $store */
        if (isset($store->image) && !empty($store->image)) {
            if (!(AdminImportController::copyImg($store->id, null, $store->image, 'stores', !$regenerate))) {
                $this->warnings[] = $store->image . ' ' . $this->trans('cannot be copied.', [], 'Admin.Advparameters.Notification');
            }
        }

        if (is_array($store->hours)) {
            $newHours = [];
            foreach ($store->hours as $hour) {
                $newHours[] = [$hour];
            }
            $store->hours = json_encode($newHours);
        }

        if (isset($store->country) && is_numeric($store->country)) {
            if (Country::getNameById((int) Configuration::get('PS_LANG_DEFAULT'), (int) $store->country)) {
                $store->id_country = (int) $store->country;
            }
        } elseif (isset($store->country) && is_string($store->country) && !empty($store->country)) {
            if ($id_country = Country::getIdByName(null, $store->country)) {
                $store->id_country = (int) $id_country;
            } else {
                $country = new Country();
                $country->active = true;
                $country->name = AdminImportController::createMultiLangField($store->country);
                $country->id_zone = 0; // Default zone for country to create
                $country->iso_code = Tools::strtoupper(Tools::substr($store->country, 0, 2)); // Default iso for country to create
                $country->contains_states = false; // Default value for country to create
                $lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true);
                if (($field_error = $country->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $country->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $country->add()) {
                    $store->id_country = (int) $country->id;
                } else {
                    if (!$validateOnly) {
                        $default_language_id = (int) Configuration::get('PS_LANG_DEFAULT');
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            [
                                '%data%' => Tools::htmlentitiesUTF8($country->name[$default_language_id]),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . ($lang_field_error !== true ? $lang_field_error : '') .
                            Db::getInstance()->getMsgError();
                    }
                }
            }
        }

        if (isset($store->state) && is_numeric($store->state)) {
            if (State::getNameById((int) $store->state)) {
                $store->id_state = (int) $store->state;
            }
        } elseif (isset($store->state) && is_string($store->state) && !empty($store->state)) {
            if ($id_state = State::getIdByName($store->state)) {
                $store->id_state = (int) $id_state;
            } else {
                $state = new State();
                $state->active = true;
                $state->name = $store->state;
                $state->id_country = isset($country->id) ? (int) $country->id : 0;
                $state->id_zone = 0; // Default zone for state to create
                $state->iso_code = Tools::strtoupper(Tools::substr($store->state, 0, 2)); // Default iso for state to create
                $state->tax_behavior = 0;
                if (($field_error = $state->validateFields(UNFRIENDLY_ERROR, true)) === true &&
                    ($lang_field_error = $state->validateFieldsLang(UNFRIENDLY_ERROR, true)) === true &&
                    !$validateOnly && // Do not move this condition: previous tests should be played always, but next ->add() test should not be played in validateOnly mode
                    $state->add()) {
                    $store->id_state = (int) $state->id;
                } else {
                    if (!$validateOnly) {
                        $this->errors[] = $this->trans(
                            '%data% cannot be saved',
                            [
                                '%data%' => Tools::htmlentitiesUTF8($state->name),
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    }
                    if ($field_error !== true || isset($lang_field_error) && $lang_field_error !== true) {
                        $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '') .
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
            $store->force_id = (bool) $force_ids;
            if (!$res) {
                $res = $validateOnly ? $validateOnly : $store->add();
            }

            if (!$res) {
                $this->errors[] = Db::getInstance()->getMsgError() . ' ' . sprintf(
                    $this->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    Tools::htmlentitiesUTF8($info['name']),
                    (isset($info['id']) ? Tools::htmlentitiesUTF8($info['id']) : 'null')
                );
            }
        } else {
            $id_lang = Language::getIdByIso(Tools::getValue('iso_lang'));
            $this->errors[] = $this->trans('Store is invalid', [], 'Admin.Advparameters.Notification') . ' (' . Tools::htmlentitiesUTF8($store->name[$id_lang]) . ')';
            $this->errors[] = ($field_error !== true ? $field_error : '') . (isset($lang_field_error) && $lang_field_error !== true ? $lang_field_error : '');
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
            ++$line_count;
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
        if (array_key_exists('id', $info) && (int) $info['id'] && SupplyOrder::exists((int) $info['id'])) {
            $supply_order = new SupplyOrder((int) $info['id']);
        } elseif (array_key_exists('reference', $info) && $info['reference'] && SupplyOrder::exists(pSQL($info['reference']))) {
            $supply_order = SupplyOrder::getSupplyOrderByReference(pSQL($info['reference']));
        } else { // new supply order
            $supply_order = new SupplyOrder();
        }

        // gets parameters
        $id_supplier = (int) $info['id_supplier'];
        $id_lang = (int) $info['id_lang'];
        $id_warehouse = (int) $info['id_warehouse'];
        $id_currency = (int) $info['id_currency'];
        $reference = pSQL($info['reference']);
        $date_delivery_expected = pSQL($info['date_delivery_expected']);
        $discount_rate = (float) $info['discount_rate'];
        $is_template = (bool) $info['is_template'];

        $error = '';
        // checks parameters
        if (!Supplier::supplierExists($id_supplier)) {
            $error = $this->trans('Supplier ID (%id%) is not valid (at line %line%).', ['%id%' => $id_supplier, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (!Language::getLanguage($id_lang)) {
            $error = $this->trans('Lang ID (%id%) is not valid (at line %line%).', ['%id%' => $id_lang, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (!Warehouse::exists($id_warehouse)) {
            $error = $this->trans('Warehouse ID (%id%) is not valid (at line %line%).', ['%id%' => $id_warehouse, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (!Currency::getCurrency($id_currency)) {
            $error = $this->trans('Currency ID (%id%) is not valid (at line %line%).', ['%id%' => $id_currency, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (empty($supply_order->reference) && SupplyOrder::exists($reference)) {
            $error = $this->trans('Reference (%ref%) already exists (at line %line%).', ['%ref%' => $reference, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (!empty($supply_order->reference) && ($supply_order->reference != $reference && SupplyOrder::exists($reference))) {
            $error = $this->trans('Reference (%ref%) already exists (at line %line%).', ['%ref%' => $reference, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }
        if (!Validate::isDateFormat($date_delivery_expected)) {
            $error = $this->trans('Date format (%date%) is not valid (at line %line%). It should be: %date_format%.', ['%date%' => $date_delivery_expected, '%line%' => $current_line + 1, '%date_format%' => $this->trans('YYYY-MM-DD', [], 'Admin.Advparameters.Notification')], 'Admin.Advparameters.Notification');
        } elseif (new DateTime($date_delivery_expected) <= new DateTime('yesterday')) {
            $error = $this->trans('Date (%date%) cannot be in the past (at line %line%). Format: %date_format%.', ['%date%' => $date_delivery_expected, '%line%' => $current_line + 1, '%date_format%' => $this->trans('YYYY-MM-DD', [], 'Admin.Advparameters.Notification')], 'Admin.Advparameters.Notification');
        }
        if ($discount_rate < 0 || $discount_rate > 100) {
            $error = $this->trans(
                'Discount rate (%rate%) is not valid (at line %line%). %format%.',
                ['%rate%' => $discount_rate, '%line%' => $current_line + 1, '%format%' => $this->trans('Format: Between 0 and 100', [], 'Admin.Advparameters.Notification')],
                'Admin.Advparameters.Notification'
            );
        }
        if ($supply_order->id > 0 && !$supply_order->isEditable()) {
            $error = $this->trans('Supply Order (%id%) is not editable (at line %line%).', ['%id%' => $supply_order->id, '%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
        }

        // if no errors, sets supply order
        if (empty($error)) {
            // adds parameters
            $info['id_ref_currency'] = (int) Currency::getDefaultCurrency()->id;
            $info['supplier_name'] = pSQL(Supplier::getNameById($id_supplier));
            if ($supply_order->id > 0) {
                $info['id_supply_order_state'] = (int) $supply_order->id_supply_order_state;
                $info['id'] = (int) $supply_order->id;
            } else {
                $info['id_supply_order_state'] = 1;
            }

            // sets parameters
            AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $supply_order);

            /** @var SupplyOrder $supply_order */
            if ((int) $supply_order->id && ($supply_order->exists((int) $supply_order->id) || $supply_order->exists($supply_order->reference))) {
                $res = ($validateOnly || $supply_order->update());
            } else {
                $supply_order->force_id = (bool) $force_ids;
                $res = ($validateOnly || $supply_order->add());
            }

            // errors
            if (!$res) {
                $this->errors[] = $this->trans('Supply Order could not be saved (at line %line%).', ['%line%' => $current_line + 1], 'Admin.Advparameters.Notification');
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

        $products = [];
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
            ++$line_count;
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
            $this->errors[] = sprintf(
                $this->trans('Supply Order (%s) could not be loaded (at line %d).', [], 'Admin.Advparameters.Notification'),
                Tools::htmlentitiesUTF8($info['supply_order_reference']),
                $current_line + 1
            );

            return;
        }

        // sets parameters
        $id_product = (int) $info['id_product'];
        if (empty($info['id_product_attribute'])) {
            $info['id_product_attribute'] = 0;
        }
        $id_product_attribute = (int) $info['id_product_attribute'];
        $unit_price_te = (float) $info['unit_price_te'];
        $quantity_expected = (int) $info['quantity_expected'];
        $discount_rate = (float) $info['discount_rate'];
        $tax_rate = (float) $info['tax_rate'];

        // checks if one product/attribute is there only once
        if (isset($products[$id_product][$id_product_attribute])) {
            $this->errors[] = sprintf(
                $this->trans('Product/Attribute (%d/%d) cannot be added twice (at line %d).', [], 'Admin.Advparameters.Notification'),
                $id_product,
                $id_product_attribute,
                $current_line + 1
            );
        } else {
            $products[$id_product][$id_product_attribute] = $quantity_expected;
        }

        // checks parameters
        $supplier_reference = ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $supply_order->id_supplier);
        if (false === $supplier_reference) {
            $this->errors[] = sprintf(
                $this->trans('Product (%d/%d) is not available for this order (at line %d).', [], 'Admin.Advparameters.Notification'),
                $id_product,
                $id_product_attribute,
                $current_line + 1
            );
        }
        if ($unit_price_te < 0) {
            $this->errors[] = sprintf($this->trans('Unit Price (tax excl.) (%d) is not valid (at line %d).', [], 'Admin.Advparameters.Notification'), $unit_price_te, $current_line + 1);
        }
        if ($quantity_expected < 0) {
            $this->errors[] = sprintf($this->trans('Quantity Expected (%d) is not valid (at line %d).', [], 'Admin.Advparameters.Notification'), $quantity_expected, $current_line + 1);
        }
        if ($discount_rate < 0 || $discount_rate > 100) {
            $this->errors[] = sprintf(
                $this->trans('Discount rate (%d) is not valid (at line %d). %s.', [], 'Admin.Advparameters.Notification'),
                $discount_rate,
                $current_line + 1,
                $this->trans('Format: Between 0 and 100', [], 'Admin.Advparameters.Notification')
            );
        }
        if ($tax_rate < 0 || $tax_rate > 100) {
            $this->errors[] = sprintf(
                $this->trans('Quantity Expected (%d) is not valid (at line %d).', [], 'Admin.Advparameters.Notification'),
                $tax_rate,
                $current_line + 1,
                $this->trans('Format: Between 0 and 100', [], 'Admin.Advparameters.Notification')
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
            AdminImportController::arrayWalk($info, ['AdminImportController', 'fillInfo'], $supply_order_detail);

            /* @var SupplyOrderDetail $supply_order_detail */

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
            $query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product AND id_product_attribute = ' . (int) $id_product_attribute);
            $query->where('p.id_product = ' . (int) $id_product);
            $query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            $product_infos = $res['0'];

            $supply_order_detail->reference = $product_infos['reference'];
            $supply_order_detail->ean13 = $product_infos['ean13'];
            $supply_order_detail->upc = $product_infos['upc'];
            $supply_order_detail->force_id = (bool) $force_ids;
            if (!$validateOnly) {
                $supply_order_detail->add();
                $supply_order->update();
            }
            unset($supply_order_detail);
        }
    }

    public function utf8EncodeArray($array)
    {
        return is_array($array) ? array_map('utf8_encode', $array) : utf8_encode($array);
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

        return ($b < $a) ? 1 : -1;
    }

    protected function openCsvFile($offset = false)
    {
        $file = $this->excelToCsvFile(Tools::getValue('csv'));
        $handle = false;
        if (is_file($file) && is_readable($file)) {
            if (!mb_check_encoding(file_get_contents($file), 'UTF-8')) {
                $this->convert = true;
            }
            $handle = fopen($file, 'rb');
        }

        if (!$handle) {
            $this->errors[] = $this->trans('Cannot read the .CSV file', [], 'Admin.Advparameters.Notification');

            return null; // error case
        }

        AdminImportController::rewindBomAware($handle);

        $toSkip = (int) Tools::getValue('skip');
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
            $dest_file = AdminImportController::getPath((string) (preg_replace('/\.{2,}/', '.', $filename)));
        } else {
            $csv_folder = AdminImportController::getPath();
            $excel_folder = $csv_folder . 'csvfromexcel/';
            $info = pathinfo($filename);
            $csv_name = basename($filename, '.' . $info['extension']) . '.csv';
            $dest_file = $excel_folder . $csv_name;

            if (!is_dir($excel_folder)) {
                mkdir($excel_folder);
            }

            if (!is_file($dest_file)) {
                $reader_excel = IOFactory::createReaderForFile($csv_folder . $filename);
                $reader_excel->setReadDataOnly(true);
                $excel_file = $reader_excel->load($csv_folder . $filename);

                /** @var Csv $csv_writer */
                $csv_writer = IOFactory::createWriter($excel_file, 'Csv');

                $csv_writer->setSheetIndex(0);
                $csv_writer->setDelimiter(';');
                $csv_writer->save($dest_file);
            }
        }

        return $dest_file;
    }

    protected function truncateTables($case)
    {
        switch ((int) $case) {
            case $this->entities[$this->trans('Categories', [], 'Admin.Global')]:
                Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category`
					WHERE id_category NOT IN (' . (int) Configuration::get('PS_HOME_CATEGORY') .
                    ', ' . (int) Configuration::get('PS_ROOT_CATEGORY') . ')');
                Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_lang`
					WHERE id_category NOT IN (' . (int) Configuration::get('PS_HOME_CATEGORY') .
                    ', ' . (int) Configuration::get('PS_ROOT_CATEGORY') . ')');
                Db::getInstance()->execute('
					DELETE FROM `' . _DB_PREFIX_ . 'category_shop`
					WHERE `id_category` NOT IN (' . (int) Configuration::get('PS_HOME_CATEGORY') .
                    ', ' . (int) Configuration::get('PS_ROOT_CATEGORY') . ')');
                Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'category` AUTO_INCREMENT = 3');
                foreach (scandir(_PS_CAT_IMG_DIR_, SCANDIR_SORT_NONE) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_CAT_IMG_DIR_ . $d);
                    }
                }

                break;
            case $this->entities[$this->trans('Products', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'feature_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'category_product`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_tag`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'image_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'specific_price_priority`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_carrier`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'cart_product`');
                if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'favorite_product\' '))) { //check if table exist
                    Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'favorite_product`');
                }
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attachment`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_country_tax`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_download`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_group_reduction_cache`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_sale`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'warehouse_product_location`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_available`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'stock_mvt`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customization_field`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supply_order_detail`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'pack`');
                Image::deleteAllImages(_PS_PRODUCT_IMG_DIR_);
                if (!file_exists(_PS_PRODUCT_IMG_DIR_)) {
                    mkdir(_PS_PRODUCT_IMG_DIR_);
                }

                break;
            case $this->entities[$this->trans('Combinations', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_group_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_shop`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_combination`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_image`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'product_attribute_lang`');
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'stock_available` WHERE id_product_attribute != 0');

                break;
            case $this->entities[$this->trans('Customers', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'customer`');

                break;
            case $this->entities[$this->trans('Addresses', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'address`');

                break;
            case $this->entities[$this->trans('Brands', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'manufacturer_shop`');
                foreach (scandir(_PS_MANU_IMG_DIR_, SCANDIR_SORT_NONE) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_MANU_IMG_DIR_ . $d);
                    }
                }

                break;
            case $this->entities[$this->trans('Suppliers', [], 'Admin.Global')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_lang`');
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'supplier_shop`');
                foreach (scandir(_PS_SUPP_IMG_DIR_, SCANDIR_SORT_NONE) as $d) {
                    if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d)) {
                        unlink(_PS_SUPP_IMG_DIR_ . $d);
                    }
                }

                break;
            case $this->entities[$this->trans('Alias', [], 'Admin.Shopparameters.Feature')]:
                Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'alias`');

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
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        if (Tools::isSubmit('import')) {
            $this->importExecuted = true;
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
                    $mime_types = ['csv' => 'text/csv'];

                    if (isset($mime_types[$b_name])) {
                        $mime_type = $mime_types[$b_name];
                    } else {
                        $mime_type = 'application/octet-stream';
                    }

                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Transfer-Encoding: binary');
                    header('Content-Type: ' . $mime_type);
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

    public function importByGroups($offset = false, $limit = false, &$results = null, $validateOnly = false, $moreStep = 0)
    {
        // Check if the CSV file exist
        if (Tools::getValue('csv')) {
            $shop_is_feature_active = Shop::isFeatureActive();
            // If i am a superadmin, i can truncate table (ONLY IF OFFSET == 0 or false and NOT FOR VALIDATION MODE!)
            if (!$offset && !$moreStep && !$validateOnly && (($shop_is_feature_active && $this->context->employee->isSuperAdmin()) || !$shop_is_feature_active) && Tools::getValue('truncate')) {
                $this->truncateTables((int) Tools::getValue('entity'));
            }
            $import_type = false;
            $doneCount = 0;
            /** @var array<string> $moreStepLabels */
            $moreStepLabels = [];
            // Sometime, import will use registers to memorize data across all elements to import (for trees, or else).
            // Since import is splitted in multiple ajax calls, we must keep these data across all steps of the full import.
            $crossStepsVariables = [];
            if ($crossStepsVars = Tools::getValue('crossStepsVars')) {
                $crossStepsVars = json_decode($crossStepsVars, true);
                if (count($crossStepsVars) > 0) {
                    $crossStepsVariables = $crossStepsVars;
                }
            }
            Db::getInstance()->disableCache();
            $clearCache = false;
            switch ((int) Tools::getValue('entity')) {
                case $this->entities[$import_type = $this->trans('Categories', [], 'Admin.Global')]:
                    $doneCount += $this->categoryImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    if ($doneCount < $limit && !$validateOnly) {
                        /* Import has finished, we can regenerate the categories nested tree */
                        Category::regenerateEntireNtree();
                    }
                    $clearCache = true;

                    break;
                case $this->entities[$import_type = $this->trans('Products', [], 'Admin.Global')]:
                    if (!defined('PS_MASS_PRODUCT_CREATION')) {
                        define('PS_MASS_PRODUCT_CREATION', true);
                    }
                    $moreStepLabels = [$this->trans('Linking Accessories...', [], 'Admin.Advparameters.Notification')];
                    $doneCount += $this->productImport($offset, $limit, $crossStepsVariables, $validateOnly, $moreStep);
                    $clearCache = true;

                    break;
                case $this->entities[$import_type = $this->trans('Customers', [], 'Admin.Global')]:
                    $doneCount += $this->customerImport($offset, $limit, $validateOnly);

                    break;
                case $this->entities[$import_type = $this->trans('Addresses', [], 'Admin.Global')]:
                    $doneCount += $this->addressImport($offset, $limit, $validateOnly);

                    break;
                case $this->entities[$import_type = $this->trans('Combinations', [], 'Admin.Global')]:
                    $doneCount += $this->attributeImport($offset, $limit, $crossStepsVariables, $validateOnly);
                    $clearCache = true;

                    break;
                case $this->entities[$import_type = $this->trans('Brands', [], 'Admin.Global')]:
                    $doneCount += $this->manufacturerImport($offset, $limit, $validateOnly);
                    $clearCache = true;

                    break;
                case $this->entities[$import_type = $this->trans('Suppliers', [], 'Admin.Global')]:
                    $doneCount += $this->supplierImport($offset, $limit, $validateOnly);
                    $clearCache = true;

                    break;
                case $this->entities[$import_type = $this->trans('Alias', [], 'Admin.Shopparameters.Feature')]:
                    $doneCount += $this->aliasImport($offset, $limit, $validateOnly);

                    break;
                case $this->entities[$import_type = $this->trans('Store contacts', [], 'Admin.Advparameters.Feature')]:
                    $doneCount += $this->storeContactImport($offset, $limit, $validateOnly);
                    $clearCache = true;

                    break;
            }

            // @since 1.5.0
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                switch ((int) Tools::getValue('entity')) {
                    case $this->entities[$import_type = $this->trans('Supply Orders', [], 'Admin.Advparameters.Feature')]:
                        $doneCount += $this->supplyOrdersImport($offset, $limit, $validateOnly);
                        break;
                    case $this->entities[$import_type = $this->trans('Supply Order Details', [], 'Admin.Advparameters.Feature')]:
                        $doneCount += $this->supplyOrdersDetailsImport($offset, $limit, $crossStepsVariables, $validateOnly);
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
                            ++$count;
                        }
                        $results['totalCount'] = $count;
                    }
                    $this->closeCsvFile($handle);
                }
                if (!$results['isFinished'] || (!$validateOnly && ($moreStep < count($moreStepLabels)))) {
                    // Since we'll have to POST this array from ajax for the next call, we should care about it size.
                    $nextPostSize = mb_strlen(json_encode($crossStepsVariables));
                    $results['crossStepsVariables'] = $crossStepsVariables;
                    $results['nextPostSize'] = $nextPostSize + (1024 * 64); // 64KB more for the rest of the POST query.
                    $results['postSizeLimit'] = Tools::getMaxUploadSize();
                }
                if ($results['isFinished'] && !$validateOnly && ($moreStep < count($moreStepLabels))) {
                    $results['oneMoreStep'] = $moreStep + 1;
                    $results['moreStepLabel'] = $moreStepLabels[$moreStep];
                }
            }

            if ($import_type !== false) {
                $log_message = sprintf($this->trans('%s import', [], 'Admin.Advparameters.Notification'), $import_type);
                if ($offset !== false && $limit !== false) {
                    $log_message .= ' ' . sprintf($this->trans('(from %s to %s)', [], 'Admin.Advparameters.Notification'), $offset, $limit);
                }
                if (Tools::getValue('truncate')) {
                    $log_message .= ' ' . $this->trans('with truncate', [], 'Admin.Advparameters.Notification');
                }
                PrestaShopLogger::addLog($log_message, 1, null, $import_type, null, true, (int) $this->context->employee->id);
            }

            Db::getInstance()->enableCache();
        } else {
            $this->errors[] = $this->trans('To proceed, please upload a file first.', [], 'Admin.Advparameters.Notification');
        }
    }

    public static function setLocale()
    {
        $iso_lang = trim(Tools::getValue('iso_lang'));
        setlocale(LC_COLLATE, strtolower($iso_lang) . '_' . strtoupper($iso_lang) . '.UTF-8');
        setlocale(LC_CTYPE, strtolower($iso_lang) . '_' . strtoupper($iso_lang) . '.UTF-8');
    }

    protected function addProductWarning($product_name, $product_id = null, $message = '')
    {
        $this->warnings[] = Tools::htmlentitiesUTF8(
            $product_name
            . (isset($product_id) ? ' (ID ' . $product_id . ')' : '')
            . ' '
            . $message
        );
    }

    public function ajaxProcessSaveImportMatchs()
    {
        if ($this->access('edit')) {
            $match = implode('|', Tools::getValue('type_value'));
            Db::getInstance()->execute('INSERT IGNORE INTO  `' . _DB_PREFIX_ . 'import_match` (
										`id_import_match` ,
										`name` ,
										`match`,
										`skip`
										)
										VALUES (
										NULL ,
										\'' . pSQL(Tools::getValue('newImportMatchs')) . '\',
										\'' . pSQL($match) . '\',
										\'' . pSQL(Tools::getValue('skip')) . '\'
										)', false);

            die('{"id" : "' . Db::getInstance()->Insert_ID() . '"}');
        }
    }

    public function ajaxProcessLoadImportMatchs()
    {
        if ($this->access('edit')) {
            $return = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'import_match` WHERE `id_import_match` = '
                . (int) Tools::getValue('idImportMatchs'), true, false);
            die('{"id" : "' . $return[0]['id_import_match'] . '", "matchs" : "' . $return[0]['match'] . '", "skip" : "'
                . $return[0]['skip'] . '"}');
        }
    }

    public function ajaxProcessDeleteImportMatchs()
    {
        if ($this->access('edit')) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'import_match` WHERE `id_import_match` = '
                . (int) Tools::getValue('idImportMatchs'), false);
            die;
        }
    }

    public static function getPath($file = '')
    {
        return _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $file;
    }

    public function ajaxProcessImport()
    {
        $offset = (int) Tools::getValue('offset');
        $limit = (int) Tools::getValue('limit');
        $validateOnly = ((int) Tools::getValue('validateOnly') == 1);
        $moreStep = (int) Tools::getValue('moreStep');

        $results = [];
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

        if (!$validateOnly && (bool) $results['isFinished'] && !isset($results['oneMoreStep']) && (bool) Tools::getValue('sendemail')) {
            // Mail::Send() can sometimes throw an error...
            try {
                unset($this->context->cookie->csv_selected); // remove CSV selection file if finished with no error.

                $templateVars = [
                    '{firstname}' => $this->context->employee->firstname,
                    '{lastname}' => $this->context->employee->lastname,
                    '{filename}' => Tools::getValue('csv'),
                ];

                $employeeLanguage = new Language((int) $this->context->employee->id_lang);
                // Mail send in last step because in case of failure, does NOT throw an error.
                $mailSuccess = @Mail::Send(
                    (int) $this->context->employee->id_lang,
                    'import',
                    $this->trans(
                        'Import complete',
                        [],
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
                    (int) $this->context->shop->id
                );
                if (!$mailSuccess) {
                    $results['warnings'][] = $this->trans('The confirmation email couldn\'t be sent, but the import is successful. Yay!', [], 'Admin.Advparameters.Notification');
                }
            } catch (\Exception $e) {
                $results['warnings'][] = $this->trans('The confirmation email couldn\'t be sent, but the import is successful. Yay!', [], 'Admin.Advparameters.Notification');
            }
        }

        die(json_encode($results));
    }

    public function initModal()
    {
        parent::initModal();
        $modal_content = $this->context->smarty->fetch('controllers/import/modal_import_progress.tpl');
        $this->modals[] = [
            'modal_id' => 'importProgress',
            'modal_class' => 'modal-md',
            'modal_title' => $this->trans('Importing your data...', [], 'Admin.Advparameters.Notification'),
            'modal_content' => $modal_content,
        ];
    }

    /**
     * Gets session from symfony container.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function getSession()
    {
        return \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('session');
    }

    /**
     * Get symfony request object.
     *
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    private function getSymfonyRequest()
    {
        $requestStack = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('request_stack');

        return $requestStack->getCurrentRequest();
    }
}
