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

{if ($product.customizedDatas)}
	<tr>
		<td align="center">{if ($product.image->id)}{$product.image_tag}{else}'--'{/if}</td>
		<td>
			<a href="index.php?controller=adminproducts&id_product={$product['product_id']}&updateproduct&token={getAdminToken tab='AdminProducts'}">
			<span class="productName">{$product['product_name']} - {l s='customized'}</span><br />
			{if ($product['product_reference'])}{l s='Ref:'} {$product['product_reference']}<br />{/if}
			{if ($product['product_supplier_reference'])}{l s='Ref Supplier:'} {$product['product_supplier_reference']}{/if}
			</a>
		</td>
		<td align="center">{displayPrice price=$product['product_price_wt'] currency=$currency->id}</td>
		<td align="center" class="productQuantity">{$product['customizationQuantityTotal']}</td>
		{if ($order->hasBeenPaid())}<td align="center" class="productQuantity">{$product['customizationQuantityRefunded']}</td>{/if}
		{if ($order->hasBeenDelivered())}<td align="center" class="productQuantity">{$product['customizationQuantityReturned']}</td>{/if}
		<td align="center" class="productQuantity"> - </td>
		<td align="center">
		{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
			{displayPrice price=Tools::ps_round($product['product_price'] * $product['customizationQuantityTotal'], 2) currency=$currency->id}
		{else}
			{displayPrice price=Tools::ps_round($product['product_price_wt'] * $product['customizationQuantityTotal'], 2) currency=$currency->id}
		{/if}
		</td>
		<td align="center" class="cancelCheck">--</td>
		<td align="center" class="cancelQuantity">--</td>
	</tr>
	{foreach from=$product.customizedDatas key=customizationId item=customization}
		<tr>
			<td colspan="2">
		{foreach from=$customization.datas key=type item=datas}
			{if ($type == Product::CUSTOMIZE_FILE)}
				<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
				{foreach from=$datas item=data}
					<li style="display: inline; margin: 2px;">
						<a href="displayImage.php?img={$data['value']}&name={$order->id}-file{$data@iteration}" target="_blank"><img src="'{$smarty.const._THEME_PROD_PIC_DIR_}{$data['value']}_small" alt="" /></a>
					</li>
				{/foreach}
				</ul>
			{elseif ($type == Product::CUSTOMIZE_TEXTFIELD)}
				<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
				{foreach from=$datas item=data}
					<li>{if $data['name']}{$data['name']}{else}{l s='Text #'}{$data@iteration}{/if}{l s=':'} {$data['value']}</li>
				{/foreach}
				</ul>
			{/if}
		{/foreach}
			</td>
			<td align="center">-</td>
			<td align="center" class="productQuantity">{$customization['quantity']}</td>
			{if ($order->hasBeenPaid())}<td align="center">{$customization['quantity_refunded']}</td>{/if}
			{if ($order->hasBeenDelivered())}<td align="center">{$customization['quantity_returned']}</td>{/if}
			<td align="center">-</td>
			<td align="center">
			{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
				{displayPrice price=Tools::ps_round($product['product_price'] * $customization['quantity'], 2) currency=$currency->id}
			{else}
				{displayPrice price=Tools::ps_round($product['product_price_wt'] * $customization['quantity'], 2) currency=$currency->id}
			{/if}
			</td>
			<td align="center" class="cancelCheck">
				<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$customization['quantity_returned']}" />
				<input type="hidden" name="totalQty" id="totalQty" value="{$customization['quantity']}" />
				<input type="hidden" name="productName" id="productName" value="{$product['product_name']}" />
			{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) && (int)(($customization['quantity_returned']) < (int)($customization['quantity'])))}
				<input type="checkbox" name="id_customization[{$customizationId}]" id="id_customization[{$customizationId}]" value="{$product['id_order_detail']}" onchange="setCancelQuantity(this, '{$customizationId}', '{$customization['quantity'] - $customization['quantity_refunded']}')" '.{if (($customization['quantity_returned'] + $customization['quantity_refunded']) >= $customization['quantity'])}disabled="disabled"{/if} />
			{else}
				--
			{/if}
			</td>
			<td class="cancelQuantity">
			{if (($customization['quantity_returned'] + $customization['quantity_refunded']) >= $customization['quantity'])}
				<input type="hidden" name="cancelCustomizationQuantity[{$customizationId}]" value="0" />
			{elseif (!$order->hasBeenDelivered() || Configuration::get('PS_ORDER_RETURN'))}
				<input type="text" id="cancelQuantity_{$customizationId}" name="cancelCustomizationQuantity[{$customizationId}]" size="2" onclick="selectCheckbox(this);" value="" />
			{/if}
			{if ($order->hasBeenDelivered())}
				{$customization['quantity_returned']}/{$customization['quantity']-$customization['quantity_refunded']}
			{elseif ($order->hasBeenPaid())}
				{$customization['quantity_returned']}/{$customization['quantity']}
			{/if}
			</td>
		</tr>
	{/foreach}
{/if}
