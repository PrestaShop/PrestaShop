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

$filePrefix = 'PREFIX_';
$engineType = 'ENGINE_TYPE';

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

define('_PS_MODULE_DIR_', realpath(INSTALL_PATH).'/../modules/');
define('_PS_INSTALLER_PHP_UPGRADE_DIR_', 'php/');
// desactivate non-native module 
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'desactivatecustommodules.php');
// utf-8 conversion if needed (before v0.9.8.1 utf-8 was badly supported)
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'utf8.php');
// Configuration cleaner in order to get unique configuration names
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'confcleaner.php');
// Order number in goal to add a number to each old orders
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'invoicenumber.php');
// Order number in goal to add a number to each old orders
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'deliverynumber.php');
// Set all groups for home category
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'setallgroupsonhomecategory.php');
// Set payment module for each currency/country
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'setpaymentmodule.php');
// Set payment module for each group
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'setpaymentmodulegroup.php');
// Set discount for all categories
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'setdiscountcategory.php');
// Module installation tools
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'module_tools.php');
// Customizations
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'customizations.php');
// Block newsletter
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'blocknewsletter.php');
// Reorder product positions for drag an drop
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'reorderpositions.php');
// Clean some module sql structures
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'updatemodulessql.php');
// Clean carrier URL
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'updatecarrierurl.php');
// Convert prices to the new 1.3 rounding system
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'price_converter.php');
// Update editorial module to delete all xml methods
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'editorial_update.php');
// Update logo and editorial image size
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'update_image_size_in_db.php');
// Update product comments
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'updateproductcomments.php');
// Update order details
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'update_order_details.php');
// Update database structure (use algorithms requiring PHP)
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'database_structure.php');
// Update cms block
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'cms_block.php');
// Move crossselling
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'move_crossselling.php');
// adminstores tab
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'admin_stores_tab.php');
// generate_tax_rules
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'generate_tax_rules.php');
// generate_ntree
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'generate_ntree.php');
// Before version 1.3.3 ecotax are not calculated into the price
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'update_products_ecotax_v133.php');

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'shop_url.php');

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'gridextjs_deprecated.php');
// generate level depth
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'regenerate_level_depth.php');
// add a new tab
require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'add_new_tab.php');

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'add_module_to_hook.php');

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'update_for_13version.php');

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'alter_cms_block.php');

//old version detection
global $oldversion;
$oldversion = false;
if (file_exists(SETTINGS_FILE) AND file_exists(DEFINES_FILE))
{
	include_once(SETTINGS_FILE);
	include_once(DEFINES_FILE);
	$oldversion = _PS_VERSION_;
}
else
	die('<action result="fail" error="30" />'."\n");
if (!file_exists(DEFINES_FILE))
	die('<action result="fail" error="37" />'."\n");
include_once(SETTINGS_FILE);

if (!defined('_THEMES_DIR_'))
	define('_THEMES_DIR_', __PS_BASE_URI__.'themes/');
if (!defined('_PS_IMG_'))
	define('_PS_IMG_', __PS_BASE_URI__.'img/');
if (!defined('_PS_JS_DIR_'))
	define('_PS_JS_DIR_', __PS_BASE_URI__.'js/');
if (!defined('_PS_CSS_DIR_'))
	define('_PS_CSS_DIR_', __PS_BASE_URI__.'css/');
include_once(DEFINES_FILE);

$oldversion = _PS_VERSION_;

$versionCompare =  version_compare(INSTALL_VERSION, _PS_VERSION_);
if ($versionCompare == '-1')
	die('<action result="fail" error="27" />'."\n");
elseif ($versionCompare == 0)
	die('<action result="fail" error="28" />'."\n");
elseif ($versionCompare === false)
	die('<action result="fail" error="29" />'."\n");

//check DB access
include_once(INSTALL_PATH.'/classes/ToolsInstall.php');
$resultDB = ToolsInstall::checkDB(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, false);
if ($resultDB !== true)
	die("<action result='fail' error='".$resultDB."'/>\n");

//custom sql file creation
$upgradeFiles = array();
if ($handle = opendir(INSTALL_PATH.'/sql/upgrade'))
{
    while (false !== ($file = readdir($handle)))
        if ($file != '.' AND $file != '..')
            $upgradeFiles[] = str_replace(".sql", "", $file);
    closedir($handle);
}
if (empty($upgradeFiles))
	die('<action result="fail" error="31" />'."\n");
natcasesort($upgradeFiles);
$neededUpgradeFiles = array();
foreach ($upgradeFiles AS $version)
	if (version_compare($version, _PS_VERSION_) == 1 AND version_compare(INSTALL_VERSION, $version) != -1)
		$neededUpgradeFiles[] = $version;
if (empty($neededUpgradeFiles))
	die('<action result="fail" error="32" />'."\n");


