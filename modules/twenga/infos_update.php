<?php

$configPath = '../../config/config.inc.php';

if (file_exists($configPath))
{
	include('../../config/config.inc.php');

	$controller = new FrontController();
	$controller->init();

	if (file_exists(dirname(__FILE__).'/twenga.php'))
	{
		include(dirname(__FILE__).'/twenga.php');

		$t = new twenga();
		$t->ajaxRequestType();
		unset($t);
	}
	else
		echo 'Class module wasn\'t found';
}
else
	echo 'Config file is missing';

?>

