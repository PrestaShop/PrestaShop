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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class CacheCore
{
	
	protected static $_instance;
	protected $_keysCached;
	protected $_tablesCached = array();
	protected $_blackList = array('cart',
												'cart_discount',
												'cart_product',
												'connections',
												'connections_source',
												'connections_page',
												'customer',
												'customer_group',
												'customized_data',
												'guest',
												'pagenotfound',
												'page_viewed');
	public static function getInstance()
	{	
		if(!isset(self::$_instance))
		{
			$caching_system =  _PS_CACHING_SYSTEM_;
			self::$_instance = new $caching_system();
			
		}
		return self::$_instance;
	}
	
	protected function __construct()
	{
	}
	
	protected function __destruct()
	{
	}
	
	protected function isBlacklist($query)
	{
		foreach ($this->_blackList AS $find)
			if (strpos($query, $find))
				return true;
		return false;
	}

	abstract public function get($key);
	abstract public function delete($key, $timeout = 0);
	abstract public function set($key, $value, $expire = 0);
	abstract public function flush();
	abstract public function setQuery($query, $result);
	abstract public function deleteQuery($query);
	
}
