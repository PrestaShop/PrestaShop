var relativ_base_dir = url_appel;
var num_mode_actif = -1;

url_appel = url_appel+'modules/mondialrelay/kit_mondialrelay/';

img_loader = new Image;
img_loader.src = url_appel+'loading.gif';

var google_map_general = null;
var geocoder = null;
var recherche = 0;
var json_addresses = null;
//var address;

function affiche_mydiv_mr(id_carrier, args)
{
	if ($('#id_carrier_mr' + id_carrier).attr("checked") == true)
	{
		if ($('#mondialrelay_' + id_carrier).css('display') == 'none')
		{
			$('#mondialrelay_' + id_carrier).toggle('slow');
			$('#mondialrelay_' + id_carrier).show();
			if ($('#mondialrelay_' + id_carrier).html() == '')
				recherche_MR(id_carrier, args);
			else
				$("#all_mondialrelay_map_" + id_carrier).show();
		}
	}
	else
	{
		if ($('#mondialrelay_' + id_carrier).css('display') == 'block')
			$('#mondialrelay_' + id_carrier).toggle('slow');
		$('#mondialrelay_' + id_carrier).hide();
		$("#all_mondialrelay_map_" + id_carrier).hide();
	}
}

function trim_MR(s)
{return s.replace(/^\s+|\s+$/g,"");}

function ltrim_MR(s)
{return s.replace(/^\s+/,"");}

function rtrim_MR(s)
{return s.replace(/\s+$/,"");}

function strtoupper_MR(s)
{return s.toUpperCase();}

