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

<script type="text/javascript">
var Customer = new Object();
var product_url = '{$link->getAdminLink('AdminProducts', true)|addslashes}';
var ecotax_tax_excl = parseFloat({$ecotax_tax_excl});
$(document).ready(function () {
	Customer = {
		"hiddenField": jQuery('#id_customer'),
		"field": jQuery('#customer'),
		"container": jQuery('#customers'),
		"loader": jQuery('#customerLoader'),
		"init": function() {
			jQuery(Customer.field).typeWatch({
				"captureLength": 1,
				"highlight": true,
				"wait": 50,
				"callback": Customer.search
			}).focus(Customer.placeholderIn).blur(Customer.placeholderOut);
		},
		"placeholderIn": function() {
			if (this.value == '{l s='All customers'}') {
				this.value = '';
			}
		},
		"placeholderOut": function() {
			if (this.value == '') {
				this.value = '{l s='All customers'}';
			}
		},
		"search": function()
		{
			Customer.showLoader();
			jQuery.ajax({
				"type": "POST",
				"url": "{$link->getAdminLink('AdminCustomers')|addslashes}",
				"async": true,
				"dataType": "json",
				"data": {
					"ajax": "1",
					"token": "{getAdminToken tab='AdminCustomers'}",
					"tab": "AdminCustomers",
					"action": "searchCustomers",
					"customer_search": Customer.field.val()
				},
				"success": Customer.success
			});
		},
		"success": function(result)
		{
			if(result.found) {
				var html = '<ul class="clearfix">';
				jQuery.each(result.customers, function() {
					html += '<li><a class="fancybox" href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a>'+(this.birthday ? ' - '+this.birthday:'')+'<br/>';
					html += '<a href="mailto:'+this.email+'">'+this.email+'</a><br />';
					html += '<a onclick="Customer.select('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false;" href="#" class="button">{l s='Choose'}</a></li>';
				});
				html += '</ul>';
			}
			else
				html = '<div class="alert alert-block">{l s='No customers found'}</div>';
			Customer.hideLoader();
			Customer.container.html(html);
			jQuery('.fancybox', Customer.container).fancybox();
		},
		"select": function(id_customer, fullname)
		{
			Customer.hiddenField.val(id_customer);
			Customer.field.val(fullname);
			Customer.container.empty();
			return false;
		},
		"showLoader": function() {
			Customer.loader.fadeIn();
		},
		"hideLoader": function() {
			Customer.loader.fadeOut();
		}
	};
	Customer.init();
});
</script>

<input type="hidden" name="submitted_tabs[]" value="Prices" />
<legend>{l s='Product price'}</legend>
<div class="alert alert-info">
	{l s='You must enter either the pre-tax retail price, or the retail price with tax. The input field will be automatically calculated.'}
</div>

{include file="controllers/products/multishop/check_fields.tpl" product_tab="Prices"}

