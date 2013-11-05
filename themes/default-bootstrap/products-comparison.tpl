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

{capture name=path}{l s='Product Comparison'}{/capture}

<h1 class="page-heading">{l s='Product Comparison'}</h1>

{if $hasProduct}
<script type="text/javascript">
	$('document').ready(function(){
		if (typeof reloadProductComparison != 'undefined')
			reloadProductComparison()
	});
</script>
<div class="products_block table-responsive">
	<table id="product_comparison" class="table table-bordered">
    	<tr>
			<td width="20%" class="td_empty">
            	<span>{l s='Features:'}</span>
            </td>
			{assign var='taxes_behavior' value=false}
			{if $use_taxes && (!$priceDisplay  || $priceDisplay == 2)}
				{assign var='taxes_behavior' value=true}
			{/if}
		{foreach from=$products item=product name=for_products}
			{assign var='replace_id' value=$product->id|cat:'|'}

			<td width="{$width}%" class="ajax_block_product comparison_infos product-block">
            	<div class="remove">
                	<a class="cmp_remove" href="{$link->getPageLink('products-comparison', true)|escape:'html'}" title="{l s='Remove'}" rel="ajax_id_product_{$product->id}"><i class="icon-trash"></i></a>
                </div>
                <div class="product-image-block">
                    <a href="{$product->getLink()}" title="{$product->name|escape:html:'UTF-8'}" class="product_image" >
                        <img class="img-responsive" src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'html'}" alt="{$product->name|escape:html:'UTF-8'}" />
                    </a>
                    {if isset($product->show_price) && $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                    	{if $product->on_sale}
                        	<div class="sale-box"><span class="sale">{l s='Sale!'}</span></div>
                        {/if}
                    {/if}
                </div>
				<h5><a class="product-name" href="{$product->getLink()}" title="{$product->name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product->name|truncate:45:'...'|escape:'htmlall':'UTF-8'}</a></h5>
                <div class="prices-container">
					{if isset($product->show_price) && $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
						<span class="price product-price">{convertPrice price=$product->getPrice($taxes_behavior)}</span>
						{if isset($product->specificPrice) && $product->specificPrice}
                        	{if {$product->specificPrice.reduction_type == 'percentage'}}
                            	<span class="old-price product-price">{displayWtPrice p=$product->getPrice($taxes_behavior)+($product->getPrice($taxes_behavior)* $product->specificPrice.reduction)}</span>
                        		<span class="price-percent-reduction">-{$product->specificPrice.reduction*100|floatval}%</span>
                            {else}
                            	<span class="old-price product-price">{convertPrice price=($product->getPrice($taxes_behavior) + $product->specificPrice.reduction)}</span>
                            	<span class="price-percent-reduction">-{convertPrice price=$product->specificPrice.reduction}</span>
                            {/if}
                        {/if}
						{if $product->on_sale}
						{elseif $product->specificPrice AND $product->specificPrice.reduction}
                            <div class="product_discount">
                                <span class="reduced-price">{l s='Reduced price!'}</span>
                            </div>
						{/if}

						{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
							{math equation="pprice / punit_price"  pprice=$product->getPrice($taxes_behavior)  punit_price=$product->unit_price_ratio assign=unit_price}
							<span class="comparison_unit_price">&nbsp;{convertPrice price=$unit_price} {l s='per %s' sprintf=$product->unity|escape:'htmlall':'UTF-8'}</span>
						{else}
						{/if}
					{/if}
					</div>
				<div class="product_desc">{$product->description_short|strip_tags|truncate:60:'...'}</div>
				
				<div class="comparison_product_infos">
				<!-- availability -->
				<p class="comparison_availability_statut">
					{if !(($product->quantity <= 0 && !$product->available_later) OR ($product->quantity != 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE)}
						<span id="availability_label">{l s='Availability:'}</span>
						<span id="availability_value"{if $product->quantity <= 0} class="warning-inline"{/if}>
							{if $product->quantity <= 0}
								{if $allow_oosp}
									{$product->available_later|escape:'htmlall':'UTF-8'}
								{else}
									{l s='This product is no longer in stock.'}
								{/if}
							{else}
								{$product->available_now|escape:'htmlall':'UTF-8'}
							{/if}
						</span>
					{/if}
				</p>
                <div class="clearfix">
                	<div class="button-container">
                            {if (!$product->hasAttributes() OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product->minimal_quantity == 1 AND $product->customizable != 2 AND !$PS_CATALOG_MODE}
                                {if ($product->quantity > 0 OR $product->allow_oosp)}
                                    <a class="button ajax_add_to_cart_button btn btn-default" rel="ajax_id_product_{$product->id}" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$product->id}&amp;token={$static_token}&amp;add")|escape:'html'}" title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a>
                                {else}
                                    <span class="ajax_add_to_cart_button button btn btn-default disabled"><span>{l s='Add to cart'}</span></span>
                                {/if}
                            {else}
                                
                            {/if}
                            <a class="button lnk_view btn btn-default" href="{$product->getLink()}" title="{l s='View'}"><span>{l s='View'}</span></a>
                        </div>
                </div>
                     
               </div>
			</td>
		{/foreach}
		</tr>
		{if $ordered_features}
		{foreach from=$ordered_features item=feature}
		<tr>
			{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
			<td class="{$classname} feature-name" >
				<strong>{$feature.name|escape:'htmlall':'UTF-8'}</strong>
			</td>

			{foreach from=$products item=product name=for_products}
				{assign var='product_id' value=$product->id}
				{assign var='feature_id' value=$feature.id_feature}
				{if isset($product_features[$product_id])}
					{assign var='tab' value=$product_features[$product_id]}
					<td  width="{$width}%" class="{$classname} comparison_infos">{if (isset($tab[$feature_id]))}{$tab[$feature_id]|escape:'htmlall':'UTF-8'}{/if}</td>
				{else}
					<td  width="{$width}%" class="{$classname} comparison_infos"></td>
				{/if}
			{/foreach}
		</tr>
		{/foreach}
		{else}
			<tr>
				<td></td>
				<td colspan="{$products|@count + 1}">{l s='No features to compare'}</td>
			</tr>
		{/if}

		{$HOOK_EXTRA_PRODUCT_COMPARISON}
	</table>
</div>
{else}
	<p class="alert alert-warning">{l s='There are no products selected for comparison.'}</p>
{/if}
<ul class="footer_link">
	<li><a class="button lnk_view btn btn-default" href="{$base_dir}"><span><i class="icon-chevron-left left"></i>{l s='Continue Shopping'}</span></a></li>
</ul>

