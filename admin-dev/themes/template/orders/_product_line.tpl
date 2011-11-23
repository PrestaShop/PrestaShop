{*
* 2007-2011 PrestaShop
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

{* Assign product price *}
{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['product_price'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['product_price_wt']}
{/if}

{if ($product['product_quantity'] > $product['customizationQuantityTotal'])}
<tr{if isset($product.image) && $product.image->id && isset($product.image_size)} height="{$product['image_size'][1] + 7}"{/if}>
	<td align="center">{if isset($product.image) && $product.image->id}{$product.image_tag}{/if}</td>
	<td><a href="index.php?controller=adminproducts&id_product={$product['product_id']}&updateproduct&token={getAdminToken tab='AdminProducts'}">
		<span class="productName">{$product['product_name']}</span><br />
		{if $product.product_reference}{l s='Ref:'} {$product.product_reference}<br />{/if}
		{if $product.product_supplier_reference}{l s='Ref Supplier:'} {$product.product_supplier_reference}{/if}
	</a></td>
	<td align="center">{displayPrice price=$product_price currency=$currency->id}</td>
	<td align="center" class="productQuantity" {if ($product['product_quantity'] > 1)}style="font-weight:700;font-size:1.1em;color:red"{/if}>{$product['product_quantity']}</td>
	{if ($order->hasBeenPaid())}<td align="center" class="productQuantity">{$product['product_quantity_refunded']}</td>{/if}
	{if ($order->hasBeenDelivered())}<td align="center" class="productQuantity">{$product['product_quantity_return']}</td>{/if}
	<td align="center" class="productQuantity">{StockManagerFactory::getManager()->getProductRealQuantities($product['product_id'], $product['product_attribute_id'], null, true)}</td>
	<td align="center">{displayPrice price=(Tools::ps_round($product_price, 2) * ($product['product_quantity'] - $product['customizationQuantityTotal'])) currency=$currency->id}</td>
	<td align="center" class="cancelCheck">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="{$product['product_quantity_return']}" />
		<input type="hidden" name="totalQty" id="totalQty" value="{$product['product_quantity']}" />
		<input type="hidden" name="productName" id="productName" value="{$product['product_name']}" />
	{if ((!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN')) AND (int)($product['product_quantity_return']) < (int)($product['product_quantity']))}
		<input type="checkbox" name="id_order_detail[{$k}]" id="id_order_detail[{$k}]" value="{$product['id_order_detail']}" onchange="setCancelQuantity(this, {$product['id_order_detail']}, {$product['product_quantity_in_stock'] - $product['customizationQuantityTotal'] - $product['product_quantity_reinjected']})" {if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}disabled="disabled" {/if}/>
	{else}
		--
	{/if}
	</td>
	<td class="cancelQuantity">
	{if ($product['product_quantity_return'] + $product['product_quantity_refunded'] >= $product['product_quantity'])}
		<input type="hidden" name="cancelQuantity[{$k}]" value="0" />
	{elseif (!$order->hasBeenDelivered() OR Configuration::get('PS_ORDER_RETURN'))}
		<input type="text" id="cancelQuantity_{$product['id_order_detail']}" name="cancelQuantity[{$k}]" size="2" onclick="selectCheckbox(this);" value="" />
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
</tr>
{/if}