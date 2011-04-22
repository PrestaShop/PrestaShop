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
require (_PS_MODULE_DIR_.'/trustedshops/lib/AbsTrustedShops.php');
require (_PS_MODULE_DIR_.'/trustedshops/lib/TrustedShopsRating.php');
require (_PS_MODULE_DIR_.'/trustedshops/lib/TSBuyerProtection.php');

class TrustedShops extends Module
{
	/**
	 * Saved each Object needed list of AbsTrustedShops extended objects
	 * @var array
	 */
	private static $objects_list = array();
	private $errors = array();
	private $warnings = array();
	public $limited_countries = array();
	private $confirmations = array();

	public function __construct()
	{
		global $smarty;
		$this->name = 'trustedshops';
		$this->tab = 'payment_security';
		$this->version = 1.2;

		parent::__construct();

		if (empty(self::$objects_list))
		{
			TSBuyerProtection::setTranslationObject($this);
			$obj_ts_rating = new TrustedShopsRating();
			$obj_ts_buyerprotection = new TSBuyerProtection();
			$obj_ts_buyerprotection->_setEnvApi('production');
			self::$objects_list = array($obj_ts_rating, $obj_ts_buyerprotection);
			self::$objects_list[0]->setModuleName($this->name);
			self::$objects_list[0]->setSmarty($smarty);
		}

		if (!extension_loaded('soap'))
			$this->warnings[] = $this->l('This module requires the SOAP PHP extension to function properly.');

		foreach (self::$objects_list as $object)
		{
			$this->limited_countries = array_merge($this->limited_countries, $object->limited_countries);
			if(!empty($object->warnings))
				$this->warnings = array_merge($this->warnings, $object->warnings);
		}

		if (!empty($this->warnings))
			$this->warning = implode(',<br />', $this->warnings).'.';

		$this->displayName = $this->l('Trusted Shops Customer Rating');
		$this->description = $this->l('Boost consumer confidence and turn more shoppers into buyers.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all your settings?');
	}

	public function install()
	{
		$return = true;
		foreach (self::$objects_list as $object)
		{
			$return = $object->install();
			if (!$return)
				break;
		}
		$return = ($return) ? (parent::install() AND $this->registerHook('orderConfirmation') AND $this->registerHook('newOrder') AND $this->registerHook('rightColumn') AND $this->registerHook('paymentTop') AND $this->registerHook('orderConfirmation')) : $return;
		$id_hook = Hook::get('payment');
		$this->updatePosition($id_hook, 0, 1);
		return $return;
	}

	public function uninstall()
	{
		$return = true;
		foreach (self::$objects_list as $object)
		{
			$return = $object->uninstall();
			if (!$return)
				break;
		}
		$return = ($return) ? parent::uninstall() : $return;
		return $return;
	}

	public function getContent()
	{
		$out = '<h2>'.$this->displayName.'</h2>';
		$tabs = array();

		foreach (self::$objects_list as $key=>$object)
		{
			$object->id_tab = $key;
			$tabs['title'][] = $object->tab_name;
			$tabs['content'][] = $object->getContent();
		}
		// Display Title Tabs
		$out .= '<ul id="menuTabs">';
		foreach($tabs['title'] as $key=>$title)
			$out .= '<li id="menuTab'.$key.'" class="menuTabButton'.( (int)$key === (int)Tools::getValue('id_tab') ? ' selected' : '' ).'">'.($key+1).'. '.$title.'</li>';
		$out .= '</ul>';

		// Display content Tabs
		$out .= '<div id="tabList">';
		foreach($tabs['content'] as $key=>$content)
			$out .= '<div id="menuTab'.$key.'Sheet" class="tabItem'.( (int)$key === (int)Tools::getValue('id_tab') ? ' selected' : '' ).'">'.$content.'</div>';
		$out .= '<br clear="left" />'.$this->displayCSSJSTab();

		// Check If each object (display as Tab) contains errors message of
		$this->checkObjectsErrorsOrConfirmations();
		return ( !empty($this->errors) ? $this->displayErrors() : $this->displayConfirmations() ).$out;
	}

	private function displayCSSJSTab()
	{
		$id_tab = isset($_GET['id_tab']) ? (int)$_GET['id_tab'] : 0;
		return '
		<style>
			#menuTabs { float: left; padding: 0; text-align: left; margin:0}
			#menuTabs li { text-align: left; float: left; display: inline; padding: 5px 10px 5px 5px; background: #EFEFEF; font-weight: bold; cursor: pointer; border-left: 1px solid #EFEFEF; border-right: 1px solid #EFEFEF; border-top: 1px solid #EFEFEF; }
			#menuTabs li.menuTabButton.selected { background: #FFF6D3; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; }
			#tabList { clear: left;}
			.tabItem { display: none; }
			.tabItem.selected { display: block; background: #fcfcfc; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px;}
		</style>
		<script>
			$().ready(function()
			{
				$("#menuTab'.$id_tab.'Sheet").addClass("selected");
				$("#menuTab'.$id_tab.'").addClass("selected");
			});
			$(".menuTabButton").click(function ()
			{
				$(".menuTabButton.selected").removeClass("selected");
				$(this).addClass("selected");
				$(".tabItem.selected").removeClass("selected");
				$("#" + this.id + "Sheet").addClass("selected");
			});
		</script>
		';
	}

	/**
	 * Check If each object (display as Tab) contains errors message of
	 *
	 * @return void
	 */
	private function checkObjectsErrorsOrConfirmations()
	{
		foreach (self::$objects_list as $object)
		{
			if(!empty($object->errors))
				$this->errors = array_merge($this->errors, $object->errors);
			if(!empty($object->confirmations))
				$this->confirmations = array_merge($this->confirmations, $object->confirmations);
		}
	}
	private function displayConfirmations()
	{
		$html = '';
		if(!empty($this->confirmations))
			foreach ($this->confirmations as $confirmations)
				$html .= $this->displayConfirmation($confirmations);
		return $html;
	}
	private function displayErrors()
	{
		$html = '';
		if(!empty($this->errors))
			foreach ($this->errors as $error)
				$html .= $this->displayError($error);
		return $html;
	}

	public function hookOrderConfirmation($params)
	{
		return $this->dynamicHook($params, __FUNCTION__);
	}

	public function hookNewOrder($params)
	{
		return $this->dynamicHook($params, __FUNCTION__);
	}

	public function hookRightColumn($params)
	{
		return $this->dynamicHook($params, __FUNCTION__);
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookPaymentTop($params)
	{
		return $this->dynamicHook($params, __FUNCTION__);
	}

	private function dynamicHook($params, $hook_name)
	{
		if(!$this->active)
			return '';
		$return = '';
		foreach (self::$objects_list as $object)
			if (method_exists($object, $hook_name))
				$return .= $object->{$hook_name}($params);
		return $return;
	}
}

