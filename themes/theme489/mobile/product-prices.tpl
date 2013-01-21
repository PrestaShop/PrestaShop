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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6625 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="content_prices">
	{if $product->online_only}
	<p class="online_only">{l s='Online only'}</p>
	{/if}
	
	<div class="price">
		{if !$priceDisplay || $priceDisplay == 2}
			{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
		{elseif $priceDisplay == 1}
			{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
		{/if}
	
		<p class="our_price_display">
		{if $priceDisplay >= 0 && $priceDisplay <= 2}
			<span id="our_price_display">{convertPrice price=$productPrice}</span>
		{/if}
		</p><!-- .our_price_display -->
	
		{if $product->on_sale}
			<span class="on_sale">{l s='On sale!'}</span>
		{/if}
		{if $priceDisplay == 2}
			<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
		{/if}
		

		{if $product->specificPrice AND $product->specificPrice.reduction}
			<p class="old_price">
			{if $priceDisplay >= 0 && $priceDisplay <= 2}
				{if $productPriceWithoutReduction > $productPrice}
					<span class="old_price_display">{convertPrice price=$productPriceWithoutReduction}</span>
				{/if}
			{/if}
			{if $product->specificPrice.reduction_type == 'percentage'}
				<span class="reduction_percent">-{$product->specificPrice.reduction*100}%</span>
			{elseif $product->specificPrice.reduction_type == 'amount'}
				<span class="reduction_amount_display">-{convertPrice price=$product->specificPrice.reduction|floatval}</span>
			{/if}
			
			</p><!-- .old_price -->
		{/if}
	
	{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
		<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
	{/if}
	
	{if $product->ecotax != 0}
		<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
			{if $product->specificPrice AND $product->specificPrice.reduction}
			<br />{l s='(not impacted by the discount)'}
			{/if}
		</p>
	{/if}
	
	{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
		 {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
		<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
	{/if}
	</div><!-- .price -->
</div><!-- .content_prices -->