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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
{else}
	{assign var="back_order_page" value="order.php"}
{/if}

<script type="text/javascript">
// <![CDATA[
	{if !$opc}
	var orderProcess = 'order';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product' js=1}";
	var txtProducts = "{l s='products' js=1}";
	{/if}
	var CloseTxt = '{l s='Submit' js=1}';
	var addressMultishippingUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back|urlencode}{/if}")|addslashes}";
	var addressUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")|addslashes}";

	var formatedAddressFieldsValuesList = new Array();

	{foreach from=$formatedAddressFieldsValuesList key=id_address item=type}
		formatedAddressFieldsValuesList[{$id_address}] =
		{ldelim}
			'ordered_fields':[
				{foreach from=$type.ordered_fields key=num_field item=field_name name=inv_loop}
					{if !$smarty.foreach.inv_loop.first},{/if}{$field_name|json_encode}
				{/foreach}
			],
			'formated_fields_values':{ldelim}
					{foreach from=$type.formated_fields_values key=pattern_name item=field_name name=inv_loop}
						{if !$smarty.foreach.inv_loop.first},{/if}{$pattern_name|json_encode}:{$field_name|json_encode}
					{/foreach}
				{rdelim}
		{rdelim}
	{/foreach}

	function getAddressesTitles()
	{
		return {
			'invoice': "<h3 class='page-subheading'>{l s='Your billing address' js=1}</h3>",
			'delivery': "<h3 class='page-subheading'>{l s='Your delivery address' js=1}</h3>"
		};
	}

	function buildAddressBlock(id_address, address_type, dest_comp)
	{
		if (isNaN(id_address))
			return;

		var adr_titles_vals = getAddressesTitles();
		var li_content = formatedAddressFieldsValuesList[id_address]['formated_fields_values'];
		var ordered_fields_name = ['title'];

		ordered_fields_name = ordered_fields_name.concat(formatedAddressFieldsValuesList[id_address]['ordered_fields']);
		ordered_fields_name = ordered_fields_name.concat(['update']);

		dest_comp.html('');

		li_content['title'] = adr_titles_vals[address_type];
		li_content['update'] = '<a class="button button-small btn btn-default" href="{$link->getPageLink('address', true, NULL, "id_address")|addslashes}'+id_address+'&amp;back={$back_order_page}?step=1{if $back}&mod={$back}{/if}" title="{l s='Update' js=1}"><span>{l s='Update' js=1}<i class="icon-chevron-right right"></i></span></a>';

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
		{
			items[field_item] = items[field_item].replace(",", "");
			vals.push(values[items[field_item]]);
		}
		return vals.join(" ");
	}

//]]>
</script>

{if !$opc}
{capture name=path}{l s='Addresses'}{/capture}
{/if}

{if !$opc}<h1 class="page-heading">{l s='Addresses'}</h1>{else}<h1 class="page-heading step-num"><span>1</span> {l s='Addresses'}</h1>{/if}

{if !$opc}
	{assign var='current_step' value='address'}
	{include file="$tpl_dir./order-steps.tpl"}
	{include file="$tpl_dir./errors.tpl"}
	
	{if !$multi_shipping && {Configuration::get('PS_ALLOW_MULTISHIPPING')} && !$cart->isVirtualCart()}
		<div class="button_multishipping_mode box" id="multishipping_mode_box">
			<div class="title">{l s='Multi-shipping'}</div>
			<div class="description">
				<a class="btn btn-default button exclusive-medium" href="{$link->getPageLink('order', true, NULL, 'step=1&multi-shipping=1')|escape:'html'}"/><span>{l s='Specify a delivery address for each product ordered.'}</span></a>
			</div>
		</div>
	{/if}
<form action="{$link->getPageLink($back_order_page, true)|escape:'html'}" method="post">
{else}
	{if {Configuration::get('PS_ALLOW_MULTISHIPPING')} && !$cart->isVirtualCart()}
		<div class="address-form-multishipping">
			<div class="button_multishipping_mode box" id="multishipping_mode_box">
				<div class="title">{l s='Multi-shipping'}</div>
				<div class="description checkbox">
					<label for="multishipping_mode_checkbox"><input type="checkbox" id="multishipping_mode_checkbox" onchange="multishippingMode(this); return false;" autocomplete="off"/>{l s='I\'d like to specify a delivery address for each product ordered.'}</label>
				</div>
				<div class="description_off">
					<a class="btn btn-default button exclusive-medium" href="{$link->getPageLink('order-opc', true, NULL, 'ajax=1&multi-shipping=1&method=multishipping')|escape:'html'}" id="link_multishipping_form" title="{l s='Choose the delivery address(es)'}">
						<span>{l s='Specify a delivery address for each product.'}</span>
					</a>
				</div>
			</div>
			<script type="text/javascript">
				{if $is_multi_address_delivery}
				var multishipping_mode = true;
				{else}
				var multishipping_mode = false;
				{/if}
				var open_multishipping_fancybox = {$open_multishipping_fancybox|intval};
			</script>
		</div>
	{/if}
