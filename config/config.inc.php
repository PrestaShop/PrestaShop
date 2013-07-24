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

require_once(dirname(__FILE__).'/defines.inc.php');
$start_time = microtime(true);

/* SSL configuration */
define('_PS_SSL_PORT_', 443);

/* Improve PHP configuration to prevent issues */
ini_set('upload_max_filesize', '100M');
ini_set('default_charset', 'utf-8');
ini_set('magic_quotes_runtime', 0);

/* correct Apache charset (except if it's too late */
if (!headers_sent())
	header('Content-Type: text/html; charset=utf-8');

/* No settings file? goto installer... */
if (!file_exists(dirname(__FILE__).'/settings.inc.php'))
{
	$dir = ((substr($_SERVER['REQUEST_URI'], -1) == '/' || is_dir($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']).'/');
	if (!file_exists(dirname(__FILE__).'/../install'))
		die('Error: "install" directory is missing');
	header('Location: install/');
	exit;
}

require_once(dirname(__FILE__).'/settings.inc.php');

require_once(dirname(__FILE__).'/autoload.php');

if (_PS_DEBUG_PROFILING_)
{
	include_once(_PS_TOOL_DIR_.'profiling/Controller.php');
	include_once(_PS_TOOL_DIR_.'profiling/ObjectModel.php');
	include_once(_PS_TOOL_DIR_.'profiling/Hook.php');
	include_once(_PS_TOOL_DIR_.'profiling/Db.php');
	include_once(_PS_TOOL_DIR_.'profiling/Tools.php');
}

if (Tools::isPHPCLI())
	Tools::argvToGET($argc, $argv);

/* Redefine REQUEST_URI if empty (on some webservers...) */
if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI']))
{
	if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME']))
		$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
	if (isset($_SERVER['SCRIPT_NAME']))
	{
		if (basename($_SERVER['SCRIPT_NAME']) == 'index.php' && empty($_SERVER['QUERY_STRING']))
			$_SERVER['REQUEST_URI'] = dirname($_SERVER['SCRIPT_NAME']).'/';
		else
		{
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
				$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
		}
	}
}

/* Trying to redefine HTTP_HOST if empty (on some webservers...) */
if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST']))
	$_SERVER['HTTP_HOST'] = @getenv('HTTP_HOST');
	
$context = Context::getContext();

/* Initialize the current Shop */
try 
{
	$context->shop = Shop::initialize();
}
catch(Exception $e)
{
	header('HTTP/1.1 503 temporarily overloaded');

	define('_PS_SMARTY_DIR_', _PS_TOOL_DIR_.'smarty/');
	require_once(_PS_SMARTY_DIR_.'Smarty.class.php');
	$context->smarty = new Smarty();		
	$context->smarty->display('error500.html');
	
	exit;
}
define('_THEME_NAME_', $context->shop->getTheme());
define('__PS_BASE_URI__', $context->shop->getBaseURI());

/* Include all defines related to base uri and theme name */
require_once(dirname(__FILE__).'/defines_uri.inc.php');

global $_MODULES;
$_MODULES = array();

/* Load configuration */
Configuration::loadConfiguration();

/* Load all languages */
Language::loadLanguages();

/* Loading default country */
$defaultCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
$context->country = $defaultCountry;

/* It is not safe to rely on the system's timezone settings, and this would generate a PHP Strict Standards notice. */
@date_default_timezone_set(Configuration::get('PS_TIMEZONE'));

/* Set locales */
$locale = strtolower(Configuration::get('PS_LOCALE_LANGUAGE')).'_'.strtoupper(Configuration::get('PS_LOCALE_COUNTRY').'.UTF-8');
setlocale(LC_COLLATE, $locale);
setlocale(LC_CTYPE, $locale);
setlocale(LC_TIME, $locale);
setlocale(LC_NUMERIC, 'en_US.UTF-8');

/* Instantiate cookie */


$cookie_lifetime = (int)(defined('_PS_ADMIN_DIR_') ? Configuration::get('PS_COOKIE_LIFETIME_BO') : Configuration::get('PS_COOKIE_LIFETIME_FO'));
$cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);

if (defined('_PS_ADMIN_DIR_'))
	$cookie = new Cookie('psAdmin', '', $cookie_lifetime);
else
{
	if ($context->shop->getGroup()->share_order)
		$cookie = new Cookie('ps-sg'.$context->shop->getGroup()->id, '', $cookie_lifetime, $context->shop->getUrlsSharedCart());
	else
	{
		$domains = null;
		if ($context->shop->domain != $context->shop->domain_ssl)
		  $domains = array($context->shop->domain_ssl, $context->shop->domain);
		
		$cookie = new Cookie('ps-s'.$context->shop->id, '', $cookie_lifetime, $domains);
	}
}

$context->cookie = $cookie;

/* Create employee if in BO, customer else */
if (defined('_PS_ADMIN_DIR_'))
{
	$employee = new Employee($cookie->id_employee);
	$context->employee = $employee;

	/* Auth on shops are recached after employee assignation */
	if ($employee->id_profile != _PS_ADMIN_PROFILE_)
		Shop::cacheShops(true);

	$cookie->id_lang = (int)$employee->id_lang;
}

/* if the language stored in the cookie is not available language, use default language */
if (isset($cookie->id_lang) && $cookie->id_lang)
	$language = new Language($cookie->id_lang);
if (!isset($language) || !Validate::isLoadedObject($language))
	$language = new Language(Configuration::get('PS_LANG_DEFAULT'));
$context->language = $language;

if (!defined('_PS_ADMIN_DIR_'))
{
	if (isset($cookie->id_customer) && (int)$cookie->id_customer)
	{
		$customer = new Customer($cookie->id_customer);
		if(!Validate::isLoadedObject($customer))
			$customer->logout();
		else
		{
			$customer->logged = $cookie->logged;

			if ($customer->id_lang != $context->language->id)
			{
				$customer->id_lang = $context->language->id;
				$customer->update();
			}
		}
	}

	if (!isset($customer) || !Validate::isLoadedObject($customer))
	{
		$customer = new Customer();
		
		// Change the default group
		if (Group::isFeatureActive())
			$customer->id_default_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
	}
	$customer->id_guest = $cookie->id_guest;
	$context->customer = $customer;
}

/* Link should also be initialized in the context here for retrocompatibility */
$https_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
$context->link = new Link($https_link, $https_link);

/**
 * @deprecated : these defines are going to be deleted on 1.6 version of Prestashop
 * USE : Configuration::get() method in order to getting the id of order state
 */
define('_PS_OS_CHEQUE_',      Configuration::get('PS_OS_CHEQUE'));
define('_PS_OS_PAYMENT_',     Configuration::get('PS_OS_PAYMENT'));
define('_PS_OS_PREPARATION_', Configuration::get('PS_OS_PREPARATION'));
define('_PS_OS_SHIPPING_',    Configuration::get('PS_OS_SHIPPING'));
define('_PS_OS_DELIVERED_',   Configuration::get('PS_OS_DELIVERED'));
define('_PS_OS_CANCELED_',    Configuration::get('PS_OS_CANCELED'));
define('_PS_OS_REFUND_',      Configuration::get('PS_OS_REFUND'));
define('_PS_OS_ERROR_',       Configuration::get('PS_OS_ERROR'));
define('_PS_OS_OUTOFSTOCK_',  Configuration::get('PS_OS_OUTOFSTOCK'));
define('_PS_OS_BANKWIRE_',    Configuration::get('PS_OS_BANKWIRE'));
define('_PS_OS_PAYPAL_',      Configuration::get('PS_OS_PAYPAL'));
define('_PS_OS_WS_PAYMENT_', Configuration::get('PS_OS_WS_PAYMENT'));

/* Get smarty */
require_once(dirname(__FILE__).'/smarty.config.inc.php');
$context->smarty = $smarty;
