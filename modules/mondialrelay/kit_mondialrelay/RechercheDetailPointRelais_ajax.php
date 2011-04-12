<?php 
include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../mondialrelay.php');

$mondialrelay = new mondialrelay();

DEFINE('_Enseigne_webservice', Configuration::get('MR_ENSEIGNE_WEBSERVICE'));
DEFINE('_Key_webservice', Configuration::get('MR_KEY_WEBSERVICE'));

header('Content-Type: text/html; charset=utf-8');

$relativ_base_dir=$_POST['relativ_base_dir'];
$client_Num=$_POST['Num'];
$client_Pays=$_POST['Pays'];
$num=$_POST['num_mode'];


function mr_horaire_fr($o1,$f1,$o2,$f2)
{
$mondialrelay = new mondialrelay();

if ($o1=='' || $o1==null) {$o1='0000';};
if ($f1=='' || $f1==null) {$f1='0000';};
if ($o2=='' || $o2==null) {$o2='0000';};
if ($f2=='' || $f2==null) {$f2='0000';};
$o1=substr($o1, 0, 2).':'.substr($o1, 2, 2);
$f1=substr($f1, 0, 2).':'.substr($f1, 2, 2);
$o2=substr($o2, 0, 2).':'.substr($o2, 2, 2);
$f2=substr($f2, 0, 2).':'.substr($f2, 2, 2);
if ($o1=='00:00' && $f1=='00:00') {$p1=$mondialrelay->getL('Closed');} else {$p1=$o1.' - '.$f1;};
if ($o2=='00:00' && $f2=='00:00') {$p2=$mondialrelay->getL('Closed');} else {$p2=$o2.' - '.$f2;};
if ($p1=='Fermé' && $p2=='Fermé' ) {$p=$mondialrelay->getL('Closed');} else {$p=$p1.'&nbsp;&nbsp;'.$p2;};
return utf8_encode($p);
};





$k_security=strtoupper(md5(_Enseigne_webservice.$client_Num.$client_Pays._Key_webservice));

require_once('tools/nusoap/lib/nusoap.php');
$client_mr = new nusoap_client("http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL",true);

$client_mr->soap_defencoding = 'UTF-8';
$client_mr->decode_utf8 = false;

$params = array(
'Enseigne' => _Enseigne_webservice,
'Num' => $client_Num,
'Pays' => $client_Pays,
'Security' => $k_security,
); 




