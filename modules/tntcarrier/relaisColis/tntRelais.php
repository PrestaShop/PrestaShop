<script type="text/javascript" src="./modules/tntcarrier/relaisColis/js/jquery-ui.js"></script>
<script type="text/javascript" src="./modules/tntcarrier/relaisColis/js/relaisColis.js"></script>
<script type="text/javascript">
	function tntRCgetCommunes() {
	
	$("#tntRCError").hide();
	tntRCcodePostal = $('#tntRCInputCP').val();
	if (tntRCcodePostal=="") return;
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
</script>
<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/ui.tabs.css" type="text/css" />
<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/ui.dialog.css" type="text/css" />
<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/tntB2CRelaisColis.css" type="text/css" />
<div id="tntB2CRelaisColis" class="exemplePresentation">
	<div id="tntRCblocEntete">
		<!--<div class="tntRCHeader">Mode de livraison</div>-->
		<div class="tntRCSubHeader">
			Choisissez le Relais Colis
			<sup class="tntRCSup">&reg;</sup>
			qui vous convient :
		</div>
		<input id="tntRCSelectedCode" type="hidden" value="C2219">
		<input id="tntRCSelectedNom" type="hidden" value="MALCO COIFFURE">
		<input id="tntRCSelectedAdresse" type="hidden" value="12 RUE LAME">
		<input id="tntRCSelectedCodePostal" type="hidden" value="78100">
		<input id="tntRCSelectedCommune" type="hidden" value="ST GERMAIN EN LAYE">
	</div>
	<div class="tntRCBody" id="blocCodePostal">
		<div class="tntRCGray">&nbsp;</div>
		<div id="tntBodyContentSC">
			<table>
				<tbody>
					<tr>
						<td>Entrez le code postal :&nbsp;</td>
						<td><input type="text" value="" size="5" maxlength="5" class="tntRCInput" id="tntRCInputCP"></td>
						<td><a onclick="tntRCgetCommunes();" href="#">
							<img onmouseout="./modules/tntcarrier/relaisColis/img/bt-OK-2.jpg&quot;" onmouseover="./modules/tntcarrier/relaisColis/img/bt-OK-1.jpg&quot;" src="./modules/tntcarrier/relaisColis/img/bt-OK-2.jpg" class="tntRCButton"></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="display:none;" id="tntRCLoading">Chargement en cours...</div>
		<div style="display:none;" class="tntRCError" id="tntRCError"></div>
	</div>
</div>
<div style="text-align: justify; font-family: arial,helvetica,sans-serif; font-size: 10pt; width: 600px;">
	<div style="height: 25px;">&nbsp;</div>
	<div id="exempleIntegration">
		<script type="text/javascript">
			function callbackSelectionRelais() {
				  		var codeRelais = $("#tntRCSelectedCode").val();
			  			var nom = $("#tntRCSelectedNom").val();
			  			var adresse = $("#tntRCSelectedAdresse").val();
			  			var codePostal = $("#tntRCSelectedCodePostal").val();
			  			var commune = $("#tntRCSelectedCommune").val();
				  			
				  		if (!codeRelais || codeRelais == "") {
				  			alert("Aucun relais n'a été sélectionné !");
				  		}
				  		else {
				  			alert("Info relais sélectionné"+
				  				  "\nCode\t\t: " + codeRelais + 
				  				  "\nNom\t\t: " + nom +
				  				  "\nAdresse\t\t: " + adresse +
				  				  "\nCode postal\t: " + codePostal +
				  				  "\nCommune\t\t: " + commune);
				  		}
				  	}
		</script>
	</div>
</div>