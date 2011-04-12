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

define('_PS_MAGIC_QUOTES_GPC_', get_magic_quotes_gpc());
define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));

include(INSTALL_PATH.'/classes/AddConfToFile.php');
include(INSTALL_PATH.'/../classes/Validate.php');
include(INSTALL_PATH.'/../classes/Db.php');
include(INSTALL_PATH.'/../classes/Tools.php');
include_once(INSTALL_PATH.'/../config/settings.inc.php');

function isFormValid()
{
	global $error;
	$validInfos = true;
	foreach ($error as $anError)
		if ($anError != '')
			$validInfos = false;
	return $validInfos;
}

$error = array();
foreach ($_GET AS &$var)
{
	if (is_string($var))
		$var = html_entity_decode($var, ENT_COMPAT, 'UTF-8');
	elseif (is_array($var))
		foreach ($var AS &$row)
			$row = html_entity_decode($row, ENT_COMPAT, 'UTF-8');
}

if(!isset($_GET['infosShop']) OR empty($_GET['infosShop']))
	$error['infosShop'] = '0';
else
	$error['infosShop'] = '';

if(!isset($_GET['infosFirstname']) OR empty($_GET['infosFirstname']))
	$error['infosFirstname'] = '0';
else
	$error['infosFirstname'] = '';

if(!isset($_GET['infosName']) OR empty($_GET['infosName']))
	$error['infosName'] = '0';
else
	$error['infosName'] = '';

if(isset($_GET['infosEmail']) AND !Validate::isEmail($_GET['infosEmail']))
	$error['infosEmail'] = '3';
else
	$error['infosEmail'] = '';

if (isset($_GET['infosShop']) AND !Validate::isGenericName($_GET['infosShop']))
	$error['validateShop'] = '46';
else
	$error['validateShop'] = '';

if (isset($_GET['infosFirstname']) AND !Validate::isName($_GET['infosFirstname']))
	$error['validateFirstname'] = '47';
else
	$error['validateFirstname'] = '';

if (isset($_GET['infosName']) AND !Validate::isName($_GET['infosName']))
	$error['validateName'] = '48';
else
	$error['validateName'] = '';

if (isset($_GET['catalogMode']) AND !Validate::isInt($_GET['catalogMode']))
	$error['validateCatalogMode'] = '52';
else
	$error['validateCatalogMode'] = '';

if(!isset($_GET['infosEmail']) OR empty($_GET['infosEmail']))
	$error['infosEmail'] = '0';

if (!isset($_GET['infosPassword']) OR empty($_GET['infosPassword']))
	$error['infosPassword'] = '0';
else
	$error['infosPassword'] = '';

if (!isset($_GET['infosPasswordRepeat']) OR empty($_GET['infosPasswordRepeat']))
	$error['infosPasswordRepeat'] = '0';
else
	$error['infosPasswordRepeat'] = '';

if($error['infosPassword'] == '' AND $_GET['infosPassword'] != $_GET['infosPasswordRepeat'])
	$error['infosPassword'] = '2';

if($error['infosPassword'] == '' AND (Tools::strlen($_GET['infosPassword']) < 8 OR !Validate::isPasswdAdmin($_GET['infosPassword'])))
	$error['infosPassword'] = '12';

/////////////////////////////
// IF ALL IS OK DO THE NEXT//
/////////////////////////////

include_once(INSTALL_PATH.'/classes/ToolsInstall.php');
$dbInstance = Db::getInstance();
// set Languages
$error['infosLanguages'] = '';
if(isFormValid())
{
	/*$idDefault = array_search($_GET['infosDL'][0], $_GET['infosWL']) + 1;
	//prepare the requests
	$sqlLanguages = array();

	$sqlLanguages[] = "UPDATE `"._DB_PREFIX_."configuration` SET `value` = '".$idDefault."' WHERE `"._DB_PREFIX_."configuration`.`id_configuration` =1";
	$sqlLanguages[] = "TRUNCATE TABLE `"._DB_PREFIX_."lang`";

	foreach ($_GET['infosWL'] AS $wl)
		$sqlLanguages[] = "INSERT INTO `"._DB_PREFIX_."lang` (`id_lang` ,`name` ,`active` ,`iso_code`)VALUES (NULL , '".ToolsInstall::getLangString($wl)."', '1', '".pSQL($wl)."')";
	foreach($sqlLanguages AS $query)
		if(!Db::getInstance()->Execute($query))
			$error['infosLanguages'] = '11';

	// Flags copy
	if(!$languagesId = Db::getInstance()->ExecuteS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang`'))
		$error['infosLanguages'] = '11';

	unset($dbInstance);*/
}

