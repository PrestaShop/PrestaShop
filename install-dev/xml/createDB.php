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

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

//delete settings file if it exist
if (file_exists(SETTINGS_FILE))
	if (!unlink(SETTINGS_FILE))
		die('<action result="fail" error="17" />'."\n");

include(INSTALL_PATH.'/classes/AddConfToFile.php');
include(INSTALL_PATH.'/../classes/Validate.php');
include(INSTALL_PATH.'/../classes/Db.php');
include(INSTALL_PATH.'/../classes/Tools.php');

//check db access
include_once(INSTALL_PATH.'/classes/ToolsInstall.php');
$resultDB = ToolsInstall::checkDB($_GET['server'], $_GET['login'], $_GET['password'], $_GET['name'], true, $_GET['engine']);
if ($resultDB !== true){
	die("<action result='fail' error='".$resultDB."'/>\n");
}


// Check POST data...
$data_check = array(
	!isset($_GET['mode']) OR ( $_GET['mode'] != "full" AND $_GET['mode'] != "lite"),
	!isset($_GET['tablePrefix']) OR !Validate::isMailName($_GET['tablePrefix']) OR !preg_match('/^[a-z0-9_]*$/i', $_GET['tablePrefix'])
);
foreach ($data_check AS $data)
	if ($data)
		die('<action result="fail" error="8"/>'."\n");

// Writing data in settings file
$oldLevel = error_reporting(E_ALL);
$__PS_BASE_URI__ = str_replace(' ', '%20', INSTALLER__PS_BASE_URI);
$datas = array(
	array('_DB_SERVER_', trim($_GET['server'])),
	array('_DB_TYPE_', trim($_GET['type'])),
	array('_DB_NAME_', trim($_GET['name'])),
	array('_DB_USER_', trim($_GET['login'])),
	array('_DB_PASSWD_', trim($_GET['password'])),
	array('_DB_PREFIX_', trim($_GET['tablePrefix'])),
	array('_MYSQL_ENGINE_', $_GET['engine']),
	array('__PS_BASE_URI__', $__PS_BASE_URI__),
	array('_PS_CACHING_SYSTEM_', 'MCached'),
	array('_PS_CACHE_ENABLED_', '0'),
	array('_MEDIA_SERVER_1_', ''),
	array('_MEDIA_SERVER_2_', ''),
	array('_MEDIA_SERVER_3_', ''),
	array('_THEME_NAME_', 'prestashop'),
	array('_COOKIE_KEY_', Tools::passwdGen(56)),
	array('_COOKIE_IV_', Tools::passwdGen(8)),
	array('_PS_CREATION_DATE_', date('Y-m-d')),
	array('_PS_VERSION_', INSTALL_VERSION)
);
error_reporting($oldLevel);
$confFile = new AddConfToFile(SETTINGS_FILE, 'w');
if ($confFile->error)
	die('<action result="fail" error="'.$confFile->error.'" />'."\n");
	
foreach ($datas AS $data){
	$confFile->writeInFile($data[0], $data[1]);
}
$confFile->writeEndTagPhp();

// Settings updated, compile and cache directories must be emptied
foreach (array(INSTALL_PATH.'/../tools/smarty/cache/', INSTALL_PATH.'/../tools/smarty/compile/', INSTALL_PATH.'/../tools/smarty_v2/cache/', INSTALL_PATH.'/../tools/smarty_v2/compile/') as $dir)
	if (file_exists($dir))
		foreach (scandir($dir) as $file)
			if ($file[0] != '.' AND $file != 'index.php')
				unlink($dir.$file);

if ($confFile->error != false)
	die('<action result="fail" error="'.$confFile->error.'" />'."\n");

//load new settings
include(SETTINGS_FILE);

