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

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
{else}
	{assign var="back_order_page" value="order.php"}
{/if}

{* Will be deleted for 1.5 version and more *}
{if !isset($formatedAddressFieldsValuesList)}
	{$ignoreList.0 = "id_address"}
	{$ignoreList.1 = "id_country"}
	{$ignoreList.2 = "id_state"}
	{$ignoreList.3 = "id_customer"}
	{$ignoreList.4 = "id_manufacturer"}
	{$ignoreList.5 = "id_supplier"}
	{$ignoreList.6 = "date_add"}
	{$ignoreList.7 = "date_upd"}
	{$ignoreList.8 = "active"}
	{$ignoreList.9 = "deleted"}	
	
	{* PrestaShop 1.4.0.17 compatibility *}
	{if isset($addresses)}
		{foreach from=$addresses key=k item=address}
			{counter start=0 skip=1 assign=address_key_number}
			{$id_address = $address.id_address}
			{foreach from=$address key=address_key item=address_content}
				{if !in_array($address_key, $ignoreList)}
					{$formatedAddressFieldsValuesList.$id_address.ordered_fields.$address_key_number = $address_key}
					{$formatedAddressFieldsValuesList.$id_address.formated_fields_values.$address_key = $address_content}
					{counter}
				{/if}
			{/foreach}
		{/foreach}
	{/if}
{/if}

<script type="text/javascript">
// <![CDATA[
	{if !$opc}
	var orderProcess = 'order';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='Product' js=1}";
	var txtProducts = "{l s='Products' js=1}";
	var txtSelectAnAddressFirst = "{l s='Please start by selecting an address' js=1}";
	{/if}

	var formatedAddressFieldsValuesList = new Array();

	{foreach from=$formatedAddressFieldsValuesList key=id_address item=type}
		formatedAddressFieldsValuesList[{$id_address}] =
		{
			'ordered_fields':[
				{foreach from=$type.ordered_fields key=num_field item=field_name name=inv_loop}
					{if !$smarty.foreach.inv_loop.first},{/if}"{$field_name}"
				{/foreach}
			],
			'formated_fields_values':{
				{foreach from=$type.formated_fields_values key=pattern_name item=field_name name=inv_loop}
					{if !$smarty.foreach.inv_loop.first},{/if}"{$pattern_name}":"{$field_name}"
				{/foreach}
			}
		}
	{/foreach}

	function getAddressesTitles()
	{
		return {
			'invoice': "{l s='Your billing address'}",
			'delivery': "{l s='Your delivery address'}"
		};

	}


	function buildAddressBlock(id_address, address_type, dest_comp)
	{
		var adr_titles_vals = getAddressesTitles();
		var li_content = formatedAddressFieldsValuesList[id_address]['formated_fields_values'];
		var ordered_fields_name = ['title'];

		ordered_fields_name = ordered_fields_name.concat(formatedAddressFieldsValuesList[id_address]['ordered_fields']);
		ordered_fields_name = ordered_fields_name.concat(['update']);
		
		dest_comp.html('');

		li_content['title'] = adr_titles_vals[address_type];
		li_content['update'] = '<a href="{$link->getPageLink('address', true, NULL, "id_address")|addslashes}'+id_address+'&amp;back=order?step=1{if $back}&mod={$back}{/if}" title="{l s='Update' js=1}">{l s='Update' js=1}</a>';

		appendAddressList(dest_comp, li_content, ordered_fields_name);
	}

	function appendAddressList(dest_comp, values, fields_name)
	{
		for (var item in fields_name)
		{
			var name = fields_name[item];
			var value = getFieldValue(name, values);
			if (value != "")
			{
				var new_li = document.createElement('li');
				new_li.className = 'address_'+ name;
				new_li.innerHTML = getFieldValue(name, values);
				dest_comp.append(new_li);
			}
		}
	}

	function getFieldValue(field_name, values)
	{
		var reg=new RegExp("[ ]+", "g");

		var items = field_name.split(reg);
		var vals = new Array();

		for (var field_item in items)
			vals.push(values[items[field_item]]);
		return vals.join(" ");
	}

//]]>
</script>

{if !$opc}
{capture name=path}{l s='Addresses'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{if !$opc}<h1>{l s='Addresses'}</h1>{else}<h2><span>1</span> {l s='Addresses'}</h2>{/if}

{if !$opc}
{assign var='current_step' value='address'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

{include file="$tpl_dir./order-address-multishipping-products.tpl"}

<form action="{$link->getPageLink('order', true, NULL, 'multi-shipping=1')|escape:'html'}" method="post">
{else}
<div id="opc_account" class="opc-main-block">
	<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
	<div class="addresses clearfix">
		<input type="hidden" name="id_address_delivery" id="id_address_delivery" value="{$cart->id_address_delivery}" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" />
		<p id="address_invoice_form" class="select" {if $cart->id_address_invoice == $cart->id_address_delivery}style="display: none;"{/if}>
		
		{if $addresses|@count >= 1}
			<label for="id_address_invoice" class="strong">{l s='Choose a billing address:'}</label>
			<select name="id_address_invoice" id="id_address_invoice" class="address_select" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">
			{section loop=$addresses step=-1 name=address}
				<option value="{$addresses[address].id_address|intval}" {if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice}selected="selected"{/if}>{$addresses[address].alias|escape:'html':'UTF-8'}</option>
			{/section}
			</select>
		{else}
			<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back}{/if}")|escape:'html'}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
		{/if}
		</p>
		<div class="clearfix">
			<ul class="address alternate_item {if $cart->isVirtualCart()}full_width{/if}" id="address_invoice">
			</ul>
		</div>
		<p class="address_add submit">
			<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back|urlencode}{/if}")|escape:'html'}" title="{l s='Add'}" class="button_large">&raquo; {l s='Add a new address'}</a>
		</p>
		{if !$opc}
		<div id="ordermsg" class="clearfix">
			<p class="txt">{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
			<p class="textarea"><textarea cols="60" rows="3" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea></p>
		</div>
		{/if}
	</div>
{if !$opc}
	<p class="cart_navigation submit">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="{$back}" />
		{if $back}
			<a href="{$link->getPageLink('order', true, NULL, "step=0&amp;back={$back}")|escape:'html'}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a>
		{else}
			<a href="{$link->getPageLink('order', true, NULL, "step=0")|escape:'html'}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a>
		{/if}
		<input type="submit" name="processAddress" value="{l s='Next'} &raquo;" class="exclusive" />
	</p>
</form>
{else}
</div>
{/if}
