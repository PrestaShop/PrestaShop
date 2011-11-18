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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{*************************************************************************************************************************************}
{* IMPORTANT : If you change some data here, you have to report these changes in the ./blockcart-json.js (to let ajaxCart available) *}
{*************************************************************************************************************************************}
{if $blockcart_ajax_allowed}
<script type="text/javascript">
var CUSTOMIZE_TEXTFIELD = {$blockcart_CUSTOMIZE_TEXTFIELD};
var customizationIdMessage = '{l s='Customization #' mod='blockcart' js=1}';
var removingLinkText = '{l s='remove this product from my cart' mod='blockcart' js=1}';
</script>
{/if}

<!-- MODULE Block cart -->
<div id="cart_block" class="block exclusive">
	<h4>
		<a href="{$link->getPageLink("$blockcart_order_process", true)}">{l s='Cart' mod='blockcart'}</a>
		{if $blockcart_ajax_allowed}
		<span id="block_cart_expand" {if isset($blockcart_colapseExpandStatus) && $blockcart_colapseExpandStatus eq 'expanded' || !isset($blockcart_colapseExpandStatus)}class="hidden"{/if}>&nbsp;</span>
		<span id="block_cart_collapse" {if isset($blockcart_colapseExpandStatus) && $blockcart_colapseExpandStatus eq 'collapsed'}class="hidden"{/if}>&nbsp;</span>
		{/if}
	</h4>
	<div class="block_content">
	<!-- block summary -->
	<div id="cart_block_summary" class="{if isset($blockcart_colapseExpandStatus) && $blockcart_colapseExpandStatus eq 'expanded' || !$blockcart_ajax_allowed || !isset($blockcart_colapseExpandStatus)}collapsed{else}expanded{/if}">
		<span class="ajax_cart_quantity" {if $cart_qties <= 0}style="display:none;"{/if}>{$cart_qties}</span>
		<span class="ajax_cart_product_txt_s" {if $cart_qties <= 1}style="display:none"{/if}>{l s='products' mod='blockcart'}</span>
		<span class="ajax_cart_product_txt" {if $cart_qties > 1}style="display:none"{/if}>{l s='product' mod='blockcart'}</span>
		<span class="ajax_cart_total" {if $cart_qties <= 0}style="display:none"{/if}>{if $priceDisplay == 1}{convertPrice price=$cart->getOrderTotal(false)}{else}{convertPrice price=$cart->getOrderTotal(true)}{/if}</span>
		<span class="ajax_cart_no_product" {if $cart_qties != 0}style="display:none"{/if}>{l s='(empty)' mod='blockcart'}</span>
	</div>
	<!-- block list of products -->
	<div id="cart_block_list" class="{if isset($blockcart_colapseExpandStatus) && $blockcart_colapseExpandStatus eq 'expanded' || !$blockcart_ajax_allowed || !isset($blockcart_colapseExpandStatus)}expanded{else}collapsed{/if}">
	{if $blockcart_products}
		<dl class="products">
		{foreach from=$blockcart_products item='product' name='myLoop'}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			<dt id="cart_block_product_{$product.id_product}{if $product.id_product_attribute}_{$product.id_product_attribute}{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
				<span class="quantity-formated"><span class="quantity">{$product.cart_quantity}</span>x</span>
				<a class="cart_block_product_name" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)}" title="{$product.name|escape:html:'UTF-8'}">
				{$product.name|truncate:13:'...'|escape:html:'UTF-8'}</a>
				<span class="remove_link">{if !isset($blockcart_customizedDatas.$productId.$productAttributeId)}<a rel="nofollow" class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product}&amp;ipa={$product.id_product_attribute}&amp;token={$static_token}")}" title="{l s='remove this product from my cart' mod='blockcart'}">&nbsp;</a>{/if}</span>
				<span class="price">{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice p="`$product.total`"}{else}{displayWtPrice p="`$product.total_wt`"}{/if}</span>
			</dt>
			{if isset($product.attributes_small)}
			<dd id="cart_block_combination_of_{$product.id_product}{if $product.id_product_attribute}_{$product.id_product_attribute}{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
				<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)}" title="{l s='Product detail'}">{$product.attributes_small}</a>
			{/if}

			<!-- Customizable datas -->
			{if isset($blockcart_customizedDatas.$productId.$productAttributeId)}
				{if !isset($product.attributes_small)}<dd id="cart_block_combination_of_{$product.id_product}{if $product.id_product_attribute}_{$product.id_product_attribute}{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">{/if}
				<ul class="cart_block_customizations" id="customization_{$productId}_{$productAttributeId}">
					{foreach from=$blockcart_customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizations'}
						<li name="customization">
							<div class="deleteCustomizableProduct" id="deleteCustomizableProduct_{$id_customization|intval}_{$product.id_product|intval}_{$product.id_product_attribute|intval}"><a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$static_token}")}"> </a></div>
							<span class="quantity-formated"><span class="quantity">{$customization.quantity}</span>x</span>{if isset($customization.datas.$blockcart_CUSTOMIZE_TEXTFIELD.0)}
							{$customization.datas.$blockcart_CUSTOMIZE_TEXTFIELD.0.value|escape:html:'UTF-8'|replace:"<br />":" "|truncate:28}
							{else}
							{l s='Customization #' mod='blockcart'}{$id_customization|intval}{l s=':' mod='blockcart'}
							{/if}
						</li>
					{/foreach}
				</ul>
				{if !isset($product.attributes_small)}</dd>{/if}
			{/if}

			{if isset($product.attributes_small)}</dd>{/if}

		{/foreach}
		</dl>
	{/if}
		<p {if $blockcart_products}class="hidden"{/if} id="cart_block_no_products">{l s='No products' mod='blockcart'}</p>

		{if $blockcart_discounts|@count > 0}<table id="vouchers">
			<tbody>
			{foreach from=$blockcart_discounts item=discount}
				<tr class="bloc_cart_voucher" id="bloc_cart_voucher_{$discount.id_discount}">
					<td class="name" title="{$discount.description}">{$discount.name|cat:' : '|cat:$discount.description|truncate:18:'...'|escape:'htmlall':'UTF-8'}</td>
					<td class="price">-{if $discount.value_real != '!'}{if $priceDisplay == 1}{convertPrice price=$discount.value_tax_exc}{else}{convertPrice price=$discount.value_real}{/if}{/if}</td>
					<td class="delete"><a href="{$link->getPageLink("$blockcart_order_process", true, NULL, "deleteDiscount={$discount.id_discount}")}" title="{l s='Delete'}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" width="11" height="13" class="icon" /></a></td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		{/if}

		<p id="cart-prices">
			<span>{l s='Shipping' mod='blockcart'}</span>
			<span id="cart_block_shipping_cost" class="price ajax_cart_shipping_cost">{$blockcart_shipping_cost}</span>
			<br/>
			{if $blockcart_show_wrapping}
				{assign var='blockcart_cart_flag' value='Cart::ONLY_WRAPPING'|constant}
				<span>{l s='Wrapping' mod='blockcart'}</span>
				<span id="cart_block_wrapping_cost" class="price cart_block_wrapping_cost">{if $priceDisplay == 1}{convertPrice price=$cart->getOrderTotal(false, $blockcart_cart_flag)}{else}{convertPrice price=$cart->getOrderTotal(true, $blockcart_cart_flag)}{/if}</span>
				<br/>
			{/if}
			{if $blockcart_show_tax && isset($blockcart_tax_cost)}
				<span>{l s='Tax' mod='blockcart'}</span>
				<span id="cart_block_tax_cost" class="price ajax_cart_tax_cost">{$blockcart_tax_cost}</span>
				<br/>
			{/if}
			<span>{l s='Total' mod='blockcart'}</span>
			<span id="cart_block_total" class="price ajax_block_cart_total">{$blockcart_total}</span>
		</p>
		{if $use_taxes && $display_tax_label == 1 && $blockcart_show_tax}
			{if $priceDisplay == 0}
				<p id="cart-price-precisions">
					{l s='Prices are tax included' mod='blockcart'}
				</p>
			{/if}
			{if $priceDisplay == 1}
				<p id="cart-price-precisions">
					{l s='Prices are tax excluded' mod='blockcart'}
				</p>
			{/if}
		{/if}
		<p id="cart-buttons">
			{capture name=step_order_process}
				{if $blockcart_order_process == 'order'}step=1{/if}
			{/capture}
			{if $blockcart_order_process == 'order'}<a href="{$link->getPageLink("$blockcart_order_process", true)}" class="button_small" title="{l s='Cart' mod='blockcart'}">{l s='Cart' mod='blockcart'}</a>{/if}
			<a href="{$link->getPageLink("$blockcart_order_process", true, NULL, "{$smarty.capture.step_order_process}")}" id="button_order_cart" class="exclusive{if $blockcart_order_process == 'order-opc'}_large{/if}" title="{l s='Check out' mod='blockcart'}">{l s='Check out' mod='blockcart'}</a>
		</p>
	</div>
	</div>
</div>
<!-- /MODULE Block cart -->

