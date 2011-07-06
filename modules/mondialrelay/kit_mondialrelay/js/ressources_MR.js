var relativ_base_dir = _PS_MR_MODULE_DIR_;
var num_mode_actif = -1;

img_loader = new Image;
img_loader.src = _PS_MR_MODULE_DIR_+'images/loader.gif';

var google_map_general = null;
var geocoder = null;
var recherche = 0;
var json_addresses = null;

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

function recherche_MR(num, args)
{
	$.ajax({
		type: "POST",
		url: _PS_MR_MODULE_DIR_ + 'kit_mondialrelay/RecherchePointRelais_ajax.php',
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
						++cpt;
					}
					if (!obj.addresses[0].num)
						$('#mondialrelay_'+num).html('<div class="error"><p>'+address_error+'</p></div>');
				}
			},
		error: function(xhr, ajaxOptions, thrownError)
		{
			
		}
	});
}
	
	function masque_recherche_MR_detail(num)	
	{
		document.getElementById('bg_detail_md_'+num).style.display='none';
		document.getElementById('detail_md_'+num).style.display='none';
		document.getElementById('detail_md_'+num).innerHTML=''
	} 
	
	function recherche_MR_detail(num, pays, num_mode)
	{
		oXmlhttpMR5.open('POST',_PS_MR_MODULE_DIR_ + 'kit_mondialrelay/RechercheDetailPointRelais_ajax.php');
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
									else if (res[0]=='z') {masque_recherche_MR_detail(res[2]); alert('Requ�te sans r�ponse.');}
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
		data: 'Num='+$('#MR_Selected_Num_'+id_carrier).val()+'&LgAdr1='+$('#MR_Selected_LgAdr1_'+id_carrier).val()+'&LgAdr2='+$('#MR_Selected_LgAdr2_'+id_carrier).val()+'&LgAdr3='+$('#MR_Selected_LgAdr3_'+id_carrier).val()+'&LgAdr4='+$('#MR_Selected_LgAdr4_'+id_carrier).val()+'&CP='+	$('#MR_Selected_CP_'+id_carrier).val()+'&Ville='+$('#MR_Selected_Ville_'+id_carrier).val()+'&Pays='+$('#MR_Selected_Pays_'+id_carrier).val()
		});
	}
	
	// 1.3 compatibility, hookProcessCarrier Missing
	// Allow to fill the database with the selected information
	$.ajax({
		type: 'POST',
		url: _PS_MR_MODULE_DIR_ + 'ajax.php',
		data: 'method=addSelectedCarrierToDB' + 
					'&id_carrier=' + id_carrier + 
					'&MR_Selected_Num_' + id_carrier + '=' + $('#MR_Selected_Num_' + id_carrier).val() + 
					'&MR_Selected_LgAdr1_' + id_carrier + '=' + $('#MR_Selected_LgAdr1_' + id_carrier).val() + 
					'&MR_Selected_LgAdr2_' + id_carrier + '=' + $('#MR_Selected_LgAdr2_' + id_carrier).val() + 
					'&MR_Selected_LgAdr3_' + id_carrier + '=' + $('#MR_Selected_LgAdr3_' + id_carrier).val() + 
					'&MR_Selected_LgAdr4_' + id_carrier + '=' + $('#MR_Selected_LgAdr4_' + id_carrier).val() + 
					'&MR_Selected_CP_' + id_carrier + '=' + $('#MR_Selected_CP_' + id_carrier).val() + 
					'&MR_Selected_Ville_' + id_carrier + '=' + $('#MR_Selected_Ville_' + id_carrier).val() + 
					'&MR_Selected_Pays_' + id_carrier  + '=' + $('#MR_Selected_Pays_' + id_carrier).val(),
		success: function(json) 
		{
			//console.log(json);
		},
		error: function(xhr, ajaxOptions, thrownError)
		{
			//console.log(xhr);
			//console.log(thrownError);
			//console.log(ajaxOptions);
		}	
	});
	
}

