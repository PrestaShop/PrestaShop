/** Javascript B2C Relais Colis - version 2.0 - 08/07/2010 **/

var pathToImages = "../modules/tntcarrier/img/";
var tntDomain = "www.tnt.fr";

var tntRCcodePostal;
var tntRCCommune;
var tntRClisteRelais;
var tntRCJsonCommunes;

var tntRCMsgHeaderTitle = "Mode de livraison";
//var tntRCMsgSubHeaderTitle = "Choisissez le Relais Colis<sup class='tntRCSup'>&#174;</sup> qui vous convient :";
var tntRCMsgSubHeaderTitle = "Choisissez l'agence depot qui vous convient :";
var tntRCMsgHeaderPopup = "D&#233;tail du Relais Colis<sup class='tntRCSup'>&#174;</sup>";
var tntRCMsgSubHeaderPopup = "Descriptif :";
var tntRCMsgBodyLoading = "Chargement en cours...";
var tntRCMsgBodyInput = "D&eacute;partement : ";
var tntRCMsgBodyBack2Communes = "Revenir &#224; la liste des communes";
var tntRCMsgErrCodePostal = "Veuillez saisir un code postal sur 5 chiffres";
var tntRCMsgErrLoadCommunes = "Aucun Relais Colis&#174; disponible";
var tntRCMsgErrLoadRelais = "Aucun Relais Colis&#174; disponible";

var tntRCsize800 = "500px";
var tntRCsize789 = "500px";
var tntRCsize670 = "400px";
var tntRCsize650 = "400px";
var tntRCsize50 = "50px";
var tntRCsize8 = "8px";
var tntRCsize5 = "5px";
var tntRCsize6 = "6px";
var tntRCsize10 = "10px";
var tntRCsize30 = "30px";
var tntRCsize109 = "109px";
var tntRCsize442 = "240px";
var tntRCsize447 = "240px";
var tntRCsize218 = "178px";
var tntRCsize253 = "213px";
var tntRCsize20 = "20px";
var tntRCsize392 = "352px";
var tntRCsize412 = "332px";

function getURLParam(name) {
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null ) return "";
	else return results[1];
};

// Chargement de la liste de relais colis après le choix de la commune parmis plusieurs
// communes correspondant au même code postal
function tntRCgetRelaisColisJSON(commune)
{
	if (!commune) {
		// La commune du code postal correspond à la sélection du radio bouton tntRCchoixComm
		tntRCCommune =	$("input[@type=radio][@checked][@name=tntRCchoixComm]").val();
	}
	else {
		// Utilisation de la valeur fournie en paramètre
		tntRCCommune = commune	
	}

	// Affichage message "chargement en cours"
	tntRCsetChargementEnCours();
	
	var ajaxUrl;
	var ajaxData;
	
	ajaxUrl = "http://" + tntDomain + "/public/b2c/relaisColis/loadJson.do?cp=" + tntRCcodePostal + "&commune=" + tntRCCommune;
	ajaxData = "";
	
	// Chargement de la liste de relais colis
	$.ajax({
	   type: "GET",
	   url: ajaxUrl,
	   data: ajaxData,
	   dataType: "script"
	});
};

// Affichage d'une liste de relais colis
function tntRCafficheRelais(jData) {
	
	var jMessage = $('#blocCodePostal');
	
	var tntRCjTable = $("<table style='border:1px solid gray;' cellpadding='0' cellspacing='0' width='" + tntRCsize800  + "'></table>");
	
	// Ligne blanche de séparation
	tntRCjTable.append(tntRCligneBlanche6Col());
	
	// Entêtes de colonnes grise
	tntRCjTable.append(tntRCenteteGrise6Col());

	//affiche le contenu du fichier dans le conteneur dédié
	jMessage.html("");
			
	var i = 0;
	
	tntRClisteRelais = jData;
	for(i = 0; i < jData.length; i++) {
			
		var oRelais = jData[i];
			
		// Les noeuds dans le fichier XML ne sont pas forcément ordonnés pour l'affichage, on va donc d'abord récupérer leur valeur
		var codeRelais = oRelais[0];
		var nomRelais = oRelais[1];
		var adresse = oRelais[4];
		var codePostal = oRelais[2];
		var commune = oRelais[3];
		var heureFermeture = oRelais[21];

		var messages="";			
			
		var logo_point = "";
		if (messages != "") logo_point = "<img src='" + pathToImages + "exception.gif' alt='Informations compl&#233;mentaires' width='16px' height='16px'>";
		
		tntRCjTable.append(
			"<tr>"+
				"<td class='tntRCblanc' width='" + tntRCsize5 + "'></td>"+
				"<td class='tntRCblanc' width='" + tntRCsize50 + "'><img src='" + pathToImages + "logo-tnt-petit.jpg'>&nbsp;" + logo_point + "</td>"+
				"<td class='tntRCrelaisColis' width='" + tntRCsize650 + "'>" + nomRelais + " - " + adresse + " - " + codePostal + " - " + commune + "<BR>&nbsp;&nbsp;&nbsp;&nbsp;>> Ouvert jusqu'&agrave; " + heureFermeture + "</td>"+
				"<td class='tntRCrelaisColis' width='" + tntRCsize10 + "'>&nbsp;</td>"+
				"<td class='tntRCrelaisColis' valign='middle' align='center' width='" + tntRCsize109 + "'>"+
					"<a href='#' onclick='tntRCafficheDetail(" + i + ");'><img src='" + pathToImages + "loupe.gif' class='tntRCBoutonLoupe'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+
					"<input type='radio' style='vertical-align: middle;' name='tntRCchoixRelais' value='" + codeRelais + "'" + ( i==0 ? "checked" : "") + " onclick='tntRCSetSelectedInfo(" + i + ")'/>"+
				"</td>"+
				"<td class='tntRCblanc' width='" + tntRCsize6 + "'></td>"+
			"</tr><tr id='tntRcDetail"+i+"' style='display:none'></tr>");
	}
	
	// Mémorisation des infos du relais sélectionné par défaut (c'est le premier)		
	tntRCSetSelectedInfo(0, true);
	
	// Ajout du lien de retour sur la liste des communes si cette dernière a été mémorisée
	if (tntRCJsonCommunes != null) {
		tntRCjTable.append(
			"<tr>"+
				"<td colspan='5' class='tntRCBack2Communes'>"+
					"<a href='#' onclick='tntRCafficheCommunes(tntRCJsonCommunes);'>"+
						"<img src='" + pathToImages + "bt-Retour.gif'>"+
						tntRCMsgBodyBack2Communes + 
					"</a>"+
				"</td>"+
				"<td />"+
			"</tr>");
	}
			
	tntRCjTable.append(tntRCligneBlanche6Col());
	jMessage.append(tntRCjTable);
	
    jMessage.append(tntRCchangerCodePostal());
};

