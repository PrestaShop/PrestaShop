function tntRCgetDepot()
{
	$("#tntRCError").hide();
	tntRCcodePostal = $("#tntRCInputCP").val();
	if (tntRCcodePostal=="") return;
	if (isNaN(parseInt(tntRCcodePostal))) {
		tntRCgetRelaisColis(tntRCMsgErrCodePostal);
		return;
	}
	tntRCsetChargementEnCours();
	$("#tntRCLoading").load(
		"../modules/tntcarrier/tntGetDepot.php?code="+tntRCcodePostal,
		function(response, status, xhr) 
			{
				if (status == "error") 
					$("#tntRCLoading").html(xhr.status + " " + xhr.statusText);
			}
	);
}
					
function	depositButtonClick()
{
	$("#googleMapTnt").css("display", "");
}
					
function	collectButtonClick()
{
	$("#googleMapTnt").css("display", "none");
}
					
$(document).ready(function() {
	var transport1 = $("#tnt_carrier_collect_yes");
	var transport2 = $("#tnt_carrier_collect_no");
	transport1.click(function() {
		$("#divPex").css("display", "none");
		$("#divClosing").css("display", "");
	});
	transport2.click(function() {
		$("#divPex").css("display", "");
		$("#divClosing").css("display", "none");
	});
});

function callbackSelectionRelais() 
{
	var code = document.getElementById("tntRCSelectedCode").value;
	var lastname = document.getElementById("tntRCSelectedNom").value;
	var address = document.getElementById("tntRCSelectedAdresse").value;
	var address2 = document.getElementById("tntRCSelectedAdresse2").value;
	var zipcode = document.getElementById("tntRCSelectedCodePostal").value;
	var city = document.getElementById("tntRCSelectedCommune").value;
	
	if (!code || code == "")
		alert("Aucun depot selectionne");
	else 
	{
		document.getElementById("tnt_carrier_shipping_pex").value = code;
		document.getElementById("tnt_carrier_shipping_company").value = lastname;
		var s = lastname.length - lastname.indexOf(" ");

		document.getElementById("tnt_carrier_shipping_last_name").value = "";
		document.getElementById("tnt_carrier_shipping_first_name").value = "";
		document.getElementById("tnt_carrier_shipping_address1").value = address;
		document.getElementById("tnt_carrier_shipping_address2").value = address2;
		document.getElementById("tnt_carrier_shipping_postal_code").value = zipcode;
		document.getElementById("tnt_carrier_shipping_city").value = city;
	}
}

function changeValueTntRC(code, name, address1, address2, zipcode, city)
{
	document.getElementById("tntRCSelectedCode").value = code;
	document.getElementById("tntRCSelectedNom").value = name;
	document.getElementById("tntRCSelectedAdresse").value = address1;
	document.getElementById("tntRCSelectedAdresse2").value = address2;
	document.getElementById("tntRCSelectedCodePostal").value = zipcode;
	document.getElementById("tntRCSelectedCommune").value = city;
}