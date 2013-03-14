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
	
	var addressMultishippingUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back|urlencode}{/if}")}";
	var addressUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")}";

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
	{
		return {
			'invoice': "<span style=\"font-weight:bold;font-size:1.2em;padding-bottom:5px;display:block\">{l s='Your billing address' js=1}</span>",
			'delivery': "<span style=\"font-weight:bold;font-size:1.2em;padding-bottom:5px;display:block\">{l s='Your delivery address' js=1}</span>"
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
		li_content['update'] = '<a href="{$link->getPageLink('address', true, NULL, "id_address")}'+id_address+'&amp;back={$back_order_page}?step=1{if $back}&mod={$back}{/if}" title="{l s='Update' js=1}" style="font-size:.9em;font-weight:normal;padding:8px 0;display:block;text-align:right">&raquo; {l s='Update' js=1}</a>';

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

{if isset($empty)}
<p class="warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
<p class="warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<div id="displayMobileShoppingCartTop">
		{hook h="displayMobileShoppingCartTop"}
	</div>
	<div data-role="content" id="content" class="cart">
		{include file="$tpl_dir./errors.tpl"}

		<h2>{l s='Addresses'}</h2>
		{if !$opc}
			{assign var='current_step' value='address'}
			{include file="$tpl_dir./errors.tpl"}
			
			{if !$multi_shipping && {Configuration::get('PS_ALLOW_MULTISHIPPING')} && !$cart->isVirtualCart()}
				<div class="button_multishipping_mode" id="multishipping_mode_box">
					<div class="title">{l s='Multi-shipping'}</div>
					<div class="description">
						<a href="{$link->getPageLink('order', true, NULL, 'step=1&multi-shipping=1')}"/>
							{l s='Specify a delivery address for each product ordered.'}
						</a>
					</div>
				</div>
			{/if}
		<form action="{$link->getPageLink($back_order_page, true)}" method="post" data-ajax="false">
		{else}
			{if {Configuration::get('PS_ALLOW_MULTISHIPPING')} && !$cart->isVirtualCart()}
				<div class="address-form-multishipping">
					<div class="button_multishipping_mode" id="multishipping_mode_box">
						<div class="title">{l s='Multi-shipping'}</div>
						<div class="description">
							<input type="checkbox" id="multishipping_mode_checkbox" onchange="multishippingMode(this); return false;"/><label for="multishipping_mode_checkbox">{l s='I\'d like to specify a delivery address for each product ordered.'}</label>
						</div>
						<div class="description_off">
							<a href="{$link->getPageLink('order-opc', true, NULL, 'ajax=1&multi-shipping=1&method=multishipping')}" id="link_multishipping_form" title="{l s='Choose the delivery address(es)'}">
								{l s='Specify a delivery address for each product.'}
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
				<p class="address_delivery select">
					<label for="id_address_delivery">{if $cart->isVirtualCart()}{l s='Choose a billing address:'}{else}{l s='Choose a delivery address:'}{/if}</label>
					<select name="id_address_delivery" id="id_address_delivery" class="address_select" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}">
		
					{foreach from=$addresses key=k item=address}
						<option value="{$address.id_address|intval}" {if $address.id_address == $cart->id_address_delivery}selected="selected"{/if}>{$address.alias|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
					
					</select>
				</p>
				<p class="checkbox addressesAreEquals" {if $cart->isVirtualCart()}style="display:none;"{/if}>
					<input type="checkbox" name="same" id="addressesAreEquals" value="1" onclick="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $cart->id_address_invoice == $cart->id_address_delivery || $addresses|@count == 1}checked="checked"{/if} />
					<label for="addressesAreEquals">{l s='Use the delivery address as the billing address.'}</label>
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
						<a style="margin-left: 221px;" href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1&select_address=1{if $back}&mod={$back}{/if}")}" title="{l s='Add'}" class="button_large">{l s='Add a new address'}</a>
					{/if}
				</p>
			</div>
			<div class="addresses">
				<div class="ui-grid-a same-height">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-c">
							<ul class="address item" id="address_delivery" {if $cart->isVirtualCart()}style="display:none;margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"{else}style="margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"{/if}></ul>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-c">
							<ul class="address alternate_item {if $cart->isVirtualCart()}full_width{/if}" id="address_invoice" style="margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"></ul>
						</div>
					</div>
				</div>
				<p class="address_add submit">
					<a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")}" title="{l s='Add'}" class="button_large" data-role="button" data-icon="plus">{l s='Add a new address'}</a>
				</p>
				{if !$opc}
				<div id="ordermsg" class="clearfix">
					<p class="txt">{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
					<p class="textarea"><textarea cols="60" rows="3" name="message">{if isset($oldMessage)}{$oldMessage}{/if}</textarea></p>
				</div>
				{/if}
			</div>
		{if !$opc}
			<fieldset class="cart_navigation submit ui-grid-a">
				<input type="hidden" class="hidden" name="step" value="2" />
				<input type="hidden" name="back" value="{$back}" />
				<div class="ui-block-a"><a href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" data-role="button" data-icon="back" data-ajax="false">&laquo; {l s='Previous'}</a></div>
				<div class="ui-block-b"><input type="submit" name="processAddress" value="{l s='Next'}" class="exclusive" data-icon="check" data-iconpos="right" data-theme="b" /></div>
			</fieldset>
		</form>
		{else}
		</div>
		{/if}
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		{hook h="displayMobileShoppingCartBottom"}
	</div>
{/if}