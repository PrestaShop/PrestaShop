<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

// Loading Models
include(_PS_MODULE_DIR_ . 'productscomparison/models/CompareProduct.php');

class ProductsComparison extends Module
{
	public $comparator_max_item = null;

	public function __construct()
	{
		$this->name = 'productscomparison';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'PrestaShop';
		$this->module_key = '';
		$this->need_instance = 0;

		$this->controllers = array('compare');

		parent::__construct();

		$this->bootstrap = true;

		$this->displayName = $this->l('Products Comparison');
		$this->description = $this->l('Add products comparator');

		$this->comparator_max_item = (int)Configuration::get('PS_COMPARATOR_MAX_ITEM');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		if (!$this->registerHook('displayProductComparisonInProductList') || !$this->registerHook('displayCompareForm') || !$this->registerHook('actionFrontControllerSetMedia') || !$this->registerHook('actionAuthentication'))
			return false;

		Configuration::updateValue('PS_COMPARATOR_MAX_ITEM', (int)3);

		return true;
	}

	public function getContent()
	{
		//@todo
		// Let's the user set the max item number

		/*
					'PS_COMPARATOR_MAX_ITEM' => array(
						'title' => $this->l('Product comparison'),
						'hint' => $this->l('Set the maximum number of products that can be selected for comparison. Set to "0" to disable this feature.'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text'
					),
		*/
	}

	public function hookActionAuthentication()
	{
		$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
		$this->context->cookie->write();
	}

	public function hookActionFrontControllerSetMedia()
	{
		if (!(int)$this->comparator_max_item)
			return false;

		$this->context->smarty->assign('compare_controller_link', $this->context->link->getModuleLink($this->name, 'compare', array(), null, (int)$this->context->language->id));

		$this->context->controller->addJS($this->_path.'js/products-comparison.js');
	}

	public function hookDisplayProductComparisonInProductList($params)
	{
		if (!(int)$this->comparator_max_item || !(int)$params['product']['id_product'])
			return false;

		$compared_products = array();
		if (isset($this->context->cookie->id_compare))
			$compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);

		Media::addJsDef('comparator_max_item', $this->comparator_max_item);
		Media::addJsDef('comparedProductsIds', $compared_products);

		Media::addJsDefL('min_item', $this->l('Please select at least one product'));
		Media::addJsDefL('max_item', sprintf($this->l('You cannot add more than %d product(s) to the product comparison'), $this->comparator_max_item));

		$this->context->smarty->assign(array(
			'product' => $params['product'],
			'comparator_max_item' => $this->comparator_max_item,
			'compared_products'   => is_array($compared_products) ? $compared_products : array(),
		));

		return $this->display(__FILE__, 'displayProductComparisonInProductList.tpl');
	}

	public function hookDisplayCompareForm($params)
	{
		if (!(int)$this->comparator_max_item)
			return false;

		if (isset($params['paginationId']))
			$this->context->smarty->assign('paginationId', $params['paginationId']);

		$compared_products = array();
		if (isset($this->context->cookie->id_compare))
			$compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);

		$this->context->smarty->assign(array(
			'comparator_max_item' => $this->comparator_max_item,
			'compared_products'   => is_array($compared_products) ? $compared_products : array(),
			'compareProducts'   => is_array($compared_products) ? $compared_products : array(),
		));

		return $this->display(__FILE__, 'product-compare.tpl');
	}
}