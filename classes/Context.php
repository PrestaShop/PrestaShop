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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ContextCore
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

	/**
	 * @var boolean|string mobile device of the customer
	 */
	protected $mobile_device;

	/**
	 * @var boolean|string touch pad device of the customer
	 */
	protected $touchpad_device;

	public function getMobileDevice()
	{
		if (is_null($this->mobile_device))
		{
			$this->mobile_device = false;
			if ($this->checkMobileContext())
				if (preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipaq|ipod|j2me|java|midp|mini|mmp|mobi\s|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|zte)/i', $_SERVER['HTTP_USER_AGENT'], $out))
					$this->mobile_device = $out[0];
		}

		return $this->mobile_device;
	}

	protected function checkMobileContext()
	{
		return file_exists(_PS_THEME_MOBILE_DIR_)
			&& Configuration::get('PS_ALLOW_MOBILE_DEVICE')
			&& isset($_SERVER['HTTP_USER_AGENT']);
	}

	protected function getTouchPadDevice()
	{
		if (is_null($this->touchpad_device))
		{
			$this->touchpad_device = false;
			if ($this->checkMobileContext())
			{
				if (preg_match('/(xoom|ipad)/i', $_SERVER['HTTP_USER_AGENT'], $out))
					$this->touchpad_device = $out[0];
				if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') && !strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile'))
					$this->touchpad_device = 'android';

				// for Galaxy tab
				if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')
					&& (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'sch-i800')
						|| strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'gt-p1000')))
					$this->touchpad_device = 'android';
			}
		}
		return $this->touchpad_device;
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
}