{include file="controllers/products/multishop/checkbox.tpl" field="wholesale_price" type="default"}
<div class="row">
	<label class="control-label col-lg-3" for="wholesale_price">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Wholesale price'}">
			{l s='Pre-tax wholesale price:'}
		</span>
		
	</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
		<input maxlength="14" name="wholesale_price" id="wholesale_price" type="text" value="{{toolsConvertPrice price=$product->wholesale_price}|string_format:'%.2f'}" onchange="this.value = this.value.replace(/,/g, '.');" />
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="price" type="price"}
	<label class="control-label col-lg-3" for="priceTE">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='The pre-tax retail price to sell this product'}">
			{l s='Pre-tax retail price:'}
		</span>
	</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
		<input type="hidden"  id="priceTEReal" name="price" value="{toolsConvertPrice price=$product->price}" />
		<input size="11" maxlength="14" id="priceTE" name="price_displayed" type="text" value="{{toolsConvertPrice price=$product->price}|string_format:'%.2f'}" onchange="noComma('priceTE'); $('#priceTEReal').val(this.value);" onkeyup="$('#priceType').val('TE'); $('#priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return; calcPriceTI();" />
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="id_tax_rules_group" type="default"}
	<label class="control-label col-lg-3" for="id_tax_rules_group">{l s='Tax rule:'}</label>
	<div class="col-lg-8">
		<script type="text/javascript">
			noTax = {if $tax_exclude_taxe_option}true{else}false{/if};
			taxesArray = new Array ();
			taxesArray[0] = 0;
			{foreach $tax_rules_groups as $tax_rules_group}
				{if isset($taxesRatesByGroup[$tax_rules_group['id_tax_rules_group']])}
				taxesArray[{$tax_rules_group.id_tax_rules_group}] = {$taxesRatesByGroup[$tax_rules_group['id_tax_rules_group']]};
					{else}
				taxesArray[{$tax_rules_group.id_tax_rules_group}] = 0;
				{/if}
			{/foreach}
			ecotaxTaxRate = {$ecotaxTaxRate / 100};
		</script>
		<div class="row">
			<div class="col-lg-6">
				<select onChange="javascript:calcPrice(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
					<option value="0">{l s='No Tax'}</option>
				{foreach from=$tax_rules_groups item=tax_rules_group}
					<option value="{$tax_rules_group.id_tax_rules_group}" {if $product->getIdTaxRulesGroup() == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
				{$tax_rules_group['name']|htmlentitiesUTF8}
					</option>
				{/foreach}
				</select>
			</div>
			<div class="col-lg-2">
				<a class="btn btn-link confirm_leave" href="{$link->getAdminLink('AdminTaxRulesGroup')|escape:'htmlall':'UTF-8'}&addtax_rules_group&id_product={$product->id}" {if $tax_exclude_taxe_option}disabled="disabled"{/if}>
					<i class="icon-plus-sign"></i>  {l s='Create new taxe'} <i class="icon-external-link-sign"></i>
				</a>
			</div>
		</div>
	</div>
</div>

{if $tax_exclude_taxe_option}
<div class="row">
	<div class="col-lg-9 col-offset-3">
		<div class="alert">
			{l s='Taxes are currently disabled'} : 
			<a href="{$link->getAdminLink('AdminTaxes')|escape:'htmlall':'UTF-8'}">{l s='Tax options'}</a>
			<input type="hidden" value="{$product->getIdTaxRulesGroup()}" name="id_tax_rules_group" />
		</div>
	</div>
</div>
{/if}

<div class="row" {if !$ps_use_ecotax} style="display:none;"{/if}>
	{include file="controllers/products/multishop/checkbox.tpl" field="ecot" type="default"}
	<label class="control-label col-lg-3" for="ecotax">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='already included in price'}">
			{l s='Eco-tax (tax incl.):'}
		</span>
	</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
		<input maxlength="14" id="ecotax" name="ecotax" type="text" value="{$product->ecotax|string_format:'%.2f'}" onkeyup="$('#priceType').val('TI');if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" />
	</div>
</div>

<div class="row" {if !$country_display_tax_label || $tax_exclude_taxe_option}style="display:none;"{/if} >
	<label class="control-label col-lg-3" for="priceTI">{l s='Retail price with tax:'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
		<input id="priceType" name="priceType" type="hidden" value="TE" />
		<input maxlength="14" id="priceTI" type="text" value="" onchange="noComma('priceTI');" onkeyup="$('#priceType').val('TI');if (isArrowKey(event)) return;  calcPriceTE();" />
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="unit_price" type="unit_price"}
	<label class="control-label col-lg-3" for="unit_price">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='e.g. per lb.'}">
			{l s='Unit price:'}
		</span>
	</label>
	<div class="input-group col-lg-4">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
		<input maxlength="14" id="unit_price" name="unit_price" type="text" value="{$unit_price|string_format:'%.2f'}" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/>
		<span class="input-group-addon">{l s='per'}</span>
		<input maxlength="10" id="unity" name="unity" type="text" value="{$product->unity|htmlentitiesUTF8}" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
	</div>
</div>

{if $ps_tax && $country_display_tax_label}
<div class="row">
	<div class="col-lg-9 col-offset-3">
		<div class="alert">
			<span>{l s='or'}
				{$currency->prefix}<span id="unit_price_with_tax">0.00</span>{$currency->suffix}
				{l s='per'} <span id="unity_second">{$product->unity}</span> {l s='with tax'}
			</span>
		</div>
	</div>
