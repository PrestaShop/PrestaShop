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
	var baseDir = '{$base_dir_ssl}';
//]]>
</script>

<script type="text/javascript">
// <![CDATA[
idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{elseif isset($address->id_state)}{$address->id_state|intval}{else}false{/if};
countries = new Array();
countriesNeedIDNumber = new Array();
countriesNeedZipCode = new Array();
{foreach from=$countries item='country'}
	{if isset($country.states) && $country.contains_states}
		countries[{$country.id_country|intval}] = new Array();
		{foreach from=$country.states item='state' name='states'}
			countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
		{/foreach}
	{/if}
	{if $country.need_identification_number}
		countriesNeedIDNumber.push({$country.id_country|intval});
	{/if}	
	{if isset($country.need_zip_code)}
		countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
	{/if}
{/foreach}
$(function(){ldelim}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state}{else}{if isset($address->id_state)}{$address->id_state|escape:'htmlall':'UTF-8'}{/if}{/if}]').attr('selected', 'selected');
{rdelim});
{if $vat_management}
{literal}
	$(document).ready(function() {
		$('#company').blur(function(){
			vat_number();
		});
		vat_number();
		function vat_number()
		{
			if ($('#company').val() != '')
				$('#vat_number').show();
			else
				$('#vat_number').hide();
		}
	});
{/literal}
{/if}
//]]>
</script>

{capture name=path}{l s='Your addresses'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Your addresses'}</h1>

<h3>{if isset($id_address)}{l s='Modify address'} {if isset($smarty.post.alias)}"{$smarty.post.alias}"{elseif isset($address->alias)}"{$address->alias|escape:'htmlall':'UTF-8'}"{/if}{else}{l s='To add a new address, please fill out the form below.'}{/if}</h3>

{include file="$tpl_dir./errors.tpl"}

<form action="{$link->getPageLink('address.php', true)}" method="post" class="std">
	<fieldset>
		<h3>{if isset($id_address)}{l s='Your address'}{else}{l s='New address'}{/if}</h3>
		<p class="text">
			<input type="hidden" name="token" value="{$token}" />
			<label for="company">{l s='Company'}</label>
			<input type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{else}{if isset($address->company)}{$address->company|escape:'htmlall':'UTF-8'}{/if}{/if}" />
		</p>
	{if $vat_display == 2}
		<div id="vat_area">
	{elseif $vat_display == 1}
		<div id="vat_area" style="display: none;">
	{else}
		<div style="display: none;">
	{/if}
		<div id="vat_number">
			<p class="text">
				<label for="vat_number">{l s='VAT number'}</label>
				<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			</p>
		</div>
		</div>
		<p class="required text">
			<label for="firstname">{l s='First name'}</label>
			<input type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{else}{if isset($address->firstname)}{$address->firstname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="lastname">{l s='Last name'}</label>
			<input type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{else}{if isset($address->lastname)}{$address->lastname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required text dni">
			<label for="dni">{l s='Identification number'}</label>
			<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			<span class="form_info">{l s='DNI / NIF / NIE'}</span>
		</p>
		<p class="required text">
			<label for="address1">{l s='Address'}</label>
			<input type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{else}{if isset($address->address1)}{$address->address1|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="address2">{l s='Address (Line 2)'}</label>
			<input type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{else}{if isset($address->address2)}{$address->address2|escape:'htmlall':'UTF-8'}{/if}{/if}" />
		</p>
		<p class="required postcode text">
			<label for="postcode">{l s='Zip / Postal Code'}</label>
			<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
			<sup>*</sup>
		</p>
		<p class="required text">
			<label for="city">{l s='City'}</label>
			<input type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($address->city)}{$address->city|escape:'htmlall':'UTF-8'}{/if}{/if}" maxlength="64" />
			<sup>*</sup>
		</p>
		<p class="required select">
			<label for="id_country">{l s='Country'}</label>
			<select id="id_country" name="id_country">{$countries_list}</select>
			<sup>*</sup>
		</p>
		<script type="text/javascript">
		var ajaxurl = '{$ajaxurl}';
		{literal}
				$(document).ready(function(){
					$('#id_country').change(function() {
						$.ajax({
							type: "GET",
							url: ajaxurl+"vatnumber/ajax.php?id_country="+$('#id_country').val(),
							success: function(isApplicable){
								if(isApplicable == "1")
								{
									$('#vat_area').show();
									$('#vat_number').show();
								}
								else
								{
									$('#vat_area').hide();
								}
							}
						});
					});
					
				});
		{/literal}
		</script>
		<p class="required id_state select">
			<label for="id_state">{l s='State'}</label>
			<select name="id_state" id="id_state">
				<option value="">-</option>
			</select>
			<sup>*</sup>
		</p>
		<p class="textarea">
			<label for="other">{l s='Additional information'}</label>
			<textarea id="other" name="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{else}{if isset($address->other)}{$address->other|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
		</p>
		<p style="margin-left:50px;">{l s='You must register at least one phone number'} <sup style="color:red;">*</sup></p>
		<p class="text">
			<label for="phone">{l s='Home phone'}</label>
			<input type="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'htmlall':'UTF-8'}{/if}{/if}" />
		</p>
		<p class="text">
			<label for="phone_mobile">{l s='Mobile phone'}</label>
			<input type="text" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'htmlall':'UTF-8'}{/if}{/if}" />
		</p>
		<p class="required text" id="adress_alias">
			<label for="alias">{l s='Assign an address title for future reference'}</label>
			<input type="text" id="alias" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{elseif isset($address->alias)}{$address->alias|escape:'htmlall':'UTF-8'}{elseif isset($select_address)}{else}{l s='My address'}{/if}" />
			<sup>*</sup>
		</p>
	</fieldset>
	<p class="submit2">
		{if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
		{if isset($back)}<input type="hidden" name="back" value="{$back}?step=1" />{/if}
		{if isset($mod)}<input type="hidden" name="mod" value="{$mod}" />{/if}
		{if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
		<input type="submit" name="submitAddress" id="submitAddress" value="{l s='Save'}" class="button" />
	</p>
	<p class="required"><sup>*</sup>{l s='Required field'}</p>
</form>
