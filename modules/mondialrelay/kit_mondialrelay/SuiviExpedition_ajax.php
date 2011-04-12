<?php 
require('config.MR.inc.php');
header('Content-Type: text/html; charset=utf-8');
$var_Expedition=$_POST['Expedition'];
$k_security=strtoupper(md5('<'._Enseigne_webservice.'>'.$var_Expedition.'<'._Key_webservice.'>'));
echo 'http://www.mondialrelay.fr/lg_fr/espaces/url/popup_exp_details.aspx?cmrq='._Enseigne_webservice.'&nexp='.$var_Expedition.'&crc='.$k_security;