$result_mr = $client_mr->call('WSI2_DetailPointRelais', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_DetailPointRelais');
if ($client_mr->fault)
{
echo 'a|<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
print_r($result);
echo '</pre>|'.$num;
} else
{
$err = $client_mr->getError();
if ($err) { echo 'a|<h2>Error</h2><pre>' . $err . '</pre>|'.$num; }
else
{
$sortie_titre="";
$sortie_loc="";
$sortie_info="";
$sortie_photo="";
$sortie_plan="";
$sortie_horaires="";
echo $result_mr['WSI2_DetailPointRelaisResult']['STAT'].'|';


$sortie_horaires.="<tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Monday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Lundi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Lundi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Lundi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Lundi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Tuesday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mardi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mardi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mardi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mardi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Wednesday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mercredi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mercredi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mercredi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Mercredi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Thursday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Jeudi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Jeudi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Jeudi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Jeudi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Friday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Vendredi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Vendredi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Vendredi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Vendredi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Saturday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Samedi']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Samedi']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Samedi']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Samedi']['string'][3])."</td>";
$sortie_horaires.="</tr>";
$sortie_horaires.="<td>".$mondialrelay->getL('Sunday')."</td>";
$sortie_horaires.="<td>".mr_horaire_fr($result_mr['WSI2_DetailPointRelaisResult']['Horaires_Dimanche']['string'][0],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Dimanche']['string'][1],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Dimanche']['string'][2],$result_mr['WSI2_DetailPointRelaisResult']['Horaires_Dimanche']['string'][3])."</td>";
$sortie_horaires.="</tr>";

$sortie_horaires="<table border=0>".$sortie_horaires."</table>";


$sortie_titre.="<strong>".$result_mr['WSI2_DetailPointRelaisResult']['LgAdr1']."</strong>";
if ($result_mr['WSI2_DetailPointRelaisResult']['LgAdr2']!='') {$sortie_titre.="<br>".$result_mr['WSI2_DetailPointRelaisResult']['LgAdr2'];};
if ($result_mr['WSI2_DetailPointRelaisResult']['LgAdr3']!='') {$sortie_titre.="<br>".$result_mr['WSI2_DetailPointRelaisResult']['LgAdr3'];};
if ($result_mr['WSI2_DetailPointRelaisResult']['LgAdr4']!='') {$sortie_titre.="<br>".$result_mr['WSI2_DetailPointRelaisResult']['LgAdr4'];};
if ($result_mr['WSI2_DetailPointRelaisResult']['CP']!='') {$sortie_titre.="<br>".$result_mr['WSI2_DetailPointRelaisResult']['CP'];};
if ($result_mr['WSI2_DetailPointRelaisResult']['Ville']!='') {$sortie_titre.=" ".$result_mr['WSI2_DetailPointRelaisResult']['Ville'];};
//if ($result_mr['WSI2_DetailPointRelaisResult']['Pays']!='') {$sortie_titre.=" - ".$result_mr['WSI2_DetailPointRelaisResult']['Pays'];};
$sortie_titre="<p>".$sortie_titre."</p>";


if ($result_mr['WSI2_DetailPointRelaisResult']['Localisation1']!='') {$sortie_loc.=$result_mr['WSI2_DetailPointRelaisResult']['Localisation1'];};
if ($result_mr['WSI2_DetailPointRelaisResult']['Localisation2']!='') {$sortie_loc.="<br>".$result_mr['WSI2_DetailPointRelaisResult']['Localisation2'];};
if ($sortie_loc!="") {$sortie_loc="<p>".$sortie_loc."</p>";}

if ($result_mr['WSI2_DetailPointRelaisResult']['Information']!='') {$sortie_info="<p>".$result_mr['WSI2_DetailPointRelaisResult']['Information']."</p>";};


if ($result_mr['WSI2_DetailPointRelaisResult']['URL_Photo']!='') {$sortie_photo="<img src=\"".$result_mr['WSI2_DetailPointRelaisResult']['URL_Photo']."\" width=\"306\" vspace=3 hspace=3>";};
if ($result_mr['WSI2_DetailPointRelaisResult']['URL_Plan']!='') {$sortie_plan.="<iframe src=\"".$result_mr['WSI2_DetailPointRelaisResult']['URL_Plan']."\" width=\"394\" height=\"234\" border=\"0\"></iframe>";};
$fermer="<a href=\"javascript:void(0)\" onclick=\"masque_recherche_MR_detail('".$num."');\"><img src=\"".$relativ_base_dir."modules/mondialrelay/kit_mondialrelay/close.gif\" border=\"0\" height=\"15\" align=\"right\"></a>";


echo "<div style=\"width=740px;height:15px;\">".$fermer."</div>
      <div style=\"width=716px;height:459px;padding:3px;overflow:auto;\">
	  <table style='border:none;' width=100%>
	  <tr style='border:none;'><td colspan=2 style='border:none;'><h1>Votre Point Relais</h1></td></tr>
      <tr style='border:none;'>
	    <td valign=top style='border:none;'>
		<table style='border:none;' ><tr style='border:none;'><td style='border:none;'><img src=\"".$relativ_base_dir."modules/mondialrelay/kit_mondialrelay/MR.gif\" vspace=3 hspace=3></td><td style='border:none;'>".utf8_encode($sortie_titre.$sortie_loc.$sortie_info)."</td></tr></table><br>
		".$sortie_plan."
		</td>
		
		<td valign=top style='border:none;'>
		".$sortie_horaires."<br>".$sortie_photo."
		</td>
	  </tr>
      </table>
	  <center><input name=\"select_MR_PR\" id=\"select_MR_PR\" value=\"".utf8_encode($mondialrelay->getL('Select this Relay Point'))."\" type=\"button\" onclick=\"
	  document.getElementById('MRchoixRelais".$num.'_'.$result_mr['WSI2_DetailPointRelaisResult']['Num']."').checked=true;select_PR_MR('".$result_mr['WSI2_DetailPointRelaisResult']['Num']."','".$num."');masque_recherche_MR_detail('".$num."');\"></center>
      </div>|".$num;


}
}
?>
