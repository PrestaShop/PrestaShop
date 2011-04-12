<?php 
header('Content-Type: text/html; charset=utf-8');

DEFINE('_Enseigne_webservice',$_POST['mr_Enseigne_WebService']);
DEFINE('_Code_Marque',$_POST['mr_code_marque']);
DEFINE('_Key_webservice',$_POST['mr_Key_WebService']);

$var_ModeCol=$_POST['ModeCol'];
$var_ModeLiv=$_POST['ModeLiv'];
$var_NDossier='PS_'.$_POST['NDossier'];
$var_NClient=$_POST['NClient'];
$var_Expe_Langage=$_POST['Expe_Langage'];
$var_Expe_Ad1=$_POST['Expe_Ad1'];
$var_Expe_Ad2=$_POST['Expe_Ad2'];
$var_Expe_Ad3=$_POST['Expe_Ad3'];
$var_Expe_Ad4=$_POST['Expe_Ad4'];
$var_Expe_Ville=$_POST['Expe_Ville'];
$var_Expe_CP=$_POST['Expe_CP'];
$var_Expe_Pays=$_POST['Expe_Pays'];
$var_Expe_Tel1=$_POST['Expe_Tel1'];
$var_Expe_Tel2=$_POST['Expe_Tel2'];
$var_Expe_Mail=$_POST['Expe_Mail'];
$var_Dest_Langage=$_POST['Dest_Langage'];
$var_Dest_Langage='FR';
$var_Dest_Ad1=$_POST['Dest_Ad1'];
$var_Dest_Ad2=$_POST['Dest_Ad2'];
$var_Dest_Ad3=$_POST['Dest_Ad3'];
$var_Dest_Ad4=$_POST['Dest_Ad4'];
$var_Dest_Ville=$_POST['Dest_Ville'];
$var_Dest_CP=$_POST['Dest_CP'];
$var_Dest_Pays=$_POST['Dest_Pays'];
$var_Dest_Tel1=$_POST['Dest_Tel1'];
$var_Dest_Tel2=$_POST['Dest_Tel2'];
$var_Dest_Mail=$_POST['Dest_Mail'];
$var_Poids=$_POST['Poids'];
$var_Longueur=$_POST['Longueur'];
$var_Taille=$_POST['Taille'];
$var_NbColis=$_POST['NbColis'];
$var_CRT_Valeur=$_POST['CRT_Valeur'];
$var_CRT_Devise=$_POST['CRT_Devise'];
$var_Exp_Valeur=$_POST['Exp_Valeur'];
$var_Exp_Devise=$_POST['Exp_Devise'];
$var_LIV_Rel_Pays=$_POST['LIV_Rel_Pays'];
$var_LIV_Rel=$_POST['LIV_Rel'];
$var_Assurance=$_POST['Assurance'];
$var_Instructions=$_POST['Instructions'];
$var_Texte=$_POST['Texte'];

$k_security=strtoupper(md5(_Enseigne_webservice.$var_ModeCol.$var_ModeLiv.$var_NDossier.$var_NClient.$var_Expe_Langage.$var_Expe_Ad1.$var_Expe_Ad2.$var_Expe_Ad3.$var_Expe_Ad4.$var_Expe_Ville.$var_Expe_CP.$var_Expe_Pays.$var_Expe_Tel1.$var_Expe_Tel2.$var_Expe_Mail.$var_Dest_Langage.$var_Dest_Ad1.$var_Dest_Ad2.$var_Dest_Ad3.$var_Dest_Ad4.$var_Dest_Ville.$var_Dest_CP.$var_Dest_Pays.$var_Dest_Tel1.$var_Dest_Tel2.$var_Dest_Mail.$var_Poids.$var_Longueur.$var_Taille.$var_NbColis.$var_CRT_Valeur.$var_CRT_Devise.$var_Exp_Valeur.$var_Exp_Devise.$var_LIV_Rel_Pays.$var_LIV_Rel.$var_Assurance.$var_Instructions._Key_webservice));

require_once('tools/nusoap/lib/nusoap.php');
$client_mr = new nusoap_client("http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL",true);

$client_mr->soap_defencoding = 'UTF-8';
$client_mr->decode_utf8 = false;

