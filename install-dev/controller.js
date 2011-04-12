/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//constant
verifMailREGEX = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
verifNameREGEX = /^[^0-9!<>,;?=+()@#"°{}_$%:]*$/;

//params
configIsOk = false;
createdBase = false;
mailIsOk = false;
smtpChecked = false;
validShopInfos = false;
upgradeCertify = false;
dropdb=false;
application="install";

function nextTab()
{
	if(verifyThisStep())
	{
		showStep(step+1, 'next');
	}
}
function backTab()
{
	if (step != 6) {
		showStep(step - 1, 'back');
	}
	else {
		constructInstallerTabs();
		showStep(1, 'back');
	} 
}

function showStep(aStep, way)
{
	step = aStep;
	
	//show the sheet
	$('div.sheet.shown').fadeOut('fast', function() {
		$($('div.sheet')[(step-1)]).fadeIn('slow').addClass('shown');
	}).removeClass('shown');

	//upgrade the tab
	$('#tabs li')
		.removeClass("selected")
		.removeClass("finished");
		if (step < 6) {
			$('#tabs li:nth-child(' + step + ')').addClass("selected");
			$('#tabs li:lt(' + (step - 1) + ')').addClass("finished");
		}
		else
		{
			switch (step)
			{
				
				case 6 :
				$('#tabs li:nth-child(1)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(2)').addClass("selected").removeClass("finished");
				$('#tabs li:nth-child(3)').removeClass("selected").removeClass("finished");
				$('#tabs li:nth-child(4)').removeClass("selected").removeClass("finished");
				break;
				
				case 7 :
				$('#tabs li:nth-child(1)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(2)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(3)').addClass("selected").removeClass("finished");
				$('#tabs li:nth-child(4)').removeClass("selected").removeClass("finished");
				break;
				
				case 8 :
				$('#tabs li:nth-child(1)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(2)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(3)').addClass("selected").removeClass("finished");
				$('#tabs li:nth-child(4)').removeClass("selected").removeClass("finished");
				break;
				
				case 9 :
				$('#tabs li:nth-child(1)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(2)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(3)').removeClass("selected").addClass("finished");
				$('#tabs li:nth-child(4)').addClass("selected").removeClass("finished");
				break;
				
			}
		}
	
	//title of the window and buttons
	switch(step)
	{
		case 1 :
		document.title = Step1Title;
		$("#btBack")
			.attr("disabled", "disabled")
			.addClass("disabled")
			.show('slow');
		$("#btNext")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 2:
		document.title = step2title;
		application = "install";
		if (way == 'next')
			verifyAndSetRequire(1);
		else
			verifyAndSetRequire(0);
		$("#btBack")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 3:
		document.title = step3title;
		$("#btBack")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 4:
		document.title = step4title;
		$("#btBack")
			.attr("disabled", "disabled")
			.addClass("disabled")
			.hide('slow');
		break;
		
		case 5 :
		document.title = step5title;
		$("#btBack")
			.attr("disabled", "disabled")
			.addClass("disabled")
			.hide('slow');
		$("#btNext").hide('slow');
		break;
		
		case 6 :
		document.title = step6title;
		application = "update";
		if (!upgradeCertify) {
			$("#btNext")
				.attr("disabled", "disabled")
				.addClass("disabled");
		} else {
			$("#btNext")
				.removeAttr("disabled")
				.removeClass("disabled");
		}
		$("#btBack")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 7:
		document.title = step7title;
		verifyAndSetRequire(0);
		$("#btBack")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 8 :
		document.title = step8title;
		$("#btNext")
			.attr("disabled", "disabled")
			.addClass("disabled");
		$("#btBack")
			.removeAttr("disabled")
			.removeClass("disabled")
			.show('slow');
		break;
		
		case 9 :
		document.title = step9title;
		$("#btBack").hide();
		$("#btNext").hide();
		break;
	}
}

function verifyThisStep()
{
	switch (step)
	{
		case 1 :
		if($("#formSetMethod input[type=radio]:checked").val() == "install" ){
			showStep(2, 'next');
		}
		else
		{
			constructUpdaterTabs();
			showStep(6, 'next');
		}
		return false;
		break;
		
		case 2 :
		return configIsOk;
		break;
		
		case 3 :
			createDB();
			return false;
		break;
		
		case 4 :
			verifyShopInfos();
			return validShopInfos;
		break;
		
		case 6 :
			return true;
		break;
		
		case 7 :
			doUpgrade();
		break;
		
	}
	
}

function setInstallerLanguage ()
{
	$("#formSetInstallerLanguage").submit();
}

function verifyAndSetRequire(firsttime)
{
	$("div#"+(application == "install" ? "sheet_require" : "sheet_require_update")+" > ul").slideUp("1500");
	$.ajax(
	{
		url: "model.php",
		data: "method=checkConfig&firsttime="+firsttime,
		success: function(ret)
		{
			isUpdate = application == "install" ? "" : "_update";
			testLists = ret.getElementsByTagName('testList');
			
			configIsOk = true;
		
			testListRequired = testLists[0].getElementsByTagName('test');
			for (i = 0; i < testListRequired.length; i++){
				result = testListRequired[i].getAttribute("result");
				$($("div#sheet_require"+isUpdate+" > ul#required"+isUpdate+" .required")[i])
				.removeClass( (result == "fail") ? "ok" : "fail" )
				.addClass(result);
				if (result == "fail") configIsOk = false;
			}
			
			
			testListOptional = testLists[1].getElementsByTagName('test');
			
			for (i = 0; i < testListOptional.length; i++){
				result = testListOptional[i].getAttribute("result");
				$($("div#sheet_require"+isUpdate+" > ul#optional"+isUpdate+" li.optional")[i])
					.removeClass( (result == "fail") ? "ok" : "fail" )
					.addClass(result);
			}
			
			if (!configIsOk) {
				$('#btNext').attr({'disabled':'disabled','class':'button little disabled'});
				$('h3#resultConfig'+isUpdate).html(txtConfigIsNotOk).slideDown('slow');
				$('h3#resultConfigHelper').show();
				$("div#sheet_require"+isUpdate+" > ul").slideDown("1500");
			} else {
				$("#btNext").removeAttr('disabled');
				$('#btNext').removeClass('disabled');
				$("input#btNext").focus();
				var firsttime = ret.getElementsByTagName('firsttime');
				if (firsttime && firsttime[0].getAttribute("value") == 1)
					$("input#btNext").click();
				else
				{
					$('h3#resultConfig'+isUpdate).html(txtConfigIsOk).slideDown('slow');
					$('h3#resultConfigHelper').hide();
					$("div#sheet_require"+isUpdate+" > ul").slideDown("1500");
				}
			}
		}
	}
	);
}

function verifyDbAccess ()
{
	//local verifications
	if($("#dbServer[value=]").length > 0)
	{
		$("#dbResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtDbServerEmpty).show('slow');
		return false;
	}
	else
	{
		$("#dbResultCheck").removeClass("fail").removeClass("ok").removeClass('userInfos').html('');
	}
	
	if($("#dbLogin[value=]").length > 0)
	{
		$("#dbResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtDbLoginEmpty).show('slow');
		return false;
	}
	else
	{
		$("#dbResultCheck").removeClass("fail").removeClass("ok").removeClass('userInfos').html('');
	}
	
	if($("#dbName[value=]").length > 0)
	{
		$("#dbResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtDbNameEmpty).show('slow');
		return false;
	}
	else
	{
		$("#dbResultCheck").removeClass("fail").removeClass("ok").removeClass('userInfos').html('');
	}
	
	//external verifications and sets
	$.ajax(
	{
		cache: false,
		url: "model.php",
		data: 
			"method=checkDB"
			+"&type=MySQL"
			+"&server="+ $("#dbServer").val()
			+"&login="+ $("#dbLogin").val()
			+"&password="+encodeURIComponent($("#dbPassword").val())
			+"&engine="+$("#dbEngine option:selected").val()
			+"&name="+ $("#dbName").val(),
		success: function(ret)
		{
			ret = ret.getElementsByTagName('action')[0];
			if (ret.getAttribute("result") == "ok")
			{
				$("#dbResultCheck")
					.addClass("ok")
					.removeClass("fail")
					.html(txtError[23])
					.show('slow');
				$("#dbCreateResultCheck")
					.hide('slow');
			} else
			{
				$("#dbResultCheck")
					.addClass("fail")
					.removeClass("ok")
					.html(txtError[parseInt(ret.getAttribute("error"))])
					.show('slow');
				$("#dbCreateResultCheck")
					.hide('slow');
			}
		}
	 }
	 );	 
	 
}

function createDB()
{
	$("#dbResultCheck").hide();
	$.ajax(
	{
	   url: "model.php",
	   cache: false,
	   data:
	   	"method=createDB"
		+"&tablePrefix="+ $("#db_prefix").val()
		+"&mode="+ $("#dbTableParam input[type=radio]:checked").val()+
		"&type=MySQL"+
		"&server="+ $("#dbServer").val()+
		"&login="+ $("#dbLogin").val()+
		"&password="+encodeURIComponent($("#dbPassword").val())+
		"&engine="+$("#dbEngine option:selected").val()+
		"&name="+ $("#dbName").val()+
		"&language="+ id_lang+
		(dropdb ? "&dropAndCreate=true" : '')
	   ,
	   success: function(ret)
	   {
			var action_ret;
			try {
				action_ret = ret.getElementsByTagName('action')[0];
			} catch (e) {
				$("#dbCreateResultCheck")
					.addClass("fail")
					.removeClass("ok")
					.removeClass('userInfos')
					.html(ret)
					.show();
				return;
			}
			if (action_ret.getAttribute("result") == "ok")
			{
				var countries_ret = ret.getElementsByTagName('country');
				var timezone_ret = ret.getElementsByTagName('timezone');
				var html = '';
				for (i = 0; countries_ret[i]; i=i+1)
				{
					html = html + '<option rel="'+countries_ret[i].getAttribute('iso')+'" value="'+countries_ret[i].getAttribute("value")+'" ';
					if (countries_ret[i].getAttribute("value") == 8)
						html = html + ' selected="selected" ';
					html = html + ' >'+countries_ret[i].getAttribute("name")+'</option>';
				}
				$('#infosCountry').html(html);
				html = '';
				for (i = 0; timezone_ret[i]; i=i+1)
				{
					html = html + '<option value="'+timezone_ret[i].getAttribute("value")+'" ';
					if (timezone_ret[i].getAttribute("value") == "Europe/Paris")
						html = html + ' selected="selected" ';
					html = html + ' >'+timezone_ret[i].getAttribute("name")+'</option>';
				}
				$('#infosTimezone').html(html);
				showStep(step+1, 'next');
			}
			else
			{
				if (action_ret.getAttribute("error") == "11")
				{
					$("#dbCreateResultCheck")
						.addClass("fail")
						.removeClass("ok")
						.removeClass('userInfos')
						.html(
							txtError[11]+ "<br />\'"+
							action_ret.getAttribute("sqlQuery") + "\'<br/>"+
							action_ret.getAttribute("sqlMsgError") + "(" + txtError[18] + " : " + action_ret.getAttribute("sqlNumberError") +")"
						)
						.show();
				}
				else
				{
					$("#dbCreateResultCheck")
						.addClass("fail")
						.removeClass("ok")
						.removeClass('userInfos')
						.html(txtError[parseInt(action_ret.getAttribute("error"))])
						.show();
				}
			}
	   }
	});
}


function verifyMail()
{
	//local verifications
	if ($("#testEmail[value=]").length > 0)
	{
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtError[0]);
		return false;
	}
	else if (!verifMailREGEX.test( $("#testEmail").val() ))
	{ 
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtError[3]);
		return false;
	}
	else
	{
		
		if (smtpChecked)
		{
			//local verifications
			if($("#smtpSrv[value=]").length > 0)
			{
				$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtSmtpSrvEmpty);
				smtpIsOk = false;
				return false;
			}
		}
		
		
		//external verifications and sets
		$.ajax(
		{
		   url: "model.php",
		   cache: false,
		   data:
				"method=checkMail"+
		   		"&mailMethod= "+(smtpChecked ? "smtp" : "native")+
				"&smtpSrv="+ $("input#smtpSrv").val()+
				"&testEmail="+ $("#testEmail").val()+
		   		"&smtpLogin="+ $("input#smtpLogin").val()+
		   		"&smtpPassword="+ $("input#smtpPassword").val()+
				"&smtpPort="+ $("input#smtpPort").val()+
				"&smtpEnc="+ $("select#smtpEnc option:selected").val()+
				"&testMsg="+testMsg+
				"&testSubject="+testSubject
			,
		   success: function(ret)
		   {
				ret = ret.getElementsByTagName('action')[0];
				
				if (ret.getAttribute("result") == "ok")
				{
					$("#mailResultCheck").addClass("ok").removeClass("fail").removeClass('userInfos').html(mailSended);
					mailIsOk = true;
				}
				else
				{
					mailIsOk = false;
					$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(txtError[26]);
				}
		   }
		 }
		 );
	}
}

function uploadLogo ()
{
	$.ajaxFileUpload
		(
			{
				url:'xml/uploadLogo.php',
				secureuri:false,
				fileElementId:'fileToUpload',
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						$("#uploadedImage").slideUp('slow', function()
						{
							if(data.error != '')
							{
								$("#resultInfosLogo").html( txtError[parseInt(data.error)] ).addClass("fail").show();
							}
							else
							{
								$(this).attr('src', ps_base_uri + 'img/logo.jpg?' + (new Date()))
								$(this).show('slow');
								$("#resultInfosLogo").html("").removeClass("fail").hide();
							}
						});
					}
				},
				error: function (data, status, e)
				{
					$("#uploadedImage").attr('src', ps_base_uri + 'img/logo.jpg?' + (new Date()));
					$("#resultInfosLogo").html("").addClass("fail");
				}
			}
		)
}