function tntB2CRelaisColisGetBodyMain() {
	return (
		"<div class='tntRCGray'>&#160;</div>"+
		"<div id='tntBodyContentSC'>" +
			"<table>"+
				"<tr>"+
					"<td>" + tntRCMsgBodyInput + "</td>"+
				 	"<td><input type='text' id='tntRCInputCP' class='tntRCInput' maxlength='5' size='5' value=''/></td>"+
					"<td><a href='#' onclick='tntRCgetDepot();'><img class='tntRCButton' src='" + pathToImages + "bt-OK-2.jpg' onmouseover='this.src=\"" + pathToImages + "bt-OK-1.jpg\"' onmouseout='this.src=\"" + pathToImages + "bt-OK-2.jpg\"'></a></td>" + 
				"</tr>"+
			"</table>" +	 
		"</div>"+
		"<div id='tntRCLoading' style='display:none;'>" + tntRCMsgBodyLoading + "</div>"+
		"<div id='tntRCError' class='tntRCError' style='display:none;'></div>");			
}

function tntB2CRelaisColis() {

	// Test si ID de référence existe, sinon on ne fait rien
	if (!document.getElementById("tntB2CRelaisColis")) {
		alert("ERREUR: Appel incorrect, objet [tntB2CRelaisColis] manquant !");
		return;
	}
	
	tntRCCommune = '';

	var tntRelaisColisB2C = $("#tntB2CRelaisColis");
	tntRelaisColisB2C.html(
		"<div id='tntRCblocEntete'>"+
			"<div class='tntRCHeader'>"+ tntRCMsgHeaderTitle + "</div>"+
			"<div class='tntRCSubHeader'>" + tntRCMsgSubHeaderTitle + "</div>"+
		"</div>"+
		"<div id='blocCodePostal' class='tntRCBody'>"+
			tntB2CRelaisColisGetBodyMain() +
		"</div>" +
		"<div class='dialog_box' id='tntRCDialog'>"+
			"<div id='tntRCdetailRelaisEntete'>"+
				"<div class='tntRCHeader'>"+ tntRCMsgHeaderPopup + "</div>"+
				"<div class='tntRCSubHeader'>" + tntRCMsgSubHeaderPopup + "</div>"+
			"</div>"+
			"<div id='tntRCdetailRelaisCorps'></div>"+
		"</div>");

	// Forçage de la propriété "top", car elle est écrasée par la gestion de jqModal
	// si on la met dans la définition de la classe du div correspondant...
	$('#tntRCDialog').css("top", "50%");

	// Ajout de la popup dans la gestion jqModal
	
	$('#tntRCDialog').dialog({
		modal: true,
		autoOpen: false,
		width: 635,
		height: 500,
		position: ['middle','middle'],
		resizable: false,
		draggable: false,
		show: 'blind',
		close: function(event, ui) {
			$("html").css({overflow: "", 'overflow-x': "", 'overflow-y': ""}); 
		}
	});
	
	// Récupérations des paramètres de l'URL
	var codePostal = getURLParam("codePostal");
	var commune = getURLParam("commune");
	
	if (codePostal != "") {
		tntRCcodePostal = codePostal;
		if (commune != "") {
			// Couple code postal + commune fourni
			tntRCgetRelaisColisJSON(commune);
		}
		else {
			$('#tntRCInputCP').val(tntRCcodePostal);
			tntRCgetCommunesJSON();
		}	
	}

	// Initialisation de Map associée
	tntRCInitMap();
};	

function tntRCgetRelaisColis(libelleErreur) {

	// RAZ des infos sélectionnées
	tntRCSetSelectedInfo();

	tntRCCommune = '';
	
	var blocCodePostal = $("#blocCodePostal");
	if(!blocCodePostal.hasClass("tntRCBody"))
		blocCodePostal.addClass("tntRCBody");
	blocCodePostal.html(tntB2CRelaisColisGetBodyMain());
	$('#tntRCInputCP').val(tntRCcodePostal);
	
	if (libelleErreur) {
		var jDivErreur = $("#tntRCError"); 
		jDivErreur.html(libelleErreur);
		jDivErreur.show();
	}
};

