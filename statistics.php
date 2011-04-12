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

if (!isset($_POST['token']) OR !isset($_POST['type']))
	die;

include(dirname(__FILE__).'/config/config.inc.php');

if ($_POST['type'] == 'navinfo')
{
	if (sha1($_POST['id_guest']._COOKIE_KEY_) != $_POST['token'])
		die;

	$guest = new Guest((int)$_POST['id_guest']);
	$guest->javascript = true;
	$guest->screen_resolution_x = (int)($_POST['screen_resolution_x']);
	$guest->screen_resolution_y = (int)($_POST['screen_resolution_y']);
	$guest->screen_color = (int)($_POST['screen_color']);
	$guest->sun_java = (int)($_POST['sun_java']);
	$guest->adobe_flash = (int)($_POST['adobe_flash']);
	$guest->adobe_director = (int)($_POST['adobe_director']);
	$guest->apple_quicktime = (int)($_POST['apple_quicktime']);
	$guest->real_player = (int)($_POST['real_player']);
	$guest->windows_media = (int)($_POST['windows_media']);
	$guest->update();
}
elseif ($_POST['type'] == 'pagetime')
{
	if (sha1($_POST['id_connections'].$_POST['id_page'].$_POST['time_start']._COOKIE_KEY_) != $_POST['token'])
		die;
	if (!Validate::isInt($_POST['time']) OR $_POST['time'] <= 0)
		die;
	Connection::setPageTime((int)$_POST['id_connections'], (int)$_POST['id_page'], substr($_POST['time_start'], 0, 19), intval($_POST['time']));
}