function moveLanguage(direction)
{
	
	switch (direction)
	{
		
		case "al2wl" :
		$("#aLList option:selected").each(
			function()
			{
				$(this).appendTo("#wLList");
				$(this).clone().prependTo("#dLList");
			}
		);
		
		break;
		
		case "wl2al" :
		if ($("#wLList option").length > 1)
		{
			$("#wLList option:selected").each(
				function()
				{
					if($(this).val() != "en" )
					{
						$(this).appendTo("#aLList");
						$("#dLList option[value = '" + $(this).attr('value') + "']").remove();
					}
				}
			);	
		}
		break;
	}
}

function ajaxRefreshField(nthField, idResultField, fieldsList, inputId)
{
	var result = fieldsList[nthField].getAttribute("result");
	if (result != "ok")
	{
		$("#"+idResultField)
			.html( txtError[parseInt(fieldsList[nthField].getAttribute("error"))] )
			.addClass("fail")
			.show("slow");
		if (validShopInfos)
			$("#"+inputId).focus();
		return false;
	}
	else
	{
		$("#"+idResultField)
			.html("")
			.removeClass("fail")
			.show("slow");
		return true;
	}
}

function verifyShopInfos()
{
	urlLanguages = "";
	$("#wLList option").each(
		function()
		{
			urlLanguages += "&infosWL[]=" + $(this).val();
		}
	);
	urlLanguages += "&infosDL[]=" + $("#dLList option:selected").val();
	
	$.ajax(
	{
	   url: "model.php",
	   async: false,
	   cache: false,
	   data:
		"method=checkShopInfos"+
		"&isoCode="+isoCodeLocalLanguage+
		"&infosActivity="+ encodeURIComponent($("select#infosActivity").val())+
		"&infosCountry="+ encodeURIComponent($("select#infosCountry").val())+
		"&infosTimezone="+ encodeURIComponent($("select#infosTimezone").val())+
		"&infosShop="+ encodeURIComponent($("input#infosShop").val())+
		"&infosFirstname="+ encodeURIComponent($("input#infosFirstname").val())+
		"&infosName="+ encodeURIComponent($("input#infosName").val())+
		"&infosEmail="+ encodeURIComponent($("input#infosEmail").val())+
		"&infosPassword="+ encodeURIComponent($("input#infosPassword").val())+
		"&infosPasswordRepeat="+ encodeURIComponent($("input#infosPasswordRepeat").val())+
		"&infosNotification="+ ( ($("#infosNotification:checked").length > 0) ? "on" : "off" )+
		"&countryName="+encodeURIComponent($("select#infosCountry option:selected").attr('rel'))+
		urlLanguages+
		"&catalogMode="+ encodeURIComponent($("input[name=catalogMode]:checked").val())+
		"&infosMailMethod=" + ((smtpChecked) ? "smtp" : "native")+
		"&smtpSrv="+ encodeURIComponent($("input#smtpSrv").val())+
		"&smtpLogin="+ encodeURIComponent($("input#smtpLogin").val())+
		"&smtpPassword="+ encodeURIComponent($("input#smtpPassword").val())+
		"&smtpPort="+ encodeURIComponent($("input#smtpPort").val())+
		"&smtpEnc="+ encodeURIComponent($("select#smtpEnc option:selected").val())+
		"&mailSubject="+ encodeURIComponent(mailSubject)+
		"&isoCodeLocalLanguage="+isoCodeLocalLanguage
	   ,
	   
	   success: function(ret)
	   {
			fieldsList = ret.getElementsByTagName('shopConfig')[0].getElementsByTagName('field');
			validShopInfos = true;
			if (!ajaxRefreshField(0, "resultInfosShop", fieldsList, "infosShop")) validShopInfos = false;
			else if (!ajaxRefreshField(4, "resultInfosShop", fieldsList, "validateShop")) validShopInfos = false;
			else if (!ajaxRefreshField(1, "resultInfosFirstname", fieldsList, "infosFirstname")) validShopInfos = false;
			else if (!ajaxRefreshField(2, "resultInfosName", fieldsList, "infosName")) validShopInfos = false;
			else if (!ajaxRefreshField(3, "resultInfosEmail", fieldsList, "infosEmail")) validShopInfos = false;
			else if (!ajaxRefreshField(7, "resultInfosPassword", fieldsList, "infosPassword")) validShopInfos = false;
			else if (!ajaxRefreshField(8, "resultInfosPasswordRepeat", fieldsList, "infosPasswordRepeat")) validShopInfos = false;
			else if (!ajaxRefreshField(9, "resultInfosLanguages", fieldsList, "infosLanguages")) validShopInfos = false;
			else if (!ajaxRefreshField(11, "resultInfosSQL", fieldsList, "infosSQL")) validShopInfos = false;
			else if (!ajaxRefreshField(10, "resultInfosNotification", fieldsList, "infosNotification")) validShopInfos = false;
			else if (!ajaxRefreshField(5, "resultInfosFirstname", fieldsList, "validateFirstname")) validShopInfos = false;
			else if (!ajaxRefreshField(6, "resultInfosName", fieldsList, "validateName")) validShopInfos = false;
			else if (!ajaxRefreshField(12, "resultCatalogMode", fieldsList, "validateCatalogMode")) validCatalogMode = false;
			else
			{
				$('#endShopName').html($('input#infosShop').val());
				$('#endFirstName').html($('input#infosFirstname').val());
				$('#endName').html($('input#infosName').val());
				$('#endEmail').html($('input#infosEmail').val());
			}
	   }
	 }
	 );
}