function tntRCafficheCommunes(jData) {

	// RAZ des infos sélectionnées
	tntRCSetSelectedInfo();
	
	if (mapDetected) resetMap();

	var tntRCjTable = $("<table style='border:1px solid gray;' cellpadding='0' cellspacing='0' width='" + tntRCsize800  + "'></table>");
	
	// Ligne blanche de séparation
	tntRCjTable.append(tntRCligneBlanche6Col());
	// Entêtes de colonnes grise
	tntRCjTable.append(tntRCenteteGrise6Col());

	var blocCodePostal = $("#blocCodePostal");
	
	var i = 1;
	//var jCommunes = jData.find("VILLE");
	for (var iIdx = 0; iIdx < jData.length; iIdx++) {
		
		var commune = jData[iIdx];
		
		//var jCommune = $(this);
		var nomVille = commune[1]; // IE vs FF

		tntRCjTable.append(
			"<tr>"+
				"<td class='tntRCblanc' width='" + tntRCsize5 + "'></td>"+
				"<td class='tntRCblanc' width='" + tntRCsize50 + "'><img src='" + pathToImages + "logo-tnt-petit.jpg'></td>" +
				"<td class='tntRCrelaisColis' width='" + tntRCsize650 + "'> " + nomVille + " (" + tntRCcodePostal + ") </td>" +
				"<td class='tntRCrelaisColis' width='" + tntRCsize10 + "'>&nbsp;</td>"+
				"<td class='tntRCrelaisColis' align='center' width='" + tntRCsize109 + "'>"+
					"<input type='radio' name='tntRCchoixComm' value='" + nomVille + "' " + ( i ==1 ? "checked" : "") + ">"+
				"</td>"+
				"<td class='tntRCblanc' width='" + tntRCsize6 + "'></td>"+
			"</tr>");
		i = 2;
	}
	
	tntRCjTable.append(
		tntRCligneBlanche6Col() +
		"<tr>"+	
			"<td class='tntRCblanc' width='" + tntRCsize5 + "'></td>"+
			"<td class='tntRCblanc' colspan='2' width='" + tntRCsize670 + "'></td>"+
			"<td class='tntRCblanc' width='" + tntRCsize10 + "'></td>"+
			"<td class='tntRCblanc' align='center' width='" + tntRCsize109 + "'>"+
				"<a href='javascript:tntRCgetRelaisColisJSON();'><img class='tntRCButton' src='" + pathToImages + "bt-Continuer-2.jpg' onmouseover='this.src=\"" + pathToImages + "bt-Continuer-1.jpg\"' onmouseout='this.src=\"" + pathToImages + "bt-Continuer-2.jpg\"'></a>" +
			"</td>"+
			"<td class='tntRCblanc' width='" + tntRCsize6 + "'></td>"+
		"</tr>" +
		tntRCligneBlanche6Col());
	
	blocCodePostal.html(tntRCjTable);	
	
	// Bloc de saisie d'un nouveau code postal			
    blocCodePostal.append(tntRCchangerCodePostal());
}

function tntRCgetCommunesJSON() {
	
	$("#tntRCError").hide();
	tntRCcodePostal = $('#tntRCInputCP').val();

	// Code postal non renseigné, on ne fait rien 
	if (tntRCcodePostal=="") return;

	if (mapDetected) resetMap();
	
	// On ne fait rien si le code postal n'est pas un nombre de 5 chiffres
	if (isNaN(parseInt(tntRCcodePostal)) || tntRCcodePostal.length != 5) {
		tntRCgetRelaisColis(tntRCMsgErrCodePostal);
		return;
	}
	
	tntRCsetChargementEnCours();
	
	var ajaxUrl;
	var ajaxData;
	
	ajaxUrl = "http://" + tntDomain + "/public/b2c/relaisColis/rechercheJson.do?code=" + tntRCcodePostal;
	ajaxData = "";
	
	$.ajax({
	   type: "GET",
	   url: ajaxUrl,
	   data: ajaxData,
	   dataType: "script",
	   error:function(msg){
		  $("#blocCodePostal").html("Error !: " + msg );
	   }
	});
};

function tntRCsetChargementEnCours() {
	$("#tntRCLoading").show();
};

