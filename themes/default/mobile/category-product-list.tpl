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
	<ul data-role="listview" id="category-list" class="ui-listview ui-grid-a">
	{foreach from=$products item=product name=products}
		<li class="ui-block-{if $smarty.foreach.products.index % 2}b{else}a{/if} product-list-row">
			<a href="{$product.link|escape:'htmlall':'UTF-8'}" data-ajax="false">
				<div class="product_img_wrapper"><img class="ui-li-thumb" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" /></div>
				<h3 class="ui-li-heading">{$product.name|escape:'htmlall':'UTF-8'}</h3>
				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<p class="ui-li-price">
					{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
						{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
					{/if}
					</p>
					{assign var='info3_str' value='&nbsp;'}
					{assign var='info3_class' value='on_sale'}
					{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						{capture assign='info3_str'}{l s='On sale!'}{/capture}
					{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						{capture assign='info3_str'}{l s='Reduced price!'}{/capture}
						{assign var='info3_class' value='discount'}
					{/if}
					<p class="ui-li-price-info {$info3_class}"><span>{$info3_str}</span></p>
					<p class="availability">
					{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
						{if ($product.allow_oosp || $product.quantity > 0)}{l s='Available'}{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}{l s='Product available with different options'}{else}{l s='Out of stock'}{/if}
					{else}
						&nbsp;
					{/if}
					</p>
					
					{if isset($product.online_only) && $product.online_only}
						<p class="online_only">{l s='Online only!'}</p>
					{/if}
				{/if}
				{if isset($product.new) && $product.new == 1}<p class="new">{l s='New'}</p>{/if}
			</a>
		</li>
	{/foreach}
	</ul><!-- #category-list -->
{/if}
