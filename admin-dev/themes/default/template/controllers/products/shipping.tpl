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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h4 class="tab">1. {l s='Info.'}</h4>
<h4>{l s='Shipping'}</h4>

<div class="separation"></div>

<table>
	<tr>
		<td class="col-left"><label>{l s='Width ( package ) :'}</label></td>
		<td style="padding-bottom:5px;">
			<input size="6" maxlength="6" name="width" type="text" value="{$product->width}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
		</td>
	</tr>
	<tr>
		<td class="col-left"><label>{l s='Height ( package ) :'}</label></td>
		<td style="padding-bottom:5px;">
			<input size="6" maxlength="6" name="height" type="text" value="{$product->height}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
		</td>
	</tr>
	<tr>
	<td class="col-left"><label>{l s='Depth ( package ) :'}</label></td>
	<td style="padding-bottom:5px;">
	<input size="6" maxlength="6" name="depth" type="text" value="{$product->depth}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_dimension_unit}
	</td>
	</tr>
	<tr>
	<td class="col-left"><label>{l s='Weight ( package ) :'}</label></td>
	<td style="padding-bottom:5px;">
	<input size="6" maxlength="6" name="weight" type="text" value="{$product->weight}" onKeyUp="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" /> {$ps_weight_unit}
	</td>
	</tr>
	<tr>
		<td class="col-left"><label>{l s='Additional shipping cost:'}</label></td>
		<td style="padding-bottom:5px;">{$currency->prefix}<input type="text" name="additional_shipping_cost"
				value="{$product->additional_shipping_cost|htmlentities}" />{$currency->suffix}
			{if $country_display_tax_label}{l s='tax excl.'}{/if}
			<p class="preference_description">{l s='Carrier tax will be applied.'}</p>
		</td>
	</tr>
	<tr>
		<td class="col-left">
			<label>{l s='Carriers:'}</label>
		</td>
		<td class="padding-bottom:5px;">
			<select name="carriers[]" multiple="multiple" size="4" style="height:100px;width:200px;">
				{foreach $carrier_list as $carrier}
					<option value="{$carrier.id_reference}" {if isset($carrier.selected) && $carrier.selected}selected="selected"{/if}>{$carrier.name}</option>
				{/foreach}
			</select>
			<p class="preference_description">{l s='If no carrier selected, all carriers could be used to ship this product.'}</p>
		</td>
	</tr>
</table>