</div>
{/if}

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="on_sale" type="default"}
	<div class="col-lg-9 col-offset-3">
		<div class="checkbox">
			<label class="control-label" for="on_sale" >
				<input type="checkbox" name="on_sale" id="on_sale" {if $product->on_sale}checked="checked"{/if} value="1" />
				{l s='Display the "on sale" icon on the product page, and in the text found within the product listing.'}
			</label>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-9 col-offset-3">
		<div class="alert">
			<strong>{l s='Final retail price:'}</strong>
			<span {if !$country_display_tax_label}style="display:none"{/if} >
				{$currency->prefix}
				<span id="finalPrice" >0.00</span>
				{$currency->suffix}
				<span {if $ps_tax}style="display:none;"{/if}> ({l s='tax incl.'})</span>
			</span>
			<span {if $ps_tax}style="display:none;"{/if} >
			{if $country_display_tax_label}
				/
			{/if}
				{$currency->prefix}
			<span id="finalPriceWithoutTax"></span>
				{$currency->suffix}
				{if $country_display_tax_label}({l s='tax excl.'}){/if}
			</span>
		</div>
	</div>
</div>



{if isset($specificPriceModificationForm)}
<legend>{l s='Specific prices'}</legend>
<div class="alert alert-info">
	{l s='You can set specific prices for clients belonging to different groups, different countries, etc...'}
</div>

<div class="row">
	<div class="col-lg-12">
		<a class="btn btn-default" href="#" id="show_specific_price">
			<i class="icon-plus-sign"></i> {l s='Add a new specific price'}
		</a>
		<a class="btn btn-default" href="#" id="hide_specific_price" style="display:none">
			<i class="icon-remove"></i> {l s='Cancel new specific price'}
		</a>
	</div>
</div>

<script type="text/javascript">
	var product_prices = new Array();
	{foreach from=$combinations item='combination'}
		product_prices['{$combination.id_product_attribute}'] = '{$combination.price}';
	{/foreach}
</script>

