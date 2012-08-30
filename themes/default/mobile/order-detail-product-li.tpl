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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Customized products -->
{* > TO CHECK ==========================*}
{*if isset($product.customizedDatas)}
	<li class="item">
		{if $return_allowed}<span class="order_cb"></span>{/if}
		<h3>{$product.product_name|escape:'htmlall':'UTF-8'}</h3>
		<p><strong>{l s='Reference'}</strong> {if $product.product_reference}{$product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
		<p><strong>{l s='Quantity'}</strong></p>
		<fieldset><input class="order_qte_input"  name="order_qte_input[{$smarty.foreach.products.index}]" type="text" size="2" value="{$product.customizationQuantityTotal|intval}" /><span class="order_qte_span editable">{$product.customizationQuantityTotal|intval}</span></fieldset>
		<p><strong>{l s='Unit price'}</strong> 
		{if $group_use_tax}
			{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
		{else}
			{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
		{/if}
		</p>
		<p><strong>{l s='Total price'}</strong> 
		{if isset($customizedDatas.$productId.$productAttributeId)}
			{if $group_use_tax}
				{convertPriceWithCurrency price=$product.total_customization_wt currency=$currency}
			{else}
				{convertPriceWithCurrency price=$product.total_customization currency=$currency}
			{/if}
		{else}
			{if $group_use_tax}
				{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
			{/if}
		{/if}
		</p>
	</li>
	{foreach from=$product.customizedDatas item='customization' key='customizationId'}
	<li class="alternate_item">
		{if $return_allowed}<p class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="customization_ids[{$product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></p>{/if}
		<p>
		{foreach from=$customization.datas key='type' item='datas'}
			{if $type == $CUSTOMIZE_FILE}
			<ul class="customizationUploaded">
				{foreach from=$datas item='data'}
					<li><img src="{$pic_dir}{$data.value}_small" alt="" class="customizationUploaded" /></li>
				{/foreach}
			</ul>
			{elseif $type == $CUSTOMIZE_TEXTFIELD}
			<ul class="typedText">{counter start=0 print=false}
				{foreach from=$datas item='data'}
					{assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
					<li>{$data.name|default:$customizationFieldName}{l s=':'} {$data.value}</li>
				{/foreach}
			</ul>
			{/if}
		{/foreach}
		</p>
		<p>
			<input class="order_qte_input" name="customization_qty_input[{$customizationId|intval}]" type="text" size="2" value="{$customization.quantity|intval}" /><span class="order_qte_span editable">{$customization.quantity|intval}</span>
		</p>
	</li>
	{/foreach}
{/if*}
{* / TO CHECK ==========================*}
<!-- Classic products -->
{if $product.product_quantity > $product.customizationQuantityTotal}
	<li class="item" id="cb-{$product.id_order_detail|intval}" data-icon="back">
		{if $return_allowed}<a href="#" data-ajax="false">{/if}
		<h3>
			{if $product.download_hash && $invoice && $product.display_filename != ''}
				{if isset($is_guest) && $is_guest}
				<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}&amp;id_order={$order->id}&secure_key={$order->secure_key}")}" title="{l s='download this product'}" data-ajax="false">
				{else}
					<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}")}" title="{l s='download this product'}" data-ajax="false">
				{/if}
					<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'}" />
					{$product.product_name|escape:'htmlall':'UTF-8'}
				</a>
			{else}
				{$product.product_name|escape:'htmlall':'UTF-8'}
			{/if}
		</h3>
		<p><strong>{l s='Reference'}</strong> {if $product.product_reference}{$product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
		<p><strong>{l s='Quantity'}</strong></p>
		<fieldset><input class="order_qte_input" data-mini="true" name="order_qte_input[{$product.id_order_detail|intval}]" type="text" size="2" value="{$productQuantity|intval}" /><span class="order_qte_span editable">{$productQuantity|intval}</span></fieldset>
		<p><strong>{l s='Unit price'}</strong> 
			{if $group_use_tax}
				{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
			{/if}
		</p>
		<p><strong>{l s='Total price'}</strong> 
			{if $group_use_tax}
				{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
			{/if}
		</p>
		{if $return_allowed}</a>{/if}
	</li>
{/if}
