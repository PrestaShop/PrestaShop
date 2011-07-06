<?php

include(dirname(__FILE__).'/../../../config/config.inc.php');

$token = Tools::getValue('token');
$realToken = Configuration::get('TREEPODIA_TOKEN');
if ($token AND $token === $realToken)
{
	Configuration::updateValue('TREEPODIA_ACCOUNT_CODE', $_POST['account-code']);
	die ($_POST['account-code']);
}
else
	die ('Bad token');