$params = array(
'Enseigne' => _Enseigne_webservice,
'ModeCol' => $var_ModeCol,
'ModeLiv' => $var_ModeLiv,
'NDossier' => $var_NDossier,
'NClient' => $var_NClient,
'Expe_Langage' => $var_Expe_Langage,
'Expe_Ad1' => $var_Expe_Ad1,
'Expe_Ad2' => $var_Expe_Ad2,
'Expe_Ad3' => $var_Expe_Ad3,
'Expe_Ad4' => $var_Expe_Ad4,
'Expe_Ville' => $var_Expe_Ville,
'Expe_CP' => $var_Expe_CP,
'Expe_Pays' => $var_Expe_Pays,
'Expe_Tel1' => $var_Expe_Tel1,
'Expe_Tel2' => $var_Expe_Tel2,
'Expe_Mail' => $var_Expe_Mail,
'Dest_Langage' => $var_Dest_Langage,
'Dest_Ad1' => $var_Dest_Ad1,
'Dest_Ad2' => $var_Dest_Ad2,
'Dest_Ad3' => $var_Dest_Ad3,
'Dest_Ad4' => $var_Dest_Ad4,
'Dest_Ville' => $var_Dest_Ville,
'Dest_CP' => $var_Dest_CP,
'Dest_Pays' => $var_Dest_Pays,
'Dest_Tel1' => $var_Dest_Tel1,
'Dest_Tel2' => $var_Dest_Tel2,
'Dest_Mail' => $var_Dest_Mail,
'Poids' => $var_Poids,
'Longueur' => $var_Longueur,
'Taille' => $var_Taille,
'NbColis' => $var_NbColis,
'CRT_Valeur' => $var_CRT_Valeur,
'CRT_Devise' => $var_CRT_Devise,
'Exp_Valeur' => $var_Exp_Valeur,
'Exp_Devise' => $var_Exp_Devise,
'LIV_Rel_Pays' => $var_LIV_Rel_Pays,
'LIV_Rel' => $var_LIV_Rel,
'Assurance' => $var_Assurance,
'Instructions' => $var_Instructions,
'Security' => $k_security,
'Texte' => $var_Texte,
); 


$sortie='';

