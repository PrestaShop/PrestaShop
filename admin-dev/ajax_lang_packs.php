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

define('PS_ADMIN_DIR', getcwd());
include(PS_ADMIN_DIR.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__).'/init.php');
	
if(!isset($_GET['iso_lang']) OR empty($_GET['iso_lang']))
	die('fail:0');
if(!isset($_GET['ps_version']) OR empty($_GET['ps_version']))
	die('fail:0');
if(@fsockopen('www.prestashop.com', 80))
{
	// Get all iso code available
	$lang_packs = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='.(string)$_GET['ps_version'].'&iso_lang='.(string)$_GET['iso_lang']);
	
	if ($lang_packs !== '' && Tools::jsonDecode($lang_packs) !== NULL)
	{
		echo $lang_packs;
	}
	else
		die('fail:2');
}
else
	die('offline');
