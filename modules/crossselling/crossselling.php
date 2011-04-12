<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class CrossSelling extends Module
{
	private $_html;
	
	public function __construct()
	{
		$this->name = 'crossselling';
		$this->tab = 'front_office_features';
		$this->version = 0.1;
		$this->author = 'PrestaShop';

		parent::__construct();
		
		$this->displayName = $this->l('Cross Selling');
		$this->description = $this->l('Customers who bought this product also bought:');
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('productFooter') OR
			!$this->registerHook('header') OR
			!Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', 0))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() OR 
			!Configuration::deleteByName('CROSSSELLING_DISPLAY_PRICE'))
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '';
		if (Tools::isSubmit('submitCross') AND Tools::getValue('displayPrice') != 0 AND Tools::getValue('displayPrice') != 1)
			$this->_html .= $this->displayError('Invalid displayPrice');
		elseif (Tools::isSubmit('submitCross'))
		{
			Configuration::updateValue('CROSSSELLING_DISPLAY_PRICE', Tools::getValue('displayPrice'));
			$this->_html .= $this->displayConfirmation($this->l('Settings updated succesfully'));
		}
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('Display price on products').'</label>
			<div class="margin-form">
				<input type="radio" name="displayPrice" id="display_on" value="1" '.(Configuration::get('CROSSSELLING_DISPLAY_PRICE') ? 'checked="checked" ' : '').'/>
				<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
				<input type="radio" name="displayPrice" id="display_off" value="0" '.(!Configuration::get('CROSSSELLING_DISPLAY_PRICE') ? 'checked="checked" ' : '').'/>
				<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				<p class="clear">'.$this->l('Show the price on the products in the block.').'</p>
			</div>
			<center><input type="submit" name="submitCross" value="'.$this->l('Save').'" class="button" /></center>
		</fieldset>
		</form>';
		return $this->_html;
	}
	
	public function hookHeader()
	{
		Tools::addCSS(($this->_path).'crossselling.css', 'all');
	}

	/**
	* Returns module content for left column
	*/
	public function hookProductFooter($params)
	{
		global $smarty, $cookie, $link;
		
		$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
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
		
			$orderProducts = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT od.product_id, pl.name, pl.link_rewrite, p.reference, i.id_image, p.show_price, cl.link_rewrite category, p.ean13
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id)
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = p.id_category_default)
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
			WHERE od.id_order IN ('.$list.') AND pl.id_lang = '.(int)($cookie->id_lang).' AND cl.id_lang = '.(int)($cookie->id_lang).' 
			AND od.product_id != '.(int)$params['product']->id.' AND i.cover = 1 AND p.active = 1
			ORDER BY RAND()
			LIMIT 10');
			
			$taxCalc = Product::getTaxCalculationMethod();
			foreach ($orderProducts AS &$orderProduct)
			{
				$orderProduct['image'] = $link->getImageLink($orderProduct['link_rewrite'], (int)$orderProduct['product_id'].'-'.(int)$orderProduct['id_image'], 'medium');
				$orderProduct['link'] = $link->getProductLink((int)$orderProduct['product_id'], $orderProduct['link_rewrite'], $orderProduct['category'], $orderProduct['ean13']);
				if (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND ($taxCalc == 0 OR $taxCalc == 2))
					$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], true, NULL);
				elseif (Configuration::get('CROSSSELLING_DISPLAY_PRICE') AND $taxCalc == 1)
					$orderProduct['displayed_price'] = Product::getPriceStatic((int)$orderProduct['product_id'], false, NULL);
			}
			
			$smarty->assign(array('orderProducts' => $orderProducts, 'middlePosition_crossselling' => round(sizeof($orderProducts) / 2, 0),
			'crossDisplayPrice' => Configuration::get('CROSSSELLING_DISPLAY_PRICE')));
		}
		return $this->display(__FILE__, 'crossselling.tpl');
	}
}