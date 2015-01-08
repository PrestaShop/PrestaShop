<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Hook extends HookCore
{
	private static $executedModules = array();
	private static $hookTime = array();
	private static $hookMemoryUsage = array();
	
	public static function getHookTime()
	{
		return self::$hookTime;
	}
	
	public static function getHookMemoryUsage()
	{
		return self::$hookMemoryUsage;
	}
	
	public static function getExecutedModules()
	{
		return self::$executedModules;
	}

	public static function exec($hook_name, $hook_args = array(), $id_module = null, $array_return = false, $check_exceptions = true, $use_push = false, $id_shop = null)
	{
		$module_list = Hook::getHookModuleExecList($hook_name);
		if (is_array($module_list))
		{
			$module_list_ids = array();
			foreach ($module_list as $module)
				$module_list_ids[] = $module['id_module'];
			self::$executedModules = array_merge(self::$executedModules, $module_list_ids);
		}
		$memoryUsage = memory_get_usage();
		$t0 = microtime(true);
		$result = parent::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop);
		self::$hookTime[$hook_name] = microtime(true) - $t0;
		self::$hookMemoryUsage[$hook_name] = memory_get_usage() - $memoryUsage;
		return $result;
	}
}
