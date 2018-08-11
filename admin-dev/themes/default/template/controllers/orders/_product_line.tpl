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
{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}

{if ($product['product_quantity'] > $product['customized_product_quantity'])}
<tr class="product-line-row">
	<td>{if isset($product.image) && $product.image->id}{$product.image_tag}{/if}</td>
	<td>
		<a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product['product_id']|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}">
			<span class="productName">{$product['product_name']}</span><br />
			{if $product.product_reference}{l s='Reference number:' d='Admin.Orderscustomers.Feature'} {$product.product_reference}<br />{/if}
			{if $product.product_supplier_reference}{l s='Supplier reference:' d='Admin.Orderscustomers.Feature'} {$product.product_supplier_reference}{/if}
		</a>
        {if isset($product.pack_items) && $product.pack_items|@count > 0}<br>
            <button name="package" class="btn btn-default" type="button" onclick="TogglePackage('{$product['id_order_detail']}'); return false;" value="{$product['id_order_detail']}">{l s='Package content' d='Admin.Orderscustomers.Feature'}</button>
        {/if}
		<div class="row-editing-warning" style="display:none;">
			<div class="alert alert-warning">
				<strong>{l s='Editing this product line will remove the reduction and base price.' d='Admin.Orderscustomers.Notification'}</strong>
			</div>
		</div>
	</td>
	<td>
		<span class="product_price_show">{displayPrice price=$product_price currency=$currency->id}</span>
		{if $can_edit}
		<div class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$product['id_order_detail']}" />
			<div class="form-group">
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
						<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)}"/>
						{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax excl.' d='Admin.Global'}</div>{/if}
					</div>
				</div>
				<br/>
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
						<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)}"/>
						{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign} {l s='tax incl.' d='Admin.Global'}</div>{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}
	</td>
	<td class="productQuantity text-center">
		<span class="product_quantity_show{if (int)$product['product_quantity'] - (int)$product['customized_product_quantity'] > 1} badge{/if}">{(int)$product['product_quantity'] - (int)$product['customized_product_quantity']}</span>
		{if $can_edit}
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="{$product['product_quantity']|htmlentities}"/>
		</span>
		{/if}
	</td>
	{if $display_warehouse}
		<td>
			{$product.warehouse_name|escape:'html':'UTF-8'}
			{if $product.warehouse_location}
				<br>{l s='Location' d='Admin.Orderscustomers.Feature'}: <strong>{$product.warehouse_location|escape:'html':'UTF-8'}</strong>
			{/if}
		</td>
	{/if}
	{if ($order->hasBeenPaid())}
		<td class="productQuantity text-center">
			{if !empty($product['amount_refund'])}
				{l s='%quantity_refunded% (%amount_refunded% refund)' sprintf=['%quantity_refunded%' => $product['product_quantity_refunded'], '%amount_refunded%' => $product['amount_refund']] d='Admin.Orderscustomers.Feature'}
			{/if}
			<input type="hidden" value="{$product['quantity_refundable']}" class="partialRefundProductQuantity" />
			<input type="hidden" value="{(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal']))}" class="partialRefundProductAmount" />
			{if count($product['refund_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title">{l s='Refund history' d='Admin.Orderscustomers.Feature'}</span>
					{foreach $product['refund_history'] as $refund}
						{l s='%refund_date% - %refund_amount%' sprintf=['%refund_date%' => {dateFormat date=$refund.date_add}, '%refund_amount%' => {displayPrice price=$refund.amount_tax_incl}] d='Admin.Orderscustomers.Feature'}<br />
					{/foreach}
					</span>
				</span>
			{/if}
		</td>
	{/if}
	{if $order->hasBeenDelivered() || $order->hasProductReturned()}
		<td class="productQuantity text-center">
			{$product['product_quantity_return']}
			{if count($product['return_history'])}
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title">{l s='Return history' d='Admin.Orderscustomers.Feature'}</span>
					{foreach $product['return_history'] as $return}
						{l s='%return_date% - %return_quantity% - %return_state%' sprintf=['%return_date%' =>{dateFormat date=$return.date_add}, '%return_quantity%' => $return.product_quantity, '3return_state%' => $return.state] d='Admin.Orderscustomers.Feature'}<br />
					{/foreach}
					</span>
				</span>
			{/if}
		</td>
	{/if}
	{if $stock_management}<td class="productQuantity product_stock text-center">{$product['current_stock']}</td>{/if}
	<td class="total_product">
		{displayPrice price=(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal'])) currency=$currency->id}
	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$product['product_quantity_return']}" />
		<input type="hidden" name="totalQty" id="totalQty" value="{$product['product_quantity']}" />
		<input type="hidden" name="productName" id="productName" value="{$product['product_name']}" />
	{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($product['product_quantity_return']) < (int)($product['product_quantity']))}
		<input type="checkbox" name="id_order_detail[{$product['id_order_detail']}]" id="id_order_detail[{$product['id_order_detail']}]" value="{$product['id_order_detail']}" onchange="setCancelQuantity(this, {$product['id_order_detail']}, {$product['product_quantity'] - $product['customizationQuantityTotal'] - $product['product_quantity_return'] - $product['product_quantity_refunded']})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
	{else}
		--
	{/if}
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	{if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}
		<input type="hidden" name="cancelQuantity[{$product['id_order_detail']}]" value="0" />
	{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
		<input type="text" id="cancelQuantity_{$product['id_order_detail']}" name="cancelQuantity[{$product['id_order_detail']}]" onchange="checkTotalRefundProductQuantity(this)" value="" />
	{/if}

	{if $product['customizationQuantityTotal']}
		{assign var=productQuantity value=($product['product_quantity']-$product['customizationQuantityTotal'])}
	{else}
		{assign var=productQuantity value=$product['product_quantity']}
	{/if}

	{if ($order->hasBeenDelivered())}
		{$product['product_quantity_refunded']}/{$productQuantity-$product['product_quantity_refunded']}
	{elseif ($order->hasBeenPaid())}
		{$product['product_quantity_return']}/{$productQuantity}
	{else}
		0/{$productQuantity}
	{/if}
	</td>
	<td class="partial_refund_fields current-edit" colspan="2" style="display:none; width: 250px;">
		{if $product['quantity_refundable'] > 0}
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{assign var='amount_refundable' value=$product['amount_refundable']}
		{else}
			{assign var='amount_refundable' value=$product['amount_refundable_tax_incl']}
		{/if}
		<div class="form-group">
			<div class="{if $product['amount_refundable'] > 0}col-lg-4{else}col-lg-12{/if}">
				<label class="control-label">
					{l s='Quantity:'}
				</label>
				<div class="input-group">
					<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="partialRefundProductQuantity[{{$product['id_order_detail']}}]" value="0" />
					<div class="input-group-addon">/ {$product['quantity_refundable']}</div>
				</div>
			</div>
			<div class="{if $product['quantity_refundable'] > 0}col-lg-8{else}col-lg-12{/if}">
				<label class="control-label">
					<span class="title_box ">{l s='Amount' d='Admin.Global'}</span>
					<small class="text-muted">({$smarty.capture.TaxMethod})</small>
				</label>
				<div class="input-group">
					{if $currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign}</div>{/if}
					<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[{$product['id_order_detail']}]" />
					{if !$currencySymbolBeforeAmount}<div class="input-group-addon">{$currency->sign}</div>{/if}
				</div>
        <p class="help-block"><i class="icon-warning-sign"></i> {l s='(Max %amount_refundable% %tax_method%)' sprintf=[ '%amount_refundable%' => Tools::displayPrice(Tools::ps_round($amount_refundable, 2), $currency->id), '%tax_method%' => $smarty.capture.TaxMethod] d='Admin.Orderscustomers.Help'}</p>
			</div>
		</div>
		{/if}
	</td>
	{if ($can_edit && !$order->hasBeenDelivered())}
	<td class="product_invoice" style="display: none;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id}" {if $invoice->id == $product['id_order_invoice']}selected="selected"{/if}>
				#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$invoice->number}
			</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
	</td>
	<td class="product_action text-right">
		{* edit/delete controls *}
		<div class="btn-group" id="btn_group_action">
			<button type="button" class="btn btn-default edit_product_change_link">
				<i class="icon-pencil"></i>
				{l s='Edit' d='Admin.Actions'}
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="#" class="delete_product_line">
						<i class="icon-trash"></i>
						{l s='Delete' d='Admin.Actions'}
					</a>
				</li>
			</ul>
		</div>
		{* Update controls *}
		<button type="button" class="btn btn-default submitProductChange" style="display: none;">
			<i class="icon-ok"></i>
			{l s='Update' d='Admin.Actions'}
		</button>
		<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
			<i class="icon-remove"></i>
			{l s='Cancel' d='Admin.Actions'}
		</button>
	</td>
	{/if}
</tr>
   {if isset($product.pack_items) && $product.pack_items|@count > 0}
    <tr>
        <td colspan="8" style="width:100%">
            <table style="width: 100%; display:none;" class="table" id="pack_items_{$product['id_order_detail']}">
            <thead>
                <th style="width:15%;">&nbsp;</th>
                <th style="width:15%;">&nbsp;</th>
                <th style="width:50%;"><span class="title_box ">{l s='Product' d='Admin.Global'}</span></th>
                <th style="width:10%;"><span class="title_box ">{l s='Qty' d='Admin.Orderscustomers.Feature'}</th>
                {if $stock_management}<th><span class="title_box ">{l s='Available quantity' d='Admin.Orderscustomers.Feature'}</span></th>{/if}
                <th>&nbsp;</th>
            </thead>
            <tbody>
            {foreach from=$product.pack_items item=pack_item}
                {if !empty($pack_item.active)}
                    <tr class="product-line-row" {if isset($pack_item.image) && $pack_item.image->id && isset($pack_item.image_size)} height="{$pack_item['image_size'][1] + 7}"{/if}>
                        <td>{l s='Package item' d='Admin.Orderscustomers.Feature'}</td>
                        <td>{if isset($pack_item.image) && $pack_item.image->id}{$pack_item.image_tag}{/if}</td>
                        <td>
                            <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $pack_item.id_product, 'updateproduct' => '1'])|escape:'html':'UTF-8'}">
                                <span class="productName">{$pack_item.name}</span><br />
                                {if $pack_item.reference}{l s='Ref:' d='Admin.Orderscustomers.Feature'} {$pack_item.reference}<br />{/if}
                                {if $pack_item.supplier_reference}{l s='Ref Supplier:' d='Admin.Orderscustomers.Feature'} {$pack_item.supplier_reference}{/if}
                            </a>
                        </td>
                        <td class="productQuantity">
                            <span class="product_quantity_show{if (int)$pack_item.pack_quantity > 1} red bold{/if}">{$pack_item.pack_quantity}</span>
                        </td>
                        {if $stock_management}<td class="productQuantity product_stock">{$pack_item.current_stock}</td>{/if}
                        <td>&nbsp;</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
            </table>
        </td>
    </tr>
    {/if}
{/if}