function tntRCafficheDetail(i) {
	
	var tntRCdetailRelais = $("#tntRcDetail"+i);
	
	tntRCdetailRelais.html("");
	
	var oRelais = tntRClisteRelais[i];

	// Les noeuds dans le fichier JSON ne sont pas forcément ordonnés pour l'affichage, on va donc d'abord récupérer leur valeur
	var codeRelais = oRelais[0]
	var nomRelais = oRelais[1];
	var adresse = oRelais[4];
	var codePostal = oRelais[2];
	var commune = oRelais[3];
	var heureFermeture = oRelais[21];

	var lundi_am = (oRelais[7] == "-")?"ferm&#233;":oRelais[7];
	var lundi_pm = (oRelais[8] == "-")?"ferm&#233;":oRelais[8];
	var mardi_am = (oRelais[9] == "-")?"ferm&#233;":oRelais[9];
	var mardi_pm = (oRelais[10] == "-")?"ferm&#233;":oRelais[10];
	var mercredi_am = (oRelais[11] == "-")?"ferm&#233;":oRelais[11];
	var mercredi_pm = (oRelais[12] == "-")?"ferm&#233;":oRelais[12];
	var jeudi_am = (oRelais[13] == "-")?"ferm&#233;":oRelais[13];
	var jeudi_pm = (oRelais[14] == "-")?"ferm&#233;":oRelais[14];
	var vendredi_am = (oRelais[15] == "-")?"ferm&#233;":oRelais[15];
	var vendredi_pm = (oRelais[16] == "-")?"ferm&#233;":oRelais[16];
	var samedi_am = (oRelais[17] == "-")?"ferm&#233;":oRelais[17];
	var samedi_pm = (oRelais[18] == "-")?"ferm&#233;":oRelais[18];
	var dimanche_am = (oRelais[19] == "-")?"ferm&#233;":oRelais[19];
	var dimanche_pm = (oRelais[20] == "-")?"ferm&#233;":oRelais[20];
	
	var messages = "";
	for (j=0; j < oRelais[24].length; j++) {
		var ligne = oRelais[24][j];
		if (ligne != "") messages = messages + ligne + "<br/>";
	}
	
	if (lundi_pm != "-") lundi_am = lundi_am + "<br/>" + lundi_pm;
	if (mardi_pm != "-") mardi_am = mardi_am + "<br/>" + mardi_pm;
	if (mercredi_pm != "-") mercredi_am = mercredi_am + "<br/>" + mercredi_pm;
	if (jeudi_pm != "-") jeudi_am = jeudi_am + "<br/>" + jeudi_pm;
	if (vendredi_pm != "-") vendredi_am = vendredi_am + "<br/>" + vendredi_pm;
	if (samedi_pm != "-") samedi_am = samedi_am + "<br/>" + samedi_pm;
	if (dimanche_pm != "-") dimanche_am = dimanche_am + "<br/>" + dimanche_pm;
	
	var logo_point = "";
	if (messages != "") logo_point = "<img src='" + pathToImages + "exception.gif' alt='Picto Informations'>";
	
	var tntRCjTableX = $(
			"<td class='detailRelais' colspan='6'><div class='ui-dialog-titlebar' style='text-align:right'>"
			+ "<span class='ui-dialog-title' onclick='document.getElementById(\"tntRcDetail"+i+"\").style.display=\"none\"'>X</span>"
			+ "</div>"
			+ "<input type='button' value='Choisir ce relais' onclick='callbackSelectionRelais();' />"
			+"<table  style='border:1px solid gray;margin:20px;' cellpadding='0' cellspacing='0' width='" + tntRCsize447 + "'>"
			+ "<tr>"
			+ 	"<td width='" + tntRCsize447  + "' valign='top'>"
			+ 		"<table style='border:0px;' cellpadding='0' cellspacing='0' width='" + tntRCsize447 + "'>"
			+			"<tr>"	
			+				"<td>"
			+					"<table style='border:0px;' cellpadding='0' cellspacing='0' >"
			+						"<tr height='" + tntRCsize8 + "'><td colspan='4'></td></tr>"
			+						"<tr>"
			+							"<td class='tntRCdetailGros' width='" + tntRCsize5 + "'>&nbsp;</td>"
			+							"<td class='tntRCdetailGros' width='" + tntRCsize442 + "' colspan='3'>Localisation : </td>"
			+						"</tr>"	
			+						"<tr height='" + tntRCsize20 + "'><td colspan='4'></td></tr>"	
			+						"<tr>"
			+ 							"<td class='tntRCdetailGros' width='"+ tntRCsize5 + "'>&nbsp;</td>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize30 + "'>&nbsp;</td>"
			+							"<td class='tntRCnoirPetit' colspan ='2'><b>" + nomRelais + "</b></td>"
			+						"</tr>"
			+						"<tr>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize5 + "'>&nbsp;</td>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize30 + "'>&nbsp;</td>"
			+							"<td class='tntRCnoirPetit'  colspan ='2'>" + adresse + "</td>"
			+						"</tr>"
			+						"<tr>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize5 + "'>&nbsp;</td>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize30 + "'>&nbsp;</td>"
			+							"<td class='tntRCnoirPetit'  colspan ='2'>" + codePostal + " " + commune + "</td>"
			+						"</tr>"
			+						"<tr height='" + tntRCsize50 + "'><td colspan='4'></td></tr>"	
			+						"<tr>"
			+							"<td class='tntRCdetailGros' width='" + tntRCsize5 + "'>&nbsp;</td>"
			+							"<td class='tntRCdetailGros' width='" + tntRCsize442 + "' colspan='3'>Informations : </td>"
			+						"</tr>"	
			+						"<tr height='" + tntRCsize8 + "'><td colspan='4'></td></tr>"
			+						"<tr>"
			+ 							"<td class='tntRCdetailGros' width='"+ tntRCsize5 + "'></td>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize30 + "'> " + logo_point + "</td>"
			+							"<td class='tntRCdetailPetit' colspan ='2'>" + messages + "</td>"
			+						"</tr>"	
			+					"</table>"
			+				"</td>"
			+			"</tr>"
			+		"</table>"
			+ 	"</td>"
			+ 	"<td width='" + tntRCsize253  + "' valign='top'>"
			+ 		"<table  style='border:0px;' cellpadding='0' cellspacing='0' width='" + tntRCsize253 + "'>"
			+			"<tr>"	
			+				"<td>"
			+					"<table style='border:0px;' cellpadding='0' cellspacing='0'>"
			+						"<tr height='" + tntRCsize8 + "'>"
			+							"<td colspan='4'></td>"
			+						"</tr>"
			+						"<tr>"	
			+							"<td class='tntRCdetailGros'><img src='" + pathToImages + "picto-delai.gif' alt='Picto delai'></td>"
			+							"<td class='tntRCdetailGros' colspan='3'>Horaires d'ouverture : </td>"
			+						"</tr>"	
			+						"<tr>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize30 + "'></td>"
			+							"<td>"
			+								"<table class='tntRCHoraire' cellpadding='0' cellspacing='0' rules='all' width='" + tntRCsize218 + "'>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Lundi</td>"
			+										"<td class='tntRCHoraireHeure'>" + lundi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Mardi</td>"
			+										"<td class='tntRCHoraireHeure'>" + mardi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Mercredi</td>"
			+										"<td class='tntRCHoraireHeure'>" + mercredi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Jeudi</td>"
			+										"<td class='tntRCHoraireHeure'>" + jeudi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Vendredi</td>"
			+										"<td class='tntRCHoraireHeure'>" + vendredi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Samedi</td>"
			+										"<td class='tntRCHoraireHeure'>" + samedi_am + "</td>"
			+									"</tr>"
			+									"<tr>"
			+										"<td class='tntRCHoraireJour'>Dimanche</td>"
			+										"<td class='tntRCHoraireHeure'>" + dimanche_am + "</td>"
			+									"</tr>"			
			+								"</table>"	
			+							"</td>"
			+							"<td class='tntRCdetailGros' width='"+ tntRCsize5 + "'></td>"
			+						"</tr>"
			+					"</table>"
			+				"</td>"
			+			"</tr>"
			+		"</table>"
			+	"</td>"
			+ "</tr>"
			+ "<tr height='" + tntRCsize8 + "'></tr>"
			+ "</table></td>");
			
	tntRCdetailRelais.append(tntRCjTableX);
	tntRCdetailRelais.show();
	//$('#tntRCDialog').dialog("open");
	//$('#tntRCDialog').css("width", "600px"); // Patch mauvais calcul jQueryUI
	// Masquage des barres de scrolling
	//$("html").css({overflow: "hidden", 'overflow-x': "hidden", 'overflow-y': "hidden"}); 
};
	
