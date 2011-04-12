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
	
class BlockBestSellers extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
			$this->name = 'blockbestsellers';
			$this->tab = 'front_office_features';
			$this->version = '1.1';
		$this->author = 'PrestaShop';
			parent::__construct();

			$this->displayName = $this->l('Top seller block');
			$this->description = $this->l('Add a block displaying the shop\'s top sellers.');
	}

	public function install()
	{
		if (!parent::install() OR
				!$this->registerHook('rightColumn') OR
				!$this->registerHook('header') OR
				!$this->registerHook('updateOrderStatus') OR
				!ProductSale::fillProductSales())
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)(Tools::getValue('always_display')));
			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Always display block').'</label>
				<div class="margin-form">
					<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Show the block even if no product is available.').'</p>
				</div>
				<center><input type="submit" name="submitBestSellers" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		
		global $smarty;
		$currency = new Currency((int)($params['cookie']->id_currency));
		$bestsellers = ProductSale::getBestSalesLight((int)($params['cookie']->id_lang), 0, 5);
		if (!$bestsellers AND !Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY'))
			return;
		$best_sellers = array();
		foreach ($bestsellers AS $bestseller)
		{
			$bestseller['price'] = Tools::displayPrice(Product::getPriceStatic((int)($bestseller['id_product'])), $currency);
			$best_sellers[] = $bestseller;
		}
		$smarty->assign(array(
			'best_sellers' => $best_sellers,
			'mediumSize' => Image::getSize('medium')));
		return $this->display(__FILE__, 'blockbestsellers.tpl');
	}
	
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		Tools::addCSS(($this->_path).'blockbestsellers.css', 'all');
	}

}


