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

class	ConfigurationTest
{
	static function		check($tests)
	{
		$res = array();
		foreach ($tests AS $key => $test)
			$res[$key] = self::run($key, $test);
		return $res;
	}
	
	static function		run($ptr, $arg = 0)
	{
		if (call_user_func(array('ConfigurationTest', 'test_'.$ptr), $arg))
			return ('ok');
		return ('fail');
	}
	
	// Misc functions	
	static function		test_phpversion()
	{
		return version_compare(substr(phpversion(), 0, 3), '5.0', '>=');
	}
	
	static function		test_mysql_support()
	{
		return function_exists('mysql_connect');
	}

	static	function		test_upload()
	{
		return  ini_get('file_uploads');
	}

	static function		test_fopen()
	{
		return ini_get('allow_url_fopen');
	}

	static function		test_system($funcs)
	{
		foreach ($funcs AS $func)
			if (!function_exists($func))
				return false;
		return true;
	}

	static function		test_gd()
	{
		return function_exists('imagecreatetruecolor');
	}
	
	static function		test_register_globals()
	{
		return !ini_get('register_globals');
	}
	
	static function		test_gz()
	{
		if (function_exists('gzencode'))
			return !(@gzencode('dd') === false); 
		return false;
	}
	
	// is_writable dirs	
	static function		test_dir($dir, $recursive = false)
	{
		if (!file_exists($dir) OR !$dh = opendir($dir))
			return false;
		$dummy = rtrim($dir, '/').'/'.uniqid();
		if (@file_put_contents($dummy, 'test'))
		{
			@unlink($dummy);
			if (!$recursive)
				return true;
		}
		elseif (!is_writable($dir))
			return false;
		if ($recursive)
		{
			while (($file = readdir($dh)) !== false)
				if (@filetype($dir.$file) == 'dir' AND $file != '.' AND $file != '..')
					if (!self::test_dir($dir.$file, true))
						return false;
		}
		closedir($dh);
		return true;
	}
	
	// is_writable files	
	static function		test_file($file)
	{
		return (file_exists($file) AND is_writable($file));
	}
	
	static function		test_config_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_sitemap($dir)
	{
		return self::test_file($dir);
	}
	
	static function		test_root_dir($dir)
	{
		return self::test_dir($dir);
	}

	static function		test_admin_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_img_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static function		test_module_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static function		test_tools_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_cache_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_tools_v2_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_cache_v2_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_download_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_mails_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static function		test_translations_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static function		test_theme_lang_dir($dir)
	{
		if (!file_exists($dir))
			return true;
		return self::test_dir($dir, true);
	}
	
	static function		test_theme_cache_dir($dir)
	{
		if (!file_exists($dir))
			return true;
		return self::test_dir($dir, true);
	}

	static function		test_customizable_products_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_virtual_products_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_mcrypt()
	{
		return function_exists('mcrypt_encrypt');
	}
}
