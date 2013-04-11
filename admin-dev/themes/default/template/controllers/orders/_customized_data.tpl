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
{if $product['customizedDatas']}
	<tr class="customized customized-{$product['id_order_detail']|intval}">
		<td align="center">
			<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
			{if isset($product['image']) && $product['image']->id|intval}{$product['image_tag']}{else}--{/if}</td>
		<td>
			<a href="index.php?controller=adminproducts&id_product={$product['product_id']|intval}&updateproduct&token={getAdminToken tab='AdminProducts'}">
			<span class="productName">{$product['product_name']} - {l s='Customized'}</span><br />
			{if ($product['product_reference'])}{l s='Ref:'} {$product['product_reference']}<br />{/if}
			{if ($product['product_supplier_reference'])}{l s='Ref Supplier:'} {$product['product_supplier_reference']}{/if}
			</a>
		</td>
		<td align="center">
			<span class="product_price_show">{displayPrice price=$product['product_price_wt'] currency=$currency->id|intval}</span>
			{if $can_edit}
			<span class="product_price_edit" style="display:none;">
				<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
				{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)}" size="5" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax excl.'}<br />
				{if $currency->sign % 2}{$currency->sign}{/if}<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)}" size="5" /> {if !($currency->sign % 2)}{$currency->sign}{/if} {l s='tax incl.'}
			</span>
			{/if}
		</td>
		<td align="center" class="productQuantity">{$product['customizationQuantityTotal']}</td>
		{if ($order->hasBeenPaid())}<td align="center" class="productQuantity">{$product['customizationQuantityRefunded']}</td>{/if}
		{if ($order->hasBeenDelivered() || $order->hasProductReturned())}<td align="center" class="productQuantity">{$product['customizationQuantityReturned']}</td>{/if}
		{if $stock_management}<td align="center" class=""> - </td>{/if}
		<td align="center" class="total_product">
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{displayPrice price=Tools::ps_round($product['product_price'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{else}
			{displayPrice price=Tools::ps_round($product['product_price_wt'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{/if}
		</td>
		<td class="cancelQuantity standard_refund_fields current-edit" style="display:none" colspan="2">
			&nbsp;
		</td>
		<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
		<td class="partial_refund_fields current-edit" style="text-align:left;display:none"></td>
		{if ($can_edit && !$order->hasBeenDelivered())}
			<td class="product_action" style="text-align:right">
				<a href="#" class="edit_product_change_link"><img src="../img/admin/edit.gif" alt="{l s='Edit'}" /></a>
				<input type="submit" class="button" name="submitProductChange" value="{l s='Update'}"  style="display: none;" />
				<a href="#" class="cancel_product_change_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel'}" /></a>
				<a href="#" class="delete_product_line"><img src="../img/admin/delete.gif" alt="{l s='Delete'}" /></a>
			</td>
		{/if}
	</tr>
	{foreach $product['customizedDatas'] as $customizationPerAddress}
		{foreach $customizationPerAddress as $customizationId => $customization}
			<tr class="customized customized-{$product['id_order_detail']|intval}">
				<td colspan="2">
				<input type="hidden" class="edit_product_id_order_detail" value="{$product['id_order_detail']|intval}" />
			{foreach $customization.datas as $type => $datas}
				{if ($type == Product::CUSTOMIZE_FILE)}
					<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
					{foreach from=$datas item=data}
						<li style="margin: 2px;">
							<span>{if $data['name']}{$data['name']}{else}{l s='Picture #'}{$data@iteration}{/if}{l s=':'}</span>&nbsp;
								<a href="displayImage.php?img={$data['value']}&name={$order->id|intval}-file{$data@iteration}" target="_blank"><img src="{$smarty.const._THEME_PROD_PIC_DIR_}{$data['value']}_small" alt="" /></a>
						</li>
					{/foreach}
					</ul>
				{elseif ($type == Product::CUSTOMIZE_TEXTFIELD)}
					<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
					{foreach from=$datas item=data}
						<li>{if $data['name']}{l s='%s:' sprintf=$data['name']}{else}{l s='Text #%s:' sprintf=$data@iteration}{/if} {$data['value']}</li>
					{/foreach}
					</ul>
				{/if}
			{/foreach}
				</td>
				<td align="center">-
				</td>
				<td align="center" class="productQuantity">
					<span class="product_quantity_show{if (int)$customization['quantity'] > 1} red bold{/if}">{$customization['quantity']}</span>
					{if $can_edit}
					<span class="product_quantity_edit" style="display:none;">
						<input type="text" name="product_quantity[{$customizationId|intval}]" class="edit_product_quantity" value="{$customization['quantity']|htmlentities}" size="2" />
					</span>
					{/if}
				</td>
				{if ($order->hasBeenPaid())}<td align="center">{$customization['quantity_refunded']}</td>{/if}
				{if ($order->hasBeenDelivered())}<td align="center">{$customization['quantity_returned']}</td>{/if}
				<td align="center">
				-
				</td>
				<td align="center" class="total_product">
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{displayPrice price=Tools::ps_round($product['product_price'] * $customization['quantity'], 2) currency=$currency->id|intval}
					{else}
						{displayPrice price=Tools::ps_round($product['product_price_wt'] * $customization['quantity'], 2) currency=$currency->id|intval}
					{/if}
				</td>				
				<td align="center" class="cancelCheck standard_refund_fields current-edit" style="display:none">
					<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$customization['quantity_returned']|intval}" />
					<input type="hidden" name="totalQty" id="totalQty" value="{$customization['quantity']|intval}" />
					<input type="hidden" name="productName" id="productName" value="{$product['product_name']}" />
					{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($customization['quantity_returned']) < (int)($customization['quantity']))}
						<input type="checkbox" name="id_customization[{$customizationId|intval}]" id="id_customization[{$customizationId|intval}]" value="{$product['id_order_detail']|intval}" onchange="setCancelQuantity(this, {$customizationId|intval}, {$customization['quantity'] - $product['customizationQuantityTotal'] - $product['product_quantity_reinjected']})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
					{else}
					--
				{/if}
				</td>
				<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
				{if ($customization['quantity_returned'] + $customization['quantity_refunded'] >= $customization['quantity'])}
					<input type="hidden" name="cancelCustomizationQuantity[{$customizationId|intval}]" value="0" />
				{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
					<input type="text" id="cancelQuantity_{$customizationId|intval}" name="cancelCustomizationQuantity[{$customizationId|intval}]" size="2" onclick="selectCheckbox(this);" value="" />0/{$customization['quantity']-$customization['quantity_refunded']}
				{/if}
				</td>
				<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
					<div style="width:40%;margin-top:5px;float:left">{l s='Quantity:'}</div> <div style="width:60%;margin-top:2px;float:left"><input type="text" size="3" name="partialRefundProductQuantity[{$product['id_order_detail']|intval}]" value="0" />		
					0/{$customization['quantity']-$customization['quantity_refunded']}
					</div>
					<div style="width:40%;margin-top:5px;float:left">{l s='Amount:'}</div> <div style="width:60%;margin-top:2px;float:left">{$currency->prefix}<input type="text" size="3" name="partialRefundProduct[{$product['id_order_detail']|intval}]" />{$currency->suffix}</div>
				</td>
				{if ($can_edit && !$order->hasBeenDelivered())}
					<td class="edit_product_fields" colspan="2" style="display:none"></td>
					<td class="product_action" style="text-align:right"></td>
				{/if}
			</tr>
		{/foreach}
	{/foreach}
{/if}
