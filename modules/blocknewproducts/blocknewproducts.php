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

class BlockNewProducts extends Module
{
	public function __construct()
	{
		$this->name = 'blocknewproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.4';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('New products block');
		$this->description = $this->l('Displays a block featuring your store\'s newest products.');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('rightColumn')
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !Configuration::updateValue('NEW_PRODUCTS_NBR', 5)
		)
			return false;
		$this->_clearCache('blocknewproducts.tpl');
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('blocknewproducts.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockNewProducts'))
		{
			if (!($productNbr = Tools::getValue('productNbr')) || empty($productNbr))
				$output .= '<div class="alert error">'.$this->l('Please complete the "products to display" field.').'</div>';
			elseif ((int)($productNbr) == 0)
				$output .= '<div class="alert error">'.$this->l('Invalid number.').'</div>';
			else
			{
				Configuration::updateValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', (int)(Tools::getValue('always_display')));
				Configuration::updateValue('NEW_PRODUCTS_NBR', (int)($productNbr));
				$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Products to display.').'</label>
					<div class="margin-form">
						<input type="text" name="productNbr" value="'.(int)(Configuration::get('NEW_PRODUCTS_NBR')).'" />
						<p class="clear">'.$this->l('Define the number of products to be displayed in this block.').'</p>
					</div>
					<label>'.$this->l('Always display this block.').'</label>
					<div class="margin-form">
						<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) ? 'checked="checked" ' : '').'/>
						<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) ? 'checked="checked" ' : '').'/>
						<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
						<p class="clear">'.$this->l('Show the block even if no products are available.').'</p>
					</div>
					<center><input type="submit" name="submitBlockNewProducts" value="'.$this->l('Save').'" class="button" /></center>
				</fieldset>
			</form>';
		return $output;
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blocknewproducts.tpl', $this->getCacheId()))
		{
			if (!Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
				return;
			$newProducts = Product::getNewProducts((int) $params['cookie']->id_lang, 0, (int) Configuration::get('NEW_PRODUCTS_NBR'));
			if (!$newProducts)
				return;

			$this->smarty->assign(array(
				'new_products' => $newProducts,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			));
		}
		return $this->display(__FILE__, 'blocknewproducts.tpl', $this->getCacheId());
	}

	protected function getCacheId($name = null)
	{
		return parent::getCacheId('blocknewproducts|'.date('Ymd'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	
	public function hookHome($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocknewproducts.css', 'all');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
	}
}