function tntRCligneBlancheDetail(){
	return("<tr height='" + tntRCsize5 + "'><td colspan='8'>&nbsp;</td></tr>");
};	
	
function tntRCligneBlancheGauche(){
	return(
		"<tr height='" + tntRCsize8 + "'>"+
			"<td class='tntRCdetailGros' width='" + tntRCsize5  + "'></td>"+
			"<td class='tntRCdetailGros' width='" + tntRCsize30  + "'></td>"+
			"<td class='tntRCdetailGros' width='" + tntRCsize20  + "'></td>"+
			"<td class='tntRCdetailGros' width='" + tntRCsize392  + "'></td>"+
		"</tr>");
};

// Table vide avec 3 colonnes pour sauter une ligne
function tntRCligneBlanche3Col() {
	return("<tr height='" + tntRCsize8 + "'><td class='tntRCblanc' width='" + tntRCsize5 + "'></td><td class='tntRCblanc' width='" + tntRCsize789 + "'></td><td class='tntRCblanc' width='" + tntRCsize6 + "'></td></tr>");
};

// Table vide avec 6 colonnes pour sauter une ligne
function tntRCligneBlanche6Col() {
	return("<tr height='" + tntRCsize8 + "'><td class='tntRCblanc' colspan='6'></td></td></tr>");
};

// Table vide avec 3 colonnes et entête en gris
function tntRCligneGrise3Col() {	
	return("<tr><td class='tntRCblanc' width='" + tntRCsize5 + "'></td><td class='tntRCgris' width='" + tntRCsize789 + "'><br/></td><td class='tntRCblanc' width='" + tntRCsize6 + "'></td></tr>");
};

// Table entête de colonnes grises 
function tntRCenteteGrise6Col() {
	return("<tr><td class='tntRCblanc' width='" + tntRCsize5 + "'></td><td class='tntRCgris' colspan='2' width='" + tntRCsize670 + "'>&nbsp;Les différents Relais Colis&#174;</td><td class='tntRCblanc' width='" + tntRCsize10 + "'></td><td class='tntRCgris' width='" + tntRCsize109 + "'>&nbsp;Mon choix</td><td class='tntRCblanc' width='" + tntRCsize6 + "'></td></tr>");
};

// Zone de saisie d'un code postal nouveau
function tntRCchangerCodePostal(){
	return(
		"<div class='tntRCWhite'>&#160;</div>"+
		"<div class='tntRCBodySearch'>"+ 
			"<table>"+
				"<tr>"+
					"<td width='300px'>Vous pouvez choisir un autre code postal de livraison :</td>"+
				 	"<td width='55px'><input type='text' id='tntRCInputCP' class='tntRCInput' maxlength='5' size='5' value='' /></td>"+
					"<td><a href='#' onclick='tntRCgetDepot();'><img class='tntRCButton' src='" + pathToImages + "bt-OK-2.jpg' onmouseover='this.src=\"" + pathToImages + "bt-OK-1.jpg\"' onmouseout='this.src=\"" + pathToImages + "bt-OK-2.jpg\"'></a></td>" + 
				"</tr>"+
			"</table>"+
		"</div>");
};