$result_mr = $client_mr->call('WSI2_CreationEtiquette', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_CreationEtiquette');
if ($client_mr->fault)
{
	$sortie.='a|<h2>Fault (Expect - The request contains an invalid SOAP body)</h2></pre>';
	$sortie.=print_r($result_mr);
	$sortie.='</pre>';
}
else
{
	$err = $client_mr->getError();
	if ($err) { $sortie.='a|<h2>Error</h2><pre>' . $err.' </pre>'; }
	else
	{
		$var_Expedition=$result_mr['WSI2_CreationEtiquetteResult']['ExpeditionNum'];
		$k_security=strtoupper(md5('<'._Enseigne_webservice._Code_Marque.'>'.$var_Expedition.'<'._Key_webservice.'>'));
		$sortie.=$result_mr['WSI2_CreationEtiquetteResult']['STAT'].'|';
		if($result_mr['WSI2_CreationEtiquetteResult']['STAT']==0)
		{
			$sortie.=$result_mr['WSI2_CreationEtiquetteResult']['ExpeditionNum'].'|';
			$sortie.='http://www.mondialrelay.fr'.$result_mr['WSI2_CreationEtiquetteResult']['URL_Etiquette'].'|';
			$sortie.='http://www.mondialrelay.fr/lg_fr/espaces/url/popup_exp_details.aspx?cmrq='._Enseigne_webservice._Code_Marque.'&nexp='.$var_Expedition.'&crc='.$k_security;
		}
		else
			$sortie.=mb_convert_encoding(error_message_MR($result_mr['WSI2_CreationEtiquetteResult']['STAT']), 'UTF-8', 'ISO-8859-15' ).'|';
	}
}
echo ':THISTAG:'.$sortie.':THISTAG:';

function error_message_MR($etat)
{
if($etat=='a') {return "Résultat vide";}
if($etat==1) {return "Enseigne invalide";}
if($etat==2) {return "Numéro d'enseigne vide ou inexistant";}
if($etat==3) {return "Numéro de compte enseigne invalide";}
if($etat==5) {return "Numéro de dossier enseigne invalide";}
if($etat==7) {return "Numéro de client enseigne invalide";}
if($etat==9) {return "Nom de ville non reconnu ou non unique";}
if($etat==10) {return "Type de collecte invalide ou incorrect (1/D > Domicile -- 3/R > Relais)";}
if($etat==11) {return "Numéro de Point Relais de collecte invalide";}
if($etat==12) {return "Pays du Point Relais de collecte invalide";}
if($etat==13) {return "Type de livraison invalide ou incorrect (1/D > Domicile -- 3/R > Relais)";}
if($etat==14) {return "Numéro du Point Relais de livraison invalide";}
if($etat==15) {return "Pays du Point Relais de livraison invalide";}
if($etat==16) {return "Code pays invalide";}
if($etat==17) {return "Adresse invalide";}
if($etat==18) {return "Ville invalide";}
if($etat==19) {return "Code postal invalide";}
if($etat==20) {return "Poids du colis invalide";}
if($etat==21) {return "Taille (Longueur + Hauteur) du colis invalide";}
if($etat==22) {return "Taille du Colis invalide";}
if($etat==24) {return "Numéro de Colis Mondial Relay invalide";}
if($etat==28) {return "Mode de collecte invalide";}
if($etat==29) {return "Mode de livraison invalide";}
if($etat==30) {return "Adresse (L1) de l'expéditeur invalide";}
if($etat==31) {return "Adresse (L2) de l'expéditeur invalide";}
if($etat==33) {return "Adresse (L3) de l'expéditeur invalide";}
if($etat==34) {return "Adresse (L4) de l'expéditeur invalide";}
if($etat==35) {return "Ville de l'expéditeur invalide";}
if($etat==36) {return "Code postal de l'expéditeur invalide";}
if($etat==37) {return "Pays de l'expéditeur invalide";}
if($etat==38) {return "Numéro de téléphone de l'expéditeur invalide";}
if($etat==39) {return "Adresse e-mail de l'expéditeur invalide";}
if($etat==40) {return "Action impossible sans ville ni code postal";}
if($etat==41) {return "Mode de livraison invalide";}
if($etat==42) {return "Montant CRT invalide";}
if($etat==43) {return "Devise CRT invalide";}
if($etat==44) {return "Valeur du colis invalide";}
if($etat==45) {return "Devise de la valeur du colis invalide";}
if($etat==46) {return "Plage de numéro d'expédition épuisée";}
if($etat==47) {return "Nombre de colis invalide";}
if($etat==48) {return "Multi-colis en Point Relais Interdit";}
if($etat==49) {return "Mode de collecte ou de livraison invalide";}
if($etat==50) {return "Adresse (L1) du destinataire invalide";}
if($etat==51) {return "Adresse (L2) du destinataire invalide";}
if($etat==53) {return "Adresse (L3) du destinataire invalide";}
if($etat==54) {return "Adresse (L4) du destinataire invalide";}
if($etat==55) {return "Ville du destinataire invalide";}
if($etat==56) {return "Code postal du destinataire invalide";}
if($etat==57) {return "Pays du destinataire invalide";}
if($etat==58) {return "Numéro de téléphone du destinataire invalide";}
if($etat==59) {return "Adresse e-mail du destinataire invalide";}
if($etat==60) {return "Champ texte libre invalide";}
if($etat==61) {return "Top avisage invalide";}
if($etat==62) {return "Instruction de livraison invalide";}
if($etat==63) {return "Assurance invalide ou incorrecte";}
if($etat==64) {return "Temps de montage invalide";}
if($etat==65) {return "Top rendez-vous invalide";}
if($etat==66) {return "Top reprise invalide";}
if($etat==70) {return "Numéro de Point Relais invalide";}
if($etat==72) {return "Langue expéditeur invalide";}
if($etat==73) {return "Langue destinataire invalide";}
if($etat==74) {return "Langue invalide";}
if($etat==80) {return "Code tracing : Colis enregistré";}
if($etat==81) {return "Code tracing : Colis en traitement chez Mondial Relay";}
if($etat==82) {return "Code tracing : Colis livré";}
if($etat==83) {return "Code tracing : Anomalie";}
if($etat==84) {return "84 (Réservé Code Tracing)";}
if($etat==85) {return "85 (Réservé Code Tracing)";}
if($etat==86) {return "86 (Réservé Code Tracing)";}
if($etat==87) {return "87 (Réservé Code Tracing)";}
if($etat==88) {return "88 (Réservé Code Tracing)";}
if($etat==89) {return "89 (Réservé Code Tracing)";}
if($etat==90) {return "AS400 indisponible";}
if($etat==91) {return "Numéro d'expédition invalide";}
if($etat==93) {return "Aucun élément retourné par le plan de tri
						Si vous effectuez une collecte ou une livraison en Point Relais, vérifiez que les
						Point Relais sont bien disponibles.
						Si vous effectuez une livraison à domicile, il est probable que le code postal que
						vous avez indiquez n'existe pas.";}
if($etat==94) {return "Colis Inexistant";}
if($etat==95) {return "Compte Enseigne non activé";}
if($etat==96) {return "Type d'enseigne incorrect en Base";}
if($etat==97) {return "Clé de sécurité invalide";}
if($etat==98) {return "Service Indisponible";}
if($etat==99) {return "Erreur générique du service
						Cette erreur peut être dû autant à un problème technique du service qu'à des
						données incorrectes ou inexistantes dans la Base de Données. Lorsque vous avez
						cette erreur veuillez la notifier à Mondial Relay en précisant la date et l'heure de la
						connexion ainsi que les informations envoyés au WebService afin d'effectuer une
						vérification.";}
};
?>
