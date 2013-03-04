{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">{literal}
// <![CDATA[
oosHookJsCodeFunctions.push('oosHookJsCodeMailAlert');

function clearText() {
	if ($('#oos_customer_email').val() == '{/literal}{l s='your@email.com' mod='mailalerts'}{literal}')
		$('#oos_customer_email').val('');
}

function oosHookJsCodeMailAlert() {
	$.ajax({
		type: 'POST',
		url: "{/literal}{$link->getModuleLink('mailalerts', 'actions', ['process' => 'check'])}{literal}",
		data: 'id_product={/literal}{$id_product}{literal}&id_product_attribute='+$('#idCombination').val(),
		success: function (msg) {
			if (msg == '0') {
				$('#mailalert_link').show();
				$('#oos_customer_email').show();
			}
			else {
				$('#mailalert_link').hide();
				$('#oos_customer_email').hide();
			}
		}
	});
}

function  addNotification() {
	$.ajax({
		type: 'POST',
		url: "{/literal}{$link->getModuleLink('mailalerts', 'actions', ['process' => 'add'])}{literal}",
		data: 'id_product={/literal}{$id_product}{literal}&id_product_attribute='+$('#idCombination').val()+'&customer_email='+$('#oos_customer_email').val()+'',
		success: function (msg) {
			if (msg == '1') {
				$('#mailalert_link').hide();
				$('#oos_customer_email').hide();
				$('#oos_customer_email_result').html("{/literal}{l s='Request notification registered' mod='mailalerts'}{literal}");
				$('#oos_customer_email_result').css('color', 'green').show();
			}
			else if (msg == '2' ) {
				$('#oos_customer_email_result').html("{/literal}{l s='You already have an alert for this product' mod='mailalerts'}{literal}");
				$('#oos_customer_email_result').css('color', 'red').show();
			} else {
				$('#oos_customer_email_result').html("{/literal}{l s='Your e-mail address is invalid' mod='mailalerts'}{literal}");
				$('#oos_customer_email_result').css('color', 'red').show();
			}
		}
	});
	return false;
}

$(document).ready(function() {
	$('#oos_customer_email').bind('keypress', function(e) {
		if(e.keyCode == 13)
		{
			addNotification();
			return false;
		}
	});
});
{/literal}
//]]>
</script>

<!-- MODULE MailAlerts -->
{if isset($email) AND $email}
	<input type="text" id="oos_customer_email" name="customer_email" size="20" value="{l s='your@email.com' mod='mailalerts'}" class="mailalerts_oos_email" onclick="clearText();" /><br />
{/if}
<a href="#" onclick="return addNotification();" id="mailalert_link">{l s='Notify me when available' mod='mailalerts'}</a>
<span id="oos_customer_email_result" style="display:none;"></span>
<!-- END : MODULE MailAlerts -->
