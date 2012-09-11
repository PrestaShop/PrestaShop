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

{capture assign='page_title'}{$product->name|escape:'htmlall':'UTF-8'}{/capture}
{include file='./page-title.tpl'}

{include file='./product-js.tpl'}

<div data-role="content" id="content" class="product">
	
	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}
	
	{include file="./product-images.tpl"}
	
	{if $product->description_short OR $packItems|@count > 0}
		{if $product->description_short}
		<div>{$product->description_short}</div>
		{/if}
	{/if}
	{if $packItems|@count > 0}
		<!-- pack description-->
		<div class="short_description_pack">
			<h3>{l s='Pack content'}</h3>
			{foreach from=$packItems item=packItem}
			<div class="pack_content">
				{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}" data-ajax="false">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
				<p>{$packItem.description_short}</p>
			</div>
			{/foreach}
		</div>
	{/if}
	
	{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference}
	<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')}" method="post" data-ajax="false">

			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" value="{$static_token}" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>
		<div class="clearfix">
			
			{include file="./product-attributes.tpl"}
			
			<div id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>
				<br />
				<label for="product_reference">{l s='Reference:'} </label>
				<span class="editable">{$product->reference|escape:'htmlall':'UTF-8'}</span>
				<br />
			</div>
			
			<!-- quantity wanted -->
			<div id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) OR $virtual OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<label for="qty" class="">{l s='Quantity:'}</label>
				<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" />
			</div><!-- #quantity_wanted_p -->
			
			<!-- minimal quantity wanted -->
			<div id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				{l s='This product is not sold individually. You must select at least'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b> {l s='quantity for this product.'}
			</div><!-- #minimal_quantity_wanted_p -->
			
			{*if $product->minimal_quantity > 1}
			<script type="text/javascript">
				ProductFn.checkMinimalQuantity();
			</script>
			{/if*}
			
			<!-- availability -->
			<div id="availability_statut"{if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<label id="availability_label" class="">{l s='Availability:'}</label>
				<div id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>
				{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}
				</div>
			</div><!-- #availability_statut -->
			
			<!-- number of item in stock -->
			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
			<p id="pQuantityAvailable"{if $product->quantity <= 0} style="display: none;"{/if}>
				<span id="quantityAvailable">{$product->quantity|intval}</span>
				<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='item in stock'}</span>
				<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='items in stock'}</span>
			</p>
			{/if}
			
			{* à checker avec JS *}
			{* ================================== *}
			<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
			{* ================================== *}
			
		</div><!-- .clearfix -->
		
		{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
			<hr width="99%" align="center" size="2" class="margin_less"/>
			{include file="./product-prices.tpl"}
		{else}
			<hr width="99%" align="center" size="2" class="margin_bottom"/>
		{/if}
		<div id="displayMobileAddToCartTop">
			{hook h="displayMobileAddToCartTop"}
		</div>
		<div id="add_to_cart" class="btn-row">
			{assign var='cart_btn_class' value='btn-cart'}
			{assign var='cart_btn_icon' value=''}
			{assign var='cart_btn_theme' value='e'}
			{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}
				{assign var='cart_btn_class' value=$cart_btn_class|cat:' disabled'}
				{assign var='cart_btn_theme' value='c'}
			{else}
				{assign var='cart_btn_icon' value='data-icon="plus"'}
			{/if}
			<button type="submit" data-theme="{$cart_btn_theme}" name="Submit" class="{$cart_btn_class}" value="submit-value" id="Submit" {$cart_btn_icon} >{l s='Add to cart'}</button>
		</div><!-- .btn-row -->
	</form><!-- #buy_block -->
	{/if}
	
	{* à checker avec JS *}
	{* ================================== *}
	{include file="./product-quantity-discount.tpl"}
	{* ================================== *}
	
	<hr width="99%" align="center" size="2" class=""/>
	<!-- description and features -->
	{include file="./product-desc-features.tpl"}
	
	{if isset($packItems) && $packItems|@count > 0}
	<!-- pack list -->
	<hr width="99%" align="center" size="2" class="margin_less"/>
	<div id="blockpack">
		<h2>{l s='Pack content'}</h2>
		{include file="./category-product-list.tpl" products=$packItems}
	</div>
{/if}
</div><!-- #content -->
{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
