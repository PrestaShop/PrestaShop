<tr id="new_product" height="52" style="display:none;background-color:#E8EDC2">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
		{l s='Product:'} <input type="text" id="add_product_product_name" value="" size="42" />
		<div id="add_product_product_attribute_area" style="margin-top: 5px;display: none;">
			{l s='Combinations:'} <select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
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
		{if $order->valid}
		<select name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup label="{l s='Existing'}">
				{foreach from=$invoices_collection item=invoice}
				<option value="{$invoice->id}">#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang)}{'%06d'|sprintf:$invoice->number}</option>
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
<tr id="new_invoice" style="display:none;background-color:#E8EDC2">
	<td colspan="10">
		<h3>{l s='New invoice informations'}</h3>
		<label>{l s='Carrier:'}</label>
		<div class="margin-form">
			{$carrier->name}
		</div>
		<div class="margin-form">
			<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
			<label class="t">{l s='Free shipping'}</label>
			<p>{l s='If you don\'t select the "Free shipping", the normal shipping cost will be applied'}</p>
		</div>
	</td>
</tr>
