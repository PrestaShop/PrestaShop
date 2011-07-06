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
*  @version  Release: $Revision: 7471 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
// <![CDATA[
	{if !$opc}
	var baseDir = '{$base_dir_ssl}';
	var orderProcess = 'order';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product'}";
	var txtProducts = "{l s='products'}";
	{/if}

	var formatedAddressFieldsValuesList = new Array();

	{foreach from=$formatedAddressFieldsValuesList key=id_address item=type}
		formatedAddressFieldsValuesList[{$id_address}] =
		{ldelim}
			'ordered_fields':[
				{foreach from=$type.ordered_fields key=num_field item=field_name name=inv_loop}
					{if !$smarty.foreach.inv_loop.first},{/if}"{$field_name}"
				{/foreach}
			],
			'formated_fields_values':{ldelim}
					{foreach from=$type.formated_fields_values key=pattern_name item=field_name name=inv_loop}
						{if !$smarty.foreach.inv_loop.first},{/if}"{$pattern_name}":"{$field_name}"
					{/foreach}
				{rdelim}
		{rdelim}
	{/foreach}

	function getAddressesTitles()
	{ldelim}
		return {ldelim}
						'invoice': "{l s='Your billing address'}"
						, 'delivery': "{l s='Your delivery address'}"
			{rdelim};

	{rdelim}


	function buildAddressBlock(id_address, address_type, dest_comp)
	{ldelim}
		var adr_titles_vals = getAddressesTitles();
		var li_content = formatedAddressFieldsValuesList[id_address]['formated_fields_values'];
		var ordered_fields_name = ['title'];

		ordered_fields_name = ordered_fields_name.concat(formatedAddressFieldsValuesList[id_address]['ordered_fields']);
		ordered_fields_name = ordered_fields_name.concat(['update']);
		
		dest_comp.html('');

		li_content['title'] = adr_titles_vals[address_type];
		li_content['update'] = '<a href="{$link->getPageLink('address', true, NULL, "id_address")}'+id_address+'&amp;back=order?step=1{if $back}&mod={$back}{/if}" title="{l s='Update'}">{l s='Update'}</a>';

		appendAddressList(dest_comp, li_content, ordered_fields_name);
	{rdelim}

	function appendAddressList(dest_comp, values, fields_name)
	{ldelim}
		for (var item in fields_name)
		{ldelim}
			var name = fields_name[item];
			var new_li = document.createElement('li');
			new_li.className = 'address_'+ name;
			new_li.innerHTML = getFieldValue(name, values);
			dest_comp.append(new_li);
		{rdelim}
	{rdelim}

	function getFieldValue(field_name, values)
	{ldelim}
		var reg=new RegExp("[ ]+", "g");

		var items = field_name.split(reg);
		var vals = new Array();

		for (var field_item in items)
			vals.push(values[items[field_item]]);
		return vals.join(" ");
	{rdelim}

//]]>
</script>

{if !$opc}
{capture name=path}{l s='Addresses'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{if !$opc}<h1>{l s='Addresses'}</h1>{else}<h2>1. {l s='Addresses'}</h2>{/if}

{if !$opc}
{assign var='current_step' value='address'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}
<form action="{$link->getPageLink('order', true)}" method="post">
{else}
<div id="opc_account" class="opc-main-block">
	<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
	<div class="addresses">
		<p class="address_delivery select">
			<label for="id_address_delivery">{l s='Choose a delivery address:'}</label>
			<select name="id_address_delivery" id="id_address_delivery" class="address_select" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">

			{foreach from=$addresses key=k item=address}
				<option value="{$address.id_address|intval}" {if $address.id_address == $cart->id_address_delivery}selected="selected"{/if}>{$address.alias|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
			
			</select>
		</p>
		<p class="checkbox">
			<input type="checkbox" name="same" id="addressesAreEquals" value="1" onclick="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $cart->id_address_invoice == $cart->id_address_delivery || $addresses|@count == 1}checked="checked"{/if} />
			<label for="addressesAreEquals">{l s='Use the same address for billing.'}</label>
		</p>
		
		<p id="address_invoice_form" class="select" {if $cart->id_address_invoice == $cart->id_address_delivery}style="display: none;"{/if}>
		
		{if $addresses|@count > 1}
			<label for="id_address_invoice" class="strong">{l s='Choose a billing address:'}</label>
			<select name="id_address_invoice" id="id_address_invoice" class="address_select" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">
			{section loop=$addresses step=-1 name=address}
				<option value="{$addresses[address].id_address|intval}" {if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice}selected="selected"{/if}>{$addresses[address].alias|escape:'htmlall':'UTF-8'}</option>
			{/section}
			</select>
			{else}
				{if $back}
					<a style="margin-left: 221px;" href="{$link->getPageLink('address', true, NULL, "back=order&amp;step=1&amp;select_address=1&mod=$back")}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
				{else}
					<a style="margin-left: 221px;" href="{$link->getPageLink('address', true, NULL, "back=order&amp;step=1&amp;select_address=1")}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
				{/if}
			{/if}
		</p>
		<div class="clear"></div>
		<ul class="address item" id="address_delivery">
		</ul>
		<ul class="address alternate_item" id="address_invoice">
		</ul>
		<br class="clear" />
		<p class="address_add submit">
		{if $back}
			<a href="{$link->getPageLink('address', true, NULL, "back=order&amp;step=1&amp;mod={$back}")}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
		{else}
			<a href="{$link->getPageLink('address', true, NULL, "back=order&amp;step=1")}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
		{/if}
		</p>
		{if !$opc}
		<div id="ordermsg">
			<p>{l s='If you would like to add a comment about your order, please write it below.'}</p>
			<p class="textarea"><textarea cols="60" rows="3" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea></p>
		</div>
		{/if}
	</div>
{if !$opc}
	<p class="cart_navigation submit">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="{$back}" />
		{if $back}
			<a href="{$link->getPageLink('order', true, NULL, "step=0&amp;back={$back}")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a>
		{else}
			<a href="{$link->getPageLink('order', true, NULL, "step=0")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a>
		{/if}
		<input type="submit" name="processAddress" value="{l s='Next'} &raquo;" class="exclusive" />
	</p>
</form>
{else}
</div>
{/if}
