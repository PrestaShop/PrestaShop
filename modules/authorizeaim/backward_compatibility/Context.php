<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7723 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Retro 1.3, 'class_exists' cause problem with autoload...
if (version_compare(_PS_VERSION_, '1.4', '<'))
{
	// Not exist for 1.3
	class Shop extends ObjectModel
	{
		public function __construct()
		{

		}

		public static function getShops()
		{
			return array(
				array('id_shop' => 1, 'name' => 'Default shop')
			);
		}

		public static function getCurrentShop()
		{
				return 1;
		}
	}
}

// Not exist for 1.3 and 1.4
class Context
{
	/**
	 * @var Context
	 */
	protected static $instance;

	/**
	 * @var Cart
	 */
	public $cart;

	/**
	 * @var Customer
	 */
	public $customer;

	/**
	 * @var Cookie
	 */
	public $cookie;

	/**
	 * @var Link
	 */
	public $link;

	/**
	 * @var Country
	 */
	public $country;

	/**
	 * @var Employee
	 */
	public $employee;

	/**
	 * @var Controller
	 */
	public $controller;

	/**
	 * @var Language
	 */
	public $language;

	/**
	 * @var Currency
	 */
	public $currency;

	/**
	 * @var AdminTab
	 */
	public $tab;

	/**
	 * @var Shop
	 */
	public $shop;

	/**
	 * @var Smarty
	 */
	public $smarty;

	public function __construct()
	{
		global $cookie, $cart, $smarty, $link;

		$this->tab = null;

		if ($cookie)
			$this->cookie = $cookie;

		$this->cart = $cart;
		$this->smarty = $smarty;
		$this->link = $link;

		$this->controller = new ControllerBackwardModule();
		if ($cookie)
		{
			$this->currency = new Currency((int)$cookie->id_currency);
			$this->language = new Language((int)$cookie->id_lang);
			$this->country = new Country((int)$cookie->id_country);
			$this->customer = new Customer((int)$cookie->id_customer);
			$this->employee = new Employee((int)$cookie->id_employee);
		}
		$this->shop = new ShopBackwardModule();
	}

	/**
	 * Get a singleton context
	 *
	 * @return Context
	 */
	public static function getContext()
	{
		if (!isset(self::$instance))
			self::$instance = new Context();
		return self::$instance;
	}

	/**
	 * Clone current context
	 *
	 * @return Context
	 */
	public function cloneContext()
	{
		return clone($this);
	}

	/**
	 * @return int Shop context type (Shop::CONTEXT_ALL, etc.)
	 */
	public static function shop()
	{
		if (!self::$instance->shop->getContextType())
			return ShopBackwardModule::CONTEXT_ALL;
		return self::$instance->shop->getContextType();
	}
}

/**
 * Class Shop for Backward compatibility
 */
class ShopBackwardModule extends Shop
{
	const CONTEXT_ALL = 1;

	public function getContextType()
	{
		return ShopBackwardModule::CONTEXT_ALL;
	}

	// Simulate shop for 1.3 / 1.4
	public function getID()
	{
		return 1;
	}
}

/**
 * Class Controller for a Backward compatibility
 * Allow to use method declared in 1.5
 */
class ControllerBackwardModule
{
	/**
	 * @param $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		Tools::addJS($js_uri);
	}

	/**
	 * @param $css_uri
	 * @param string $css_media_type
	 * @return void
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		Tools::addCSS($css_uri, $css_media_type);
	}
}
