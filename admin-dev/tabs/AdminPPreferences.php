<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

class AdminPPreferences extends AdminPreferences
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';
 		
 		$this->_fieldsProduct = array(
			'PS_CATALOG_MODE' => array('title' => $this->l('Catalog mode:'), 'desc' => $this->l('When active, all features for shopping will be disabled'), 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool'),
 			'PS_ORDER_OUT_OF_STOCK' => array('title' => $this->l('Allow ordering out-of-stock product:'), 'desc' => $this->l('Add to cart button is hidden when product is unavailable'), 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool'),
			'PS_STOCK_MANAGEMENT' => array('title' => $this->l('Enable stock management:'), 'desc' => '', 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool', 'js' => array('on' => 'onchange="stockManagementActivationAuthorization()"', 'off' => 'onchange="stockManagementActivationAuthorization()"')),
			'PS_DISPLAY_QTIES' => array('title' => $this->l('Display available quantities on product page:'), 'desc' => '', 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool'),
			'PS_DISPLAY_JQZOOM' => array('title' => $this->l('Enable JqZoom instead of Thickbox on product page:'), 'desc' => '', 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool'),
			'PS_DISP_UNAVAILABLE_ATTR' => array('title' => $this->l('Display unavailable product attributes on product page:'), 'desc' => '', 'validation' => 'isBool', 'cast' => 'intval', 'required' => true, 'type' => 'bool'),
			'PS_ATTRIBUTE_CATEGORY_DISPLAY' => array('title' => $this->l('Display "add to cart" button when product has attributes:'), 'desc' => $this->l('Display or hide the "add to cart" button on category pages for products that have attributes to force customers to see the product detail'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_COMPARATOR_MAX_ITEM' => array('title' => $this->l('Max items in the comparator:'), 'desc' => $this->l('Set to 0 to disable this feature'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'text'),
			'PS_PURCHASE_MINIMUM' => array('title' => $this->l('Minimum purchase total required in order to validate order:'), 'desc' => $this->l('Set to 0 to disable this feature'), 'validation' => 'isFloat', 'cast' => 'floatval', 'type' => 'price'),
			'PS_LAST_QTIES' => array('title' => $this->l('Display last quantities when qty is lower than:'), 'desc' => $this->l('Set to 0 to disable this feature'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'text'),
			'PS_NB_DAYS_NEW_PRODUCT' => array('title' => $this->l('Number of days during which the product is considered \'new\':'), 'validation' => 'isUnsignedInt', 'cast' => 'intval', 'type' => 'text'),
			'PS_CART_REDIRECT' => array('title' => $this->l('Re-direction after adding product to cart:'), 'desc' => $this->l('Concerns only the non-AJAX version of the cart'), 'cast' => 'intval', 'show' => true, 'required' => true, 'type' => 'radio', 'validation' => 'isBool', 'choices' => array(0 => $this->l('previous page'), 1 => $this->l('cart summary'))),
			'PS_PRODUCTS_PER_PAGE' => array('title' => $this->l('Products per page:'), 'desc' => $this->l('Products displayed per page. Default is 10.'), 'validation' => 'isUnsignedInt', 'cast' => 'intval', 'type' => 'text'),
			'PS_PRODUCTS_ORDER_BY' => array('title' => $this->l('Default order by:'), 'desc' => $this->l('Default order by for product list'), 'type' => 'select', 'list' => 
				array(
					array('id' => '0', 'name' => $this->l('Product name')),
					array('id' => '1', 'name' => $this->l('Product price')),
					array('id' => '2', 'name' => $this->l('Product added date')),
					array('id' => '4', 'name' => $this->l('Position inside category')),
					array('id' => '5', 'name' => $this->l('Manufacturer')),
					array('id' => '3', 'name' => $this->l('Product modified date'))
				), 'identifier' => 'id'),
			'PS_PRODUCTS_ORDER_WAY' => array('title' => $this->l('Default order way:'), 'desc' => $this->l('Default order way for product list'), 'type' => 'select', 'list' => array(array('id' => '0', 'name' => $this->l('Ascending')), array('id' => '1', 'name' => $this->l('Descending'))), 'identifier' => 'id'),
			'PS_IMAGE_GENERATION_METHOD' => array('title' => $this->l('Image generated by:'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'select', 'list' => array(array('id' => '0', 'name' => $this->l('auto')), array('id' => '1', 'name' => $this->l('width')), array('id' => '2', 'name' => $this->l('height'))), 'identifier' => 'id'),
			'PS_PRODUCT_PICTURE_MAX_SIZE' => array('title' => $this->l('Maximum size of product pictures:'), 'desc' => $this->l('The maximum size of pictures uploadable by customers (in Bytes)'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'text'),
			'PS_PRODUCT_PICTURE_WIDTH' => array('title' => $this->l('Product pictures width:'), 'desc' => $this->l('The maximum width of pictures uploadable by customers'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'text'),
			'PS_PRODUCT_PICTURE_HEIGHT' => array('title' => $this->l('Product pictures height:'), 'desc' => $this->l('The maximum height of pictures uploadable by customers'), 'validation' => 'isUnsignedId', 'required' => true, 'cast' => 'intval', 'type' => 'text')
		);
	
		parent::__construct();
	}
	
	public function postProcess()
	{
		if (isset($_POST['submitProducts'.$this->table]))
		{
		 	if ($this->tabAccess['add'] === '1')
		 		{
		 			if(!Tools::getValue('PS_STOCK_MANAGEMENT'))
		 			{
		 				$_POST['PS_ORDER_OUT_OF_STOCK'] = 1;
		 				$_POST['PS_DISPLAY_QTIES'] = 0;
		 			}
					$this->_postConfig($this->_fieldsProduct);
				}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
	}

	public function display()
	{
		$this->_displayForm('products', $this->_fieldsProduct, $this->l('Products'), 'width3', 'tab-orders');
		echo '<script type="text/javascript">stockManagementActivationAuthorization();</script>';
	}
}


