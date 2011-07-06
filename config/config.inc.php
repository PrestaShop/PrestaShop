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
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Debug only */
@ini_set('display_errors', 'on');
define('_PS_DEBUG_SQL_', true);

$start_time = microtime(true);

/* Compatibility warning */
define('_PS_DISPLAY_COMPATIBILITY_WARNING_', true);

/* SSL configuration */
define('_PS_SSL_PORT_', 443);

/* Improve PHP configuration to prevent issues */
ini_set('upload_max_filesize', '100M');
ini_set('default_charset', 'utf-8');
ini_set('magic_quotes_runtime', 0);

// correct Apache charset (except if it's too late
if(!headers_sent())
	header('Content-Type: text/html; charset=utf-8');

/* No settings file? goto installer...*/
if (!file_exists(dirname(__FILE__).'/settings.inc.php'))
{
	$dir = ((is_dir($_SERVER['REQUEST_URI']) OR substr($_SERVER['REQUEST_URI'], -1) == '/') ? $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']).'/');
	if(!file_exists(dirname(__FILE__).'/../install'))
		die('Error: \'install\' directory is missing');
	header('Location: install/');
	exit;
}
require_once(dirname(__FILE__).'/settings.inc.php');

/* Redefine REQUEST_URI if empty (on some webservers...) */
if (!isset($_SERVER['REQUEST_URI']) OR empty($_SERVER['REQUEST_URI']))
{
	if (substr($_SERVER['SCRIPT_NAME'], -9) == 'index.php')
		$_SERVER['REQUEST_URI'] = dirname($_SERVER['SCRIPT_NAME']).'/';
	else
	{
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		if (isset($_SERVER['QUERY_STRING']) AND !empty($_SERVER['QUERY_STRING']))
			$_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
	}
}

/* Autoload */
require_once(dirname(__FILE__).'/autoload.php');

if (!defined('_PS_MAGIC_QUOTES_GPC_'))
	define('_PS_MAGIC_QUOTES_GPC_',         get_magic_quotes_gpc());
if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_'))
	define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));

/* aliases */
function p($var) {
	return (Tools::p($var));
}
function d($var) {
	Tools::d($var);
}

function ppp($var) {
	return (Tools::p($var));
}
function ddd($var) {
	Tools::d($var);
}
/* Set the current Shop */
Shop::setCurrentShop();
define('_THEME_NAME_', Shop::getCurrentTheme());
define('__PS_BASE_URI__', Shop::getCurrentBaseURI());

/* Include all defines */
require_once(dirname(__FILE__).'/defines.inc.php');

if (!defined('_PS_MODULE_DIR_'))
	define('_PS_MODULE_DIR_',           _PS_ROOT_DIR_.'/modules/');

global $_MODULES;
$_MODULES = array();

/* Load all configuration keys */
Configuration::loadConfiguration();

/* Load all language definitions */
Language::loadLanguages();

/* Loading default country */
global $defaultCountry;
$defaultCountry = new Country((int)(Configuration::get('PS_COUNTRY_DEFAULT')), Configuration::get('PS_LANG_DEFAULT'));

/* It is not safe to rely on the system's timezone settings, and this would generate a PHP Strict Standards notice. */
if (function_exists('date_default_timezone_set'))
	@date_default_timezone_set(Configuration::get('PS_TIMEZONE'));

/* Smarty */
require_once(dirname(__FILE__).'/smarty.config.inc.php');
/* Possible value are true, false, 'URL'
 (for 'URL' append SMARTY_DEBUG as a parameter to the url)
 default is false for production environment */
define('SMARTY_DEBUG_CONSOLE', false);
