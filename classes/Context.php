<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class ContextCore
{
	protected static $instance;

	public $cart;
	public $customer;
	public $cookie;
	public $link;
	public $country;
	public $employee;
	public $controller;
	public $lang;
	public $currency;
	public $tab;
	
	/**
	 * Create a context without singleton constraint
	 */
	public function __construct($cart = null, 
								$customer = null,
								$cookie = null,
								$link = null,
								$country = null,
								$employee = null,
								$lang = null,
								$currency = null,
								$tab = null)
	{
		$this->cart = $cart;
		$this->customer = $customer;
		$this->cookie = $cookie;
		$this->link = $link;
		$this->country = $country;
		$this->employee = $employee;
		$this->lang = $lang;
		$this->currency = $currency;
		$this->tab = $tab;
	}
	
	/**
	 * Get a singleton context
	 *
	 * @return Context
	 */
	public static function getContext()
	{
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}
}