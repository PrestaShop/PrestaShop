<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockSearch extends Module
{
	public function __construct()
	{
		$this->name = 'blocksearch';
		$this->tab = 'search_filter';
		$this->version = 1.2;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Quick Search block');
		$this->description = $this->l('Adds a block with a quick search field.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('header') || !$this->registerHook('displayMobileTopSiteMap'))
			return false;
		return true;
	}
	
	public function hookdisplayMobileTopSiteMap($params)
	{
		$this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
		return $this->hookTop($params);
	}
	
	/*
public function hookDisplayMobileHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
	}
*/
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		$this->context->controller->addCSS(($this->_path).'blocksearch.css', 'all');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		$this->calculHookCommon($params);
		$this->smarty->assign('blocksearch_type', 'block');
		return $this->display(__FILE__, 'blocksearch.tpl');
	}

	public function hookTop($params)
	{
		$this->calculHookCommon($params);
		$this->smarty->assign('blocksearch_type', 'top');
		return $this->display(__FILE__, 'blocksearch-top.tpl');
	}

	/**
	 * _hookAll has to be called in each hookXXX methods. This is made to avoid code duplication.
	 *
	 * @param mixed $params
	 * @return void
	 */
	private function calculHookCommon($params)
	{
		$this->smarty->assign(array(
			'ENT_QUOTES' =>		ENT_QUOTES,
			'search_ssl' =>		Tools::usingSecureMode(),
			'ajaxsearch' =>		Configuration::get('PS_SEARCH_AJAX'),
			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),
			'self' =>			dirname(__FILE__),
		));

		return true;
	}
}