//-----------
//import SQL data
//-----------
switch (_DB_TYPE_) {
	case "MySQL" :
		
		$filePrefix = 'PREFIX_';
		$engineType = 'ENGINE_TYPE';
		//send the SQL structure file requests
		$structureFile = dirname(__FILE__)."/../sql/db.sql";
		if(!file_exists($structureFile))
			die('<action result="fail" error="10" />'."\n");
		$db_structure_settings ="";
		if ( !$db_structure_settings .= file_get_contents($structureFile) )
			die('<action result="fail" error="9" />'."\n");
		$db_structure_settings = str_replace(array($filePrefix, $engineType), array($_GET['tablePrefix'], $_GET['engine']), $db_structure_settings);
		$db_structure_settings = preg_split("/;\s*[\r\n]+/",$db_structure_settings);
		if (isset($_GET['dropAndCreate']) && $_GET['dropAndCreate'] == 'true')
		{
			array_unshift($db_structure_settings, 'USE `'.trim($_GET['name']).'`;');
			array_unshift($db_structure_settings, 'CREATE DATABASE `'.trim($_GET['name']).'`;');
			array_unshift($db_structure_settings, 'DROP DATABASE `'.trim($_GET['name']).'`;');
		}
		foreach($db_structure_settings as $query){
			$query = trim($query);
			if(!empty($query)){
				if(!Db::getInstance()->Execute($query)){
					if(Db::getInstance()->getNumberError() == 1050){
						die('<action result="fail" error="14" />'."\n");
					} else {
						die(
							'<action
							result="fail"
							error="11"
							sqlMsgError="'.addslashes(htmlentities(Db::getInstance()->getMsgError())).'"
							sqlNumberError="'.htmlentities(Db::getInstance()->getNumberError()).'"
							sqlQuery="'.addslashes(htmlentities($query)).'"
							/>'
						);
					}
				}
			}
		}
		
		//send the SQL data file requests
		
		$db_data_settings = "";
		
		$liteFile = dirname(__FILE__)."/../sql/db_settings_lite.sql";
		if(!file_exists($liteFile))
			die('<action result="fail" error="10" />'."\n");
		if ( !$db_data_settings .= file_get_contents( $liteFile ) )
			die('<action result="fail" error="9" />'."\n");
		
		if($_GET['mode'] == "full"){
			$fullFile = dirname(__FILE__)."/../sql/db_settings_extends.sql";
			if(!file_exists($fullFile))
				die('<action result="fail" error="10" />'."\n");
			if (!$db_data_settings .= file_get_contents($fullFile))
				die('<action result="fail" error="9" />'."\n");
		}
		$db_data_settings .= "\n".'UPDATE `PREFIX_customer` SET `passwd` = \''.md5(_COOKIE_KEY_.'123456789').'\' WHERE `id_customer` =1';
		$db_data_settings = str_replace(array($filePrefix, $engineType), array($_GET['tablePrefix'], $_GET['engine']), $db_data_settings);
		$db_data_settings = preg_split("/;\s*[\r\n]+/",$db_data_settings);
		/* UTF-8 support */
		array_unshift($db_data_settings, 'SET NAMES \'utf8\';');
		foreach($db_data_settings as $query){
			$query = trim($query);
			if(!empty($query)){
				if(!Db::getInstance()->Execute($query)){
					if(Db::getInstance()->getNumberError() == 1050){
						die('<action result="fail" error="14" />'."\n");
					} else {
						die(
							'<action
							result="fail"
							error="11"
							sqlMsgError="'.addslashes(htmlentities(Db::getInstance()->getMsgError())).'"
							sqlNumberError="'.htmlentities(Db::getInstance()->getNumberError()).'"
							sqlQuery="'.addslashes(htmlentities($query)).'"
							/>'
						);
					}
				}
			}
		}
	break;
}
$xml = '<result><action result="ok" error="" />'."\n";

$countries = Db::getInstance()->ExecuteS('
SELECT c.`id_country`, cl.`name`, c.`iso_code` FROM `'.$_GET['tablePrefix'].'country` c
INNER JOIN `'.$_GET['tablePrefix'].'country_lang` cl ON (c.`id_country` = cl.`id_country`)
WHERE cl.`id_lang` = '.(int)($_GET['language'] + 1).'
ORDER BY cl.`name`');

$timezones = Db::getInstance()->ExecuteS('
SELECT * FROM `'.$_GET['tablePrefix'].'timezone`
ORDER BY `name`');

$xml .= '<countries>'."\n";
foreach ($countries as $country)
	$xml .= "\t".'<country iso="'.$country['iso_code'].'" value="'.$country['id_country'].'" name="'.$country['name'].'" />'."\n";
$xml .= '</countries>'."\n".'<timezones>'."\n";
foreach ($timezones as $timezone)
	$xml .= "\t".'<timezone value="'.$timezone['name'].'" name="'.$timezone['name'].'" />'."\n";
$xml .= '</timezones></result>'."\n";

die($xml);

