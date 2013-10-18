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
<!-- Customized products -->
{* > TO CHECK ==========================*}
{*if isset($delivery_product.customizedDatas)}
	<li class="item">
		{if $return_allowed}<span class="order_cb"></span>{/if}
		<h3>{$delivery_product.product_name|escape:'htmlall':'UTF-8'}</h3>
		<p><strong>{l s='Reference'}</strong> {if $delivery_product.product_reference}{$delivery_product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
		<p><strong>{l s='Quantity'}</strong></p>
		<fieldset><input class="order_qte_input"  name="order_qte_input[{$smarty.foreach.products.index}]" type="text" size="2" value="{$delivery_product.customizationQuantityTotal|intval}" /><span class="order_qte_span editable">{$delivery_product.customizationQuantityTotal|intval}</span></fieldset>
		<p><strong>{l s='Unit price'}</strong> 
		{if $group_use_tax}
			{convertPriceWithCurrency price=$delivery_product.unit_price_tax_incl currency=$currency}
		{else}
			{convertPriceWithCurrency price=$delivery_product.unit_price_tax_excl currency=$currency}
		{/if}
		</p>
		<p><strong>{l s='Total price'}</strong> 
		{if isset($customizedDatas.$delivery_productId.$delivery_productAttributeId)}
			{if $group_use_tax}
				{convertPriceWithCurrency price=$delivery_product.total_customization_wt currency=$currency}
			{else}
				{convertPriceWithCurrency price=$delivery_product.total_customization currency=$currency}
			{/if}
		{else}
			{if $group_use_tax}
				{convertPriceWithCurrency price=$delivery_product.total_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$delivery_product.total_price_tax_excl currency=$currency}
			{/if}
		{/if}
		</p>
	</li>
	{foreach from=$delivery_product.customizedDatas item='customization' key='customizationId'}
	<li class="alternate_item">
		{if $return_allowed}<p class="order_cb"><input type="checkbox" id="cb_{$delivery_product.id_order_detail|intval}" name="customization_ids[{$delivery_product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></p>{/if}
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
{if $delivery_product.product_quantity > $delivery_product.customizationQuantityTotal}
	<li class="item" id="cb-{$delivery_product.id_order_detail|intval}" data-icon="back">
		{if $return_allowed}<a href="#" data-ajax="false">{/if}
		<h3>
			{if $delivery_product.download_hash && $invoice && $delivery_product.display_filename != ''}
				{if isset($is_guest) && $is_guest}
				<a href="{$link->getPageLink('get-file', true, NULL, "key={$delivery_product.filename|escape:'htmlall':'UTF-8'}-{$delivery_product.download_hash|escape:'htmlall':'UTF-8'}&amp;id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html'}" title="{l s='Download this product'}" data-ajax="false">
				{else}
					<a href="{$link->getPageLink('get-file', true, NULL, "key={$delivery_product.filename|escape:'htmlall':'UTF-8'}-{$delivery_product.download_hash|escape:'htmlall':'UTF-8'}")|escape:'html'}" title="{l s='Download this product'}" data-ajax="false">
				{/if}
					<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'}" />
					{$delivery_product.product_name|escape:'htmlall':'UTF-8'}
				</a>
			{else}
				{$delivery_product.product_name|escape:'htmlall':'UTF-8'}
			{/if}
		</h3>
		<p><strong>{l s='Reference'}</strong> {if $delivery_product.product_reference}{$delivery_product.product_reference|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
		<p><strong>{l s='Quantity'}</strong></p>
		<fieldset><input class="order_qte_input" data-mini="true" name="order_qte_input[{$delivery_product.id_order_detail|intval}]" type="text" size="2" value="{$deliveryproductQuantity|intval}" /><span class="order_qte_span editable">{$deliveryproductQuantity|intval}</span></fieldset>
		<p><strong>{l s='Unit price'}</strong> 
			{if $group_use_tax}
				{convertPriceWithCurrency price=$delivery_product.unit_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$delivery_product.unit_price_tax_excl currency=$currency}
			{/if}
		</p>
		<p><strong>{l s='Total price'}</strong> 
			{if $group_use_tax}
				{convertPriceWithCurrency price=$delivery_product.total_price_tax_incl currency=$currency}
			{else}
				{convertPriceWithCurrency price=$delivery_product.total_price_tax_excl currency=$currency}
			{/if}
		</p>
		{if $return_allowed}</a>{/if}
	</li>
{/if}