// Mail Notification
$error['infosNotification'] = '';
if (isFormValid())
{
	if (isset($_GET['infosNotification']) AND $_GET['infosNotification'] == 'on') {
		include_once(INSTALL_PATH.'/classes/ToolsInstall.php');
		$smtpChecked = (trim($_GET['infosMailMethod']) ==  'smtp');
		$smtpServer = $_GET['smtpSrv'];
		$subject = $_GET['infosShop']." - " . $_GET['mailSubject'];
		$type = 'text/html';
		$to =  $_GET['infosEmail'];
		$from = "no-reply@".ToolsInstall::getHttpHost(false, true);
		$smtpLogin = $_GET['smtpLogin'];
		$smtpPassword = $_GET['smtpPassword'];
		$smtpPort = $_GET['smtpPort'];//'default','secure'
		$smtpEncryption = $_GET['smtpEnc'];//"tls","ssl","off"
		$content = ToolsInstall::getNotificationMail($_GET['infosShop'], INSTALLER__PS_BASE_URI_ABSOLUTE, INSTALLER__PS_BASE_URI_ABSOLUTE."img/logo.jpg", ToolsInstall::strtoupper($_GET['infosFirstname']), $_GET['infosName'], $_GET['infosPassword'], $_GET['infosEmail']);

		$result = @ToolsInstall::sendMail($smtpChecked, $smtpServer, $content, $subject, $type, $to, $from, $smtpLogin, $smtpPassword, $smtpPort, $smtpEncryption);
	}
}

