{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{assign var="currencySymbolBeforeAmount" value=$currency->format[0] === '¤'}
<tr id="new_product" style="display:none">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />

		<div class="form-group">
			<label>{l s='Product' d='Admin.Global'}</label>
			<div class="input-group">
				<input type="text" id="add_product_product_name" value=""/>
				<div class="input-group-addon">
					<i class="icon-search"></i>
				</div>
			</div>
		</div>

		<div id="add_product_product_attribute_area" class="form-group" style="display: none;">
			<label>{l s='Combinations' d='Admin.Global'}</label>
			<select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>

		<div id="add_product_product_warehouse_area" class="form-group" style="display: none;">
			<label>{l s='Warehouse'}</label>
			<select  id="add_product_warehouse" name="add_product_warehouse"></select>
		</div>
	</td>

	<td style="display:none;">
		<div class="row">
			<div class="input-group fixed-width-xl">
				{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
				<input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" disabled="disabled" />
				{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="input-group fixed-width-xl">
				{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
				<input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" disabled="disabled" />
				{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
			</div>
		</div>
	</td>

	<td style="display:none;" class="productQuantity">
		<input type="number" class="form-control fixed-width-sm" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" disabled="disabled" />
	</td>
	{if ($order->hasBeenPaid())}<td style="display:none;" class="productQuantity"></td>{/if}
	{if $display_warehouse}<td></td>{/if}
	{if ($order->hasBeenDelivered())}<td style="display:none;" class="productQuantity"></td>{/if}
	<td style="display:none;" class="productQuantity" id="add_product_product_stock">0</td>
	<td style="display:none;" id="add_product_product_total">{displayPrice price=0 currency=$currency->id}</td>
	<td style="display:none;" colspan="2">
		{if sizeof($invoices_collection)}
		<select class="form-control" name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="{l s='Existing'}">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}">{$invoice->getInvoiceNumberFormatted($current_id_lang)}</option>
				{/foreach}
			</optgroup>
			<optgroup label="{l s='New'}">
				<option value="0">{l s='Create a new invoice' d='Admin.Orderscustomers.Feature'}</option>
			</optgroup>
		</select>
		{/if}
	</td>
	<td style="display:none;">
		<button type="button" class="btn btn-default" id="cancelAddProduct">
			<i class="icon-remove text-danger"></i>
			{l s='Cancel' d='Admin.Actions'}
		</button>
		<button type="button" class="btn btn-default" id="submitAddProduct" disabled="disabled">
			<i class="icon-ok text-success"></i>
			{l s='Add' d='Admin.Actions'}
		</button>
	</td>
</tr>

<tr id="new_invoice" style="display:none">
	<td colspan="10">
		<h4>{l s='New invoice information' d='Admin.Orderscustomers.Feature'}</h4>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Carrier' d='Admin.Shipping.Feature'}</label>
				<div class="col-lg-9">
					<p class="form-control-static"><strong>{$carrier->name}</strong></p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Shipping Costs' d='Admin.Orderscustomers.Feature'}</label>
				<div class="col-lg-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
							{l s='Free shipping' d='Admin.Shipping.Feature'}
						</label>
						<p class="help-block">{l s='If you don\'t select "Free shipping," the normal shipping costs will be applied.' d='Admin.Orderscustomers.Help'}</p>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>
