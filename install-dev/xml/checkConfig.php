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
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé
include_once(INSTALL_PATH.'/classes/ConfigurationTest.php');

// Functions list to test with 'test_system'
$funcs = array('fopen', 'fclose', 'fread', 'fwrite', 'rename', 'file_exists', 'unlink', 'rmdir', 'mkdir', 'getcwd', 'chdir', 'chmod');

// Test list to execute (function/args)
$tests = array(
	'phpversion' => false,
	'upload' => false,
	'system' => $funcs,
	'gd' => false,
	'mysql_support' => false,
	'config_dir' => INSTALL_PATH.'/../config/',
	'tools_dir' => INSTALL_PATH.'/../tools/smarty/compile/',
	'cache_dir' => INSTALL_PATH.'/../tools/smarty/cache/',
	'tools_v2_dir' => INSTALL_PATH.'/../tools/smarty_v2/compile/',
	'cache_v2_dir' => INSTALL_PATH.'/../tools/smarty_v2/cache/',
	'sitemap' => INSTALL_PATH.'/../sitemap.xml',
	'img_dir' => INSTALL_PATH.'/../img/',
	'mails_dir' => INSTALL_PATH.'/../mails/',
	'module_dir' => INSTALL_PATH.'/../modules/',
	'theme_lang_dir' => INSTALL_PATH.'/../themes/prestashop/lang/',
	'theme_cache_dir' => INSTALL_PATH.'/../themes/prestashop/cache/',
	'translations_dir' => INSTALL_PATH.'/../translations/',
	'customizable_products_dir' => INSTALL_PATH.'/../upload/',
	'virtual_products_dir' => INSTALL_PATH.'/../download/',
);
$tests_op = array(
	'fopen' => false,
	'register_globals' => false,
	'gz' => false,
	'mcrypt' => false,
);

// Execute tests
$res = ConfigurationTest::check($tests);
$res_op = ConfigurationTest::check($tests_op);

// Building XML Tree...
echo '<config>'."\n";
	echo '<firsttime value="'.((isset($_GET['firsttime']) AND $_GET['firsttime'] == 1) ? 1 : 0).'" />'."\n";
	echo '<testList id="required">'."\n";
	foreach ($res AS $key => $line)
		echo '<test id="'.$key.'" result="'.$line.'"/>'."\n";
	echo '</testList>'."\n";
	echo '<testList id="optional">'."\n";
	foreach ($res_op AS $key => $line)
		echo '<test id="'.$key.'" result="'.$line.'"/>'."\n";
	echo '</testList>'."\n";
echo '</config>';
