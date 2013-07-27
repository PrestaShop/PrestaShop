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
<tr id="new_product" height="52" style="display:none;background-color:#e9f1f6">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
		{l s='Product:'} <input type="text" id="add_product_product_name" value="" size="42" />
		<div id="add_product_product_attribute_area" style="margin-top: 5px;display: none;">
			{l s='Combinations'} <select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>
		<div id="add_product_product_warehouse_area" style="margin-top: 5px; display: none;">
			{l s='Warehouse'} <select  id="add_product_warehouse" name="add_product_warehouse">
			</select>
		</div>
	</td>
	<td style="display:none;">
		{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" size="4" disabled="disabled" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax excl.'}<br />
		{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" size="4" disabled="disabled" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax incl.'}<br />
	</td>
	<td style="display:none;" align="center" class="productQuantity"><input type="text" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" size="3" disabled="disabled" /></td>
	{if ($order->hasBeenPaid())}<td style="display:none;" align="center" class="productQuantity">&nbsp;</td>{/if}
	{if ($order->hasBeenDelivered())}<td style="display:none;" align="center" class="productQuantity">&nbsp;</td>{/if}
	<td style="display:none;" align="center" class="productQuantity" id="add_product_product_stock">0</td>
	<td style="display:none;" align="center" id="add_product_product_total">{displayPrice price=0 currency=$currency->id}</td>
	<td style="display:none;" align="center" colspan="2">
		{if sizeof($invoices_collection)}
		<select name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="{l s='Existing'}">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}">{$invoice->getInvoiceNumberFormatted($current_id_lang)}</option>
				{/foreach}
			</optgroup>
			<optgroup label="{l s='New'}">
				<option value="0">{l s='Create a new invoice'}</option>
			</optgroup>
		</select>
		{/if}
	</td>
	<td style="display:none;">
		<input type="button" class="button" id="submitAddProduct" value="{l s='Add product'}" disabled="disabled" />
	</td>
</tr>
<tr id="new_invoice" style="display:none;background-color:#e9f1f6;">
	<td colspan="10">
		<h3>{l s='New invoice information'}</h3>
		<label>{l s='Carrier'}</label>
		<div class="margin-form">
			{$carrier->name}
		</div>
		<div class="margin-form">
			<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
			<label class="t">{l s='Free shipping'}</label>
			<p>{l s='If you don\'t select "Free shipping," the normal shipping cost will be applied.'}</p>
		</div>
	</td>
</tr>