var oXmlhttpMR1 = null;
if (window.XMLHttpRequest) {
  // If IE7, Mozilla, Safari, and so on: Use native object.
  oXmlhttpMR1 = new XMLHttpRequest();
}
else
{
  if (window.ActiveXObject) {
	 // ...otherwise, use the ActiveX control for IE5.x and IE6.
	 oXmlhttpMR1 = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
}

var oXmlhttpMR2 = null;
if (window.XMLHttpRequest) {
  // If IE7, Mozilla, Safari, and so on: Use native object.
  oXmlhttpMR2 = new XMLHttpRequest();
}
else
{
  if (window.ActiveXObject) {
	 // ...otherwise, use the ActiveX control for IE5.x and IE6.
	 oXmlhttpMR2 = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
}
	
var oXmlhttpMR3 = null;
if (window.XMLHttpRequest) {
  // If IE7, Mozilla, Safari, and so on: Use native object.
  oXmlhttpMR3 = new XMLHttpRequest();
}
else
{
  if (window.ActiveXObject) {
	 // ...otherwise, use the ActiveX control for IE5.x and IE6.
	 oXmlhttpMR3 = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
}		
		
var oXmlhttpMR5 = null;
if (window.XMLHttpRequest) {
  // If IE7, Mozilla, Safari, and so on: Use native object.
  oXmlhttpMR5 = new XMLHttpRequest();
}
else
{
  if (window.ActiveXObject) {
	 // ...otherwise, use the ActiveX control for IE5.x and IE6.
	 oXmlhttpMR5 = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
}	

function Url_suivi_MR(numexp)
{
	var ok = 0;
	if (numexp != '')
		ok = 1;
	if (ok == 1)
	{
	
	oXmlhttpMR1.open('POST',url_appel+'SuiviExpedition_ajax.php');
		oXmlhttpMR1.onreadystatechange = function()
			{
				if (oXmlhttpMR1.readyState == 4 && oXmlhttpMR1.status == 200)
				{
					var response = oXmlhttpMR1.responseText;
					document.getElementById('list_Url_suivi_MR_expediation').innerHTML=document.getElementById('list_Url_suivi_MR_expediation').innerHTML
					 + '<br><a href="'+response+'" target="suivi_exp">'+response+'</a>';
				}
			}
		oXmlhttpMR1.setRequestHeader("Content-type", "application/x-www-form-urlencoded; ");
		var data = 'Expedition='+encodeURIComponent(numexp);
		oXmlhttpMR1.send(data);
	}
	else
	{
		alert('Formulaire incomplet');
		return false;
	}
}
	
function impression_etiquette_MR()
{
	if (document.getElementById('input_Expeditions').value != '' && is_ok_mr('Pays','input_Langue',''))
	{
		oXmlhttpMR2.open('POST',url_appel+'ImpressionEtiquettePointRelais_ajax.php');
		oXmlhttpMR2.onreadystatechange=function()
			{
				if (oXmlhttpMR2.readyState == 1)
					document.getElementById('reponse_impression_etiquette_MR').innerHTML='Création en cours...';
				if (oXmlhttpMR2.readyState == 2)
					document.getElementById('reponse_impression_etiquette_MR').innerHTML='Création en cours...';
				if (oXmlhttpMR2.readyState == 4 && oXmlhttpMR2.status == 200)
				{
					var response = oXmlhttpMR2.responseText || "z|";
					document.getElementById('reponse_impression_etiquette_MR').innerHTML = '';
					res=response.split('|');
					if (res[0] != 0 && res[0] != 'a' && res[0] != 'z') 
						message_MR(res[0]);
					else if (res[0] == 'a')
						document.getElementById('reponse_impression_etiquette_MR').innerHTML = res[1];	
					else if (res[0] == 'z')
					{
						document.getElementById('reponse_impression_etiquette_MR').innerHTML = '';
						alert('Requête sans réponse.');
					}
					else
					{
						document.getElementById('list_url_reponse_impression_etiquette_MR_A4').innerHTML=document.getElementById('list_url_reponse_impression_etiquette_MR_A4').innerHTML
						+ '<br><a href="' + res[1] + '" target="print_etiquette">' + res[1] + '</a>';
						document.getElementById('list_url_reponse_impression_etiquette_MR_A5').innerHTML=document.getElementById('list_url_reponse_impression_etiquette_MR_A5').innerHTML
						+ '<br><a href="' + res[2] + '" target="print_etiquette">' + res[2] + '</a>';
					}
				}
			}
			oXmlhttpMR2.setRequestHeader("Content-type", "application/x-www-form-urlencoded; ");
			var data = 'Expeditions=' + encodeURIComponent(document.getElementById('input_Expeditions').value) + '&Langue=' + encodeURIComponent($('#input_Langue').val());
			oXmlhttpMR2.send (data);
		}
		else
		{
			alert('Formulaire incomplet');
			return false;
		}
	}


function creation_etiquette_MR()
{
	if (	is_ok_mr('NDossier','input_NDossier','')
			&& is_ok_mr('NClient','input_NClient','')
			&& is_ok_mr('Pays','input_Expe_Langage','')
			&& is_ok_mr('Ad2','input_Expe_Ad2','')
			&& is_ok_mr('Ad3','input_Expe_Ad3','')
			&& is_ok_mr('Ad2','input_Expe_Ad4','')
			&& is_ok_mr('VilleO','input_Expe_Ville','')
			&& is_ok_mr('CPO','input_Expe_CP','')
			&& is_ok_mr('Pays','input_Expe_Pays','')
			&& is_ok_mr('Tel1','input_Expe_Tel1','')
			&& is_ok_mr('Tel2','input_Expe_Tel2','')
			&& is_ok_mr('Mail','input_Expe_Mail','')
			&& is_ok_mr('Pays','input_Dest_Langage','')
			&& is_ok_mr('Ad2','input_Dest_Ad2','')
			&& is_ok_mr('Ad3','input_Dest_Ad3','')
			&& is_ok_mr('Ad2','input_Dest_Ad4','')
			&& is_ok_mr('VilleO','input_Dest_Ville','')
			&& is_ok_mr('CPO','input_Dest_CP','')
			&& is_ok_mr('Pays','input_Dest_Pays','')
			&& is_ok_mr('Tel2','input_Dest_Tel1','')
			&& is_ok_mr('Tel2','input_Dest_Tel2','')
			&& is_ok_mr('MailO','input_Dest_Mail','')
			&& is_ok_mr('PoidsO','input_Poids','')
			&& is_ok_mr('Longueur','input_Longueur','')
		)
	{			
		oXmlhttpMR3.open('POST',url_appel+'CreationEtiquettePointRelais_ajax.php');
		oXmlhttpMR3.onreadystatechange = function()
		{
			if (oXmlhttpMR3.readyState == 1)
				document.getElementById('reponse_creation_etiquette_MR').innerHTML = 'Création en cours...';
			if (oXmlhttpMR3.readyState == 2)
				document.getElementById('reponse_creation_etiquette_MR').innerHTML = 'Création en cours...';
			if (oXmlhttpMR3.readyState == 4 && oXmlhttpMR3.status == 200)
			{
				var response = oXmlhttpMR3.responseText || "z|";
				document.getElementById('reponse_creation_etiquette_MR').innerHTML = '';
				res = response.split('|');
				if (res[0] != 0 && res[0] != 'a' && res[0] != 'z') 
					{message_MR(res[0]);}
				else if (res[0] == 'a')
					document.getElementById('reponse_creation_etiquette_MR').innerHTML = res[1];
				else if (res[0] == 'z')
				{
					document.getElementById('reponse_creation_etiquette_MR').innerHTML = '';
					alert('Requête sans réponse.');
				}
				else
				{
					document.getElementById('list_url_reponse_creation_etiquette_MR').innerHTML = document.getElementById('list_url_reponse_creation_etiquette_MR').innerHTML
					+ '<br><a href="'+res[2]+'" target="print_etiquette">'+res[2]+'</a>';
					Url_suivi_MR(res[1]);//ligne à supprimer seulement utilisée pour le test
					if (document.getElementById('input_Expeditions').value != '')
						document.getElementById('input_Expeditions').value = document.getElementById('input_Expeditions').value+';'+res[1];
					else
						document.getElementById('input_Expeditions').value = res[1];
				}
			}
		}
		oXmlhttpMR3.setRequestHeader("Content-type", "application/x-www-form-urlencoded; ");
		var data = 'ModeCol='+encodeURIComponent(document.getElementById('input_ModeCol').value)+'&ModeLiv='+encodeURIComponent(document.getElementById('input_ModeLiv').value)+'&NDossier='+encodeURIComponent(document.getElementById('input_NDossier').value)+'&NClient='+encodeURIComponent(document.getElementById('input_NClient').value)+'&Expe_Langage='+encodeURIComponent(document.getElementById('input_Expe_Langage').value)+'&Expe_Ad1='+encodeURIComponent(document.getElementById('input_Expe_Ad1').value)+'&Expe_Ad2='+encodeURIComponent(document.getElementById('input_Expe_Ad2').value)+'&Expe_Ad3='+encodeURIComponent(document.getElementById('input_Expe_Ad3').value)+'&Expe_Ad4='+encodeURIComponent(document.getElementById('input_Expe_Ad4').value)+'&Expe_Ville='+encodeURIComponent(document.getElementById('input_Expe_Ville').value)+'&Expe_CP='+encodeURIComponent(document.getElementById('input_Expe_CP').value)+'&Expe_Pays='+encodeURIComponent(document.getElementById('input_Expe_Pays').value)+'&Expe_Tel1='+encodeURIComponent(document.getElementById('input_Expe_Tel1').value)+'&Expe_Tel2='+encodeURIComponent(document.getElementById('input_Expe_Tel2').value)+'&Expe_Mail='+encodeURIComponent(document.getElementById('input_Expe_Mail').value)+'&Dest_Langage='+encodeURIComponent(document.getElementById('input_Dest_Langage').value)+'&Dest_Ad1='+encodeURIComponent(document.getElementById('input_Dest_Ad1').value)+'&Dest_Ad2='+encodeURIComponent(document.getElementById('input_Dest_Ad2').value)+'&Dest_Ad3='+encodeURIComponent(document.getElementById('input_Dest_Ad3').value)+'&Dest_Ad4='+encodeURIComponent(document.getElementById('input_Dest_Ad4').value)+'&Dest_Ville='+encodeURIComponent(document.getElementById('input_Dest_Ville').value)+'&Dest_CP='+encodeURIComponent(document.getElementById('input_Dest_CP').value)+'&Dest_Pays='+encodeURIComponent(document.getElementById('input_Dest_Pays').value)+'&Dest_Tel1='+encodeURIComponent(document.getElementById('input_Dest_Tel1').value)+'&Dest_Tel2='+encodeURIComponent(document.getElementById('input_Dest_Tel2').value)+'&Dest_Mail='+encodeURIComponent(document.getElementById('input_Dest_Mail').value)+'&Poids='+encodeURIComponent(document.getElementById('input_Poids').value)+'&Longueur='+encodeURIComponent(document.getElementById('input_Longueur').value)+'&Taille='+encodeURIComponent(document.getElementById('input_Taille').options[document.getElementById('input_Taille').selectedIndex].value)+'&NbColis='+encodeURIComponent(document.getElementById('input_NbColis').options[document.getElementById('input_NbColis').selectedIndex].value)+'&CRT_Valeur='+encodeURIComponent(document.getElementById('input_CRT_Valeur').value)+'&CRT_Devise='+encodeURIComponent(document.getElementById('input_CRT_Devise').value)+'&Exp_Valeur='+encodeURIComponent(document.getElementById('input_Exp_Valeur').value)+'&Exp_Devise='+encodeURIComponent(document.getElementById('input_Exp_Devise').value)+'&LIV_Rel_Pays='+encodeURIComponent(document.getElementById('input_LIV_Rel_Pays').value)+'&LIV_Rel='+encodeURIComponent(document.getElementById('input_LIV_Rel').value)+'&Assurance='+encodeURIComponent(document.getElementById('input_Assurance').options[document.getElementById('input_Assurance').selectedIndex].value)+'&Instructions='+encodeURIComponent(document.getElementById('input_Instructions').value)+'&Texte='+encodeURIComponent(document.getElementById('input_Texte').value);
		oXmlhttpMR3.send(data);
	}
	else
	{
		alert('Formulaire incomplet');
		return false;
	}
}

function recherche_MR(num, args)
{
	$.ajax({
		type: "POST",
		url: url_appel+'RecherchePointRelais_ajax.php',
		data: args ,
		dataType: 'json',
		beforeSend : function(params)
			{
				$('#loading_mr').show();
			},
		success: function(obj)
			{
				$('#loading_mr').hide();
				$('#mondialrelay_'+num).html('');
				$("#all_mondialrelay_map_" + num).show();
				var cpt = 0;
				if (obj.error)
					$('#mondialrelay_'+num).html('<div class="error"><p>'+server_error+'</p></div>');
				else
				{
					while (obj.addresses[cpt])
					{
						if (obj.addresses[cpt].num)
							set_html_MR_recherche(obj.addresses[cpt], num, obj.base_dir, cpt);
						cpt++;
					}
					if (!obj.addresses[0].num)
						$('#mondialrelay_'+num).html('<div class="error"><p>'+address_error+'</p></div>');
				}
			}
	});
}

	
function is_numeric(id,message)
{
	var string = trim_MR(document.getElementById(id).value);
	if ((isNaN(string)) == true || string == '')
	{
		document.getElementById(id).focus();
		alert(message);
		return false;
	}
	else
		return true;
}	
	
function is_no_null_mr(id,message)
{
	var string = trim_MR(document.getElementById(id).value);
	if (string != '')
		return true;
	else
	{
		document.getElementById(id).focus();
		alert(message);
		return false;
	}
}
	
function is_ok_mr(type,id,message)
	{
		if (type != 'key') document.getElementById(id).value = trim_MR(strtoupper_MR(document.getElementById(id).value));
		var string = document.getElementById(id).value;
		var reg = "";
		if (type == 'Enseigne' )
			reg = "^[0-9A-Z]{2}[0-9A-Z ]{6}$";
		else if (type == 'CM' ) 
			reg = "^[0-9]{2}$";
		else if (type == 'key' )
			reg = "^[0-9A-Za-z_\'., /\-]{2,32}$";
		else if (type == 'Longueur' && string != '')
			reg = "^[0-9]{0,3}$";
		else if (type == 'MailO' )
			reg = "^[a-zA-Z0-9\-\.@_]{6,70}$";
		else if (type == 'Mail' && string != '')
			reg = "^[a-zA-Z0-9\-\.@_]{0,70}$";
		else if (type == 'Tel1' )
			reg = "^(((00|[\+])33)|0)[0-9]{1}[0-9]{8}$";
		else if (type == 'Tel2' && string != '')
			reg = "^(((00|[\+])33)|0)[0-9]{1}[0-9]{8}$";
		else if (type == 'Ad2' && string != '')
			reg = "^[0-9A-Z_\'., /\-]{2,32}$";
		else if (type == 'Ad3')
			reg = "^[0-9A-Z_\'., /\-]{2,32}$";
		else if (type == 'NDossier' && string != '')
			reg = "^[0-9A-Z_ \-]{0,15}$";
		else if (type == 'NClient' && string != '')
			reg = "^[0-9A-Z]{0,9}$";
		else if (type == 'Pays')
			reg = "^[A-Z]{2}$";
		else if (type == 'VilleO' )
			reg = "^[A-Z_' \-]{2,25}$";
		else if (type == 'Ville' && string != '')
			reg = "^[A-Z_' \-]{2,25}$";
		else if (type == 'CP' && string != '')
			reg = "^[0-9]{4,5}$";
		else if (type == 'CPO' )
			reg = "^[0-9]{4,5}$";
		else if (type == 'Taille' && string != '')
			reg = "^XS|S|M|L|XL|XXL|3XL$";
		else if (type == 'Poids' && string != '')
			reg = "^[0-9]{1,6}$";
		else if (type == 'PoidsO')
			reg = "^[0-9]{1,6}$";
		else if (type == 'Action' && string != '')
			reg = "^REL|24R|ESP|DRI|LDS|LDR|LD1$";
		else if (reg!="")
			{
			try {
			 if (string != string.match(reg)[0]) 
			  {if (message!='') {document.getElementById(id).focus();alert(message);}
			  return false;
			  }
			  else {return true;}
			   } catch(err) 
			  {
			  document.getElementById(id).focus();alert(message);
			  return false;
			  }
			} else {return true;} 
	}

	function masque_recherche_MR_detail(num)	
	{
		document.getElementById('bg_detail_md_'+num).style.display='none';
		document.getElementById('detail_md_'+num).style.display='none';
		document.getElementById('detail_md_'+num).innerHTML=''
	} 
	
	function recherche_MR_detail(num, pays, num_mode)
	{
		oXmlhttpMR5.open('POST',url_appel+'RechercheDetailPointRelais_ajax.php');
		oXmlhttpMR5.onreadystatechange=function() {
			if (oXmlhttpMR5.readyState==1) {
									var arrayPageSize = getPageSize_MR();
									var arrayScroll = getPageScroll_MR();
									document.getElementById('bg_detail_md_'+num_mode).style.height = (arrayPageSize[1] + 'px');
									document.getElementById('bg_detail_md_'+num_mode).style.display='block';
									document.getElementById('detail_md_'+num_mode).style.top = (Math.round(arrayScroll[1]+(arrayPageSize[3]-480)/2)+ 'px');
									document.getElementById('detail_md_'+num_mode).style.left = (Math.round((arrayPageSize[2]-740)/2)+ 'px');
									document.getElementById('detail_md_'+num_mode).innerHTML="<table width=100%><tr height=478 style='border:none;'><td valing=middle style='border:none;'><center><img class='none' src='"+img_loader.src+"' ></center></td></tr></table>";
									document.getElementById('detail_md_'+num_mode).style.display='block';
			}
			/*if (oXmlhttpMR5.readyState==2) {
									var arrayPageSize = getPageSize_MR();
									var arrayScroll = getPageScroll_MR();
									document.getElementById('bg_detail_md').style.height = (arrayPageSize[1] + 'px');
									document.getElementById('bg_detail_md').style.display='block';
									document.getElementById('detail_md').style.top = (Math.round(arrayScroll[1]+(arrayPageSize[3]-480)/2)+ 'px');
									document.getElementById('detail_md').style.left = (Math.round((arrayPageSize[2]-740)/2)+ 'px');
									document.getElementById('detail_md').innerHTML="<table width=100%><tr height=478 style='border:none;'><td valing=middle style='border:none;'><center><img class='none' src='"+img_loader.src+"' ></center></td></tr></table>";
									document.getElementById('detail_md').style.display='block';
			}*/
			if (oXmlhttpMR5.readyState==4 && oXmlhttpMR5.status == 200) {
			var response = oXmlhttpMR5.responseText || "z|";
									res=response.split('|');
									if (res[0]!=0 && res[0]!='a' && res[0]!='z') 
										{masque_recherche_MR_detail(num_mode);
										 message_MR(res[0]);}
									else if (res[0]=='a') {document.getElementById('detail_md_'+res[2]).innerHTML=res[1];}	
									else if (res[0]=='z') {masque_recherche_MR_detail(res[2]); alert('Requête sans réponse.');}
									else {document.getElementById('detail_md_'+res[2]).innerHTML=res[1];};};
			}
	
		oXmlhttpMR5.setRequestHeader("Content-type", "application/x-www-form-urlencoded; ");
		var data = 'relativ_base_dir='+encodeURIComponent(relativ_base_dir)+'&Num='+encodeURIComponent(num)+'&Pays='+encodeURIComponent(pays)+'&num_mode='+num_mode;
		oXmlhttpMR5.send (data);
				

	}
	
function select_PR_MR(num, id_carrier)
{
	if (num != '')
	{
		$('#MR_Selected_Num_'+id_carrier).val(num);
		$('#MR_Selected_LgAdr1_'+id_carrier).val($('#MR_LgAdr1_'+num).html());
		$('#MR_Selected_LgAdr2_'+id_carrier).val($('#MR_LgAdr2_'+num).html());
		$('#MR_Selected_LgAdr3_'+id_carrier).val($('#MR_LgAdr3_'+num).html());
		$('#MR_Selected_LgAdr4_'+id_carrier).val($('#MR_LgAdr4_'+num).html());
		$('#MR_Selected_CP_'+id_carrier).val($('#MR_CP_'+num).html());
		$('#MR_Selected_Ville_'+id_carrier).val($('#MR_Ville_'+num).html());
		$('#MR_Selected_Pays_'+id_carrier).val($('#MR_Pays_'+num).html());
	}
	else 
	{
		$('#MR_Selected_Num_'+id_carrier).val('');
		$('#MR_Selected_LgAdr1_'+id_carrier).val('');
		$('#MR_Selected_LgAdr2_'+id_carrier).val('');
		$('#MR_Selected_LgAdr3_'+id_carrier).val('');
		$('#MR_Selected_LgAdr4_'+id_carrier).val('');
		$('#MR_Selected_CP_'+id_carrier).val('');
		$('#MR_Selected_Ville_'+id_carrier).val('');
		$('#MR_Selected_Pays_'+id_carrier).val('');
	};
	if (one_page_checkout == true)
	{
		$.ajax({
		type: 'POST',
		url: gl_base_dir + 'modules/mondialrelay/kit_mondialrelay/mr_opc_ajax.php',
		async: true,
		cache: false,
		data: 'Num='+$('#MR_Selected_Num_'+id_carrier).val()+'&LgAdr1='+$('#MR_Selected_LgAdr1_'+id_carrier).val()+'&LgAdr2='+$('#MR_Selected_LgAdr2_'+id_carrier).val()+'&LgAdr3='+$('#MR_Selected_LgAdr3_'+id_carrier).val()+'&LgAdr4='+$('#MR_Selected_LgAdr4_'+id_carrier).val()+'&CP='+$('#MR_Selected_CP_'+id_carrier).val()+'&Ville='+$('#MR_Selected_Ville_'+id_carrier).val()+'&Pays='+$('#MR_Selected_Pays_'+id_carrier).val()
		});
	}
}

function message_MR(etat)
{
	if (etat == 'a') {alert("Résultat vide");}
	if (etat == 1) {alert("Enseigne invalide");}
	if (etat == 2) {alert("Numéro d'enseigne vide ou inexistant");}
	if (etat == 3) {alert("Numéro de compte enseigne invalide");}
	if (etat == 5) {alert("Numéro de dossier enseigne invalide");}
	if (etat == 7) {alert("Numéro de client enseigne invalide");}
	if (etat == 9) {alert("Nom de ville non reconnu ou non unique");}
	if (etat == 10) {alert("Type de collecte invalide ou incorrect (1/D > Domicile -- 3/R > Relais)");}
	if (etat == 11) {alert("Numéro de Point Relais de collecte invalide");}
	if (etat == 12) {alert("Pays du Point Relais de collecte invalide");}
	if (etat == 13) {alert("Type de livraison invalide ou incorrect (1/D > Domicile -- 3/R > Relais)");}
	if (etat == 14) {alert("Numéro du Point Relais de livraison invalide");}
	if (etat == 15) {alert("Pays du Point Relais de livraison invalide");}
	if (etat == 16) {alert("Code pays invalide");}
	if (etat == 17) {alert("Adresse invalide");}
	if (etat == 18) {alert("Ville invalide");}
	if (etat == 19) {alert("Code postal invalide");}
	if (etat == 20) {alert("Poids du colis invalide");}
	if (etat == 21) {alert("Taille (Longueur + Hauteur) du colis invalide");}
	if (etat == 22) {alert("Taille du Colis invalide");}
	if (etat == 24) {alert("Numéro de Colis Mondial Relay invalide");}
	if (etat == 28) {alert("Mode de collecte invalide");}
	if (etat == 29) {alert("Mode de livraison invalide");}
	if (etat == 30) {alert("Adresse (L1) de l'expéditeur invalide");}
	if (etat == 31) {alert("Adresse (L2) de l'expéditeur invalide");}
	if (etat == 33) {alert("Adresse (L3) de l'expéditeur invalide");}
	if (etat == 34) {alert("Adresse (L4) de l'expéditeur invalide");}
	if (etat == 35) {alert("Ville de l'expéditeur invalide");}
	if (etat == 36) {alert("Code postal de l'expéditeur invalide");}
	if (etat == 37) {alert("Pays de l'expéditeur invalide");}
	if (etat == 38) {alert("Numéro de téléphone de l'expéditeur invalide");}
	if (etat == 39) {alert("Adresse e-mail de l'expéditeur invalide");}
	if (etat == 40) {alert("Action impossible sans ville ni code postal");}
	if (etat == 41) {alert("Mode de livraison invalide");}
	if (etat == 42) {alert("Montant CRT invalide");}
	if (etat == 43) {alert("Devise CRT invalide");}
	if (etat == 44) {alert("Valeur du colis invalide");}
	if (etat == 45) {alert("Devise de la valeur du colis invalide");}
	if (etat == 46) {alert("Plage de numéro d'expédition epuisee");}
	if (etat == 47) {alert("Nombre de colis invalide");}
	if (etat == 48) {alert("Multi-colis en Point Relais Interdit");}
	if (etat == 49) {alert("Mode de collecte ou de livraison invalide");}
	if (etat == 50) {alert("Adresse (L1) du destinataire invalide");}
	if (etat == 51) {alert("Adresse (L2) du destinataire invalide");}
	if (etat == 53) {alert("Adresse (L3) du destinataire invalide");}
	if (etat == 54) {alert("Adresse (L4) du destinataire invalide");}
	if (etat == 55) {alert("Ville du destinataire invalide");}
	if (etat == 56) {alert("Code postal du destinataire invalide");}
	if (etat == 57) {alert("Pays du destinataire invalide");}
	if (etat == 58) {alert("Numéro de téléphone du destinataire invalide");}
	if (etat == 59) {alert("Adresse e-mail du destinataire invalide");}
	if (etat == 60) {alert("Champ texte libre invalide");}
	if (etat == 61) {alert("Top avisage invalide");}
	if (etat == 62) {alert("Instruction de livraison invalide");}
	if (etat == 63) {alert("Assurance invalide ou incorrecte");}
	if (etat == 64) {alert("Temps de montage invalide");}
	if (etat == 65) {alert("Top rendez-vous invalide");}
	if (etat == 66) {alert("Top reprise invalide");}
	if (etat == 70) {alert("Numéro de Point Relais invalide");}
	if (etat == 72) {alert("Langue expéditeur invalide");}
	if (etat == 73) {alert("Langue destinataire invalide");}
	if (etat == 74) {alert("Langue invalide");}
	if (etat == 80) {alert("Code tracing : Colis enregistre");}
	if (etat == 81) {alert("Code tracing : Colis en traitement chez Mondial Relay");}
	if (etat == 82) {alert("Code tracing : Colis livre");}
	if (etat == 83) {alert("Code tracing : Anomalie");}
	if (etat == 84) {alert("84 (Réservé Code Tracing)");}
	if (etat == 85) {alert("85 (Réservé Code Tracing)");}
	if (etat == 86) {alert("86 (Réservé Code Tracing)");}
	if (etat == 87) {alert("87 (Réservé Code Tracing)");}
	if (etat == 88) {alert("88 (Réservé Code Tracing)");}
	if (etat == 89) {alert("89 (Réservé Code Tracing)");}
	if (etat == 90) {alert("AS400 indisponible");}
	if (etat == 91) {alert("Numéro d'expédition invalide");}
	if (etat == 93) {alert("Aucun élément retourné par le plan de tri\n\
							Si vous effectuez une collecte ou une livraison en Point Relais, vérifiez que les\n\
							Point Relais sont bien disponibles.\n\
							Si vous effectuez une livraison à domicile, il est probable que le code postal que\n\
							vous avez indiquez n'existe pas.");}
	if (etat == 94) {alert("Colis Inexistant");}
	if (etat == 95) {alert("Compte Enseigne non active");}
	if (etat == 96) {alert("Type d'enseigne incorrect en Base");}
	if (etat == 97) {alert("Clé de sécurité invalide");}
	if (etat == 98) {alert("Service Indisponible");}
	if (etat == 99) {alert("Erreur générique du service\n\
							Cette erreur peut être dû autant à un problème technique du service qu'à des\n\
							données incorrectes ou inexistantes dans la Base de Données. Lorsque vous avez\n\
							cette erreur veuillez la notifier à Mondial Relay en précisant la date et l'heure de la\n\
							connexion ainsi que les informations envoyés au WebService afin d'effectuer une\n\
							vérification.");}
}


function getPageScroll_MR()
{

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}


function getPageSize_MR()
{
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}
