<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class CrossSelling extends Module
{
	private $_html;

	public function __construct()
	{
		$this->name = 'crossselling';
		$this->tab = 'front_office_features';
		$this->version = 0.7;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Cross Selling');
		$this->description = $this->l('Customers who bought this product also bought:');

		if (!$this->isRegisteredInHook('header'))
			$this->registerHook('header');
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('productFooter') OR
			!$this->registerHook('header') OR
			!$this->registerHook('shoppingCart') OR
			!$this->registerHook('actionOrderStatusPostUpdate') OR
			!Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', 0) OR
			!Configuration::updateValue('CROSSSELLING_NBR', 10))
			return false;
		$this->_clearCache('crossselling.tpl');
		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('crossselling.tpl');
		if (!parent::uninstall() OR
			!Configuration::deleteByName('CROSSSELLING_DISPLAY_PRICE') OR
			!Configuration::deleteByName('CROSSSELLING_NBR'))
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '';
		
		if (Tools::isSubmit('submitCross'))
		{
			if (Tools::getValue('displayPrice') != 0 AND Tools::getValue('CROSSSELLING_DISPLAY_PRICE') != 1)
				$this->_html .= $this->displayError('Invalid displayPrice');
			else if (!($productNbr = Tools::getValue('CROSSSELLING_NBR')) || empty($productNbr))
				$this->_html .= $this->displayError('You must fill in the \'Products displayed\' field.');
			elseif ((int)($productNbr) == 0)
				$this->_html .= $this->displayError('Invalid number.');
			else
			{			
				Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', (int)Tools::getValue('CROSSSELLING_DISPLAY_PRICE'));
				Configuration::updateValue('CROSSSELLING_NBR', (int)Tools::getValue('CROSSSELLING_NBR'));
				$this->_clearCache('crossselling.tpl');
				$this->_html .= $this->displayConfirmation($this->l('Settings updated successfully'));
			}
		}
		return $this->_html.$this->renderForm();
	}

	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'crossselling.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/crossselling.js');
		$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
	}

	/**
	 * Returns module content
	 */
	public function hookshoppingCart($params)
	{
		if (!$params['products'])
			return;

		$cache_id = 'crossselling|shoppingcart|'.(int)$params['products'];
		if (!$this->isCached('crossselling.tpl', $this->getCacheId($cache_id)))
		{
			$qOrders = 'SELECT o.id_order
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order = o.id_order)
			WHERE o.valid = 1 AND (';
			$nProducts = count($params['products']);
			$i = 1;
			$pIds = array();
			foreach ($params['products'] as $product)
			{
				$qOrders .= 'od.product_id = '.(int)$product['id_product'];
				if ($i < $nProducts)
					$qOrders .= ' OR ';
				++$i;
				$pIds[] = (int)$product['id_product'];
			}
			$qOrders .= ')';
			$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qOrders);

			if (sizeof($orders))
			{
				$list = '';
				foreach ($orders AS $order)
					$list .= (int)$order['id_order'].',';
				$list = rtrim($list, ',');

				$list_product_ids = join(',', $pIds);
				$orderProducts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT DISTINCT od.product_id, pl.name, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price, cl.link_rewrite category, p.ean13
					FROM '._DB_PREFIX_.'order_detail od
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id'.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
					LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
					WHERE od.id_order IN ('.$list.')
						AND pl.id_lang = '.(int)$this->context->language->id.'
						AND cl.id_lang = '.(int)$this->context->language->id.'
						AND od.product_id NOT IN ('.$list_product_ids.')
						AND i.cover = 1
						AND product_shop.active = 1
					ORDER BY RAND()
					LIMIT '.(int)Configuration::get('CROSSSELLING_NBR').'
				');

				$taxCalc = Product::getTaxCalculationMethod();
				foreach ($orderProducts AS &$orderProduct)
				{
					$orderProduct['image'] = $this->context->link->getImageLink($orderProduct['link_rewrite'], (int)$orderProduct['product_id'].'-'.(int)$orderProduct['id_image'], ImageType::getFormatedName('medium'));
					$orderProduct['link'] = $this->context->link->getProductLink((int)$orderProduct['product_id'], $orderProduct['link_rewrite'], $orderProduct['category'], $orderProduct['ean13']);
					if (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND ($taxCalc == 0 OR $taxCalc == 2))
						$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], true, NULL);
					elseif (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND $taxCalc == 1)
						$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], false, NULL);
				}

				$this->smarty->assign(array('order' => (count($pIds) > 1 ? true : false), 'orderProducts' => $orderProducts, 'middlePosition_crossselling' => round(sizeof($orderProducts) / 2, 0),
				'crossDisplayPrice' => Configuration::get('CROSSSELLING_DISPLAY_PRICE')));
			}
		}
		return $this->display(__FILE__, 'crossselling.tpl', $this->getCacheId($cache_id));
	}

	/**
	* Returns module content for left column
	*/
	public function hookProductFooter($params)
	{
		$cache_id = 'crossselling|productfooter|'.(int)$params['product']->id;
		if (!$this->isCached('crossselling.tpl', $this->getCacheId($cache_id)))
		{
			$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT o.id_order
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order = o.id_order)
			WHERE o.valid = 1 AND od.product_id = '.(int)$params['product']->id);

			if (sizeof($orders))
			{
				$list = '';
				foreach ($orders AS $order)
					$list .= (int)$order['id_order'].',';
				$list = rtrim($list, ',');

				$orderProducts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT DISTINCT od.product_id, pl.name, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price, cl.link_rewrite category, p.ean13
					FROM '._DB_PREFIX_.'order_detail od
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id'.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
					LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
					WHERE od.id_order IN ('.$list.')
						AND pl.id_lang = '.(int)$this->context->language->id.'
						AND cl.id_lang = '.(int)$this->context->language->id.'
						AND od.product_id != '.(int)$params['product']->id.'
						AND i.cover = 1
						AND product_shop.active = 1
					ORDER BY RAND()
					LIMIT '.(int)Configuration::get('CROSSSELLING_NBR').'
				');

				$taxCalc = Product::getTaxCalculationMethod();
				foreach ($orderProducts AS &$orderProduct)
				{
					$orderProduct['image'] = $this->context->link->getImageLink($orderProduct['link_rewrite'], (int)$orderProduct['product_id'].'-'.(int)$orderProduct['id_image'], ImageType::getFormatedName('medium'));
					$orderProduct['link'] = $this->context->link->getProductLink((int)$orderProduct['product_id'], $orderProduct['link_rewrite'], $orderProduct['category'], $orderProduct['ean13']);
					if (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND ($taxCalc == 0 OR $taxCalc == 2))
						$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], true, NULL);
					elseif (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND $taxCalc == 1)
						$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], false, NULL);
				}

				$this->smarty->assign(array('order' => false, 'orderProducts' => $orderProducts, 'middlePosition_crossselling' => round(sizeof($orderProducts) / 2, 0),
				'crossDisplayPrice' => Configuration::get('CROSSSELLING_DISPLAY_PRICE')));
			}
		}
		return $this->display(__FILE__, 'crossselling.tpl', $this->getCacheId($cache_id));
	}
	
	public function hookActionOrderStatusPostUpdate($params)
	{
		$this->_clearCache('crossselling.tpl');
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Display price on products'),
						'name' => 'CROSSSELLING_DISPLAY_PRICE',
						'desc' => $this->l('Show the price on the products in the block.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of products displayed'),
						'name' => 'CROSSSELLING_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products displayed in this block.'),
					),
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCross';
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
			'CROSSSELLING_NBR' => Tools::getValue('CROSSSELLING_NBR', Configuration::get('CROSSSELLING_NBR')),
			'CROSSSELLING_DISPLAY_PRICE' => Tools::getValue('CROSSSELLING_DISPLAY_PRICE', Configuration::get('CROSSSELLING_DISPLAY_PRICE')),
		);
	}
}
