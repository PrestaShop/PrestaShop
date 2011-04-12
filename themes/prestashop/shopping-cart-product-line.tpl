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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<tr id="product_{$product.id_product}_{$product.id_product_attribute}" class="{if $smarty.foreach.productLoop.last}last_item{elseif $smarty.foreach.productLoop.first}first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item">
	<td class="cart_product">
		<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small')}" alt="{$product.name|escape:'htmlall':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
	</td>
	<td class="cart_description">
		<h5><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></h5>
		{if isset($product.attributes) && $product.attributes}<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">{$product.attributes|escape:'htmlall':'UTF-8'}</a>{/if}
	</td>
	<td class="cart_ref">{if $product.reference}{$product.reference|escape:'htmlall':'UTF-8'}{else}--{/if}</td>
	<td class="cart_availability">
		{if $product.active AND ($product.allow_oosp OR ($product.quantity <= $product.stock_quantity)) AND $product.available_for_order AND !$PS_CATALOG_MODE}
			<img src="{$img_dir}icon/available.gif" alt="{l s='Available'}" width="14" height="14" />
		{else}
			<img src="{$img_dir}icon/unavailable.gif" alt="{l s='Out of stock'}" width="14" height="14" />
		{/if}
	</td>
	<td class="cart_unit">
		<span class="price" id="product_price_{$product.id_product}_{$product.id_product_attribute}">
			{if !$priceDisplay}{convertPrice price=$product.price_wt}{else}{convertPrice price=$product.price}{/if}
		</span>
	</td>
	<td class="cart_quantity"{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} style="text-align: center;"{/if}>
		{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}" >{$product.customizationQuantityTotal}</span>{/if}
		{if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}
			<div>
				<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}" href="{$link->getPageLink('cart.php', true)}?delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;token={$token_cart}" title="{l s='Delete'}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="icon" width="11" height="13" /></a>
			</div>
			<div id="cart_quantity_button" style="float:left;">
			<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}" href="{$link->getPageLink('cart.php', true)}?add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;token={$token_cart}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" /></a><br />
			{if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
			<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}" href="{$link->getPageLink('cart.php', true)}?add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;op=down&amp;token={$token_cart}" title="{l s='Subtract'}">
				<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
			</a>
			{else}
			<a class="cart_quantity_down" style="opacity: 0.3;" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}" title="{l s='You must purchase a minimum of '}{$product.minimal_quantity}{l s=' of this product.'}">
				<img src="{$img_dir}icon/quantity_down.gif" width="14" height="9" alt="{l s='Subtract'}" />
			</a>
			{/if}
			</div>
			<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_hidden" />
			<input size="2" type="text" class="cart_quantity_input" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}" />
			
		{/if}
	</td>
	<td class="cart_total">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}">
			{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
				{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
			{else}
				{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
			{/if}
		</span>
	</td>
</tr>