<div id="opc_account" class="opc-main-block">
	<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
{/if}
	<div class="addresses clearfix">
    	<div class="row">
        	<div class="col-xs-12 col-sm-6">
            	<div class="address_delivery select form-group selector1">
                <label for="id_address_delivery">{if $cart->isVirtualCart()}{l s='Choose a billing address:'}{else}{l s='Choose a delivery address:'}{/if}</label>
                <select name="id_address_delivery" id="id_address_delivery" class="address_select form-control" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">
    
                {foreach from=$addresses key=k item=address}
                    <option value="{$address.id_address|intval}" {if $address.id_address == $cart->id_address_delivery}selected="selected"{/if}>{$address.alias|escape:'html':'UTF-8'}</option>
                {/foreach}
                
                </select>
            </div>
                <p class="checkbox addressesAreEquals" {if $cart->isVirtualCart()}style="display:none;"{/if}>
                <input type="checkbox" name="same" id="addressesAreEquals" value="1" onclick="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}"{if $cart->id_address_invoice == $cart->id_address_delivery || $addresses|@count == 1} checked="checked"{/if} autocomplete="off"/>
                <label for="addressesAreEquals">{l s='Use the delivery address as the billing address.'}</label>
            </p>
            </div>
            <div class="col-xs-12 col-sm-6">
            	<div id="address_invoice_form" class="select form-group selector1" {if $cart->id_address_invoice == $cart->id_address_delivery}style="display: none;"{/if}>
    
                {if $addresses|@count > 1}
                        <label for="id_address_invoice" class="strong">{l s='Choose a billing address:'}</label>
                        <select name="id_address_invoice" id="id_address_invoice" class="address_select form-control" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">
                        {section loop=$addresses step=-1 name=address}
                            <option value="{$addresses[address].id_address|intval}" {if $addresses[address].id_address == $cart->id_address_invoice && $cart->id_address_delivery != $cart->id_address_invoice}selected="selected"{/if}>{$addresses[address].alias|escape:'html':'UTF-8'}</option>
                        {/section}
                        </select>
                    {else}
                        <a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1&select_address=1{if $back}&mod={$back}{/if}")|escape:'html'}" title="{l s='Add'}" class="button button-small btn btn-default"><span>{l s='Add a new address'}<i class="icon-chevron-right right"></i></span></a>
                    {/if}
            	</div>
            </div>
        </div>	
		<div class="row">
        	<div class="col-xs-12 col-sm-6">
                <ul class="address item box" id="address_delivery" {if $cart->isVirtualCart()}style="display:none;"{/if}>
                </ul>
            </div>
            <div class="col-xs-12 col-sm-6">
                <ul class="address alternate_item {if $cart->isVirtualCart()}full_width{/if} box" id="address_invoice">
                </ul>
            </div>
		</div>
		<p class="address_add submit">
			<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")|escape:'html'}" title="{l s='Add'}" class="button button-small btn btn-default"><span>{l s='Add a new address'}<i class="icon-chevron-right right"></i></span></a>
		</p>
		{if !$opc}
		<div id="ordermsg" class="form-group">
			<label>{l s='If you would like to add a comment about your order, please write it in the field below.'}</label>
			<textarea class="form-control" cols="60" rows="6" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea>
		</div>
		{/if}
	</div>
{if !$opc}
	<p class="cart_navigation clearfix">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="{$back}" />
		<a href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")|escape:'html'}" title="{l s='Previous'}" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Continue Shopping'}</a>
        <button type="submit" name="processAddress" class="button btn btn-default button-medium"><span>{l s='Proceed to checkout'}<i class="icon-chevron-right right"></i></span></button>
	</p>
</form>
{else}
</div>
{/if}
