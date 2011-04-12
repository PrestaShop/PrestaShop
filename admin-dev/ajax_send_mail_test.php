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

include_once(dirname(__FILE__).'/../config/config.inc.php');

/* Getting cookie or logout */
require_once(dirname(__FILE__).'/init.php');

$smtpChecked = (trim($_GET['mailMethod']) ==  'smtp');
$smtpServer = $_GET['smtpSrv'];
$content = $_GET['testMsg'];
$subject = $_GET['testSubject'];
$type = 'text/html';
$to =  $_GET['testEmail'];
$from = Configuration::get('PS_SHOP_EMAIL');
$smtpLogin = $_GET['smtpLogin'];
$smtpPassword = $_GET['smtpPassword'];
$smtpPort = $_GET['smtpPort'];
$smtpEncryption = $_GET['smtpEnc'];

$result = Mail::sendMailTest(Tools::htmlentitiesUTF8($smtpChecked), Tools::htmlentitiesUTF8($smtpServer), Tools::htmlentitiesUTF8($content), Tools::htmlentitiesUTF8($subject), Tools::htmlentitiesUTF8($type), Tools::htmlentitiesUTF8($to), Tools::htmlentitiesUTF8($from), Tools::htmlentitiesUTF8($smtpLogin), Tools::htmlentitiesUTF8($smtpPassword), Tools::htmlentitiesUTF8($smtpPort), Tools::htmlentitiesUTF8($smtpEncryption));
die($result ? 'ok' : 'fail');

