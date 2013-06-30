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

class BlockSpecials extends Module
{
	private $_html = '';
	private $_postErrors = array();

    function __construct()
    {
        $this->name = 'blockspecials';
        $this->tab = 'pricing_promotion';
        $this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Specials block');
		$this->description = $this->l('Adds a block displaying current product specials.');
	}

	public function install()
	{
		if (!Configuration::get('BLOCKSPECIALS_NB_CACHES'))
			Configuration::updateValue('BLOCKSPECIALS_NB_CACHES', 20);
		$this->_clearCache('blockspecials.tpl');

		return (parent::install()
			&& $this->registerHook('rightColumn')
			&& $this->registerHook('header')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
		);
	}
	
	public function uninstall()
	{
		$this->_clearCache('blockspecials.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitSpecials'))
		{
			Configuration::updateValue('PS_BLOCK_SPECIALS_DISPLAY', (int)Tools::getValue('always_display'));
			Configuration::updateValue('BLOCKSPECIALS_NB_CACHES', (int)Tools::getValue('BLOCKSPECIALS_NB_CACHES'));
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
					<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_SPECIALS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_SPECIALS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Show the block even if no product is available.').'</p>
				</div>
				<label>'.$this->l('Number of cache files.').'</label>
				<div class="margin-form">
					<input type="text" name="BLOCKSPECIALS_NB_CACHES" value="'.(int)Configuration::get('BLOCKSPECIALS_NB_CACHES').'" />
					<p class="clear">'.$this->l('Specials are displayed randomly on the front end, but since it takes a lot of ressources, it is better to cache the results. Cache is reset everyday. 0 will disable the cache.').'</p>
				</div>
				<center><input type="submit" name="submitSpecials" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		
		// We need to create multiple caches because the products are sorted randomly
		$random = date('Ymd').'|'.round(rand(1, max(Configuration::get('BLOCKSPECIALS_NB_CACHES'), 1)));

		if (!Configuration::get('BLOCKSPECIALS_NB_CACHES') || !$this->isCached('blockspecials.tpl', $this->getCacheId('blockspecials|'.$random)))
		{
			if (!($special = Product::getRandomSpecial((int)$params['cookie']->id_lang)) && !Configuration::get('PS_BLOCK_SPECIALS_DISPLAY'))
				return;

			$this->smarty->assign(array(
				'special' => $special,
				'priceWithoutReduction_tax_excl' => Tools::ps_round($special['price_without_reduction'], 2),
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			));
		}

		return $this->display(__FILE__, 'blockspecials.tpl', $this->getCacheId('blockspecials|'.$random));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$this->context->controller->addCSS(($this->_path).'blockspecials.css', 'all');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('blockspecials.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('blockspecials.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('blockspecials.tpl');
	}
}