<?php

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');

if (isset($_GET['token']) AND !empty($_GET['token']))
{
	$token = Tools::getValue('token');
	$realToken = Configuration::get('TREEPODIA_TOKEN');

	if ($token === $realToken)
	{
		include(dirname(__FILE__).'/../treepodia.php');
		$treepodia = new Treepodia();
		$treepodia->generateXmlFlow();
	}
	else
		echo 'Bad token';
}
else
	echo 'Bad token';

?>