//refresh conf file
include(INSTALL_PATH.'/classes/AddConfToFile.php');
$oldLevel = error_reporting(E_ALL);
$mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM');
$datas = array(
	array('_DB_SERVER_', _DB_SERVER_),
	array('_DB_TYPE_', _DB_TYPE_),
	array('_DB_NAME_', _DB_NAME_),
	array('_DB_USER_', _DB_USER_),
	array('_DB_PASSWD_', _DB_PASSWD_),
	array('_DB_PREFIX_', _DB_PREFIX_),
	array('_MYSQL_ENGINE_', $mysqlEngine),
	array('_PS_CACHING_SYSTEM_', (defined('_PS_CACHING_SYSTEM_') AND _PS_CACHING_SYSTEM_ != 'MemCached') ? _PS_CACHING_SYSTEM_ : 'MCached'),
	array('_PS_CACHE_ENABLED_', defined('_PS_CACHE_ENABLED_') ? _PS_CACHE_ENABLED_ : '0'),
	array('_MEDIA_SERVER_1_', defined('_MEDIA_SERVER_1_') ? _MEDIA_SERVER_1_ : ''),
	array('_MEDIA_SERVER_2_', defined('_MEDIA_SERVER_2_') ? _MEDIA_SERVER_2_ : ''),
	array('_MEDIA_SERVER_3_', defined('_MEDIA_SERVER_3_') ? _MEDIA_SERVER_3_ : ''),
	array('__PS_BASE_URI__', __PS_BASE_URI__),
	array('_THEME_NAME_', _THEME_NAME_),
	array('_COOKIE_KEY_', _COOKIE_KEY_),
	array('_COOKIE_IV_', _COOKIE_IV_),
	array('_PS_CREATION_DATE_', defined("_PS_CREATION_DATE_") ? _PS_CREATION_DATE_ : date('Y-m-d')),
	array('_PS_VERSION_', INSTALL_VERSION)
);
if (defined('_RIJNDAEL_KEY_'))
	$datas[] = array('_RIJNDAEL_KEY_', _RIJNDAEL_KEY_);
if (defined('_RIJNDAEL_IV_'))
	$datas[] = array('_RIJNDAEL_IV_', _RIJNDAEL_IV_);
if(!defined('_PS_CACHE_ENABLED_'))
	define('_PS_CACHE_ENABLED_', '0');
if(!defined('_MYSQL_ENGINE_'))
	define('_MYSQL_ENGINE_', 'MyISAM');

$sqlContent = '';
if(isset($_GET['customModule']) AND $_GET['customModule'] == 'desactivate')
	desactivate_custom_modules();

foreach($neededUpgradeFiles AS $version)
{
	$file = INSTALL_PATH.'/sql/upgrade/'.$version.'.sql';
	if (!file_exists($file))
		die('<action result="fail" error="33" />'."\n");
	if (!$sqlContent .= file_get_contents($file))
		die('<action result="fail" error="33" />'."\n");
	$sqlContent .= "\n";
}

$sqlContent = str_replace(array($filePrefix, $engineType), array(_DB_PREFIX_, $mysqlEngine), $sqlContent);
$sqlContent = preg_split("/;\s*[\r\n]+/",$sqlContent);

error_reporting($oldLevel);
$confFile = new AddConfToFile(SETTINGS_FILE, 'w');
if ($confFile->error)
	die('<action result="fail" error="'.$confFile->error.'" />'."\n");

foreach ($datas AS $data){
	$confFile->writeInFile($data[0], $data[1]);
}

if ($confFile->error != false)
	die('<action result="fail" error="'.$confFile->error.'" />'."\n");

// Settings updated, compile and cache directories must be emptied
foreach (array(INSTALL_PATH.'/../tools/smarty/cache/', INSTALL_PATH.'/../tools/smarty/compile/', INSTALL_PATH.'/../tools/smarty_v2/cache/', INSTALL_PATH.'/../tools/smarty_v2/compile/') as $dir)
	if (file_exists($dir))
		foreach (scandir($dir) as $file)
			if ($file[0] != '.' AND $file != 'index.php')
				unlink($dir.$file);

//sql file execution
global $requests, $warningExist;
$requests = '';
$warningExist = false;

Configuration::loadConfiguration();

foreach($sqlContent as $query)
{
	$query = trim($query);
	if(!empty($query))
	{
		/* If php code have to be executed */
		if (strpos($query, '/* PHP:') !== false)
		{
			/* Parsing php code */
			$pos = strpos($query, '/* PHP:') + strlen('/* PHP:');
			$phpString = substr($query, $pos, strlen($query) - $pos - strlen(' */;'));
			$php = explode('::', $phpString);
			preg_match('/\((.*)\)/', $phpString, $pattern);
			$paramsString = trim($pattern[0], '()');
			preg_match_all('/([^,]+),? ?/', $paramsString, $parameters);
			if (isset($parameters[1]))
				$parameters = $parameters[1];
			else
				$parameters = array();
			if (is_array($parameters))
				foreach ($parameters AS &$parameter)
					$parameter = str_replace('\'', '', $parameter);

			/* Call a simple function */
			if (strpos($phpString, '::') === false)
				call_user_func_array(str_replace($pattern[0], '', $php[0]), $parameters);
			/* Or an object method */
			else
				call_user_func_array(array($php[0], str_replace($pattern[0], '', $php[1])), $parameters);
			$requests .=
'	<request result="ok">
		<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
	</request>'."\n";
		}
		elseif(!Db::getInstance()->Execute($query))
		{
			$warningExist = true;
			$requests .=
'	<request result="fail">
		<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
		<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
		<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
	</request>'."\n";
		}
		else
			$requests .=
'	<request result="ok">
		<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
	</request>'."\n";
	}
}
Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 0);
$result = $warningExist ? '<action result="fail" error="34">'."\n" : '<action result="ok" error="">'."\n";
$result .= $requests;
die($result.'</action>'."\n");

