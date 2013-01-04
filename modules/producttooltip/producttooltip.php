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

class ProductToolTip extends Module
{
	public function __construct()
	{
		$this->name = 'producttooltip';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Product tooltips');
		$this->description = $this->l('Show how many people are watching a product page, last sale and last cart added.');
	}

	public function install()
	{
	 	if (!parent::install())
	 		return false;

		/* Default configuration values */
		Configuration::updateValue('PS_PTOOLTIP_PEOPLE', 1);
		Configuration::updateValue('PS_PTOOLTIP_DATE_CART', 1);
		Configuration::updateValue('PS_PTOOLTIP_DATE_ORDER', 1);
		Configuration::updateValue('PS_PTOOLTIP_DAYS', 3);
		Configuration::updateValue('PS_PTOOLTIP_LIFETIME', 30);

	 	return $this->registerHook('header') AND $this->registerHook('productfooter');
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('PS_PTOOLTIP_PEOPLE')
			OR !Configuration::deleteByName('PS_PTOOLTIP_DATE_CART')
			OR !Configuration::deleteByName('PS_PTOOLTIP_DATE_ORDER')
			OR !Configuration::deleteByName('PS_PTOOLTIP_DAYS')
			OR !Configuration::deleteByName('PS_PTOOLTIP_LIFETIME')
			OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		/* Update values in DB */
		if (Tools::isSubmit('SubmitToolTip'))
		{
			Configuration::updateValue('PS_PTOOLTIP_PEOPLE', (int)(Tools::getValue('ps_ptooltip_people')));
			Configuration::updateValue('PS_PTOOLTIP_DATE_CART', (int)(Tools::getValue('ps_ptooltip_date_cart')));
			Configuration::updateValue('PS_PTOOLTIP_DATE_ORDER', (int)(Tools::getValue('ps_ptooltip_date_order')));
			Configuration::updateValue('PS_PTOOLTIP_DAYS', ((int)(Tools::getValue('ps_ptooltip_days') < 0 ? 0 : (int)Tools::getValue('ps_ptooltip_days'))));
			Configuration::updateValue('PS_PTOOLTIP_LIFETIME', ((int)(Tools::getValue('ps_ptooltip_lifetime') < 0 ? 0 : (int)Tools::getValue('ps_ptooltip_lifetime'))));

			echo $this->displayConfirmation($this->l('Settings updated'));
		}

		/* Configuration form */
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset class="width2" style="float: left;">
			<legend><img src="'.__PS_BASE_URI__.'modules/producttooltip/logo.gif" alt="" />'.$this->l('Product tooltips').'</legend>
			<p>
				'.$this->l('Display the number of people who are currently watching this product?').'<br /><br />
				<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="" /><input type="radio" name="ps_ptooltip_people" value="1"'.(Configuration::get('PS_PTOOLTIP_PEOPLE') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('Yes').'
				&nbsp;<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="" /><input type="radio" name="ps_ptooltip_people" value="0"'.(!Configuration::get('PS_PTOOLTIP_PEOPLE') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('No').'<br />
			</p>
			<p>
				'.$this->l('Lifetime:').'
				<input type="text" name="ps_ptooltip_lifetime" style="width: 30px;" value="'.(int)(Configuration::get('PS_PTOOLTIP_LIFETIME')).'" /> '.$this->l('minutes').'<br />
			</p>
			<p>
				'.$this->l('If you activate the option above, you must activate the first option of StatData module').'
			</p>
			<hr size="1" noshade />
			<p>
				'.$this->l('Display the last time the product has been ordered?').'<br /><br />
				<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="" /><input type="radio" name="ps_ptooltip_date_order" value="1"'.(Configuration::get('PS_PTOOLTIP_DATE_ORDER') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('Yes').'
				&nbsp;<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="" /><input type="radio" name="ps_ptooltip_date_order" value="0"'.(!Configuration::get('PS_PTOOLTIP_DATE_ORDER') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('No').'<br /><br />
			</p>
			<p>
				'.$this->l('If not ordered yet, display the last time the product has been added to a cart?').'<br /><br />
				<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="" /><input type="radio" name="ps_ptooltip_date_cart" value="1"'.(Configuration::get('PS_PTOOLTIP_DATE_CART') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('Yes').'
				&nbsp;<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="" /><input type="radio" name="ps_ptooltip_date_cart" value="0"'.(!Configuration::get('PS_PTOOLTIP_DATE_CART') ? ' checked="checked"' : '').' style="vertical-align: middle;" /> '.$this->l('No').'<br /><br />

			</p>
			<p>
				'.$this->l('Do not display events older than:').'
				<input type="text" name="ps_ptooltip_days" style="width: 30px;" value="'.(int)(Configuration::get('PS_PTOOLTIP_DAYS')).'" /> '.$this->l('days').'<br />
			</p>
			<hr size="1" noshade />
			<center><input type="submit" name="SubmitToolTip" class="button" value="'.$this->l('Update settings').'" style="margin-top: 10px;"  /></center>
		</fieldset>
		<p style="float: left; margin: 10px 0 0 30px;">
			<b>'.$this->l('Sample:').'</b><br />
			<img src="'.__PS_BASE_URI__.'modules/producttooltip/sample.gif" style="margin-top: 10px;" />
		</p>
		<div style="clear: both; font-size: 0;"></div>
		</form>';

		return $output;
	}

