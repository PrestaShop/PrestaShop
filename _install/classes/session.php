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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Manage session for install script
 */
class InstallSession
{
	protected static $_instance;

	public static function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new self();
		return self::$_instance;
	}

	public function __construct()
	{
		session_name('install_'.md5(__PS_BASE_URI__));
		session_start();
	}

	public function clean()
	{
		foreach ($_SESSION as $k => $v)
			unset($_SESSION[$k]);
	}

	public function &__get($varname)
	{
		if (isset($_SESSION[$varname]))
			$ref = &$_SESSION[$varname];
		else
		{
			$null = null;
			$ref = &$null;
		}
		return $ref;
	}

	public function __set($varname, $value)
	{
		$_SESSION[$varname] = $value;
	}

	public function __isset($varname)
	{
		return isset($_SESSION[$varname]);
	}

	public function __unset($varname)
	{
		unset($_SESSION[$varname]);
	}
}
