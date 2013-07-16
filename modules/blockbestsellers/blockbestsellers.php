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
	
class BlockBestSellers extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'blockbestsellers';
		$this->tab = 'front_office_features';
		$this->version = '1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Top-seller block');
		$this->description = $this->l('Add a block displaying your store\'s top-selling products.');
	}

	/**
	 * @see ModuleCore::install()
	 */
	public function install()
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
		
		if (!parent::install()
			|| !$this->registerHook('rightColumn')
			|| !$this->registerHook('header')
			|| !$this->registerHook('actionOrderStatusPostUpdate')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !ProductSale::fillProductSales()
		)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
		
		return parent::uninstall();
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookActionOrderStatusPostUpdate($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)Tools::getValue('always_display'));
			$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Always display this block.').'</label>
				<div class="margin-form">
					<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Show the block even if no products are available.').'</p>
				</div>
				<center><input type="submit" name="submitBestSellers" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS($this->_path.'blockbestsellers.css', 'all');
	}

	public function hookHome($params)
	{
		if (!$this->isCached('blockbestsellers-home.tpl', $this->getCacheId('blockbestsellers-home')))
		{
			$best_sellers = $this->getBestSellers($params);
			if ($best_sellers === false)
				return;

			$this->smarty->assign(array(
				'best_sellers' => $best_sellers,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		return $this->display(__FILE__, 'blockbestsellers-home.tpl', $this->getCacheId('blockbestsellers-home'));
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blockbestsellers.tpl', $this->getCacheId('blockbestsellers')))
		{
			$best_sellers = $this->getBestSellers($params);
			if ($best_sellers === false)
				return;

			$this->smarty->assign(array(
				'best_sellers' => $best_sellers,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'smallSize' => Image::getSize(ImageType::getFormatedName('small'))
			));
		}
		return $this->display(__FILE__, 'blockbestsellers.tpl', $this->getCacheId('blockbestsellers'));
	}
		
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	
	protected function getBestSellers($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$params['cookie']->id_lang, 0, 5)))
			return (Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY') ? array() : false);

		$bestsellers = array();
		$currency = new Currency($params['cookie']->id_currency);
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);
		return $result;
	}
}