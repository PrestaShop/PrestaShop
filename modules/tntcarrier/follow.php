<?php
require('../../config/config.inc.php');
require_once(_PS_MODULE_DIR_."/tntcarrier/classes/TntWebService.php");
//$erreur = '';
global $smarty;
try
{
	$tntWebService = new TntWebService();
	$follow[] = $tntWebService->followPackage($_GET['code']);
} 
catch( SoapFault $e ) 
{
	$erreur = $e->faultstring;
	echo $erreur;
}
catch( Exception $e ) 
{
	$erreur = "Problem : follow failed";
}
$config['date'] = '%d/%m/%y';
$config['time'] = '%I:%M %p';
//$smarty->assign('erreur', $erreur);
$smarty->assign('config',$config);
$smarty->assign( 'follow', $follow );
$smarty->display('tpl/follow.tpl' );
?>