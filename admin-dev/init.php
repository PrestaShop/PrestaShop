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

ob_start();
$timerStart = microtime(true);

$currentFileName = array_reverse(explode("/", $_SERVER['SCRIPT_NAME']));
$cookie = new Cookie('psAdmin', substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -strlen($currentFileName['0'])));
if (isset($_GET['logout']))
	$cookie->logout();

if (!$cookie->isLoggedBack())
{
	
	$destination = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])) + 1);
	Tools::redirectAdmin('login.php'.(empty($destination) || ($destination == 'index.php?logout') ? '' : '?redirect='.$destination));
}
else
{
	$link = new Link();

	$currentIndex = $_SERVER['SCRIPT_NAME'].(($tab = Tools::getValue('tab')) ? '?tab='.$tab : '');
	if ($back = Tools::getValue('back'))
		$currentIndex .= '&back='.urlencode($back);

	/* Server Params */
	$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
	$protocol_content = (isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
	define('_PS_BASE_URL_', Tools::getShopDomain(true));
	define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

	$employee = new Employee((int)$cookie->id_employee);
	$cookie->id_lang = (int)$employee->id_lang;
	$iso = strtolower(Language::getIsoById($cookie->id_lang ? $cookie->id_lang : Configuration::get('PS_LANG_DEFAULT')));
	include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
	include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
	include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');

	/* attribute id_lang is often needed, so we create a constant for performance reasons */
	define('_USER_ID_LANG_', (int)$cookie->id_lang);

	$path = dirname(__FILE__).'/themes/';
	if (empty($employee->bo_theme) OR !file_exists($path.$employee->bo_theme.'/admin.css'))
	{
		if (file_exists($path.'oldschool/admin.css'))
			$employee->bo_theme = 'oldschool';
		elseif (file_exists($path.'origins/admin.css'))
			$employee->bo_theme = 'origins';
		else
			foreach (scandir($path) as $theme)
				if ($theme[0] != '.' AND file_exists($path.$theme.'/admin.css'))
				{
					$employee->bo_theme = $theme;
					break;
				}
		$employee->update();
	}
}
