/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//constant
verifMailREGEX = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;

function verifyMail(testMsg, testSubject)
{
	$("#mailResultCheck").removeClass("ok").removeClass('fail').html('<img src="../img/admin/ajax-loader.gif" alt="" />');
	$("#mailResultCheck").slideDown("slow");

	//local verifications
	if ($("#testEmail[value=]").length > 0)
	{
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(errorMail);
		return false;
	}
	else if (!verifMailREGEX.test( $("#testEmail").val() ))
	{
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(errorMail);
		return false;
	}
	else
	{
		//external verifications and sets
		$.ajax(
		{
		   url: "index.php",
		   cache: false,
		   type : "POST",
		   data:
			{
				"mailMethod"	: (($("input[name=PS_MAIL_METHOD]:checked").val() == 2) ? "smtp" : "native"),
				"smtpSrv"		: $("input[name=PS_MAIL_SERVER]").val(),
				"testEmail"		: $("#testEmail").val(),
				"smtpLogin"		: $("input[name=PS_MAIL_USER]").val(),
				"smtpPassword"	: $("input[name=PS_MAIL_PASSWD]").val(),
				"smtpPort"		: $("input[name=PS_MAIL_SMTP_PORT]").val(),
				"smtpEnc"		: $("select[name=PS_MAIL_SMTP_ENCRYPTION]").val(),
				"testMsg"		: textMsg,
				"testSubject"	: textSubject,
				"token"			: token_mail,
				"ajax"			: 1,
				"tab"				: 'AdminEmails',
				"action"			: 'sendMailTest'
			},
		   success: function(ret)
		   {
				if (ret == "ok")
				{
					$("#mailResultCheck").addClass("ok").removeClass("fail").removeClass('userInfos').html(textSendOk);
					mailIsOk = true;
				}
				else
				{
					mailIsOk = false;
					$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(textSendError + '<br />' + ret);
				}
		   }
		 }
		 );
	}
}
