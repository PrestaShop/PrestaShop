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

{if isset($products)}
	{*define numbers of product per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
		{assign var='nbItemsPerLine' value=3}
		{assign var='nbItemsPerLineTablet' value=2}
		{assign var='nbItemsPerLineMobile' value=3}
	{else}
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbItemsPerLineTablet' value=3}
		{assign var='nbItemsPerLineMobile' value=2}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
	<!-- Products list -->
	<ul class="product_list grid row {if isset($class) && $class} {$class}{/if}">
	{foreach from=$products item=product name=products}
		{math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
		{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
		{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
		{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
		<li class="ajax_block_product {if $page_name == 'index' || $page_name == 'product'}col-xs-12 col-sm-4 col-md-3{/if} {if $smarty.foreach.products.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLine == 1} first-in-line{/if} {if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModulo)}last-line{/if} {if $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 0}last-item-of-tablet-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 1}first-item-of-tablet-line{/if} {if $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 0}last-item-of-mobile-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 1}first-item-of-mobile-line{/if} {if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModuloMobile)}last-mobile-line{/if}">
			<div class="product-container" itemscope itemtype="http://schema.org/Product">
				<div class="left-block">
					<div class="product-image-container">
						<a
							class="product_img_link"
							href="{$product.link|escape:'html':'UTF-8'}"  
							title="{$product.name|escape:'html':'UTF-8'}"
							itemprop="url">
							<img 
								class="replace-2x img-responsive" 
								src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" 
								alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" 
								title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" 
								{if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}
								itemprop="image" />
						</a>
						{if isset($quick_view) && $quick_view}
							<a  
								class="quick-view"
								href="#" 
								rel="{$product.link|escape:'html':'UTF-8'}">
									<span>Quick view</span>
							</a>
						{/if}
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div 
								class="content_price"
								itemprop="offers" 
								itemscope itemtype="http://schema.org/Offer">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									<span itemprop="price" class="price product-price">
										{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									</span>
									<meta itemprop="priceCurrency" content="{$priceDisplay}" />
									{if isset($product.specific_prices) && $product.specific_prices}
										<span class="old-price product-price">
											{displayWtPrice p=$product.price_without_reduction}
										</span>
										{if isset($product.specific_prices.reduction) && $product.specific_prices.reduction && $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
										{/if}
									{/if}
								{/if}
							</div>
						{/if}
						{if isset($product.new) && $product.new == 1}
							<span class="new-box">
								<span class="new-label">{l s='New'}</span>
							</span>
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							<span class="sale-box">
								<span class="sale-label">{l s='Sale!'}</span>
							</span>
						{/if}
					</div>
				</div>
				<div class="right-block">
					<h5 itemprop="name">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a 
							class="product-name" 
							href="{$product.link|escape:'html':'UTF-8'}" 
							title="{$product.name|escape:'html':'UTF-8'}"
							itemprop="url" >
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>

					{hook h='displayProductListReviews' product=$product}

					<p class="product-desc" itemprop="description">
						{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
					</p>

					{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span itemprop="price" class="price product-price">
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							<meta itemprop="priceCurrency" content="{$priceDisplay}" />
							{if isset($product.specific_prices) && $product.specific_prices}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								{if isset($product.specific_prices.reduction) && $product.specific_prices.reduction && $product.specific_prices.reduction_type == 'percentage'}
									<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
								{/if}
							{/if}
						{/if}
					</div>
					{/if}

					<div class="button-container">
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if ($product.allow_oosp || $product.quantity > 0)}
								{if isset($static_token)}
									<a
										class="button ajax_add_to_cart_button btn btn-default" 
										href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html'}"
										rel="nofollow"
										title="{l s='Add to cart'}"
										data-id-product="{$product.id_product|intval}"
										token={$static_token}", false)|escape:'html'}">
										<span>{l s='Add to cart'}</span>
									</a>
								{else}
									<a
										class="button ajax_add_to_cart_button btn btn-default"
										href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}", false)|escape:'html'}"
										rel="nofollow"
										title="{l s='Add to cart'}"
										data-id-product="{$product.id_product|intval}">
										<span>{l s='Add to cart'}</span>
									</a>
								{/if}						
							{else}
								<span class="button ajax_add_to_cart_button btn btn-default disabled">
									<span>{l s='Add to cart'}</span>
								</span>
							{/if}
						{/if}
						<a itemprop="url" class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{l s='More'}</span>
						</a>
					</div>
					
					{if isset($product.color_list)}
						<div class="color-list-container">{$product.color_list} </div>
					{/if}

					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="discount">{l s='Reduced price!'}</span>
							{/if}
					</div>

					{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="availability">
								{if ($product.allow_oosp || $product.quantity > 0)}
									<span class="available-now">
										<link itemprop="availability" href="http://schema.org/InStock" />{l s='In Stock'}
									</span>
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="available-dif">
										<link itemprop="availability" href="http://schema.org/LimitedAvailability" />{l s='Product available with different options'}
									</span>
								{else}
									<span class="out-of-stock">
										<link itemprop="availability" href="http://schema.org/OutOfStock" />{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
					
				</div>

				<div class="functional-buttons clearfix">
					{hook h='displayProductListFunctionalButtons' product=$product}
					{if isset($comparator_max_item) && $comparator_max_item}
						<div class="compare">
							<a class="addToCompare" href="#" data-id-product="{$product.id_product}" onClick="addToCompare('{$product.id_product|intval}'); return false;">{l s='Add to Compare'}</a>
						</div>
					{/if}
				</div>
			</div><!-- .product-container> -->
		</li>
	{/foreach}
	</ul>
<!-- Script for transformation Grid/List layouts -->
{if $page_name == 'product'}
	<script type="text/javascript">
		var comparator_max_item = {$comparator_max_item};
		var comparedProductsIds = [];
		{foreach from=$compared_products key=k item=product}comparedProductsIds.push({$product});{/foreach}
		$('document').ready(function(){
			blockHover();
		});
	</script>
{/if}
<script type="text/javascript">
	function blockHover(status) {
		$('.product_list.grid li.ajax_block_product').each(function() {
			$(this).find('.product-container').hover(
			function(){
				if ($('body').find('.container').width() == 1170)
				$(this).parent().addClass('hovered'); 
			},
			function(){
				if ($('body').find('.container').width() == 1170)
				$(this).parent().removeClass('hovered');
			}
		)});	
	}
</script>

{if $page_name !='index' && $page_name !='product'}  <!--// excluding page for Grid/List-->
	<script type="text/javascript"><!--
		function display(view) {
			if (view == 'list') {
				$('ul.product_list').removeClass('grid').addClass('list row');
				$('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
				$('.product_list > li').each(function(index, element) {
					html = '';
					html = '<div class="product-container"><div class="row">';
						html += '<div class="left-block col-xs-4 col-xs-5 col-md-4">' + $(element).find('.left-block').html() + '</div>';
						html += '<div class="center-block col-xs-4 col-xs-7 col-md-4">';
							html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
							html += '<h5>'+ $(element).find('h5').html() + '</h5>';
							var rating = $(element).find('.comments_note').html(); // check : rating
							if (rating != null) { 
								html += '<div class="comments_note">'+ rating + '</div>';
							}
							html += '<p class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
							var colorList = $(element).find('.color-list-container').html();
							if (colorList != null) {
								html += '<div class="color-list-container">'+ colorList +'</div>';
							}
							var availability = $(element).find('.availability').html();	// check : catalog mode is enabled
							if (availability != null) {
								html += '<span class="availability">'+ availability +'</span>';
							}
						html += '</div>';	
						html += '<div class="right-block col-xs-4 col-xs-12 col-md-4"><div class="right-block-content row">';
							var price = $(element).find('.content_price').html();       // check : catalog mode is enabled
							if (price != null) { 
								html += '<div class="content_price col-xs-5 col-md-12">'+ price + '</div>';
							}
							html += '<div class="button-container col-xs-7 col-md-12">'+ $(element).find('.button-container').html() +'</div>';
							html += '<div class="functional-buttons clearfix col-sm-12">' + $(element).find('.functional-buttons').html() + '</div>';
						html += '</div>';
					html += '</div></div>';
				$(element).html(html);
				});		
				$('.display').find('li#list').addClass('selected');
				$('.display').find('li#grid').removeAttr('class');
				$.totalStorage('display', 'list');
				if (typeof ajaxCart != 'undefined')      // cart button reload
					ajaxCart.overrideButtonsInThePage();
				if (typeof quick_view != 'undefined') 	// qick view button reload
					quick_view();
			} else {
				$('ul.product_list').removeClass('list').addClass('grid row');
				$('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
				$('.product_list > li').each(function(index, element) {
				html = '';
				html += '<div class="product-container">';
					html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
					html += '<div class="right-block">';
						html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
						html += '<h5>'+ $(element).find('h5').html() + '</h5>';
						var rating = $(element).find('.comments_note').html(); // check : rating
							if (rating != null) { 
								html += '<div class="comments_note">'+ rating + '</div>';
							}
						html += '<p class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
						var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
							if (price != null) { 
								html += '<div class="content_price">'+ price + '</div>';
							}
						html += '<div class="button-container">'+ $(element).find('.button-container').html() +'</div>';
						var colorList = $(element).find('.color-list-container').html();
						if (colorList != null) {
							html += '<div class="color-list-container">'+ colorList +'</div>';
						}
						var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
						if (availability != null) {
							html += '<span class="availability">'+ availability +'</span>';
						}
					html += '</div>';
					html += '<div class="functional-buttons clearfix">' + $(element).find('.functional-buttons').html() + '</div>';
				html += '</div>';		
				$(element).html(html);
				});
				$('.display').find('li#grid').addClass('selected');
				$('.display').find('li#list').removeAttr('class');
				$.totalStorage('display', 'grid');			
				if (typeof ajaxCart != 'undefined') 	// cart button reload
					ajaxCart.overrideButtonsInThePage();
				if (typeof quick_view != 'undefined') 	// qick view button reload
					quick_view();
				blockHover();
			}	
		}
	view = $.totalStorage('display');
	if (view) {
		display(view);
	} else {
		display('grid');
	}
	//--></script>
{/if}
{/if}