function tntRCSetSelectedInfo(selectedIdx, noMarkerInfo) {
	
	if (!selectedIdx && selectedIdx != 0) {
		// RAZ des infos sélectionnées
		$("#tntRCSelectedCode").val("");
		$("#tntRCSelectedNom").val("");
		$("#tntRCSelectedAdresse").val("");
		$("#tntRCSelectedCodePostal").val("");
		$("#tntRCSelectedCommune").val("");
		return
	}
	
	var oRelais = tntRClisteRelais[selectedIdx];

	$("#tntRCSelectedCode").val(oRelais[0]);
	$("#tntRCSelectedNom").val(oRelais[1]);
	$("#tntRCSelectedAdresse").val(oRelais[4]);
	$("#tntRCSelectedCodePostal").val(oRelais[2]);
	$("#tntRCSelectedCommune").val(oRelais[3]);

	if (mapDetected && !noMarkerInfo) {
		
		// Les noeuds dans le fichier XML ne sont pas forcément ordonnés pour l'affichage, on va donc d'abord récupérer leur valeur
		var codeRelais = oRelais[0]
		var nomRelais = oRelais[1];
		var adresse = oRelais[4];
		var codePostal = oRelais[2];
		var commune = oRelais[3];
		var heureFermeture = oRelais[21];

		var messages = "";
		var lundi_am = (oRelais[7] == "-")?",":oRelais[7]+",";
		var lundi_pm = oRelais[8];
		var mardi_am = (oRelais[9] == "-")?",":oRelais[9]+",";
		var mardi_pm = oRelais[10];
		var mercredi_am = (oRelais[11] == "-")?",":oRelais[11]+",";
		var mercredi_pm = oRelais[12];
		var jeudi_am = (oRelais[13] == "-")?",":oRelais[13]+",";
		var jeudi_pm = oRelais[14];
		var vendredi_am = (oRelais[15] == "-")?",":oRelais[15]+",";
		var vendredi_pm = oRelais[16];
		var samedi_am = (oRelais[17] == "-")?",":oRelais[17]+",";
		var samedi_pm = oRelais[18];
		var dimanche_am = (oRelais[19] == "-")?",":oRelais[19]+",";
		var dimanche_pm = oRelais[20];
		
		if (lundi_pm != "-") lundi_am = lundi_am + lundi_pm;
		if (mardi_pm != "-") mardi_am = mardi_am + mardi_pm;
		if (mercredi_pm != "-") mercredi_am = mercredi_am + mercredi_pm;
		if (jeudi_pm != "-") jeudi_am = jeudi_am + jeudi_pm;
		if (vendredi_pm != "-") vendredi_am = vendredi_am + vendredi_pm;
		if (samedi_pm != "-") samedi_am = samedi_am + samedi_pm;
		if (dimanche_pm != "-") dimanche_am = dimanche_am + dimanche_pm;
		
		var horaires = new Array();
		horaires['lundi'] = lundi_am + ",1";
		horaires['mardi'] = mardi_am + ",2";
		horaires['mercredi'] = mercredi_am + ",3";
		horaires['jeudi'] = jeudi_am + ",4";
		horaires['vendredi'] = vendredi_am + ",5";
		horaires['samedi'] = samedi_am + ",6";
		horaires['dimanche'] = dimanche_am + ",0";
		
		var messages = "";
		for (j=0; j < oRelais[24].length; j++) {
			var ligne = oRelais[24][j];
			if (ligne != "") messages = messages + ligne + "<br/>";
		}

		setInfoMarker(codeRelais, nomRelais, adresse, codePostal, commune, messages, selectedIdx, horaires, relaisMarkers[selectedIdx]);
	}
}

function resetMap() {
	
	if (map) {
		
		map.getStreetView().setVisible(false);
		
		for (var i = 0; i < relaisMarkers.length; i++) { 
			relaisMarkers[i].setMap(null);
			relaisMarkers[i] = null;
		}
		relaisMarkers = new Array();
		if (infowindow) infowindow.close();
		map.setZoom(defaultZoom);
		map.setCenter(defaultCenter);
	}	
}

/*
 * Fonction appellée en retour de la recherche des communes par rapport à un code postal
 * si plusieurs communes ont été trouvées
 */

function listeCommunes(tabCommunes) {
	tntRCJsonCommunes = null;
	
	// TEMP: car le contenu du div est entièrement reconstruit
	$("#blocCodePostal").removeClass("tntRCBody");

	tntRCJsonCommunes = tabCommunes;
	tntRCafficheCommunes(tabCommunes);
}

/*
 * Fonction appellée en retour de la recherche des communes par rapport à un code postal
 * si une seule commune a été trouvée
 */

function listeRelais(tabRelais) {
	
	tntRClisteRelais = null;

	// TEMP: car le contenu du div est entièrement reconstruit
	$("#blocCodePostal").removeClass("tntRCBody");

	tntRCafficheRelais(tabRelais);
	if (mapDetected) init_marker(tabRelais);
}

/*
 * Fonction appellée en retour de la recherche des communes si aucune commune trouvée
 */
