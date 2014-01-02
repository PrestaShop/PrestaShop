<?php
/*
* 2007-2014 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPPreferencesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		$this->fields_options = array(
			'products' => array(
				'title' =>	$this->l('Products (general)'),
				'fields' =>	array(
					'PS_CATALOG_MODE' => array(
						'title' => $this->l('Catalog mode'),
						'hint' => $this->l('When active, all shopping features will be disabled.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					),
					'PS_COMPARATOR_MAX_ITEM' => array(
						'title' => $this->l('Product comparison'),
						'hint' => $this->l('Set the maximum number of products that can be selected for comparison. Set to "0" to disable this feature.'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text'
					),
					'PS_NB_DAYS_NEW_PRODUCT' => array(
						'title' => $this->l('Number of days for which the product is considered \'new\''),
						'validation' => 'isUnsignedInt',
						'cast' => 'intval',
						'type' => 'text'
					),
					'PS_CART_REDIRECT' => array(
						'title' => $this->l('Redirect after adding product to cart'),
						'hint' => $this->l('Only for non-AJAX versions of the cart.'),
						'cast' => 'intval',
						'show' => true,
						'required' => false,
						'type' => 'radio',
						'validation' => 'isBool',
						'choices' => array(
							0 => $this->l('Previous page'),
							1 => $this->l('Cart summary')
						)
					),
					'PS_PRODUCT_SHORT_DESC_LIMIT' => array(
						'title' => $this->l('Max size of short description'),
						'hint' => $this->l('Set the maximum size of product short description (in characters).'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('characters'),
					),
					'PS_QTY_DISCOUNT_ON_COMBINATION' => array(
						'title' => $this->l('Quantity discounts based on'),
						'hint' => $this->l('How to calculate quantity discounts'),
						'cast' => 'intval',
						'show' => true,
						'required' => false,
						'type' => 'radio',
						'validation' => 'isBool',
						'choices' => array(
							0 => $this->l('Products:'),
							1 => $this->l('Combinations')
						)
					),
					'PS_FORCE_FRIENDLY_PRODUCT' => array(
						'title' => $this->l('Force update of friendly url'),
						'hint' => $this->l('When active, friendly url will be updated on every save'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'order_by_pagination' => array(
				'title' =>	$this->l('Pagination'),
				'fields' =>	array(
					'PS_PRODUCTS_PER_PAGE' => array(
						'title' => $this->l('Products per page'),
						'hint' => $this->l('Number of products displayed per page. Default is 10.'),
						'validation' => 'isUnsignedInt',
						'cast' => 'intval',
						'type' => 'text'
					),
					'PS_PRODUCTS_ORDER_BY' => array(
						'title' => $this->l('Default order by'),
						'hint' => $this->l('The order in which products are displayed in the product list.'),
						'type' => 'select',
						'list' => array(
							array('id' => '0', 'name' => $this->l('Product name')),
							array('id' => '1', 'name' => $this->l('Product price')),
							array('id' => '2', 'name' => $this->l('Product add date')),
							array('id' => '3', 'name' => $this->l('Product modified date')),
							array('id' => '4', 'name' => $this->l('Position inside category')),
							array('id' => '5', 'name' => $this->l('Manufacturer')),
							array('id' => '6', 'name' => $this->l('Product quantity')),
							array('id' => '7', 'name' => $this->l('Product reference')) 
						),
						'identifier' => 'id'
					),
					'PS_PRODUCTS_ORDER_WAY' => array(
						'title' => $this->l('Default order method'),
						'hint' => $this->l('Default order method for product list.'),
						'type' => 'select',
						'list' => array(
							array(
								'id' => '0',
								'name' => $this->l('Ascending')
							),
							array(
								'id' => '1',
								'name' => $this->l('Descending')
							)
						),
						'identifier' => 'id'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'fo_product_page' => array(
				'title' =>	$this->l('Product page'),
				'fields' =>	array(
					'PS_DISPLAY_QTIES' => array(
						'title' => $this->l('Display available quantities on the product page'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					),
					'PS_LAST_QTIES' => array(
						'title' => $this->l('Display remaining quantities when the quantity is lower than'),
						'hint' => $this->l('Set to "0" to disable this feature.'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text'
					),
					'PS_DISPLAY_JQZOOM' => array(
						'title' => $this->l('Enable JqZoom instead of Thickbox on the product page'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					),
					'PS_DISP_UNAVAILABLE_ATTR' => array(
						'title' => $this->l('Display unavailable product attributes on the product page'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					),
					'PS_ATTRIBUTE_CATEGORY_DISPLAY' => array(
						'title' => $this->l('Display the "add to cart" button when a product has attributes'),
						'hint' => $this->l('Display or hide the "add to cart" button on category pages for products that have attributes forcing customers to see product details.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_ATTRIBUTE_ANCHOR_SEPARATOR' => array(
						'title' => $this->l('Separator of attribute anchor on the product links'),
						'type' => 'select',
						'list' => array(
							array('id' => '-', 'name' => '-'),
							array('id' => ',', 'name' => ','),
						),
						'identifier' => 'id'
					),
					'PS_DISPLAY_DISCOUNT_PRICE' => array(
						'title' => $this->l('Display discounted price'),
						'desc' => $this->l('"In the volume discounts board, display the new price with the applied discount instead of showing the discount (ie. "-5%").'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool'
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'stock' => array(
				'title' =>	$this->l('Products stock'),
				'fields' =>	array(
					'PS_ORDER_OUT_OF_STOCK' => array(
		 				'title' => $this->l('Allow ordering of out-of-stock products'),
		 				'hint' => $this->l('By default, the Add to Cart button is hidden when a product is unavailable. You can choose to have it displayed in all cases.'),
		 				'validation' => 'isBool',
		 				'cast' => 'intval',
		 				'required' => false,
		 				'type' => 'bool'
					),
					'PS_STOCK_MANAGEMENT' => array(
						'title' => $this->l('Enable stock management'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool',
						'js' => array(
							'on' => 'onchange="stockManagementActivationAuthorization()"',
							'off' => 'onchange="stockManagementActivationAuthorization()"'
						)
					),
					'PS_ADVANCED_STOCK_MANAGEMENT' => array(
						'title' => $this->l('Enable advanced stock management'),
						'hint' => $this->l('Allows you to manage physical stock, warehouses and supply orders in a new Stock menu.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL,
					),
				),
				'bottom' => '<script type="text/javascript">stockManagementActivationAuthorization();</script>',
				'submit' => array('title' => $this->l('Save'))
			),
		);
	}

	public function beforeUpdateOptions()
	{
		if (!Tools::getValue('PS_STOCK_MANAGEMENT', true))
		{
			$_POST['PS_ORDER_OUT_OF_STOCK'] = 1;
			$_POST['PS_DISPLAY_QTIES'] = 0;
		}

		// if advanced stock management is disabled, updates concerned tables
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 1 &&
			(int)Tools::getValue('PS_ADVANCED_STOCK_MANAGEMENT') == 0 && Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL)
		{
			ObjectModel::updateMultishopTable('Product', array('advanced_stock_management' => 0), 'product_shop.`advanced_stock_management` = 1');

			Db::getInstance()->execute(
				'UPDATE `'._DB_PREFIX_.'stock_available`
				 SET `depends_on_stock` = 0, `quantity` = 0
				 WHERE `depends_on_stock` = 1');
		}
	}

}
