{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $display_mode == 0}
	<form action="https://www.moneybookers.com/app/payment.pl" method="post">
	<p class="payment_module" style="border: 1px solid #595A5E; display: block; text-decoration: none; margin-left: 7px; padding: 0.6em;">
		<input type="hidden" name="pay_to_email" value="{$pay_to_email}" />
		<input type="hidden" name="recipient_description" value="{$recipient_description}" />
		<input type="hidden" name="transaction_id" value="{$transaction_id}" />
		<input type="hidden" name="return_url" value="{$return_url}" />
		<input type="hidden" name="return_url_text" value="{$return_url}" />
		<input type="hidden" name="cancel_url" value="{$return_url}" />
		<input type="hidden" name="status_url" value="{$status_url}" />
		<input type="hidden" name="status_url2" value="{$pay_to_email}" />
		<input type="hidden" name="language" value="{$language}" />
		<input type="hidden" name="hide_login" value="{$hide_login}" />
		<input type="hidden" name="pay_from_email" value="{$pay_from_email}" />
		<input type="hidden" name="firstname" value="{$firstname}" />
		<input type="hidden" name="lastname" value="{$lastname}" />
		{if (!empty($date_of_birth))}<input type="hidden" name="date_of_birth" value="{$date_of_birth}" />{/if}
		<input type="hidden" name="address" value="{$address}" />
		{if (!empty($address2))}<input type="hidden" name="address2" value="{$address2}" />{/if}
		{if (!empty($phone_number))}<input type="hidden" name="phone_number" value="{$phone_number}" />{/if}
		<input type="hidden" name="postal_code" value="{$postal_code}" />
		<input type="hidden" name="city" value="{$city}" />
		{if isset($state) && !empty($state)}<input type="hidden" name="state" value="{$state}" />{/if}
		<input type="hidden" name="country" value="{$country}" />
		<input type="hidden" name="amount" value="{$amount}" />
		<input type="hidden" name="currency" value="{$currency}" />
		<input type="hidden" name="amount2_description" value="{if isset($amount2_description)}{$amount2_description}{/if}" />
		<input type="hidden" name="amount2" value="{if isset($amount2)}{$amount2}{/if}" />
		<input type="hidden" name="amount3_description" value="{if isset($amount3_description)}{$amount3_description}{/if}" />
		<input type="hidden" name="amount3" value="{if isset($amount3)}{$amount3}{/if}" />
		<input type="hidden" name="amount4_description" value="{if isset($amount4_description)}{$amount4_description}{/if}" />
		<input type="hidden" name="amount4" value="{if isset($amount4)}{$amount4}{/if}" />
		<input type="hidden" name="return_url_target" value="2">
		<input type="hidden" name="cancel_url_target" value="2">
		<input type="hidden" class="payment_methods" name="payment_methods" value="ACC">
		<input type="hidden" name="merchant_fields" value="platform">
		<input type="hidden" name="platform" value="21445510">
		{foreach from=$inter item=i}
			<input type="image" src="modules/moneybookers/logos/international/{$inter_logos[$i].file}.gif" value="{$inter_logos[$i].code}" name="Submit" style="margin-right: 10px; border: none;" onclick="$('input.payment_methods').val($(this).val());" />
		{/foreach}
		{foreach from=$local item=i}
			<input type="image" src="modules/moneybookers/logos/local/{$local_logos[$i].file}.gif" value="{$local_logos[$i].code}" name="Submit" style="margin-right: 10px; border: none;" onclick="$('input.payment_methods').val($(this).val());" />
		{/foreach}
	</p>
	</form>
	<div class="clear"></div>
{else}
	{foreach from=$inter item=i}
	<form action="https://www.moneybookers.com/app/payment.pl" method="post">
	<p class="payment_module" style="border: 1px solid #595A5E; display: block; text-decoration: none; height: 50px; margin-left: 7px; padding: 0.6em;">
		<input type="hidden" name="pay_to_email" value="{$pay_to_email}" />
		<input type="hidden" name="recipient_description" value="{$recipient_description}" />
		<input type="hidden" name="transaction_id" value="{$transaction_id}" />
		<input type="hidden" name="return_url" value="{$return_url}" />
		<input type="hidden" name="return_url_text" value="{$return_url}" />
		<input type="hidden" name="cancel_url" value="{$return_url}" />
		<input type="hidden" name="status_url" value="{$status_url}" />
		<input type="hidden" name="status_url2" value="{$pay_to_email}" />
		<input type="hidden" name="language" value="{$language}" />
		<input type="hidden" name="hide_login" value="{$hide_login}" />
		<input type="hidden" name="pay_from_email" value="{$pay_from_email}" />
		<input type="hidden" name="firstname" value="{$firstname}" />
		<input type="hidden" name="lastname" value="{$lastname}" />
		{if (!empty($date_of_birth))}<input type="hidden" name="date_of_birth" value="{$date_of_birth}" />{/if}
		<input type="hidden" name="address" value="{$address}" />
		{if (!empty($address2))}<input type="hidden" name="address2" value="{$address2}" />{/if}
		{if (!empty($phone_number))}<input type="hidden" name="phone_number" value="{$phone_number}" />{/if}
		<input type="hidden" name="postal_code" value="{$postal_code}" />
		<input type="hidden" name="city" value="{$city}" />
		{if isset($state) && !empty($state)}<input type="hidden" name="state" value="{$state}" />{/if}
		<input type="hidden" name="country" value="{$country}" />
		<input type="hidden" name="amount" value="{$amount}" />
		<input type="hidden" name="currency" value="{$currency}" />
		<input type="hidden" name="amount2_description" value="{if isset($amount2_description)}{$amount2_description}{/if}" />
		<input type="hidden" name="amount2" value="{if isset($amount2)}{$amount2}{/if}" />
		<input type="hidden" name="amount3_description" value="{if isset($amount3_description)}{$amount3_description}{/if}" />
		<input type="hidden" name="amount3" value="{if isset($amount3)}{$amount3}{/if}" />
		<input type="hidden" name="amount4_description" value="{if isset($amount4_description)}{$amount4_description}{/if}" />
		<input type="hidden" name="amount4" value="{if isset($amount4)}{$amount4}{/if}" />
		<input type="hidden" class="payment_methods" name="payment_methods" value="ACC">
		<input type="hidden" name="return_url_target" value="2">
		<input type="hidden" name="cancel_url_target" value="2">
		<input type="hidden" name="merchant_fields" value="platform">
		<input type="hidden" name="platform" value="21445510">
		<input type="image" src="modules/moneybookers/logos/international/{$inter_logos[$i].file}.gif" name="Submit" value="{$inter_logos[$i].code}" style="float: left; margin-right: 10px; border: none;" onclick="$('input.payment_methods').val($(this).val());" />
		<span style="margin-top: 25px; display: block;">{l s='Pay by' mod='moneybookers'} {$inter_logos[$i].name}</span>
	</p>
	</form>
	{/foreach}
	{foreach from=$local item=i}
	<form action="https://www.moneybookers.com/app/payment.pl" method="post">
	<p class="payment_module" style="border: 1px solid #595A5E; display: block; text-decoration: none; height: 50px; margin-left: 7px; padding: 0.6em;">
		<input type="hidden" name="pay_to_email" value="{$pay_to_email}" />
		<input type="hidden" name="recipient_description" value="{$recipient_description}" />
		<input type="hidden" name="transaction_id" value="{$transaction_id}" />
		<input type="hidden" name="return_url" value="{$return_url}" />
		<input type="hidden" name="return_url_text" value="{$return_url}" />
		<input type="hidden" name="cancel_url" value="{$return_url}" />
		<input type="hidden" name="status_url" value="{$status_url}" />
		<input type="hidden" name="status_url2" value="{$pay_to_email}" />
		<input type="hidden" name="language" value="{$language}" />
		<input type="hidden" name="hide_login" value="{$hide_login}" />
		<input type="hidden" name="pay_from_email" value="{$pay_from_email}" />
		<input type="hidden" name="firstname" value="{$firstname}" />
		<input type="hidden" name="lastname" value="{$lastname}" />
		{if (!empty($date_of_birth))}<input type="hidden" name="date_of_birth" value="{$date_of_birth}" />{/if}
		<input type="hidden" name="address" value="{$address}" />
		{if (!empty($address2))}<input type="hidden" name="address2" value="{$address2}" />{/if}
		{if (!empty($phone_number))}<input type="hidden" name="phone_number" value="{$phone_number}" />{/if}
		<input type="hidden" name="postal_code" value="{$postal_code}" />
		<input type="hidden" name="city" value="{$city}" />
		{if isset($state) && (!empty($state))}<input type="hidden" name="state" value="{$state}" />{/if}
		<input type="hidden" name="country" value="{$country}" />
		<input type="hidden" name="amount" value="{$amount}" />
		<input type="hidden" name="currency" value="{$currency}" />
		<input type="hidden" name="amount2_description" value="{if isset($amount2_description)}{$amount2_description}{/if}" />
		<input type="hidden" name="amount2" value="{if isset($amount2)}{$amount2}{/if}" />
		<input type="hidden" name="amount3_description" value="{if isset($amount3_description)}{$amount3_description}{/if}" />
		<input type="hidden" name="amount3" value="{if isset($amount3)}{$amount3}{/if}" />
		<input type="hidden" name="amount4_description" value="{if isset($amount4_description)}{$amount4_description}{/if}" />
		<input type="hidden" name="amount4" value="{if isset($amount4)}{$amount4}{/if}" />
		<input type="hidden" class="payment_methods" name="payment_methods" value="ACC">
		<input type="hidden" name="return_url_target" value="2">
		<input type="hidden" name="cancel_url_target" value="2">
		<input type="hidden" name="merchant_fields" value="platform">
		<input type="hidden" name="platform" value="21445510">
		<input type="image" src="modules/moneybookers/logos/local/{$local_logos[$i].file}.gif" name="Submit" value="{$local_logos[$i].code}" style="float: left; margin-right: 10px; border: none;" onclick="$('input.payment_methods').val($(this).val());" />
		<span style="margin-top: 25px; display: block;">{l s='Pay by' mod='moneybookers'} {$local_logos[$i].name}</span>
		<br style="clear: both;" />
	</p>
	</form>
	{/foreach}
{/if}

