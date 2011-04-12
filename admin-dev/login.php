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
define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');

if ((empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) == 'off')
	 AND Configuration::get('PS_SSL_ENABLED'))
{
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
	exit();
}

$errors = array();

$cookie = new Cookie('psAdmin', substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__), -10));
if (!isset($cookie->id_lang))
	$cookie->id_lang = Configuration::get('PS_LANG_DEFAULT');
$iso = strtolower(Language::getIsoById((int)($cookie->id_lang)));
include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');

/* Cookie creation and redirection */
if (Tools::isSubmit('Submit'))
{
 	/* Check fields validity */
	$passwd = trim(Tools::getValue('passwd'));
	$email = trim(Tools::getValue('email'));
	if (empty($email))
		$errors[] = Tools::displayError('E-mail is empty');
	elseif (!Validate::isEmail($email))
		$errors[] = Tools::displayError('Invalid e-mail address');
	elseif (empty($passwd))
		$errors[] = Tools::displayError('Password is blank');
	elseif (!Validate::isPasswd($passwd))
		$errors[] = Tools::displayError('Invalid password');
	else
	{
	 	/* Seeking for employee */
		$employee = new Employee();
		$employee = $employee->getByemail($email, $passwd);
		if (!$employee)
		{
			$errors[] = Tools::displayError('Employee does not exist or password is incorrect.');
			$cookie->logout();
		}
		else
		{
		 	/* Creating cookie */
			$cookie->id_employee = $employee->id;
			$cookie->email = $employee->email;
			$cookie->profile = $employee->id_profile;
			$cookie->passwd = $employee->passwd;
			$cookie->remote_addr = ip2long(Tools::getRemoteAddr());
			$cookie->write();
			/* Redirect to admin panel */
			if (isset($_GET['redirect']))
				$url = strval($_GET['redirect'].(isset($_GET['token']) ? ('&token='.$_GET['token']) : ''));
			else
				$url = 'index.php';
			if (!Validate::isCleanHtml($url))
				die(Tools::displayError());
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$iso.'" lang="'.$iso.'">
				<meta http-equiv="Refresh" content="0;URL='.Tools::safeOutput($url, true).'">
				<head>
					<script language="javascript" type="text/javascript">
						window.location.replace("'.Tools::safeOutput($url, true).'");
					</script>
					<div style="text-align:center; margin-top:250px;"><a href="'.Tools::safeOutput($url, true).'">'.translate('Click here to launch Administration panel').'</a></div>
				</head>
			</html>';
			exit ;
		}
	}
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$iso.'" lang="'.$iso.'">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="../css/login.css" />
		<title>PrestaShop&trade; - '.translate('Administration panel').'</title>';
echo '
	</head>
	<body>
		<div id="container">';

if ($nbErrors = sizeof($errors))
{
	echo '
	<div id="error">
		<h3>'.($nbErrors > 1 ? translate('There are') : translate('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? translate('errors') : translate('error')).'</h3>
		<ol style="margin: 0 0 0 20px;">';
		foreach ($errors AS $error)
			echo '<li>'.$error.'</li>';
		echo '
		</ol>
	</div>
	<br />';
}

echo '
			<div id="login">
				<h1>'.Tools::htmlentitiesUTF8(Configuration::get('PS_SHOP_NAME')).'</h1>
				<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">';

$randomNb = rand(100, 999);
if(file_exists(PS_ADMIN_DIR.'/../install') OR file_exists(PS_ADMIN_DIR.'/../admin'))
{
	echo '				<span>'.translate('For security reasons, you cannot connect to the Back Office until after you have:').'<br /><br />
		- '.translate('deleted the /install folder').'<br />
		- '.translate('renamed the /admin folder (eg. ').'/admin'.$randomNb.')<br />
		<br />'.translate('Please then access this page by the new url (eg. http://www.domain.tld/admin').$randomNb.')</span>';
}
else
{
	echo '			<label>'.translate('E-mail address:').'</label><br />
					<input type="text" id="email" name="email" value="'.Tools::safeOutput(Tools::getValue('email')).'" class="input"/>
					<div style="margin: 1.8em 0 0 0;">
						<label>'.translate('Password:').'</label><br />
						<input type="password" name="passwd" class="input" value=""/>
					</div>
					<div>
						<div id="submit"><input type="submit" name="Submit" value="'.translate('Log in').'" class="button" /></div>
						<div id="lost"><a href="password.php">'.translate('Lost password?').'</a></div>
					</div>
	';
}
?>
<script type="text/javascript">
<!--
if (document.getElementById('email')) document.getElementById('email').focus();
-->
</script>
<?php
echo '
				</form>
			</div>
			<h2><a href="http://www.prestashop.com">&copy; Copyright by PrestaShop. all rights reserved.</a></h2>
		</div>
	</body>
</html>';
