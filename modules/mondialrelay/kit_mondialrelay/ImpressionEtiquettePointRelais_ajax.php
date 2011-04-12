<?php 
header('Content-Type: text/html; charset=utf-8');

DEFINE('_Enseigne_webservice',$_POST['mr_Enseigne_WebService']);
DEFINE('_Key_webservice',$_POST['mr_Key_WebService']);
$var_Expeditions=$_POST['Expeditions'];
$var_Langue=$_POST['Langue'];


$k_security=strtoupper(md5(_Enseigne_webservice.$var_Expeditions.$var_Langue._Key_webservice));

require_once('tools/nusoap/lib/nusoap.php');
$client_mr = new nusoap_client("http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL",true);

$client_mr->soap_defencoding = 'UTF-8';
$client_mr->decode_utf8 = false;

$params = array(
'Enseigne' => _Enseigne_webservice,
'Expeditions' => $var_Expeditions,
'Langue' => $var_Langue,
'Security' => $k_security,
); 

$sortie='';
$result_mr = $client_mr->call('WSI2_GetEtiquettes', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_GetEtiquettes');
if ($client_mr->fault)
{
$sortie.='a|<h2>Fault (Expect - The request contains an invalid SOAP body)</h2></pre>';
$sortie.=print_r($result);
$sortie.='</pre>';
} else
{
$err = $client_mr->getError();
if ($err) { $sortie.='a|<h2>Error</h2><pre>' . $err . '</pre>'; }
else
{
$sortie.=$result_mr['WSI2_GetEtiquettesResult']['STAT'].'|';
$sortie.='http://www.mondialrelay.fr'.$result_mr['WSI2_GetEtiquettesResult']['URL_PDF_A4'].'|';
$sortie.='http://www.mondialrelay.fr'.$result_mr['WSI2_GetEtiquettesResult']['URL_PDF_A5'].'|';
}
}
echo ':THISTAG:'.$sortie.':THISTAG:';
