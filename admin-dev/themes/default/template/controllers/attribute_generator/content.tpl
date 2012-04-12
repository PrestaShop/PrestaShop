{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	i18n_tax_exc = "{l s='Tax Excl.:'}";
	i18n_tax_inc = "{l s='Tax Incl.:'}";

	var product_tax = "{$tax_rates}";
	function calcPrice(element, element_has_tax)
	{
			name = element.attr("name");
			var element_price = element.val().replace(/,/g, ".");
			var other_element_price = 0;

			if (!isNaN(element_price) && element_price > 0)
			{
				if (element_has_tax)
					other_element_price = parseFloat(element_price / ((product_tax / 100) + 1));
				else
					other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2);

				other_element_price = other_element_price.toFixed(2);
			}

			$("#related_to_"+name).val(other_element_price);
	}


	$(document).ready(function()
	{
		$(".price_impact").each(function()
		{
			calcPrice($(this), false);
		});
	});
</script>


{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>


{if $generate}
	<div class="module_confirmation conf confirm">
		{$combinations_size} {l s='product(s) successfully created.'}
	</div>
{/if}
<script type="text/javascript" src="../js/attributesBack.js"></script>
<form enctype="multipart/form-data" method="post" id="generator" action="{$url_generator}">
	<fieldset style="margin-bottom: 35px;">
		<legend><img src="../img/admin/asterisk.gif" />{l s='Attributes generator'}</legend>
        <div style="display: block; margin-bottom: 20px;" class="hint">{l s='To generate the attributes, hold down the "Ctrl" key, select your attributes and click "Add".'}</div>
		<div style="float: left; margin-right:50px;">
			<div>
				<select multiple name="attributes[]" id="attribute_group" style="width: 200px; height: 350px; margin-bottom: 10px;">
					{foreach $attribute_groups as $k => $attribute_group}
						{if isset($attribute_js[$attribute_group['id_attribute_group']])}
							<optgroup name="{$attribute_group['id_attribute_group']}" id="{$attribute_group['id_attribute_group']}" label="{$attribute_group['name']|escape:'htmlall':'UTF-8'}">
								{foreach $attribute_js[$attribute_group['id_attribute_group']] as $k => $v}
									<option name="{$k}" id="attr_{$k}" value="{$v|escape:'htmlall':'UTF-8'}" title="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</optgroup>
						{/if}
					{/foreach}
				</select>
			</div>
			<div>
				<p style="text-align: center;">
					<input class="button" type="button" style="margin: 0 0 10px 20px;" value="{l s='Add'}" class="button" onclick="add_attr_multiple();" />
					<input class="button" type="button" style="margin: 0 0 10px 20px;" value="{l s='Delete'}" class="button" onclick="del_attr_multiple();" /><br />
				</p>
			</div>
		</div>
		<br />
		{l s='Add or modify attributes for:'} <b>{$product_name}</b>
		<br /><br />
		<div style="padding-top:10px; float: left; width: 570px;">
			<div style="float:left;">
				<label>{l s='Quantity'}</label>
				<div class="margin-form">
					<input type="text" size="20" name="quantity" value="0"/>
				</div>
				<label>{l s='Reference'}</label>
				<div class="margin-form">
					<input type="text" size="20" name="reference" value="{$product_reference|escape:'htmlall':'UTF-8'}"/>
				</div>
			</div>
			<div style="float:left; text-align:center; margin-left:20px;">
				<input type="submit" class="button" style="margin-bottom:5px;" name="generate" value="{l s='Generate'}" /><br />
			</div>
			<br style="clear:both;" />
			<div style="margin-top: 15px;">
				{foreach $attribute_groups as $k => $attribute_group}
					{if isset($attribute_js[$attribute_group['id_attribute_group']])}
						<br class="clear"/>
						<table class="table" cellpadding="0" cellspacing="0" align="left" style="margin-bottom: 10px; display: none;">
							<thead>
								<tr>
									<th id="tab_h1" style="width: 150px">{$attribute_group['name']|escape:'htmlall':'UTF-8'}</th>
									<th id="tab_h2" style="width: 350px" colspan="2">{l s='Price impact'} {$currency_sign}</th>
									<th style="width: 150px">{l s='Weight impact'} ({$weight_unit})</th>
								</tr>
							</thead>
							<tbody id="table_{$attribute_group['id_attribute_group']}" name="result_table">
							</tbody>
						</table>
						{if isset($attributes[$attribute_group['id_attribute_group']])}
							{foreach $attributes[$attribute_group['id_attribute_group']] AS $k => $attribute}
								<script type="text/javascript">
									$('#table_{$attribute_group['id_attribute_group']}').append(create_attribute_row({$k}, {$attribute_group['id_attribute_group']}, '{$attribute['attribute_name']|addslashes}', {$attribute['price']}, {$attribute['weight']}));
									toggle(getE('table_' + {$attribute_group['id_attribute_group']}).parentNode, true);
								</script>
							{/foreach}						
						{/if}
					{/if}
				{/foreach}
            </div>
		</div>
	</fieldset>
</form>