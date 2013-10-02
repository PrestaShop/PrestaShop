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

{* Assign product price *}
{if isset($deliverd_products)} {
	{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
		{assign var=product_price value=($deliverd_products['unit_price_tax_excl'] + $deliverd_products['ecotax'])}
	{else}
		{assign var=product_price value=$deliverd_products['unit_price_tax_incl']}
	{/if}
{/if}

{if (isset($deliverd_products) && $deliverd_products['product_quantity'] > $deliverd_products['customizationQuantityTotal'])}
<!--Here i need to compare products to order_delivery table -->
<!-- sctach that, use getPdeeliv and insted of 4 sue $delivered_p-->
<tr{if isset($deliverd_products.image) && $deliverd_products.image->id && isset($deliverd_products.image_size)} height="{$deliverd_products['image_size'][1] + 7}"{/if}>
	<td align="center">{if isset($deliverd_products.image) && $deliverd_products.image->id}{$deliverd_products.image_tag}{/if}</td>
	<td>
<!-- 	<a href="index.php?controller=adminproducts&id_product={$deliverd_products['product_id']}&updateproduct&token={getAdminToken tab='AdminProducts'}"> -->
		<span class="productName">{$deliverd_products['product_name']}</span><br />
		{if $deliverd_products.product_reference}{l s='Ref:'} {$deliverd_products.product_reference}<br />{/if}
		{if $deliverd_products.product_supplier_reference}{l s='Ref Supplier:'} {$deliverd_products.product_supplier_reference}{/if}
<!-- 	</a> -->
	</td>
	<td align="center">
		<span class="product_price_show">{displayPrice price=$deliverd_products_price currency=$currency->id}</span>
		{if $can_edit}
		<span class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$deliverd_products['id_order_detail']}" />
			{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($deliverd_products['unit_price_tax_excl'], 2)}" size="5" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax excl.'}<br />
			{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($deliverd_products['unit_price_tax_incl'], 2)}" size="5" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax incl.'}
		</span>
		{/if}
	</td>
	<td align="center" class="productQuantity">
		<span class="product_quantity_show{if (int)$deliverd_products['product_quantity'] > 1} red bold{/if}">{$deliverd_products['product_quantity']}</span>
		{if $can_edit}
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="{$deliverd_products['product_quantity']|htmlentities}" size="2" />
		</span>
		{/if}
	</td>
	{if $display_warehouse}<td>{$deliverd_products.warehouse_name|escape:'htmlall':'UTF-8'}</td>{/if}
	{if ($order->hasBeenPaid())}
		<td align="center" class="productQuantity">
			{$deliverd_products['product_quantity_refunded']}
			{if count($deliverd_products['refund_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title">{l s='Refund history'}</span>
					{foreach $deliverd_products['refund_history'] as $refund}
						{l s='%1s - %2s' sprintf=[{dateFormat date=$refund.date_add}, {displayPrice price=$refund.amount_tax_incl}]}<br />
					{/foreach}
					</div>
				</span>
			{/if}
		</td>
	{/if}
	{if $order->hasBeenDelivered() || $order->hasProductReturned()}
		<td align="center" class="productQuantity">
			{$deliverd_products['product_quantity_return']}
			{if count($deliverd_products['return_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<div class="tooltip_content">
					<span class="title">{l s='Return history'}</span>
					{foreach $deliverd_products['return_history'] as $return}
						{l s='%1s - %2s - %3s' sprintf=[{dateFormat date=$return.date_add}, $return.product_quantity, $return.state]}<br />
					{/foreach}
					</div>
				</span>
			{/if}
		</td>
	{/if}
	{if $stock_management}<td align="center" class="productQuantity product_stock">{$deliverd_products['current_stock']}</td>{/if}
	<td align="center" class="total_product">
		{displayPrice price=(Tools::ps_round($deliverd_products_price, 2) * ($deliverd_products['product_quantity'] - $deliverd_products['customizationQuantityTotal'])) currency=$currency->id}
	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$deliverd_products['product_quantity_return']}" />
		<input type="hidden" name="totalQty" id="totalQty" value="{$deliverd_products['product_quantity']}" />
		<input type="hidden" name="productName" id="productName" value="{$deliverd_products['product_name']}" />
	{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($deliverd_products['product_quantity_return']) < (int)($deliverd_products['product_quantity']))}
		<input type="checkbox" name="id_order_detail[{$deliverd_products['id_order_detail']}]" id="id_order_detail[{$deliverd_products['id_order_detail']}]" value="{$deliverd_products['id_order_detail']}" onchange="setCancelQuantity(this, {$deliverd_products['id_order_detail']}, {$deliverd_products['product_quantity'] - $deliverd_products['customizationQuantityTotal'] - $deliverd_products['product_quantity_return']})" {if ($deliverd_products['product_quantity_return'] + $deliverd_products['product_quantity_refunded'] >= $deliverd_products['product_quantity'])}disabled="disabled" {/if}/>
	{else}
		--
	{/if}
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	{if ($deliverd_products['product_quantity_return'] + $deliverd_products['product_quantity_refunded'] >= $deliverd_products['product_quantity'])}
		<input type="hidden" name="cancelQuantity[{$deliverd_products['id_order_detail']}]" value="0" />
	{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
		<input type="text" id="cancelQuantity_{$deliverd_products['id_order_detail']}" name="cancelQuantity[{$deliverd_products['id_order_detail']}]" size="2" onclick="selectCheckbox(this);" value="" />
	{/if}

	{if $deliverd_products['customizationQuantityTotal']}
		{assign var=productQuantity value=($deliverd_products['product_quantity']-$deliverd_products['customizationQuantityTotal'])}
	{else}
		{assign var=productQuantity value=$deliverd_products['product_quantity']}
	{/if}

	{if ($order->hasBeenDelivered())}
		{$deliverd_products['product_quantity_refunded']}/{$deliverd_productsQuantity-$deliverd_products['product_quantity_refunded']}
	{elseif ($order->hasBeenPaid())}
		{$deliverd_products['product_quantity_return']}/{$deliverd_productsQuantity}
	{else}
		0/{$deliverd_productsQuantity}
	{/if}
	</td>
	<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
		<div style="width:40%;margin-top:5px;float:left">{l s='Quantity:'}</div> <div style="width:60%;margin-top:2px;float:left"><input onchange="checkPartialRefundProductQuantity(this)" type="text" size="3" name="partialRefundProductQuantity[{{$deliverd_products['id_order_detail']}}]" value="0" /> 0/{$deliverd_productsQuantity-$deliverd_products['product_quantity_refunded']}</div>
		<div style="width:40%;margin-top:5px;float:left">{l s='Amount:'}</div> <div style="width:60%;margin-top:2px;float:left">{$currency->prefix}<input onchange="checkPartialRefundProductAmount(this)" type="text" size="3" name="partialRefundProduct[{$deliverd_products['id_order_detail']}]" /> {$currency->suffix}</div> {if !empty($deliverd_products['amount_refund']) && $deliverd_products['amount_refund'] > 0}({l s='%s refund' sprintf=$deliverd_products['amount_refund']}){/if}
		<input type="hidden" value="{$deliverd_products['quantity_refundable']}" class="partialRefundProductQuantity" />
		<input type="hidden" value="{$deliverd_products['amount_refundable']}" class="partialRefundProductAmount" />
	</td>
	{if ($can_edit && !$order->hasBeenDelivered())}
	<td class="product_invoice" colspan="2" style="display: none;text-align:center;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id}" {if $invoice->id == $deliverd_products['id_order_invoice']}selected="selected"{/if}>#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$invoice->number}</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
	</td>
	{/if}
</tr>
{/if}
