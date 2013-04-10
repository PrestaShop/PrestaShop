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

class ConfigurationTestCore
{

	/**
	 * getDefaultTests return an array of tests to executes.
	 * key are method name, value are parameters (false for no parameter)
	 * all path are _PS_ROOT_DIR_ related
	 *
	 * @return array
	 */
	public static function getDefaultTests()
	{
		return array(
			'system' => array(
				'fopen', 'fclose', 'fread', 'fwrite',
				'rename', 'file_exists', 'unlink', 'rmdir', 'mkdir',
				'getcwd', 'chdir', 'chmod'
				),

			'phpversion' => false,
			'upload' => false,
			'gd' => false,
			'mysql_support' => false,
			'config_dir' => 'config',
			'cache_dir' => 'cache',
			'sitemap' => 'sitemap.xml',
			'log_dir' => 'log',
			'img_dir' => 'img',
			'mails_dir' => 'mails',
			'module_dir' => 'modules',
			'theme_lang_dir' => 'themes/'._THEME_NAME_.'/lang/',
			'theme_pdf_lang_dir' => 'themes/'._THEME_NAME_.'/pdf/lang/',
			'theme_cache_dir' => 'themes/'._THEME_NAME_.'/cache/',
			'translations_dir' => 'translations',
			'customizable_products_dir' => 'upload',
			'virtual_products_dir' => 'download'
		);
	}

	/**
	 * getDefaultTestsOp return an array of tests to executes.
	 * key are method name, value are parameters (false for no parameter)
	 *
	 * @return array
	 */
	public static function getDefaultTestsOp()
	{
		return array(
			'fopen' => false,
			'register_globals' => false,
			'gz' => false,
			'mcrypt' => false,
			'mbstring' => false,
			'magicquotes' => false,
			'dom' => false,
			'pdo_mysql' => false,
		);
	}

	/**
	 * run all test defined in $tests
	 *
	 * @param array $tests
	 * @return array results of tests
	 */
	public static function check($tests)
	{
		$res = array();
		foreach ($tests as $key => $test)
			$res[$key] = ConfigurationTest::run($key, $test);
		return $res;
	}

	public static function run($ptr, $arg = 0)
	{
		if (call_user_func(array('ConfigurationTest', 'test_'.$ptr), $arg))
			return 'ok';
		return 'fail';
	}

	public static function test_phpversion()
	{
		return version_compare(substr(phpversion(), 0, 5), '5.1.0', '>=');
	}

	public static function test_mysql_support()
	{
		return extension_loaded('mysql') || extension_loaded('mysqli') || extension_loaded('pdo_mysql');
	}

	public static function test_pdo_mysql()
	{
		return extension_loaded('pdo_mysql');
	}

	public static function test_magicquotes()
	{
		return !get_magic_quotes_gpc();
	}

	public static function test_upload()
	{
		return ini_get('file_uploads');
	}

	public static function test_fopen()
	{
		return ini_get('allow_url_fopen');
	}

	public static function test_system($funcs)
	{
		foreach ($funcs as $func)
			if (!function_exists($func))
				return false;
		return true;
	}

	public static function test_gd()
	{
		return function_exists('imagecreatetruecolor');
	}

	public static function test_register_globals()
	{
		return !ini_get('register_globals');
	}

	public static function test_gz()
	{
		if (function_exists('gzencode'))
			return @gzencode('dd') !== false;
		return false;
	}

	public static function test_dir($relative_dir, $recursive = false, &$full_report = null)
	{
		$dir = rtrim(_PS_ROOT_DIR_, '\\/').DIRECTORY_SEPARATOR.trim($relative_dir, '\\/');
		if (!file_exists($dir) || !$dh = @opendir($dir))
		{
			$full_report = sprintf('Directory %s does not exists or is not writable', $dir); // sprintf for future translation
			return false;
		}
		$dummy = rtrim($dir, '\\/').DIRECTORY_SEPARATOR.uniqid();
		if (false && @file_put_contents($dummy, 'test'))
		{
			@unlink($dummy);
			if (!$recursive)
			{
				closedir($dh);
				return true;
			}
		}
		elseif (!is_writable($dir))
		{
			$full_report = sprintf('Directory %s is not writable', $dir); // sprintf for future translation
			return false;
		}
		
		if ($recursive)
			while (($file = readdir($dh)) !== false)
				if (is_dir($dir.DIRECTORY_SEPARATOR.$file) && $file != '.' && $file != '..' && $file != '.svn')
					if (!ConfigurationTest::test_dir($relative_dir.DIRECTORY_SEPARATOR.$file, $recursive, $full_report))
						return false;

		closedir($dh);
		return true;
	}

	public static function test_file($file_relative)
	{
		$file = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$file_relative;
		return (file_exists($file) && is_writable($file));
	}

	public static function test_config_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_sitemap($dir)
	{
		return ConfigurationTest::test_file($dir);
	}

	public static function test_root_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_log_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_admin_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_img_dir($dir)
	{
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_module_dir($dir)
	{
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_cache_dir($dir)
	{
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_tools_v2_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_cache_v2_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_download_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_mails_dir($dir)
	{
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_translations_dir($dir)
	{
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_theme_lang_dir($dir)
	{
		$absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/').DIRECTORY_SEPARATOR.trim($dir, '\\/');
		if (!file_exists($absoluteDir))
			return true;		
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_theme_pdf_lang_dir($dir)
	{
		$absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/').DIRECTORY_SEPARATOR.trim($dir, '\\/');
		if (!file_exists($absoluteDir))
			return true;
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_theme_cache_dir($dir)
	{
		$absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/').DIRECTORY_SEPARATOR.trim($dir, '\\/');
		if (!file_exists($absoluteDir))
			return true;
		return ConfigurationTest::test_dir($dir, true);
	}

	public static function test_customizable_products_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_virtual_products_dir($dir)
	{
		return ConfigurationTest::test_dir($dir);
	}

	public static function test_mbstring()
	{
		return function_exists('mb_strtolower');
	}

	public static function test_mcrypt()
	{
		return function_exists('mcrypt_encrypt');
	}

	public static function test_sessions()
	{
		if (!$path = @ini_get('session.save_path'))
			return true;

		return is_writable($path);
	}
	public static function test_dom()
	{
		return extension_loaded('Dom');
	}
}