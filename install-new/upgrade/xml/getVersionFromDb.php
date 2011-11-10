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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(SETTINGS_FILE);
require_once(INSTALL_PATH.'/classes/GetVersionFromDb.php');

require_once(INSTALL_PATH.'/classes/LanguagesManager.php');
$lm = new LanguageManager(INSTALL_PATH.'/langs/list.xml');
$_LANG = array();
$_LIST_WORDS = array();
function lang($txt) {
	global $_LANG , $_LIST_WORDS;
	return (isset($_LANG[$txt]) ? $_LANG[$txt] : $txt);
}
if ($lm->getIncludeTradFilename())
	include_once($lm->getIncludeTradFilename());

$dbVersion = new GetVersionFromDb();
$versions = $dbVersion->getVersions();
$psVersionDb = Configuration::get('PS_VERSION_DB');

// Usefull debug
/* echo '<xmp>';
print_r($versions);
print_r($dbVersion->getErrors());
exit;*/

if (!$versions)
{
	// Desactivate this case temporary
	die('<action result="ok" />');
	
	$message = lang('Warning, the installer was unable to detect what is your current PrestaShop version from a database structure analysis. This means some fields or tables are missing, and upgrade is under your own risk.');
	if ($psVersionDb)
	{
		$message .= '<br /><br />' . sprintf(lang('However the installer has detected that the version stored in your configuration table is %1$s'), '<span class="versionInfo">' . $psVersionDb . '</span>');
	}
	die('<action result="ko" lang="' . htmlspecialchars($message) . '" />');
}

foreach ($versions as $version)
{
	if (version_compare(_PS_VERSION_, $version) == 0)
	{
		die('<action result="ok" />');
	}
}

if (count($versions) == 1)
{
	die('<action result="ko" lang="' . htmlspecialchars(sprintf(lang('Warning, we detected that the version specified in your config/settings.inc.php does not match your SQL database structure.<br />File config/settings.inc.php indicates: %1$s<br />Our automatic detection tool has detected version: %2$s<br /><br />You should edit your config/settings.inc.php file to replace %1$s by %2$s.<br />Failure to fix this issue before upgrading may result in severe complications.<br />Do not forget to relaunch the installer after this modification by pressing F5 on your web browser.'), '<span class="versionInfo">' . _PS_VERSION_ . '</span>', '<span class="versionInfo">' . $versions[0] . '</span>')) . '" />');
}

if ($psVersionDb && in_array($psVersionDb, $versions))
{
	die('<action result="ko" lang="' . htmlspecialchars(sprintf(lang('Warning, we detected that the version specified in your config/settings.inc.php does not match your SQL database structure.<br />File config/settings.inc.php indicates: %1$s<br />Our automatic detection tool has detected version: %2$s<br /><br />You should edit your config/settings.inc.php file to replace %1$s by %2$s.<br />Failure to fix this issue before upgrading may result in severe complications.<br />Do not forget to relaunch the installer after this modification by pressing F5 on your web browser.'), '<span class="versionInfo">' . _PS_VERSION_ . '</span>', '<span class="versionInfo">' . $psVersionDb . '</span>')) . '" />');
}
else
{
	die('<action result="ko" lang="' . htmlspecialchars(sprintf(lang('Warning, we detected that the version specified in your config/settings.inc.php does not match your SQL database structure.<br />File config/settings.inc.php indicates: %1$s<br />Our automatic detection tool has detected a version between %2$s and %3$s<br /><br />You should edit your config/settings.inc.php file to replace %1$s by your real shop version.<br />Failure to fix this issue before upgrading may result in severe complications.<br />Do not forget to relaunch the installer after this modification by pressing F5 on your web browser.'), '<span class="versionInfo">' . _PS_VERSION_ . '</span>', '<span class="versionInfo">' . $versions[count($versions) - 1] . '</span>', '<span class="versionInfo">' . $versions[0] . '</span>')) . '" />');
}
