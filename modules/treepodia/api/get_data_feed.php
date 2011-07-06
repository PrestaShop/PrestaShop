<?php

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');

$token = Tools::getValue('token');
$realToken = Configuration::get('TREEPODIA_TOKEN');
if ($token AND $token === $realToken)
{
	@set_time_limit(3600);
	@ini_set("memory_limit", "64M");
	include(dirname(__FILE__).'/../treepodia.php');
	$treepodia = new Treepodia();
	$treepodia->generateXmlFlow();
}
else
	die ('Bad token');

?>