//Insert configuration parameters into the database
$error['infosInsertSQL'] = '';
if (isFormValid())
{
	$sqlParams = array();
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_SHOP_DOMAIN', '".Tools::getHttpHost()."', NOW(), NOW())";
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_SHOP_DOMAIN_SSL', '".Tools::getHttpHost()."', NOW(), NOW())";
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_INSTALL_VERSION', '".pSQL(INSTALL_VERSION)."', NOW(), NOW())";
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_SHOP_NAME', '".pSQL($_GET['infosShop'])."', NOW(), NOW())";
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_SHOP_EMAIL', '".pSQL($_GET['infosEmail'])."', NOW(), NOW())";
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_METHOD', '".pSQL($_GET['infosMailMethod'] == "smtp" ? "2": "1")."', NOW(), NOW())";
	$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = \''.pSQL($_GET['isoCode']).'\' WHERE name = \'PS_LOCALE_LANGUAGE\'';
	$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = \''.(int)$_GET['catalogMode'].'\' WHERE name = \'PS_CATALOG_MODE\'';
	$sqlParams[] = "INSERT IGNORE INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_SHOP_ACTIVITY', '".(int)($_GET['infosActivity'])."', NOW(), NOW())";
	if ((int)($_GET['infosCountry']) != 0)
	{
		$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = '.(int)($_GET['infosCountry']).' WHERE name = \'PS_COUNTRY_DEFAULT\'';
		$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = "'.pSQL($_GET['infosTimezone']).'" WHERE name = \'PS_TIMEZONE\'';
		$sql_isocode = Db::getInstance()->getValue('SELECT `iso_code` FROM `'._DB_PREFIX_.'country` WHERE `id_country` = '.(int)($_GET['infosCountry']));
		$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = \''.pSQL($sql_isocode).'\' WHERE name = \'PS_LOCALE_COUNTRY\'';

	}
	Language::loadLanguages();
	Configuration::loadConfiguration();
	require_once(dirname(__FILE__).'/../../config/defines.inc.php');
	require_once(dirname(__FILE__).'/../../classes/LocalizationPack.php');
	
	
	$context = stream_context_create(array('http' => array('timeout' => 5)));
	$localization_file = @Tools::file_get_contents('http://www.prestashop.com/download/localization_pack.php?country='.$_GET['countryName'], false, $context);
	if (!$localization_file AND file_exists(dirname(__FILE__).'/../../localization/'.strtolower($_GET['countryName']).'.xml'))
		$localization_file = @file_get_contents(dirname(__FILE__).'/../../localization/'.strtolower($_GET['countryName']).'.xml');
	if ($localization_file)
	{
		$localizationPack = new LocalizationPackCore();
		$localizationPack->loadLocalisationPack($localization_file, '', true);
		if (Configuration::get('PS_LANG_DEFAULT') == 1)
		{
			$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = (SELECT id_lang FROM '._DB_PREFIX_.'lang WHERE iso_code = \''.pSQL($_GET['isoCode']).'\') WHERE name = \'PS_LANG_DEFAULT\'';
		}
	}
	if (isset($_GET['infosMailMethod']) AND $_GET['infosMailMethod'] == "smtp")
	{
		$sqlParams[] = "INSERT INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_SERVER', '".pSQL($_GET['smtpSrv'])."', NOW(), NOW())";
		$sqlParams[] = "INSERT INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_USER', '".pSQL($_GET['smtpLogin'])."', NOW(), NOW())";
		$sqlParams[] = "INSERT INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_PASSWD', '".pSQL($_GET['smtpPassword'])."', NOW(), NOW())";
		$sqlParams[] = "INSERT INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_SMTP_ENCRYPTION', '".pSQL($_GET['smtpEnc'])."', NOW(), NOW())";
		$sqlParams[] = "INSERT INTO "._DB_PREFIX_."configuration (name, value, date_add, date_upd) VALUES ('PS_MAIL_SMTP_PORT', '".pSQL($_GET['smtpPort'])."', NOW(), NOW())";
	}
	$sqlParams[] = 'INSERT INTO '._DB_PREFIX_.'employee (id_employee, lastname, firstname, email, passwd, last_passwd_gen, bo_theme, active, id_profile, id_lang) VALUES (NULL, \''.pSQL(ToolsInstall::ucfirst($_GET['infosName'])).'\', \''.pSQL(ToolsInstall::ucfirst($_GET['infosFirstname'])).'\', \''.pSQL($_GET['infosEmail']).'\', \''.md5(pSQL(_COOKIE_KEY_.$_GET['infosPassword'])).'\', \''.date('Y-m-d h:i:s', strtotime('-360 minutes')).'\', \'oldschool\', 1, 1, (SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE "PS_LANG_DEFAULT"))';
	$sqlParams[] = 'INSERT INTO '._DB_PREFIX_.'contact (id_contact, email, customer_service) VALUES (NULL, \''.pSQL($_GET['infosEmail']).'\', 1), (NULL, \''.pSQL($_GET['infosEmail']).'\', 1)';

	if (function_exists('mcrypt_encrypt'))
	{
		$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
		if (!strstr($settings, '_RIJNDAEL_KEY_'))
		{
			$key_size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$key = Tools::passwdGen($key_size);
			$settings = preg_replace('/define\(\'_COOKIE_KEY_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_KEY_\', \'\1\');'."\n".'define(\'_RIJNDAEL_KEY_\', \''.$key.'\');', $settings);
		}
		if (!strstr($settings, '_RIJNDAEL_IV_'))
		{
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND));
			$settings = preg_replace('/define\(\'_COOKIE_IV_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_IV_\', \'\1\');'."\n".'define(\'_RIJNDAEL_IV_\', \''.$iv.'\');', $settings);
		}
		if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
			$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = 1 WHERE name = \'PS_CIPHER_ALGORITHM\'';
	}

	if (file_exists(realpath(INSTALL_PATH.'/../img').'/logo.jpg'))
	{
		list($width, $height, $type, $attr) = getimagesize(realpath(INSTALL_PATH.'/../img').'/logo.jpg');
		$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = '.(int)round($width).' WHERE name = \'SHOP_LOGO_WIDTH\'';
		$sqlParams[] = 'UPDATE '._DB_PREFIX_.'configuration SET value = '.(int)round($height).' WHERE name = \'SHOP_LOGO_HEIGHT\'';
	}

	if ((int)$_GET['catalogMode'] == 1)
	{
		$sqlParams[] = 'DELETE c, cl FROM `'._DB_PREFIX_.'cms` AS c LEFT JOIN `'._DB_PREFIX_.'cms_lang` AS cl ON c.id_cms = cl.id_cms WHERE 1 AND c.`id_cms` IN (1, 5)';
	}

	$dbInstance = Db::getInstance();
	foreach($sqlParams as $query)
		if(!$dbInstance->Execute($query))
			$error['infosInsertSQL'] = '11';
	unset($dbInstance);
}

//////////////////////////
// Building XML Response//
//////////////////////////

echo '<shopConfig>'."\n";
foreach ($error AS $key => $line)
	echo '<field id="'.$key.'" result="'.( $line != "" ? 'fail' : 'ok').'" error="'.$line.'" />'."\n";
echo '</shopConfig>';

