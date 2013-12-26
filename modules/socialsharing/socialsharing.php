<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
    exit;

class SocialSharing extends Module
{
	protected static $_networks = array('Facebook', 'Twitter', 'Google');
	protected $_html = '';

	public function __construct()
	{
		$this->name = 'socialsharing';
		$this->author = 'PrestaShop';
		$this->tab = 'advertising_marketing';
		$this->need_instance = 0;
		$this->version = '0.9';
		$this->bootstrap = true;
		$this->_directory = dirname(__FILE__);

		parent::__construct();	

		$this->displayName = $this->l('Social Sharing');
		$this->description = $this->l('Display social sharing buttons (twitter, facebook, google plus)');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		// Activate every option by default
		Configuration::updateValue('PS_SC_TWITTER', 1);
		Configuration::updateValue('PS_SC_GOOGLE', 1);
		Configuration::updateValue('PS_SC_FACEBOOK', 1);
		
		// The module will add a meta in the product page header and add a javascript file
		$this->registerHook('header');
		
		// This hook could have been called only from the product page, but it's better to add the JS in all the pages with CCC
		/*
			$id_hook_header = Hook::getIdByName('header');
			$pages = array();
			foreach (Meta::getPages() as $page)
				if ($page != 'product')
					$pages[] = $page;
			$this->registerExceptions($id_hook_header, $pages);
		*/

		// The module need to clear the product page cache after update/delete
		$this->registerHook('actionObjectProductUpdateAfter');
		$this->registerHook('actionObjectProductDeleteAfter');

		// The module will then be hooked on the product and comparison pages
		$this->registerHook('displayRightColumnProduct');
		$this->registerHook('displayCompareExtraInformation');
		
		return true;
	}

	public function getConfigFieldsValues()
	{
		$values = array();
		foreach (self::$_networks as $network)
			$values['PS_SC_'.strtoupper($network)] = (int)Tools::getValue('PS_SC_'.strtoupper($network), Configuration::get('PS_SC_'.strtoupper($network)));
		return $values;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitSocialSharing'))
		{
			foreach (self::$_networks as $network)
				Configuration::updateValue('PS_SC_'.strtoupper($network), (int)Tools::getValue('PS_SC_'.strtoupper($network)));
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
		}

		$helper = new HelperForm();
		$helper->submit_action = 'submitSocialSharing';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array('fields_value' => $this->getConfigFieldsValues());

		$fields = array();
		foreach (self::$_networks as $network)
			$fields[] = array(
				'type' => 'switch',
				'label' => $network,
				'name' => 'PS_SC_'.strtoupper($network),
				'values' => array(
					array(
						'id' => strtolower($network).'_active_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => strtolower($network).'_active_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				)
			);

		return $this->_html.$helper->generateForm(array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->displayName,
						'icon' => 'icon-envelope'
					),
					'input' => $fields,
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'btn btn-default'
					)
				)
			)
		));
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addJS($this->_path.'js/socialsharing.js');

		// Exception are managed with Module::registerExceptions() but this is needed in case the merchant added new controllers afterwards
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'product')
			return;

		$product = $this->context->controller->getProduct();
		if (!$this->isCached('socialsharing_header.tpl', $this->getCacheId('socialsharing_header|'.(int)$product->id)))
		{
			$this->context->smarty->assign(array(
				'cover' => Product::getCover($product->id),
				'legend' => $product->link_rewrite,
			));
		}
		return $this->display(__FILE__, 'socialsharing_header.tpl', $this->getCacheId('socialsharing_header|'.(int)$product->id));
	}

	protected function displaySocialSharing()
	{
		if (!$this->isCached('socialsharing.tpl', $this->getCacheId()))
		{
			$this->context->smarty->assign(array(
				'product' => $this->context->controller->getProduct(),
				'PS_SC_TWITTER' => Configuration::get('PS_SC_TWITTER'),
				'PS_SC_GOOGLE' => Configuration::get('PS_SC_GOOGLE'),
				'PS_SC_FACEBOOK' => Configuration::get('PS_SC_FACEBOOK')
			));
		}
		return $this->display(__FILE__, 'socialsharing.tpl', $this->getCacheId());
	}

	protected function clearProductHeaderCache($id_product)
	{
		return $this->_clearCache('socialsharing_header.tpl', 'socialsharing_header|'.(int)$id_product);
	}

	public function hookDisplayCompareExtraInformation($params)
	{
		if (!$this->isCached('socialsharing_compare.tpl', $this->getCacheId('socialsharing_compare')))
		{
			$this->context->smarty->assign(array(
				'PS_SC_TWITTER' => Configuration::get('PS_SC_TWITTER'),
				'PS_SC_GOOGLE' => Configuration::get('PS_SC_GOOGLE'),
				'PS_SC_FACEBOOK' => Configuration::get('PS_SC_FACEBOOK')
			));
		}
		return $this->display(__FILE__, 'socialsharing_compare.tpl', $this->getCacheId('socialsharing_compare'));
	}

	public function hookDisplayRightColumnProduct($params)
	{
		return $this->displaySocialSharing();
	}

	public function hookLeftColumn($params)
	{
		return $this->displaySocialSharing();
	}

	public function hookFooter($params)
	{
		return $this->displaySocialSharing();
	}
	
	public function hookHome($params)
	{
		return $this->displaySocialSharing();
	}

	public function hookExtraleft($params)
	{
		return $this->displaySocialSharing();
	}

	public function hookProductActions($params)
	{
		return $this->displaySocialSharing();
	}
	
	public function hookProductFooter($params)
	{
		return $this->displaySocialSharing();
	}

	public function hookActionObjectProductUpdateAfter($params)
	{
		return $this->clearProductHeaderCache($params['object']->id);
	}
	
	public function hookActionObjectProductDeleteAfter($params)
	{
		return $this->clearProductHeaderCache($params['object']->id);
	}
}