function erreurListeCommunes() {
	tntRCJsonCommunes = null;
	tntRCgetRelaisColis(tntRCMsgErrLoadCommunes);
}

function erreurListeRelais() {
	tntRCgetRelaisColis(tntRCMsgErrLoadRelais);
}


/************************************************************************************************
 * 							Partie Google Map
 ***********************************************************************************************/

var map;
var adresse_pointclic;
var zone_chalandise;
var zoomZoneChalandiseDefault;
var centerZoneChalandiseDefault;
var init_streetview = false;

var contentTo = [
                 '<br/><div>',
                     'Itin&#233;raire : <b>Vers ce lieu</b> - <a href="javascript:fromhere(0)">A partir de ce lieu</a><br/>',
                     'Lieu de d&#233;part<br/>',
                     '<input type="text" id="saisie" name="saisie" value="" maxlength="500" size="30">',
                     '<input type="hidden" id="mode" name="mode" value="toPoint">',
                     '<input type="hidden" id="point_choisi" name="point_choisi" value="">',
                     '<input type="submit" onclick="popup_roadmap();" value="Ok">',
                     '<br/>Ex: 58 avenue Leclerc 69007 Lyon',
                 '</div>'].join('');
     
var contentFrom = [
                  '<br/><div>',
                      'Itin&#233;raire : <a href="javascript:tohere(0)">Vers ce lieu</a> - <b>A partir de ce lieu</b><br/>',
                      'Lieu d\'arriv&#233;e<br/>',
                      '<input type="text" id="saisie" name="saisie" value="" maxlength="500" size="30">',
                      '<input type="hidden" id="mode" name="mode" value="fromPoint">',
                      '<input type="hidden" id="point_choisi" name="point_choisi" value="">',
                      '<input type="button" onclick="popup_roadmap();" value="Ok">',
                      '<br/>Ex: 58 avenue Leclerc 69007 Lyon',
                  '</div>'].join('');

var infowindow;

var relaisMarkers = [];
var iconRelais = new google.maps.MarkerImage(
		"img/google/relaisColis.png", 
		new google.maps.Size(40, 30), 
		new google.maps.Point(0, 0), 
		new google.maps.Point(20, 30))

//Limites de la France
var allowedBounds = new google.maps.LatLngBounds(
		new google.maps.LatLng(39.56533418570851, -7.41426946590909), 
		new google.maps.LatLng(52.88994181429149, 11.84176746590909));

var defaultCenter = new google.maps.LatLng(46.2276380, 2.2137490); // the center ???
var defaultZoom = 5; 						// default zoom level
var aberration = 0.2; 						// this value is a good choice for france (?!)
var minMapScale = 5;
//var maxMapScale = 20;

var mapDetected = false;
var callbackLinkMarker = "";

// fonction appellé après saisie du code postal de recherche
function init_marker(json) {
	
	zone_chalandise = new google.maps.LatLngBounds();
	
	for (var i = 0; i < relaisMarkers.length; i++) { 
		relaisMarkers[i].setMap(null);
		relaisMarkers[i] = null;
	}
	relaisMarkers = new Array();
	
	if (infowindow) infowindow.close();
	
	var markers = json;
	
	for (var i = 0; i < markers.length; i++) {
		createMarker(markers[i], i);
	}
	
	zoomZoneChalandiseDefault = zone_chalandise.getCenter();
	centerZoneChalandiseDefault = zone_chalandise;
	
	retourZoomChalandise();
}

function setInfoMarker(codeRelais, nomRelais, adresse, codePostal, commune, messages, indice, horaires, marker) {
	
	var htmlInfo = [
		"<div>",
			"<div class='rc'>",
				"<b>RELAIS COLIS N° ", codeRelais, "</b><br/>",
				"<b>", nomRelais, "</b><br/>", 
				adresse, "<br/>", 
				codePostal, " ", commune,
			"</div>",
			"<div><br/>", messages, "</div>",
			callbackLinkMarker,
		"</div>",
		"<div id='trajet'>" + contentTo + "</div>"
	].join('');

	// Création du contenu de l'onglet horaire
	var htmlHoraires = "<table class='horairesRCPopup'>";
	var jourSemaine = (new Date()).getDay();
	for (jour in horaires) {
		var heures = (horaires[jour]).split(",");
		if (heures[0] == '' && heures[1] == '') heures[0] = "ferm&#233;";
		htmlHoraires = htmlHoraires  + "<tr" + (jourSemaine == parseInt(heures[2]) ? " class='selected'" : "") + "><td class='horairesRCJourPopup'>&nbsp;" + jour + "</td><td class='horaireRCPopup'>" + heures[0] + " " + heures[1] + "</td></tr>";
	}
	htmlHoraires = htmlHoraires + "</table>";
	
	adresse_pointclic = [adresse, "|", codePostal, " ", commune].join('');
	
	var contentString = [
         '<div id="tabs" style="width:340px;">',
         '<ul>',
           '<li><a href="#tabInfos"><span>Infos</span></a></li>',
           '<li><a href="#tabHoraires"><span>Horaires</span></a></li>',
         '</ul>',
         '<div id="tabInfos">',
           htmlInfo,
         '</div>',
         '<div id="tabHoraires">',
           htmlHoraires,
         '</div>',
         '</div>'
       ].join('');

    if (infowindow) infowindow.close();
    infowindow = new google.maps.InfoWindow({content: contentString});

	google.maps.event.addListener(infowindow, "domready", function() {  
		$("#point_choisi").attr("value", adresse_pointclic);
		$("#tabs").tabs();
		$("#tabs").parent().removeAttr("style");
	});

	infowindow.open(map, marker);
}