<div id="add_specific_price" class="well" style="display: none;">
	<div class="col-lg-12">

		<div class="row">
			<label class="control-label col-lg-3" for="{if !$multi_shop}spm_currency_0{else}sp_id_shop{/if}">{l s='For:'}</label>
			<div class="col-lg-9">
				<div class="row">
				{if !$multi_shop}
					<input type="hidden" name="sp_id_shop" value="0" />
				{else}
					<div class="col-lg-3">
						<select name="sp_id_shop" id="sp_id_shop">
							{if !$admin_one_shop}<option value="0">{l s='All shops'}</option>{/if}
							{foreach from=$shops item=shop}
							<option value="{$shop.id_shop}">{$shop.name|htmlentitiesUTF8}</option>
							{/foreach}
						</select>
					</div>
				{/if}
					<div class="col-lg-3">
						<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);">
							<option value="0">{l s='All currencies'}</option>
							{foreach from=$currencies item=curr}
							<option value="{$curr.id_currency}">{$curr.name|htmlentitiesUTF8}</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-3">
						<select name="sp_id_country" id="sp_id_country">
							<option value="0">{l s='All countries'}</option>
							{foreach from=$countries item=country}
							<option value="{$country.id_country}">{$country.name|htmlentitiesUTF8}</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-3">
						<select name="sp_id_group" id="sp_id_group">
							<option value="0">{l s='All groups'}</option>
							{foreach from=$groups item=group}
							<option value="{$group.id_group}">{$group.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3" for="customer">{l s='Customer:'}</label>
			<div class="col-lg-9">
				<input type="hidden" name="sp_id_customer" id="id_customer" value="0" />
				<input type="text" name="customer" value="{l s='All customers'}" id="customer" autocomplete="off" />
			</div>
			<img src="../img/admin/field-loader.gif" id="customerLoader" alt="{l s='Loading...'}" style="display: none;" />
			<div id="customers"></div>
		</div>

		{if $combinations|@count != 0}
		<div class="row">
			<label class="control-label col-lg-3" for="sp_id_product_attribute">{l s='Combination:'}</label>
			<div class="col-lg-4">
				<select id="sp_id_product_attribute" name="sp_id_product_attribute">
					<option value="0">{l s='Apply to all combinations'}</option>
				{foreach from=$combinations item='combination'}
					<option value="{$combination.id_product_attribute}">{$combination.attributes}</option>
				{/foreach}
				</select>
			</div>
		</div>
		{/if}

		<div class="row">
			<label class="control-label col-lg-3" for="sp_from">{l s='Available from:'}</label>
			<div class="input-group col-lg-4">
				<input class="datepicker" type="text" name="sp_from" value="" style="text-align: center" id="sp_from" />
				<span class="input-group-addon">{l s='to'}</span>
				<input class="datepicker" type="text" name="sp_to" value="" style="text-align: center" id="sp_to" />
			</div>
		</div>
		
		<div class="row">
			<label class="control-label col-lg-3" for="sp_from_quantity">{l s='Starting at'}</label>
			<div class="input-group col-lg-4">
				<span class="input-group-addon">{l s='unit'}</span>
				<input type="text" name="sp_from_quantity" id="sp_from_quantity" value="1" />
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3" for="sp_price">{l s='Product price'}
				{if $country_display_tax_label}
					{l s='(tax excl.):'}
				{/if}
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="input-group col-lg-4">
						<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
						<input type="text" disabled="disabled" name="sp_price" id="sp_price" value="{$product->price|string_format:'%.2f'}" />
					</div>
					<div class="col-lg-8">
						<p class="checkbox">
							<label for="leave_bprice">{l s='Leave base price:'}</label>
							<input id="leave_bprice" type="checkbox" value="1" checked="checked" name="leave_bprice" />
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<label class="control-label col-lg-3" for="sp_reduction">{l s='Apply a discount of:'}</label>
			<div class="col-lg-4">
				<div class="row">
					<div class="col-lg-6">
						<input type="text" name="sp_reduction" id="sp_reduction" value="0.00"/>
					</div>
					<div class="col-lg-6">
						<select name="sp_reduction_type" id="sp_reduction_type">
							<option selected="selected">---</option>
							<option value="amount">{l s='Amount'}</option>
							<option value="percentage">{l s='Percentage'}</option>
						</select>
					</div>
				</div>
			</div>
			<p class="help-block">{l s='The discount is applied after the tax'}</p>
		</div>
	
	</div>	
</div>

<script type="text/javascript">
	$(document).ready(function(){
		product_prices['0'] = $('#sp_current_ht_price').html();
		$('#id_product_attribute').change(function() {
			$('#sp_current_ht_price').html(product_prices[$('#id_product_attribute option:selected').val()]);
		});
		$('#leave_bprice').click(function() {
			if (this.checked)
				$('#sp_price').attr('disabled', 'disabled');
			else
				$('#sp_price').removeAttr('disabled');
		});
		$('.datepicker').datetimepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',
			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText: '{l s='Now'}',
			closeText: '{l s='Done'}',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: '{l s='Choose Time'}',
			timeText: '{l s='Time'}',
			hourText: '{l s='Hour'}',
			minuteText: '{l s='Minute'}',
		});
	});
</script>

<table id="specific_prices_list" class="table">
	<thead>
		<tr>
			<th class="cell border" >{l s='Rule'}</th>
			<th class="cell border" >{l s='Combination'}</th>
			{if $multi_shop}<th>{l s='Shop'}</th>{/if}
			<th class="cell border">{l s='Currency'}</th>
			<th class="cell border">{l s='Country'}</th>
			<th class="cell border">{l s='Group'}</th>
			<th class="cell border">{l s='Customer'}</th>
			<th class="cell border">{l s='Fixed price'}</th>
			<th class="cell border">{l s='Impact'}</th>
			<th class="cell border">{l s='Period'}</th>
			<th class="cell border">{l s='From (quantity)'}</th>
			<th class="cell border">{l s='Action'}</th>
		</tr>
	</thead>
	<tbody>
		{$specificPriceModificationForm}
			<script type="text/javascript">
				$(document).ready(function() {
					calcPriceTI();
					unitPriceWithTax('unit');
					});
			</script>
		{/if}