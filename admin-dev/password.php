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
include(PS_ADMIN_DIR.'/functions.php');

$cookie = new Cookie('psAdmin', substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__), -10));

$errors = array();

$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
$iso = strtolower(Language::getIsoById((int)$id_lang));
include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');

if (isset($_POST['Submit']))
{
	$errors = array();
	if (empty($_POST['email']))
		$errors[] = Tools::displayError('E-mail is empty');
	elseif (!Validate::isEmail($_POST['email']))
		$errors[] = Tools::displayError('Invalid e-mail address');
	else
	{
		$employee = new Employee();
		if (!$employee->getByemail($_POST['email']) OR !$employee)
			$errors[] = Tools::displayError('This account does not exist');
		else
		{
			if ((strtotime($employee->last_passwd_gen.'+'.Configuration::get('PS_PASSWD_TIME_BACK').' minutes') - time()) > 0 )
				$errors[] = Tools::displayError('You can regenerate your password only each').' '.Configuration::get('PS_PASSWD_TIME_BACK').' '.Tools::displayError('minute(s)');
			else
			{	
				$pwd = Tools::passwdGen();
				$employee->passwd = md5(pSQL(_COOKIE_KEY_.$pwd));
				$employee->last_passwd_gen = date('Y-m-d H:i:s', time());
				$result = $employee->update();
				if (!$result)
					$errors[] = Tools::displayError('An error occurred during your password change.');
				else
				{
					Mail::Send((int)$id_lang, 'password', Mail::l('Your new admin password'), array('{email}' => $employee->email, '{lastname}' => $employee->lastname, '{firstname}' => $employee->firstname, '{passwd}' => $pwd), $employee->email, $employee->firstname.' '.$employee->lastname);
					$confirmation = 'ok';
				}
			}
		}
	}
}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$iso.'" lang="'.$iso.'">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="../css/login.css" />
	<title>PrestaShop&trade; - '.translate('Administration panel').'</title>
</head>
<body><div id="container">';

if (sizeof($errors))
{
	echo '<div id="error">
	<h3>'.translate('There is 1 error').'</h3>
	<ol>';
	foreach ($errors AS $error)
		echo '<li>'.$error.'</li>';
	echo '</ol>
	</div>';
}

echo '
	<div id="login">
		<h1>'.Configuration::get('PS_SHOP_NAME').'</h1>
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<div class="page-title center">'.translate('Forgot your password?').'</div><br />';
if (isset($confirmation))
	echo '	<br />
			<div style="font-weight: bold;">'.translate('Your password has been e-mailed to you').'.</div>
			<div style="margin: 2em 0 0 0; text-align: right;"><a href="login.php?email='.Tools::safeOutput(Tools::getValue('email')).'">> '.translate('back to login home').'</a></div>';
else
	echo '	<span style="font-weight: bold;">'.translate('Please, enter your e-mail address').' </span>
			'.translate('(the one you wrote during your registration) in order to receive your access codes by e-mail').'.<br />
			<input type="text" name="email" class="input" />
			<div>
				<div id="submit"><input type="submit" name="Submit" value="'.translate('Send').'" class="button" /></div>
				<div id="lost">&nbsp;</div>
			</div>
		</form>
	</div>
	<h2><a href="http://www.prestashop.com">&copy; Copyright by PrestaShop. all rights reserved.</a></h2>
</div></body></html>';