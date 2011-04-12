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

<script type="text/javascript">
// <![CDATA[
idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}false{/if};
countries = new Array();
{foreach from=$countries item='country'}
	{if isset($country.states)}
		countries[{$country.id_country|intval}] = new Array();
		{foreach from=$country.states item='state' name='states'}
			countries[{$country.id_country|intval}]['{$state.id_state|intval}'] = '{$state.name|escape:'htmlall':'UTF-8'}';
		{/foreach}
	{/if}
{/foreach}
//]]>
</script>

<h2>{l s='Check your information' mod='paypal'}</h2>

{assign var='current_step' value='login'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

<form action="{$base_dir_ssl}modules/paypal/express/submit.php" method="post" id="account-creation_form" class="std">
	<fieldset class="account_creation">
		<h3>{l s='Your personal information' mod='paypal'}</h3>
		<p class="radio required">
			<span>{l s='Title'}</span>
			<input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == 1}checked="checked"{/if} />
			<label for="id_gender1" class="top">{l s='Mr.' mod='paypal'}</label>
			<input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == 2}checked="checked"{/if} />
			<label for="id_gender2" class="top">{l s='Ms.' mod='paypal'}</label>
		</p>
		<p class="required text">
			<label for="customer_firstname">{l s='First name' mod='paypal'}</label>
			<input onkeyup="$('#firstname').val(this.value);" type="text" class="text" id="customer_firstname" name="customer_firstname" value="{$firstname}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="customer_lastname">{l s='Last name' mod='paypal'}</label>
			<input onkeyup="$('#lastname').val(this.value);" type="text" class="text" id="customer_lastname" name="customer_lastname" value="{$lastname}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="email">{l s='E-mail' mod='paypal'}</label>
			<input type="text" class="text" id="email" name="email" value="{$email}" />
			<sup>*</sup>
		</p>
		<p class="required password">
			<label for="password">{l s='Password' mod='paypal'}</label>
			<input type="password" class="text" name="passwd" id="passwd" />
			<sup>*</sup>
			<span class="form_info">{l s='(5 characters min.)' mod='paypal'}</span>
		</p>
		<p class="select">
			<span>{l s='Birthday' mod='paypal'}</span>
			<select id="days" name="days">
				<option value="">-</option>
				{foreach from=$days item=day}
					<option value="{$day|escape:'htmlall':'UTF-8'}" {if ($sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
			{*
				{l s='January' mod='paypal'}
				{l s='February' mod='paypal'}
				{l s='March' mod='paypal'}
				{l s='April' mod='paypal'}
				{l s='May' mod='paypal'}
				{l s='June' mod='paypal'}
				{l s='July' mod='paypal'}
				{l s='August' mod='paypal'}
				{l s='September' mod='paypal'}
				{l s='October' mod='paypal'}
				{l s='November' mod='paypal'}
				{l s='December' mod='paypal'}
			*}
			<select id="months" name="months">
				<option value="">-</option>
				{foreach from=$months key=k item=month}
					<option value="{$k|escape:'htmlall':'UTF-8'}" {if ($sl_month == $k)} selected="selected"{/if}>{$month}&nbsp;</option>
				{/foreach}
			</select>
			<select id="years" name="years">
				<option value="">-</option>
				{foreach from=$years item=year}
					<option value="{$year|escape:'htmlall':'UTF-8'}" {if ($sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
		</p>
		<p class="checkbox" >
			<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
			<label for="newsletter">{l s='Sign up for our newsletter' mod='paypal'}</label>
		</p>
		<p class="checkbox" >
			<input type="checkbox"name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
			<label for="optin">{l s='Receive special offers from our partners' mod='paypal'}</label>
		</p>
	</fieldset>
	<fieldset class="account_creation">
		<h3>{l s='Your address' mod='paypal'}</h3>
		<p class="text">
			<label for="company">{l s='Company' mod='paypal'}</label>
			<input type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
		</p>
		<p class="required text">
			<label for="firstname">{l s='First name' mod='paypal'}</label>
			<input type="text" class="text" id="firstname" name="firstname" value="{$firstname}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="lastname">{l s='Last name' mod='paypal'}</label>
			<input type="text" class="text" id="lastname" name="lastname" value="{$lastname}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="address1">{l s='Address' mod='paypal'}</label>
			<input type="text" class="text" name="address1" id="address1" value="{$street}" />
			<sup>*</sup>
		</p>
		<p class="text">
			<label for="address2">{l s='Address (2)' mod='paypal'}</label>
			<input type="text" class="text" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
		</p>
		<p class="required text">
			<label for="postcode">{l s='Postal code / Zip code' mod='paypal'}</label>
			<input type="text" class="text" name="postcode" id="postcode" value="{$zip}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="city">{l s='City' mod='paypal'}</label>
			<input type="text" class="text" name="city" id="city" value="{$city}" />
			<sup>*</sup>
		</p>
		<p class="required select">
			<label for="id_country">{l s='Country' mod='paypal'}</label>
			<select name="id_country" id="id_country">
				<option value="">-</option>
				{foreach from=$countries item=v}
				<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
			<sup>*</sup>
		</p>
		<p class="required id_state select">
			<label for="id_state">{l s='State' mod='paypal'}</label>
			<select name="id_state" id="id_state">
				<option value="">-</option>
			</select>
			<sup>*</sup>
		</p>
		<p class="textarea">
			<label for="other">{l s='Additional information' mod='paypal'}</label>
			<textarea name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
		</p>
		<p class="text">
			<label for="phone">{l s='Home phone' mod='paypal'}</label>
			<input type="text" class="text" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
		</p>
		<p class="text">
			<label for="phone_mobile">{l s='Mobile phone' mod='paypal'}</label>
			<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
		</p>
		<p class="required text" id="address_alias">
			<label for="alias">{l s='Assign an address title for future reference' mod='paypal'} !</label>
			<input type="text" class="text" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address' mod='paypal'}{/if}" />
			<sup>*</sup>
		</p>
	</fieldset>
	<p class="cart_navigation required submit">
		<input type="hidden" name="token" value="{$ppToken|escape:'htmlall'|stripslashes}" />
		<input type="hidden" name="payerID" value="{$payerID|escape:'htmlall'|stripslashes}" />
		<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Continue' mod='paypal'}" class="exclusive" />
		<span><sup>*</sup>{l s='Required field' mod='paypal'}</span>
	</p>
</form>