function autoCheckField(idField, idResultSpan, typeVerif)
{
	switch (typeVerif)
	{
		case "required" :
			$(idField).blur(function()
			{
				if($(this).val() == "")
				{
					$(idResultSpan)
						.show("slow")
						.addClass("fail")
						.html(txtError[0]);
				}
				else
				{
					$(idResultSpan)
						.hide("slow")
						.removeClass("fail")
						.html("");
				}
			}
			);
		break;
		
		case "mailFormat" :
			$(idField).blur(
			function()
				{
					if (!verifMailREGEX.test( $(this).val() ))
					{
						$(idResultSpan)
							.show("slow")
							.addClass("fail")
							.html(txtError[3]);
					}
					else
					{
						$(idResultSpan)
							.hide("slow")
							.removeClass("fail")
							.html("");
					}
				}
			);
		break;
		
		case "firstnameFormat" :
			$(idField).blur(
			function()
				{
					if (!verifNameREGEX.test( $(this).val() ))
					{
						$(idResultSpan)
							.show("slow")
							.addClass("fail")
							.html(txtError[47]);
					}
					else
					{
						$(idResultSpan)
							.hide("slow")
							.removeClass("fail")
							.html("");
					}
				}
			);
		break;
		
		case "nameFormat" :
			$(idField).blur(
			function()
				{
					if (!verifNameREGEX.test( $(this).val() ))
					{
						$(idResultSpan)
							.show("slow")
							.addClass("fail")
							.html(txtError[48]);
					}
					else
					{
						$(idResultSpan)
							.hide("slow")
							.removeClass("fail")
							.html("");
					}
				}
			);
		break;
		
		default : return false;
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//upgrader
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function constructUpdaterTabs()
{
	$("#tabs")
		.empty()
		.append("<li id='tabUpdaterWelcome' class='selected'><span class='number1' >"+txtTabUpdater1+"</span></li>")
		.append("<li id='tabUpdaterDisclaimer'><span class='number2' >"+txtTabUpdater2+"</span></li>")
		.append("<li id='tabUpdaterRequire'><span class='number3' >"+txtTabUpdater3+"</span></li>")
		.append("<li id='tabUpdaterFinish'><span class='number4' >"+txtTabUpdater4+"</span></li>")
	;
	$(".installerVersion").hide();
	$(".updaterVersion").show();
}

function constructInstallerTabs()
{
	$("#tabs")
		.empty()
		.append("<li id='tab_lang' class='selected'><span class='number1' >"+txtTabInstaller1+"</span></li>")
		.append("<li id='tab_require'><span class='number2' >"+txtTabInstaller2+"</span></li>")
		.append("<li id='tab_db'><span class='number3' >"+txtTabInstaller3+"</span></li>")
		.append("<li id='tab_infos'><span class='number4' >"+txtTabInstaller4+"</span></li>")
		.append("<li id='tab_end'><span class='number5' >"+txtTabInstaller5+"</span></li>")
	;
	$(".installerVersion").show();
	$(".updaterVersion").hide();
}

function doUpgrade()
{
	$.ajax(
	{
	   url: "model.php",
	   cache: false,
	   data:
	   	"method=doUpgrade"
	   ,
	   success: function(ret)
	   {
	   		var ret;
			try {
				ret = ret.getElementsByTagName('action')[0];
			} catch (e) {
				$("#resultUpdate").html(ret);
				showStep(8);
				return;
			}

			var countSqlError = 0;
			if (ret.getAttribute("result") == "ok" || (ret.getAttribute("result") == "fail" && (ret.getAttribute("error") == "34")))
			{
				requests = ret.getElementsByTagName('request');
				$("#updateLog").empty();
				
				$(requests).each(function()
				{
					$("#updateLog").append("<div class='request'>" + $(this).children("sqlQuery").text() + "</div><br/>");
					if($(this).attr("result") == "fail")
					{
						countSqlError++;
						$("#updateLog").append("<span class='fail'>(" + $(this).children("sqlNumberError").text() + ") " + $(this).children("sqlMsgError").text() + "</span><br/>");
					}
				});
				if (ret.getAttribute("error") == "34")
					$("#txtErrorUpdateSQL").html(txtError[35]+" "+countSqlError+" "+txtError[36]);
				showStep(9);
			}
			else
			{
				$("#resultUpdate").html(txtError[parseInt(ret.getAttribute("error"))]);
				showStep(8);
			}
		},
		error: function (data, status, e)
		{
			$('#resultUpdate').html('<p>Error during install/upgrade: <b style="color: #D41958; font-weight: bold;">'+data.responseText.replace(/<\/?[^>]+>/gi, '')+'</b><br /><br />You may have to:<br /><ol><li>Fix the error(s) displayed</li><li>Put your database backup</li><li>Modify the file settings.inc.php to put the old version for the line with _PS_VERSION_</li><li>Restart the upgrade process from the begining</li></ol></p>');
			$('#detailsError').html(data);
			showStep(8);
		}
	});
}

function showUpdateLog(){
	$("div#updateLog").toggle('slow');
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// end upgrader
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//when ready....
$(document).ready(
	function()
	{
		//show container only if JS is available
		$("#noJavaScript").hide();
		$("#container").show();
		
		//ajax animation
		$("#loader").ajaxStart(
			function()
			{
				$(this).fadeIn();
				$("#btNext[disabled!=1], #btBack[disabled!=1]").attr("disabled", "disabled").addClass("disabled").addClass("lockedForAjax");
			}
		);
		$("#loader").ajaxComplete(
			function()
			{
				$(this).fadeOut();
				$(".lockedForAjax").removeAttr("disabled").removeClass("disabled").removeClass("lockedForAjax");
			}
		);
		//set actions on clicks
		$('#btNext').bind("click",nextTab);
		$('#btBack').bind("click",backTab);
		$('#btVerifyMail').bind("click",verifyMail);
		
		$('#al2wl, #wl2al').click(
			function()
			{
				moveLanguage(this.id);
			}
		);
		$('#req_bt_refresh, #req_bt_refresh_update').click(
			function()
			{
				verifyAndSetRequire(0);
			}
		);

		//set SMTP pannels states
		$("#set_stmp").bind("click",
			function()
			{
				switch ($("input#set_stmp:checked").length)
				{
					case 0 :
					$("div#mailSMTPParam").slideUp('slow');
					smtpChecked = false;
					$("#mailResultCheck").addClass("userInfos").removeClass("ok").removeClass('fail').html("");
					break;
					
					case 1 :
					$("div#mailSMTPParam").slideDown('slow');
					smtpChecked = true;
					$("#mailResultCheck").addClass("userInfos").removeClass("ok").removeClass('fail').html("");
					break;
				}
			}
		);

		//preset mail step 4
		$("#testEmail").change(
			function()
			{
				$('#infosEmail').val( $(this).val() );
			}
		);
		
		//certification needed for upgrade
		$('#btDisclaimerOk').click(function()
		{
		    if ($(this).attr('checked'))
		    {
		    	upgradeCertify = true;
		    	$('#btNext').removeAttr('disabled').removeClass('disabled');
		    	$('#upgradeProcess').slideDown(500);
		    }
		    else
		    {
		    	upgradeCertify = false;
		    	$('#btNext').attr('disabled', 'disabled').addClass('disabled');
		    	$('#upgradeProcess').slideUp(500);
		    }
		});
		
		//autocheck fields
		autoCheckField("#infosShop", "#resultInfosShop", "required");
		autoCheckField("#infosFirstname", "#resultInfosFirstname", "firstnameFormat");
		autoCheckField("#infosName", "#resultInfosName", "nameFormat");
		autoCheckField("#infosEmail", "#resultInfosEmail", "mailFormat");
		autoCheckField("#infosPassword", "#resultInfosPassword", "required");
		autoCheckField("#infosPasswordRepeat", "#resultInfosPasswordRepeat", "required");
		autoCheckField("#infosPasswordRepeat", "#resultInfosPassword", "required");
		
		//fix PNG for IE < IE7
		/*
		$(document).ready(function(){ 
	        $("#dfdsf").pngFix(); 
	    });*/
	    
		
		constructInstallerTabs();
		
		//show 1st step
		step=1;
		$("input#btNext").focus();
		
		// hide next button for licence validation
		$("#btNext")
		.attr("disabled", "disabled")
		.addClass("disabled");
		
		
			
		function checkLicenseButton(elt)
		{
			if ($(elt).is(':checked'))
			{
				$("#btNext")
				.removeAttr('disabled')
				.removeClass('disabled');
			}
			else
			{
				$("#btNext")
				.attr("disabled", "disabled")
				.addClass("disabled");
			}
		}
			
		$('#set_license').click(function() {
			checkLicenseButton(this);
		});
	}
);
