<?php 
// tu récup les point relay

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
include(dirname(__FILE__).'/../mondialrelay.php');
include(dirname(__FILE__).'/statCodeError.php');

global $cookie;

$id_cart_address = Db::getInstance()->getValue('SELECT `MR_Selected_Num` FROM `'._DB_PREFIX_.'mr_selected` WHERE `id_cart` = '.$cookie->id_cart);

$mondialrelay = new MondialRelay();

DEFINE('_Enseigne_webservice', Configuration::get('MR_ENSEIGNE_WEBSERVICE'));
DEFINE('_Key_webservice', Configuration::get('MR_KEY_WEBSERVICE'));

header('Content-Type: text/html; charset=utf-8');

$relativ_base_dir = $_POST['relativ_base_dir'];
$client_pays_iso = $_POST['Pays'];
$client_ville = $_POST['Ville'];
$client_cp = $_POST['CP'];
$card_taille = $_POST['Taille'];
$card_weight = $_POST['Poids'];
$client_type_exp = $_POST['Action'];
$num = $_POST['num'];


$k_security = strtoupper(md5(_Enseigne_webservice.$client_pays_iso.$client_ville.$client_cp.$card_taille.$card_weight.$client_type_exp._Key_webservice));

require_once('tools/nusoap/lib/nusoap.php');
$client_mr = new nusoap_client("http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL", true);

$client_mr->soap_defencoding = 'UTF-8';
$client_mr->decode_utf8 = false;

$params = array(
'Enseigne' => _Enseigne_webservice,
'Pays' => $client_pays_iso,
'Ville' => $client_ville,
'CP' => $client_cp,
'Taille' => '',
'Poids' => $card_weight,
'Action' => $client_type_exp,
'Security' => $k_security,
); 

$result_mr = $client_mr->call('WSI2_RecherchePointRelais', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_RecherchePointRelais');
if ($client_mr->fault)
{
	echo 'a|<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
	print_r($result);
	echo '</pre>|'.$num;
}
else
{
	$err = $client_mr->getError();
	if ($err)
		echo '{"error" : "'.$err.'"}';
	else if (($code = $result_mr['WSI2_RecherchePointRelaisResult']['STAT']) != 0)
		echo '{"statError": {"code": "'.$code.'", "msg": "'.
		(array_key_exists($code, $statCode)) ? $statCode[$code] : 'Unknown error'.'}}';
	else
	{		
		$tr = "";
		echo '{';
		for ($i = 1; $i <= 10; $i++)
		{
			//if ($i==1) {$checked="checked=\"checked\"" ;} else {$checked="";};
			if($i < 10)
				$l = '0'.$i;
			else
				$l = '10';
			$numWSI2 = $result_mr['WSI2_RecherchePointRelaisResult']['PR'.$l]['Num'];
		}
		
		echo '"base_dir" : "'.$relativ_base_dir.'", ';
		echo '"addresses" : [';
		$i = 0;
		$total = count($result_mr['WSI2_RecherchePointRelaisResult'] );

		foreach ($result_mr['WSI2_RecherchePointRelaisResult'] as $key => $val)
		{
			if ($key != 'STAT')
			{
				echo '{"address1" : "'.addslashes($val['LgAdr1']).'", 
					"address2" : "'.addslashes($val['LgAdr2']).'", 
					"address3" : "'.addslashes($val['LgAdr3']).'", 
					"address4" : "'.addslashes($val['LgAdr4']).'", 
					"postcode" : "'.addslashes($val['CP']).'", 
					"city" : "'.addslashes($val['Ville']).'", 
					"iso_country" : "'.addslashes($val['Pays']).'", 
					"num" : "'.$val['Num'].'",
					"checked" : '.($val['Num'] == $id_cart_address ? 1 : 0).'}'.(($i + 1) == $total ? '' : ', ');
			}
			++$i;
		}
		echo ']}';
	}
}
?>
