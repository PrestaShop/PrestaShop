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
	const INSTALL_SQL_FILE = 'install.sql';

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
		if (!file_exists(dirname(__FILE__).'/sql/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
			if ($query)
				if (!Db::getInstance()->execute(trim($query)))
					return false;

		if (!parent::install())
			return false;

		if (!$this->registerHook('displayProductComparisonInProductList') || !$this->registerHook('displayCompareForm') || !$this->registerHook('actionFrontControllerSetMedia') || !$this->registerHook('actionAuthentication'))
			return false;

		Configuration::updateValue('PS_COMPARATOR_MAX_ITEM', (int)3);

		return true;
	}
	public function uninstall()
	{
		if (!$this->deleteTables() || !parent::uninstall())
			return false;

		return true;
	}

	private function deleteTables()
	{
		return Db::getInstance()->execute(
			'DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'compare`,
			`'._DB_PREFIX_.'compare_product`'
		);
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitConfigure'))
		{
			if (!($comparator_max_item = Tools::getValue('PS_COMPARATOR_MAX_ITEM')) || empty($comparator_max_item))
				$output .= $this->displayError($this->l('Please complete the field.'));
			else
			{
				Configuration::updateValue('PS_COMPARATOR_MAX_ITEM', (int)$comparator_max_item);
				$this->_clearCache('*');
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
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

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Product comparison'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'required' => true,
						'label' => $this->l('Maximum number of products'),
						'name' => 'PS_COMPARATOR_MAX_ITEM',
						'class' => 'fixed-width-xs',
						'cast' => 'intval',
						'desc' => $this->l('Set the maximum number of products that can be selected for comparison. Set to "0" to disable this feature.')
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitConfigure';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'PS_COMPARATOR_MAX_ITEM' => Tools::getValue('PS_COMPARATOR_MAX_ITEM', Configuration::get('PS_COMPARATOR_MAX_ITEM'))
		);
	}
}