function createMarker(markerData, indice) {
	
	var marker = new google.maps.Marker({
		icon: iconRelais,
		position: new google.maps.LatLng(markerData[5], markerData[6]),
		map: map,
		title:markerData[1]
	});
	
	google.maps.event.addListener(marker, "click", function() {
		// Sélectionne le relais correspondant dans la liste
		$("input[@type=radio][@name=tntRCchoixRelais]:eq("+ indice + ")").attr("checked", true);
		tntRCSetSelectedInfo(indice);
	});

	relaisMarkers.push(marker);
	zone_chalandise.extend(marker.getPosition());
}


function tntRCInitMap() {
	
	// Si la carte n'est pas présente, fin de l'initialisation
	if (!document.getElementById("map_canvas")) return;
	mapDetected = true;
	
	// Si une fonction de callback a été définie, un lien est ajouté
 	// dans la popup d'info du marqueur de relais colis
	if (window.callbackSelectionRelais) callbackLinkMarker = "<a onclick='callbackSelectionRelais();' href='#' style='color:#FF6600'>Choisir ce relais</a>";

	//Ajout du lien pour retour en zoom zone de chalandise
	var jMapCanvas = $("#map_canvas");
	jMapCanvas.wrap("<div></div>");
	jMapCanvas.parent().append("<a class=\"lien_reset\" href=\"#\" onclick=\"javascript:retourZoomChalandise();\" style=\"text-decoration:none;\">Retour &agrave; la vue initiale</a>");
	
	var mapClass = jMapCanvas.attr("class"); 
	if (mapClass && mapClass != "") {
		jMapCanvas.attr("class", "");
		jMapCanvas.parent().attr("class", mapClass);
	}
	
	var myOptions = {
		zoom: defaultZoom,
		center: defaultCenter,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		navigationControl: true,
		scaleControl: true,
		mapTypeControl: true,
		streetViewControl: true
	};
	
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
    // If the map position is out of range, move it back
    function checkBounds() {

		// Perform the check and return if OK
		var currentBounds = map.getBounds();
		var cSpan = currentBounds.toSpan(); // width and height of the bounds
		var offsetX = cSpan.lng() / (2+aberration); // we need a little border
		var offsetY = cSpan.lat() / (2+aberration);
		var C = map.getCenter(); // current center coords
		var X = C.lng();
		var Y = C.lat();
	
		// now check if the current rectangle in the allowed area
		var checkSW = new google.maps.LatLng(C.lat()-offsetY,C.lng()-offsetX);
		var checkNE = new google.maps.LatLng(C.lat()+offsetY,C.lng()+offsetX);
	
		if (allowedBounds.contains(checkSW) &&
			allowedBounds.contains(checkNE)) {
			return; // nothing to do
		}
	
		var AmaxX = allowedBounds.getNorthEast().lng();
		var AmaxY = allowedBounds.getNorthEast().lat();
		var AminX = allowedBounds.getSouthWest().lng();
		var AminY = allowedBounds.getSouthWest().lat();
	
		if (X < (AminX+offsetX)) {X = AminX + offsetX;}
		if (X > (AmaxX-offsetX)) {X = AmaxX - offsetX;}
		if (Y < (AminY+offsetY)) {Y = AminY + offsetY;}
		if (Y > (AmaxY-offsetY)) {Y = AmaxY - offsetY;}
	
		map.setCenter(new google.maps.LatLng(Y,X));
		return;
    }
    
	google.maps.event.addListener(map, "drag", function() {
		checkBounds();
	});
	
	google.maps.event.addListener(map, "zoom_changed", function() {
		if (map.getZoom() < minMapScale) {
			map.setZoom(minMapScale);
		}
	});
	
	google.maps.event.addListener(map.getStreetView(), "visible_changed", function() {
		//premier accès lors du chargement de la page, il ne faut pas cacher les markers
		if (init_streetview == true) {
			if(map.getStreetView().getVisible() == true) {
				for (var k = 0; k < relaisMarkers.length; k++) { 
					relaisMarkers[k].setVisible(false);
				}
			}
			else {
				for (var k = 0; k < relaisMarkers.length; k++) { 
					relaisMarkers[k].setVisible(true);
				}
			}
		}
		else init_streetview = true;
	});
}

function retourZoomChalandise() {
	if(zoomZoneChalandiseDefault){
		map.setCenter(zoomZoneChalandiseDefault);
		map.fitBounds(centerZoneChalandiseDefault);
	}
}

function fromhere() {
	switchFromTo(contentFrom);
}

function tohere() {
	switchFromTo(contentTo);
}

function switchFromTo(htmlContent) {
	var adresse_saisie = $("#saisie").val();
	$("#trajet").html(htmlContent);
	$("#point_choisi").attr('value', adresse_pointclic);
	$("#saisie").val(adresse_saisie);
}

function popup_roadmap() {
	if($("#saisie").val() == "") return;
	window.open("http://" + tntDomain + "/public/geolocalisation/print_roadmap.do?mode="+ $("#mode").val() +"&point_choisi="+ $("#point_choisi").val() +"&saisie="+ $("#saisie").val());
}

$().ready(tntB2CRelaisColis);