function message_MR(etat)
{
	if (etat == 'a') {alert("R�sultat vide");}
	if (etat == 1) {alert("Enseigne invalide");}
	if (etat == 2) {alert("Num�ro d'enseigne vide ou inexistant");}
	if (etat == 3) {alert("Num�ro de compte enseigne invalide");}
	if (etat == 5) {alert("Num�ro de dossier enseigne invalide");}
	if (etat == 7) {alert("Num�ro de client enseigne invalide");}
	if (etat == 9) {alert("Nom de ville non reconnu ou non unique");}
	if (etat == 10) {alert("Type de collecte invalide ou incorrect (1/D > Domicile -- 3/R > Relais)");}
	if (etat == 11) {alert("Num�ro de Point Relais de collecte invalide");}
	if (etat == 12) {alert("Pays du Point Relais de collecte invalide");}
	if (etat == 13) {alert("Type de livraison invalide ou incorrect (1/D > Domicile -- 3/R > Relais)");}
	if (etat == 14) {alert("Num�ro du Point Relais de livraison invalide");}
	if (etat == 15) {alert("Pays du Point Relais de livraison invalide");}
	if (etat == 16) {alert("Code pays invalide");}
	if (etat == 17) {alert("Adresse invalide");}
	if (etat == 18) {alert("Ville invalide");}
	if (etat == 19) {alert("Code postal invalide");}
	if (etat == 20) {alert("Poids du colis invalide");}
	if (etat == 21) {alert("Taille (Longueur + Hauteur) du colis invalide");}
	if (etat == 22) {alert("Taille du Colis invalide");}
	if (etat == 24) {alert("Num�ro de Colis Mondial Relay invalide");}
	if (etat == 28) {alert("Mode de collecte invalide");}
	if (etat == 29) {alert("Mode de livraison invalide");}
	if (etat == 30) {alert("Adresse (L1) de l'exp�diteur invalide");}
	if (etat == 31) {alert("Adresse (L2) de l'exp�diteur invalide");}
	if (etat == 33) {alert("Adresse (L3) de l'exp�diteur invalide");}
	if (etat == 34) {alert("Adresse (L4) de l'exp�diteur invalide");}
	if (etat == 35) {alert("Ville de l'exp�diteur invalide");}
	if (etat == 36) {alert("Code postal de l'exp�diteur invalide");}
	if (etat == 37) {alert("Pays de l'exp�diteur invalide");}
	if (etat == 38) {alert("Num�ro de t�l�phone de l'exp�diteur invalide");}
	if (etat == 39) {alert("Adresse e-mail de l'exp�diteur invalide");}
	if (etat == 40) {alert("Action impossible sans ville ni code postal");}
	if (etat == 41) {alert("Mode de livraison invalide");}
	if (etat == 42) {alert("Montant CRT invalide");}
	if (etat == 43) {alert("Devise CRT invalide");}
	if (etat == 44) {alert("Valeur du colis invalide");}
	if (etat == 45) {alert("Devise de la valeur du colis invalide");}
	if (etat == 46) {alert("Plage de num�ro d'exp�dition epuisee");}
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
	if (etat == 58) {alert("Num�ro de t�l�phone du destinataire invalide");}
	if (etat == 59) {alert("Adresse e-mail du destinataire invalide");}
	if (etat == 60) {alert("Champ texte libre invalide");}
	if (etat == 61) {alert("Top avisage invalide");}
	if (etat == 62) {alert("Instruction de livraison invalide");}
	if (etat == 63) {alert("Assurance invalide ou incorrecte");}
	if (etat == 64) {alert("Temps de montage invalide");}
	if (etat == 65) {alert("Top rendez-vous invalide");}
	if (etat == 66) {alert("Top reprise invalide");}
	if (etat == 70) {alert("Num�ro de Point Relais invalide");}
	if (etat == 72) {alert("Langue exp�diteur invalide");}
	if (etat == 73) {alert("Langue destinataire invalide");}
	if (etat == 74) {alert("Langue invalide");}
	if (etat == 80) {alert("Code tracing : Colis enregistre");}
	if (etat == 81) {alert("Code tracing : Colis en traitement chez Mondial Relay");}
	if (etat == 82) {alert("Code tracing : Colis livre");}
	if (etat == 83) {alert("Code tracing : Anomalie");}
	if (etat == 84) {alert("84 (R�serv� Code Tracing)");}
	if (etat == 85) {alert("85 (R�serv� Code Tracing)");}
	if (etat == 86) {alert("86 (R�serv� Code Tracing)");}
	if (etat == 87) {alert("87 (R�serv� Code Tracing)");}
	if (etat == 88) {alert("88 (R�serv� Code Tracing)");}
	if (etat == 89) {alert("89 (R�serv� Code Tracing)");}
	if (etat == 90) {alert("AS400 indisponible");}
	if (etat == 91) {alert("Num�ro d'exp�dition invalide");}
	if (etat == 93) {alert("Aucun �l�ment retourn� par le plan de tri\n\
							Si vous effectuez une collecte ou une livraison en Point Relais, v�rifiez que les\n\
							Point Relais sont bien disponibles.\n\
							Si vous effectuez une livraison � domicile, il est probable que le code postal que\n\
							vous avez indiquez n'existe pas.");}
	if (etat == 94) {alert("Colis Inexistant");}
	if (etat == 95) {alert("Compte Enseigne non active");}
	if (etat == 96) {alert("Type d'enseigne incorrect en Base");}
	if (etat == 97) {alert("Cl� de s�curit� invalide");}
	if (etat == 98) {alert("Service Indisponible");}
	if (etat == 99) {alert("Erreur g�n�rique du service\n\
							Cette erreur peut �tre d� autant � un probl�me technique du service qu'� des\n\
							donn�es incorrectes ou inexistantes dans la Base de Donn�es. Lorsque vous avez\n\
							cette erreur veuillez la notifier � Mondial Relay en pr�cisant la date et l'heure de la\n\
							connexion ainsi que les informations envoy�s au WebService afin d'effectuer une\n\
							v�rification.");}
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
