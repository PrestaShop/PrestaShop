<?php
require('../../../config/config.inc.php');
$relais = Db::getInstance()->getValue('SELECT c.id_carrier 
													FROM `'._DB_PREFIX_.'carrier` as c, `'._DB_PREFIX_.'tnt_carrier_option` as o 
													WHERE c.id_carrier = o.id_carrier 
													AND o.option LIKE "%D" 
													AND c.external_module_name = "tntcarrier"
													AND c.deleted = "0" AND c.id_carrier = "'.(int)($_GET['id_carrier']).'"');
 if ($relais)
 {
?>
		<script type="text/javascript">
        $('input[name=processCarrier]').click(function()
                                              {
                                              if ($("#tntRCSelectedCode").val() == '')
                                              {
                                              alert("Vous n'avez pas choisi de relais colis");
                                              return false;
                                              }
                                              });
		/*$("#form").submit(function()
		{
                          if ($("#tntRCSelectedCode").val() == '')
                          {
                          //alert("Vous n'avez pas choisi de relais colis");
                          return false;
                          }
		}
		);*/
		</script>
		<script type="text/javascript" src="./modules/tntcarrier/relaisColis/js/jquery.js"></script>
		<script type="text/javascript" src="./modules/tntcarrier/relaisColis/js/jquery-ui.js"></script>
		<script type="text/javascript" src="./modules/tntcarrier/relaisColis/js/relaisColis.js"></script>
		<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/ui.tabs.css" type="text/css" />
		<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/ui.dialog.css" type="text/css" />
		<link rel="stylesheet" href="./modules/tntcarrier/relaisColis/css/tntB2CRelaisColis.css" type="text/css" />
		<div id="tntB2CRelaisColis" class="exemplePresentation">
		<script type="text/javascript">
		tntB2CRelaisColis();
		</script>
		</div>
		<div style="text-align: justify; font-family: arial,helvetica,sans-serif; font-size: 10pt; width: 600px;">
			<div style="height: 25px;">&nbsp;</div>
			<div id="exempleIntegration">
				<script type="text/javascript">
				  	function callbackSelectionRelais() {
				  		
				  		// Récupération des informations
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
<?php
}
?>