	public function hookHeader($params)
	{
		$this->context->controller->addJQueryPlugin('jgrowl');
	}

	public function hookProductFooter($params)
	{
		$id_product = (int)($params['product']->id);

		/* First we try to display the number of people who are currently watching this product page */
		if (Configuration::get('PS_PTOOLTIP_PEOPLE'))
		{
			$date = strftime('%Y-%m-%d %H:%M:%S' , time() - (int)(Configuration::get('PS_PTOOLTIP_LIFETIME') * 60));

			$nbPeople = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT COUNT(DISTINCT(id_connections)) nb
			FROM '._DB_PREFIX_.'page p
			LEFT JOIN '._DB_PREFIX_.'connections_page cp ON (p.id_page = cp.id_page)
			WHERE p.id_page_type = 1 AND p.id_object = '.(int)($id_product).' AND cp.time_start > \''.pSQL($date).'\'');

			if (isset($nbPeople['nb']) AND $nbPeople['nb'] > 0)
				$this->smarty->assign('nb_people', (int)($nbPeople['nb']));
		}

		/* Then, we try to display last sale */
		if (Configuration::get('PS_PTOOLTIP_DATE_ORDER'))
		{
			$days = (int)(Configuration::get('PS_PTOOLTIP_DAYS'));
			$date = strftime('%Y-%m-%d' , strtotime('-'.(int)($days).' day'));

			$order = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT o.date_add
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'orders o ON (od.id_order = o.id_order)
			WHERE od.product_id = '.(int)($id_product).' AND o.date_add >= \''.pSQL($date).'\'
			ORDER BY o.date_add DESC');

			if (isset($order['date_add']) && Validate::isDateFormat($order['date_add'])  && $order['date_add'] != '0000-00-00 00:00:00')
				$this->smarty->assign('date_last_order', $order['date_add']);
			else
			{
				/* No sale? display last cart add instead */
				if (Configuration::get('PS_PTOOLTIP_DATE_CART'))
				{
					$cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
					SELECT cp.date_add
					FROM '._DB_PREFIX_.'cart_product cp
					WHERE cp.id_product = '.(int)($id_product));

					if (isset($cart['date_add']) && Validate::isDateFormat($cart['date_add'])  && $cart['date_add'] != '0000-00-00 00:00:00')
						$this->smarty->assign('date_last_cart', $cart['date_add']);
				}
			}
		}

		if ((isset($nbPeople['nb']) AND $nbPeople['nb'] > 0) OR isset($order['date_add']) OR isset($cart['date_add']))
			return $this->display(__FILE__, 'producttooltip.tpl');
	}
}


