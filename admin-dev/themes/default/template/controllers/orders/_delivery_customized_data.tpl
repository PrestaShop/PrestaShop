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
			<span class="productName">{$product['product_name']} - {l s='Customized'}</span><br />
			{if ($product['product_reference'])}{l s='Ref:'} {$product['product_reference']}<br />{/if}
			{if ($product['product_supplier_reference'])}{l s='Ref Supplier:'} {$product['product_supplier_reference']}{/if}
		</td>
		<td align="center">
			<span class="product_price_show">{displayPrice price=$product['product_price_wt'] currency=$currency->id|intval}</span>
		</td>
		<td align="center" class="productQuantity">{$product['customizationQuantityTotal']}</td>
		{if $display_warehouse}<td style="" align="center">&nbsp;</td>{/if}
		<td align="center" class="total_product">
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{displayPrice price=Tools::ps_round($product['product_price'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{else}
			{displayPrice price=Tools::ps_round($product['product_price_wt'] * $product['customizationQuantityTotal'], 2) currency=$currency->id|intval}
		{/if}
		</td>
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
				{if $display_warehouse}<td style="" align="center">&nbsp;</td>{/if}
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
				<td class="partial_refund_fields current-edit" style="text-align:left;display:none">
					<div style="width:40%;margin-top:5px;float:left">{l s='Quantity:'}</div> <div style="width:60%;margin-top:2px;float:left"><input type="text" size="3" name="partialRefundProductQuantity[{$product['id_order_detail']|intval}]" value="0" />		
					0/{$customization['quantity']-$customization['quantity_refunded']}
					</div>
					<div style="width:40%;margin-top:5px;float:left">{l s='Amount:'}</div> <div style="width:60%;margin-top:2px;float:left">{$currency->prefix}<input type="text" size="3" name="partialRefundProduct[{$product['id_order_detail']|intval}]" />{$currency->suffix}</div>
				</td>
			</tr>
		{/foreach}
	{/foreach}
{/if}
