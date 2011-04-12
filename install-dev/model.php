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

@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '64M');
require(dirname(__FILE__).'/../config/autoload.php');

/* Redefine REQUEST_URI if empty (on some webservers...) */
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '')
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
if ($tmp = strpos($_SERVER['REQUEST_URI'], '?'))
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

define('INSTALL_VERSION', '1.4.1.0');
define('INSTALL_PATH', dirname(__FILE__));
define('PS_INSTALLATION_IN_PROGRESS', true);
include_once(INSTALL_PATH.'/classes/ToolsInstall.php');
define('SETTINGS_FILE', INSTALL_PATH.'/../config/settings.inc.php');
define('DEFINES_FILE', INSTALL_PATH.'/../config/defines.inc.php');
define('INSTALLER__PS_BASE_URI', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/')+1))));
define('INSTALLER__PS_BASE_URI_ABSOLUTE', 'http://'.ToolsInstall::getHttpHost(false, true).INSTALLER__PS_BASE_URI);

// XML Header
header('Content-Type: text/xml');

// Switching method
if(isset($_GET['method']))
{
	switch ($_GET['method'])
	{
		case 'checkConfig' :
			include_once('xml/checkConfig.php');
		break;

		case 'checkDB' :
			include_once('xml/checkDB.php');
		break;

		case 'createDB' :
			include_once('xml/createDB.php');
		break;

		case 'checkMail' :
			include_once('xml/checkMail.php');
		break;

		case 'checkShopInfos' :
			include_once('xml/checkShopInfos.php');
		break;

		case 'doUpgrade' :
			include_once('xml/doUpgrade.php');
		break;
	}
}
