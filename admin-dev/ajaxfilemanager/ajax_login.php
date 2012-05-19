<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * access control login form
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
//Code added to adjust for local admin rights.
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
	
	if(isset($_POST['username']))
	{
		if($auth->login())
		{
			header('Location: ' . appendQueryString(CONFIG_URL_HOME, makeQueryString()));
			exit;
		}
	}
}else{
	$_SESSION['ajax_user'] = true;
	header('Location: ' . appendQueryString(CONFIG_URL_HOME, makeQueryString()));
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/login.css" rel="stylesheet" />
<title><?php echo LOGIN_PAGE_TITLE; ?></title>
</head>
<body>
<div id="container">
    <div id="content">
			<form name="frmLogin" method="post" action="">
			    <table class="adminLoginTable" cellpadding="0" cellspacing="0">
			        <thead>
			            <tr>
			                <th colspan="2"><?php echo LOGIN_FORM_TITLE; ?></th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr>
			                <th class="padTop"><label><?php echo LOGIN_USERNAME; ?> </label></th>
			                <td class="padTop"><input type="text" value="" class="input" name="username" id="username" /></td>
			            </tr>
			            <tr>
			                <th><label><?php echo LOGIN_PASSWORD; ?> </label></th>
			                <td><input type="password" value="" class="input" name="password" id="password" /></td>
			            </tr>
			        </tbody>
			        <tfoot>
			            <tr>
			            	<td>&nbsp;</td>
			              <td><input type="submit" class="button" value="Login" /></td>
			            </tr>			        
			        </tfoot>
			    </table>
			</form>
    </div>
</div>
</body>
</html>
