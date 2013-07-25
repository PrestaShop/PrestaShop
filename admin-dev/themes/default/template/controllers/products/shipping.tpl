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

<input type="hidden" name="submitted_tabs[]" value="Shipping" />
<legend>{l s='Shipping'}</legend>

{if isset($display_common_field) && $display_common_field}
	<div class="alert alert-info">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
{/if}

<div class="row">
	<label class="control-label col-lg-5" for="width">{l s='Width (package):'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$ps_dimension_unit}</span>
		<input maxlength="6" id="width" name="width" type="text" value="{$product->width}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
		{$bullet_common_field}
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-5" for="height">{l s='Height (package):'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$ps_dimension_unit}</span>
		<input maxlength="6" id="height" name="height" type="text" value="{$product->height}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
		{$bullet_common_field}
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-5" for="depth">{l s='Depth (package):'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$ps_dimension_unit}</span>
		<input maxlength="6" id="depth" name="depth" type="text" value="{$product->depth}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
		{$bullet_common_field}
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-5" for="weight">{l s='Weight (package):'}</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$ps_weight_unit}</span>
		<input maxlength="6" id="weight" name="weight" type="text" value="{$product->weight}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" />
		{$bullet_common_field}
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-5" for="additional_shipping_cost">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='A carrier tax will be applied.'}">
			{l s='Additional shipping cost (per quantity):'}
		</span>
		
	</label>
	<div class="input-group col-lg-2">
		<span class="input-group-addon">{$currency->prefix}{$currency->suffix} {if $country_display_tax_label}({l s='tax excl.'}){/if}</span>
		<input type="text" id="additional_shipping_cost" name="additional_shipping_cost" onchange="this.value = this.value.replace(/,/g, '.');" value="{$product->additional_shipping_cost|htmlentities}" />
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-5" for="availableCarriers">{l s='Carriers:'}</label>
	<div class="input-group col-lg-7">
		<div class="row">
			<div class="col-lg-6">
				<label for="availableCarriers">{l s='Available carriers'}</label>
				<select multiple id="availableCarriers" name="availableCarriers">
					{foreach $carrier_list as $carrier}
						{if !isset($carrier.selected) || !$carrier.selected}
							<option value="{$carrier.id_reference}">{$carrier.name}</option>
						{/if}
					{/foreach}
				</select>
				<a href="#" id="addCarrier" class="btn btn-default btn-block">{l s='Add'} <i class="icon-arrow-right"></i></a>
			</div>
			<div class="col-lg-6">
				<label for="selectedCarriers">{l s='Selected carriers'}</label>
				<select multiple id="selectedCarriers" name="selectedCarriers[]">
					{foreach $carrier_list as $carrier}
						{if isset($carrier.selected) && $carrier.selected}
							<option value="{$carrier.id_reference}">{$carrier.name}</option>
						{/if}
					{/foreach}
				</select>
				<a href="#" id="removeCarrier" class="btn btn-default btn-block"><i class="icon-arrow-left"></i> {l s='Remove'}</a>
			</div>
		</div>
	</div>
</div>