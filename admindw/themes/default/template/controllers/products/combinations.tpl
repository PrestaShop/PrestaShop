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
*  @version  Release: $Revision: 17797 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var msg_combination_1 = '{l s='Please choose an attribute'}';
	var msg_combination_2 = '{l s='Please choose a value'}';
	var msg_combination_3 = '{l s='You can only add one combination per type of attribute'}';
	var msg_new_combination = '{l s='New combination'}';
</script>

{if isset($product->id) && !$product->is_virtual}
	<input type="hidden" name="submitted_tabs[]" value="Combinations" />
	<script type="text/javascript">
		var attrs = new Array();
		var modifyattributegroup = "{l s='Modify this attribute combination' js=1}";
		attrs[0] = new Array(0, "---");
	{foreach from=$attributeJs key=idgrp item=group}
		attrs[{$idgrp}] = new Array(0
		, '---'
		{foreach from=$group key=idattr item=attrname}
			, "{$idattr}", "{$attrname|addslashes}"
		{/foreach}
		);
	{/foreach}
	</script>
	<h4>{l s='Add or modify combinations for this product'}</h4>
	<div class="separation"></div> {l s='or use the'}
		&nbsp;<a class="button bt-icon confirm_leave" href="index.php?tab=AdminAttributeGenerator&id_product={$product->id}&attributegenerator&token={$token_generator}"><img src="../img/admin/appearance.gif" alt="combinations_generator" class="middle" title="{l s='Product combinations generator'}" /><span>{l s='Product combinations generator'}</span></a> 
		{l s='in order to create automatically a set of combinations'}
	{if $combination_exists}
	<div class="warn" style="display:block">
		<ul>
			<li>{l s='Some combinations already exist. If you want to generate some new combinations, the quantities for the actual combinations will be lost.'}</li>
			<li>{l s='You still can add a combination by clicking the link "Add new combination" on the toolbar.'}</li>
		</ul>
	</div>
	{/if}
	{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
		<br />
		{include file="controllers/products/multishop/check_fields.tpl" product_tab="Combinations"}
	{/if}
	<div class="separation"></div>
	
	<div id="add_new_combination" style="display: none;">
		<table cellpadding="5" style="width:100%">
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
					<label>{l s='Attribute:'}</label>
				</td>
				<td style="padding-bottom:5px;">
					<select name="attribute_group" id="attribute_group" style="width: 200px;" onchange="populate_attrs();">
						{if isset($attributes_groups)}
							{foreach from=$attributes_groups key=k item=attribute_group}
								<option value="{$attribute_group.id_attribute_group}">{$attribute_group.name|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
							{/foreach}
						{/if}
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
					<label>{l s='Value:'}</label>
				</td>
				<td style="padding-bottom:5px;">
					<select name="attribute" id="attribute" style="width: 200px;">
						<option value="0">---</option>
					</select>
					<script type="text/javascript">
					$(document).ready(function(){
						populate_attrs();
					});
					</script>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" valign="top">
				<input style="width: 140px; margin-bottom: 10px;" type="button" value="{l s='Add'}" class="button" onclick="add_attr();"/><br />
				<input style="width: 140px;" type="button" value="{l s='Delete'}" class="button" onclick="del_attr()"/></td>
				<td align="left">
					<select id="product_att_list" name="attribute_combination_list[]" multiple="multiple" size="4" style="width: 320px;"></select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;"><label>{l s='Reference:'}</label></td>
				<td style="padding-bottom:5px;">
					<input size="55" type="text" id="attribute_reference" name="attribute_reference" value="" style="width: 130px; margin-right: 44px;" />
					{l s='EAN13:'}<input size="55" maxlength="13" type="text" id="attribute_ean13" name="attribute_ean13" value="" style="width: 110px; margin-left: 10px; margin-right: 44px;" />
					{l s='UPC:'}<input size="55" maxlength="12" type="text" id="attribute_upc" name="attribute_upc" value="" style="width: 110px; margin-left: 10px; margin-right: 44px;" />
					<span class="hint" name="help_box">{l s='Special characters allowed:'} .-_#<span class="hint-pointer">&nbsp;</span></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_wholesale_price" type="default"}
					<label>{l s='Wholesale price:'}</label>
				</td>
				<td style="padding-bottom:5px;">
					{if $currency->format % 2 != 0}{$currency->sign}{/if}
					<input type="text" size="6"  name="attribute_wholesale_price" id="attribute_wholesale_price" value="" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
					{if $currency->format % 2 == 0} {$currency->sign} {/if}<span id="attribute_wholesale_price_blank">({l s='leave blank if the price does not change'})</span>
					<span style="display:none" id="attribute_wholesale_price_full">({l s='Overrides wholesale price on "Information" tab'})</span>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_price_impact" type="attribute_price_impact"}
					<label>{l s='Impact on price:'}</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_price_impact" id="attribute_price_impact" style="width: 140px;" onchange="check_impact(); calcImpactPriceTI();">
						<option value="0">{l s='None'}</option>
						<option value="1">{l s='Increase'}</option>
						<option value="-1">{l s='Reduction'}</option>
					</select>
					<span id="span_impact">&nbsp;&nbsp;{l s='of'}&nbsp;&nbsp;{if $currency->format % 2 != 0}{$currency->sign} {/if}
						<input type="hidden"  id="attribute_priceTEReal" name="attribute_price" value="0.00" />
						<input type="text" size="6" id="attribute_price" value="0.00" onkeyup="$('#attribute_priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); calcImpactPriceTI();"/>{if $currency->format % 2 == 0} {$currency->sign}{/if}
						{if $country_display_tax_label}
							{l s='(tax excl.)'}
							<span {if $tax_exclude_option}style="display:none"{/if}> {l s='or'}
							{if $currency->format % 2 != 0}{$currency->sign} {/if}
							<input type="text" size="6" name="attribute_priceTI" id="attribute_priceTI" value="0.00" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); calcImpactPriceTE();"/>
							{if $currency->format % 2 == 0} {$currency->sign}{/if} {l s='(tax incl.)'}
							</span> {l s='final product price will be set to'}
							{if $currency->format % 2 != 0}{$currency->sign} {/if}
							<span id="attribute_new_total_price">0.00</span>
							{if $currency->format % 2 == 0}{$currency->sign} {/if}
						{/if}
					</span>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_weight_impact" type="attribute_weight_impact"}
					<label>{l s='Impact on weight:'}</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_weight_impact" id="attribute_weight_impact" style="width: 140px;" onchange="check_weight_impact();">
						<option value="0">{l s='None'}</option>
						<option value="1">{l s='Increase'}</option>
						<option value="-1">{l s='Reduction'}</option>
					</select>
					<span id="span_weight_impact">&nbsp;&nbsp;{l s='of'}&nbsp;&nbsp;
						<input type="text" size="6" name="attribute_weight" id="attribute_weight" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
						{$ps_weight_unit}
					</span>
				</td>
			</tr>
			<tr id="tr_unit_impact">
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_unit_impact" type="attribute_unit_impact"}
					<label style="width: 100px; float: right">{l s='Impact on unit price :'}</label>
				</td>
				<td colspan="2" style="padding-bottom:5px;">
					<select name="attribute_unit_impact" id="attribute_unit_impact" style="width: 140px;" onchange="check_unit_impact();">
						<option value="0">{l s='None'}</option>
						<option value="1">{l s='Increase'}</option>
						<option value="-1">{l s='Reduction'}</option>
					</select>
					<span id="span_weight_impact">&nbsp;&nbsp;{l s='of'}&nbsp;&nbsp;&nbsp;&nbsp;
						{if $currency->format % 2 != 0} {$currency->sign} {/if}
						<input type="text" size="6" name="attribute_unity" id="attribute_unity" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />{if $currency->format % 2 == 0} {$currency->sign}{/if} / <span id="unity_third">{$field_value_unity}</span>
					</span>
				</td>
			</tr>
			{if $ps_use_ecotax}
				<tr>
					<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;">
						{include file="controllers/products/multishop/checkbox.tpl" field="attribute_ecotax" type="default"}
						<label>{l s='Eco-tax (tax excl.):'}</label>
					</td>
					<td style="padding-bottom:5px;">{if $currency->format % 2 != 0}{$currency->sign}{/if}
						<input type="text" size="3" name="attribute_ecotax" id="attribute_ecotax" value="0.00" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
						{if $currency->format % 2 == 0} {$currency->sign}{/if} 
						({l s='overrides Eco-tax on "Information" tab'})
					</td>
				</tr>
			{/if}
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" class="col-left">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_minimal_quantity" type="default"}
					<label>{l s='Minimum quantity:'}</label>
				</td>
				<td style="padding-bottom:5px;">
					<input size="3" maxlength="6" name="attribute_minimal_quantity" id="attribute_minimal_quantity" type="text" value="{$minimal_quantity}" />
					<p>{l s='The minimum quantity to buy this product (set to 1 to disable this feature)'}</p>
				</td>
			</tr>
			<tr>
				<td style="width:150px;vertical-align:top;text-align:right;padding-right:10px;font-weight:bold;" class="col-left">
					{include file="controllers/products/multishop/checkbox.tpl" field="available_date_attribute" type="default"}
					<label>{l s='Available date:'}</label>
				</td>
				<td style="padding-bottom:5px;">
					<input class="datepicker" id="available_date_attribute" name="available_date_attribute" value="{$available_date}" style="text-align: center;" type="text" />
					<p>{l s='The available date when this product is out of stock'}</p>
					<script type="text/javascript">
						$(document).ready(function(){
							$(".datepicker").datepicker({
								prevText: '',
								nextText: '',
								dateFormat: 'yy-mm-dd'
							});
						});
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="separation"></div>
				</td>
			</tr>
			<tr>
				<td style="width:150px"><label>{l s='Image:'}</label></td>
				<td style="padding-bottom:5px;">
					<ul id="id_image_attr">
						{foreach from=$images key=k item=image}
							<li style="float: left; width: {$imageWidth}px;">
								<input type="checkbox" name="id_image_attr[]" value="{$image.id_image}" id="id_image_attr_{$image.id_image}" />
								<label for="id_image_attr_{$image.id_image}" style="float: none;">
									<img src="{$smarty.const._THEME_PROD_DIR_}{$image.obj->getExistingImgPath()}-small_default.jpg" alt="{$image.legend|escape:'htmlall':'UTF-8'}" title="{$image.legend|escape:'htmlall':'UTF-8'}" />
								</label>
							</li>
						{/foreach}
					</ul>
					<img id="pic" alt="" title="" style="display: none; width: 100px; height: 100px; float: left; border: 1px dashed #BBB; margin-left: 20px;" />
				</td>
			</tr>
			<tr>
				<td style="width:150px">
					{include file="controllers/products/multishop/checkbox.tpl" field="attribute_default" type="attribute_default"}
					<label>{l s='Default:'}</label><br /><br />
				</td>
				<td style="padding-bottom:5px;">
					<input type="checkbox" name="attribute_default" id="attribute_default" value="1" />
					&nbsp;{l s='Make this the default combination for this product'}<br /><br />
				</td>
			</tr>
			<tr>
				<td style="width:150px">&nbsp;</td>
				<td style="padding-bottom:5px;">
					<span id="ResetSpan" style="float:left;margin-left:8px;display:none;">
						<input type="reset" name="ResetBtn" id="ResetBtn" onclick="getE('id_product_attribute').value = 0;" class="button" value="{l s='Cancel modification'}" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
		</table>
		<div class="separation"></div>
	</div>
	
	{$list}
{/if}
