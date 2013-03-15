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

<input type="hidden" name="cart_product_id[]" value="{$product.id_product}"/>
<input type="hidden" id="cart_product_attribute_id_{$product.id_product}" value="{$product.id_product_attribute|intval}"/>
<input type="hidden" id="cart_product_address_delivery_id_{$product.id_product}" value="{$product.id_address_delivery}"/>

<div class="fl width-20">
	<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" class="img_product_cart" />
</div>
<div class="fl width-60 padding-left-5px">
	<h3>{$product.name}</h3>
	{if $product.reference}<p>{l s='Ref:'} {$product.reference}</p>{/if}
	<p>{$product.description_short}</p>
</div>
<div class="fl width-15" style="text-align:right">
	<p class="price" id="product_price_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" style="padding-top:20px">
		{if !empty($product.gift)}
			<h3 class="gift-icon" style="background-color:#0088CC;color:white;display:inline;padding:2px 14px;-webkit-border-radius:6px;border-radius:6px;font-weight:normal">{l s='Gift!'}</h3>
		{else}
			{if isset($product.is_discounted) && $product.is_discounted}
				<span style="text-decoration:line-through;">{convertPrice price=$product.price_without_specific_price}</span><br />
			{/if}
			{if !$priceDisplay}
				{convertPrice price=$product.price_wt}
			{else}
				{convertPrice price=$product.price}
			{/if}
		{/if}
	</p>
</div>