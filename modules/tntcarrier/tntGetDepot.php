<?php
include( '../../config/config.inc.php' );

function genAuth($username, $password)
	{
		 return sprintf('
				<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
				<wsse:Username>%s</wsse:Username>
				<wsse:Password>%s</wsse:Password>
				</wsse:UsernameToken>
				</wsse:Security>', htmlspecialchars($username), htmlspecialchars($password));
	}

function getDepot($soapclient, $code)
{
	$services = $soapclient->tntDepots(array('department' => $code));
	return ($services);
}

if (!Configuration::get('TNT_CARRIER_LOGIN') || !Configuration::get('TNT_CARRIER_PASSWORD') || !Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'))
	echo '<span style="color:red">No account found</span>';
else
{
	$code = $_GET['code'];
	$authheader = genAuth(Configuration::get('TNT_CARRIER_LOGIN'), Configuration::get('TNT_CARRIER_PASSWORD'));
	$authvars = new SoapVar($authheader, XSD_ANYXML);
	$header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $authvars);
	$file = "http://www.tnt.fr/service/?wsdl";

	try {
		$soapclient = new SoapClient($file, array('trace'=>1));
		$soapclient->__setSOAPHeaders(array($header));
		$follow = getDepot($soapclient, $code);
		} 
	catch( SoapFault $e ) {
		$erreur = $e->faultstring;
		echo $erreur;
		}
	catch( Exception $e ) {
		$erreur = "Problem : follow failed";      
	}
	if (isset($follow))
	{
		//var_dump($follow->DepotInfo);
		$v = $follow->DepotInfo;
		if (!is_array($follow->DepotInfo))
			echo "
				<input type='hidden' id='tntRCSelectedCode' value='$v->pexCode'/>
				<input type='hidden' id='tntRCSelectedNom' value='$v->name'/>
				<input type='hidden' id='tntRCSelectedAdresse' value='$v->address1'/>
				<input type='hidden' id='tntRCSelectedAdresse2' value='$v->address2'/>
				<input type='hidden' id='tntRCSelectedCodePostal' value='$v->zipCode'/>
				<input type='hidden' id='tntRCSelectedCommune' value='$v->city'/>";
		else
			echo "
				<input type='hidden' id='tntRCSelectedCode' />
				<input type='hidden' id='tntRCSelectedNom' />
				<input type='hidden' id='tntRCSelectedAdresse' />
				<input type='hidden' id='tntRCSelectedAdresse2' />
				<input type='hidden' id='tntRCSelectedCodePostal' />
				<input type='hidden' id='tntRCSelectedCommune' />";
		echo "
		<table width='480px' cellspacing='0' cellpadding='0' style='border:1px solid gray;'>
			<tbody>
			<tr height='8px'>
			<td class='tntRCblanc' colspan='6'></td>
			</tr>
			<tr>
			<td class='tntRCblanc' width='5px'></td>
			<td class='tntRCgris'  colspan='2'>&nbsp;Agences TNT</td>";
		if (is_array($follow->DepotInfo))
			echo "<td class='tntRCgris'>Choix</td>";
		else
			echo "<td></td>";
		echo "
			<td class='tntRCblanc' width='5px'></td>
			</tr>";
			if (is_array($follow->DepotInfo))
				foreach ($follow->DepotInfo as $key => $v)
				{
					echo"
						<tr>
						<td class='tntRCblanc' ></td>
						<td class='tntRCblanc' ><img src='../modules/tntcarrier/img/logo-tnt-petit.jpg'></td>
						<td class='tntRCrelaisColis'> $v->name $v->address1 $v->address2 <br/>$v->zipCode $v->city</td>
						<td><input type='radio' name='depotTnt' value='$key' onclick='changeValueTntRC(\"$v->pexCode\", \"$v->name\", \"$v->address1\", \"$v->address2\", \"$v->zipCode\", \"$v->city\")'/></td>
						<td class='tntRCblanc' ></td>
						</tr>
						<tr><td class='tntRCblanc'></td><td class='tntRCrelaisColis' colspan='2'>$v->message</td><td class='tntRCblanc' ></td></tr>
						<tr id='tntRcDetail'>
						<td class='tntRCblanc'></td>
						<td></td>
						<td></td>
						<td></td>
						<td class='tntRCblanc'></td>
						</tr>";
				}
			else
				echo"
						<tr>
						<td class='tntRCblanc' ></td>
						<td class='tntRCblanc' ><img src='../modules/tntcarrier/img/logo-tnt-petit.jpg'></td>
						<td class='tntRCrelaisColis'> $v->name $v->address1 $v->address2 <br/>$v->zipCode $v->city</td>
						<td></td>
						<td class='tntRCblanc' ></td>
						</tr>
						<tr><td class='tntRCblanc'></td><td class='tntRCrelaisColis' colspan='2'>$v->message</td><td class='tntRCblanc' ></td></tr>
						<tr id='tntRcDetail'>
						<td class='tntRCblanc'></td>
						<td></td>
						<td></td>
						<td></td>
						<td class='tntRCblanc'></td>
						</tr>";
		echo "
		</table>
		";
	}
}
?>