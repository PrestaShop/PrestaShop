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

{capture assign='page_title'}{l s='Order'} {l s='#'}{$order->id|string_format:"%06d"}{/capture}
{include file='./page-title.tpl'}

<div class="ui-grid-a">
	<div class="ui-block-a">
		<a data-role="button" data-icon="arrow-l" data-theme="a" data-rel="back" href="#" title="" data-ajax="false">{l s='Back'}</a>
	</div>
	<div class="ui-block-b">
		{assign var='type_order' value="order"}
		{if isset($opc) && $opc}
			{assign var='type_order' value="order-opc"}
		{/if}
		<a data-icon="refresh" data-role="button" data-theme="e" href="{$link->getPageLink({$type_order}, true, NULL, "submitReorder&id_order={$order->id|intval}")}" title="{l s='Reorder'}" data-ajax="false">
		{l s='Reorder'}
		</a>
	</div>
</div><!-- .ui-grid-a -->

<div data-role="content" id="content">
	<h3 class="bg">{l s='Order #%s - placed on' sprintf=$order->id|string_format:"%06d"} {dateFormat date=$order->date_add full=0}</h3>


<ul class="info-order" data-role="listview">
	{if $carrier->id}<li><strong>{l s='Carrier'}</strong> {if $carrier->name == "0"}{$shop_name|escape:'htmlall':'UTF-8'}{else}{$carrier->name|escape:'htmlall':'UTF-8'}{/if}</li>{/if}
	<li><strong>{l s='Payment method'}</strong> <span class="color-myaccount">{$order->payment|escape:'htmlall':'UTF-8'}</span></li>
	{if $invoice AND $invoiceAllowed}
	<li>
		<img src="{$img_dir}icon/pdf.gif" alt="" class="icon" />
		<a href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&secure_key={$order->secure_key}{/if}" data-ajax="false">{l s='Download your invoice as a PDF file.'}</li>
	</li>
	{/if}
	{if $order->recyclable}
	<li><img src="{$img_dir}icon/recyclable.gif" alt="" class="icon" />&nbsp;{l s='You have given permission to receive your order in recycled packaging.'}</li>
	{/if}
	{if $order->gift}
		<li><img src="{$img_dir}icon/gift.gif" alt="" class="icon" />&nbsp;{l s='You have requested gift wrapping for this order.'}</li>
		<li>{l s='Message'} {$order->gift_message|nl2br}</li>
	{/if}
</ul><!-- .info-order -->

{if count($order_history)}
<h3 class="bg">{l s='Follow your order\'s status step-by-step'}</h3>
<ul data-role="listview" >
	{foreach from=$order_history item=state name="orderStates"}
	<li>
		{$state.ostate_name|escape:'htmlall':'UTF-8'}
		<span class="ui-li-aside">{dateFormat date=$state.date_add full=1}</span>
	</li>
	{/foreach}
</ul>
{/if}


{* > TO CHECK ==========================*}
{if isset($followup)}
<p class="bold">{l s='Click the following link to track the delivery of your order'}</p>
<a href="{$followup|escape:'htmlall':'UTF-8'}" data-ajax="false">{$followup|escape:'htmlall':'UTF-8'}</a>
{/if}
{* / TO CHECK ==========================*}

<h3 class="bg">{l s='Addresses'}</h3>
<div class="adresses_bloc clearfix">

{* > TO CHECK ==========================*}
{if $invoice AND $invoiceAllowed}
<p>
	<img src="{$img_dir}icon/pdf.gif" alt="" class="icon" />
	{if $is_guest}
		<a href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order->id}&amp;secure_key=$order->secure_key")}" >{l s='Download your invoice as a PDF file.'}</a>
	{else}
		<a href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order->id}")}" >{l s='Download your invoice as a PDF file.'}</a>
	{/if}
</p>
{/if}

{if $order->recyclable && isset($isRecyclable) && $isRecyclable}
<p><img src="{$img_dir}icon/recyclable.gif" alt="" class="icon" />&nbsp;{l s='You have given permission to receive your order in recycled packaging.'}</p>
{/if}
{if $order->gift}
	<p><img src="{$img_dir}icon/gift.gif" alt="" class="icon" />&nbsp;{l s='You have requested gift wrapping for this order.'}</p>
	<p>{l s='Message'} {$order->gift_message|nl2br}</p>
{/if}
{* / TO CHECK ==========================*}

<ul data-role="listview" data-inset="true" data-dividertheme="c">
	{if !$order->isVirtual()}
	<li data-role="list-divider">{l s='Invoice'}</li>
	<li>
	{foreach from=$inv_adr_fields name=inv_loop item=field_item}
		{if $field_item eq "company" && isset($address_invoice->company)}<p class="address_company">{$address_invoice->company|escape:'htmlall':'UTF-8'}</p>
		{elseif $field_item eq "address2" && $address_invoice->address2}<p class="address_address2">{$address_invoice->address2|escape:'htmlall':'UTF-8'}</p>
		{elseif $field_item eq "phone_mobile" && $address_invoice->phone_mobile}<p class="address_phone_mobile">{$address_invoice->phone_mobile|escape:'htmlall':'UTF-8'}</p>
		{else}
				{assign var=address_words value=" "|explode:$field_item}
				<p>{foreach from=$address_words item=word_item name="word_loop"}{if !$smarty.foreach.word_loop.first} {/if}<span class="address_{$word_item}">{$invoiceAddressFormatedValues[$word_item]|escape:'htmlall':'UTF-8'}</span>{/foreach}</p>
		{/if}
	{/foreach}
	</li>
	{/if}
	<li data-role="list-divider" >{l s='Delivery'}</li>
	<li>
	{foreach from=$dlv_adr_fields name=dlv_loop item=field_item}
		{if $field_item eq "company" && isset($address_delivery->company)}<p class="address_company">{$address_delivery->company|escape:'htmlall':'UTF-8'}</p>
		{elseif $field_item eq "address2" && $address_delivery->address2}<p class="address_address2">{$address_delivery->address2|escape:'htmlall':'UTF-8'}</p>
		{elseif $field_item eq "phone_mobile" && $address_delivery->phone_mobile}<p class="address_phone_mobile">{$address_delivery->phone_mobile|escape:'htmlall':'UTF-8'}</p>
		{else}
				{assign var=address_words value=" "|explode:$field_item} 
				<p>{foreach from=$address_words item=word_item name="word_loop"}{if !$smarty.foreach.word_loop.first} {/if}<span class="address_{$word_item}">{$deliveryAddressFormatedValues[$word_item]|escape:'htmlall':'UTF-8'}</span>{/foreach}</p>
		{/if}
	{/foreach}
	</li>
</ul>
</div><!-- .adresses_bloc -->

<!-- order details -->
<h3 class="bg">{l s='Order details'}</h3>
{* > TO CHECK ==========================*}
{*$HOOK_ORDERDETAILDISPLAYED*}
{* / TO CHECK ==========================*}
{if $return_allowed}<p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>{/if}
{if !$is_guest}<form action="{$link->getPageLink('order-follow', true)}" method="post">{/if}
<ul data-role="listview" data-inset="true">
{foreach from=$products item=product name=products}
	{if !isset($product.deleted)}
		{assign var='productId' value=$product.product_id}
		{assign var='productAttributeId' value=$product.product_attribute_id}
		{if isset($product.customizedDatas)}
			{assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
		{else}
			{assign var='productQuantity' value=$product.product_quantity}
		{/if}
		{include file="./order-detail-product-li.tpl"}
	{/if}
{/foreach}
{* > TO CHECK ==========================*}
{foreach from=$discounts item=discount}
	<li class="item">
		<h3>{$discount.name|escape:'htmlall':'UTF-8'}</h3>
		<p>{l s='Voucher'} {$discount.name|escape:'htmlall':'UTF-8'}</p>
		<p><span class="order_qte_span editable">1</span></p>
		<p>&nbsp;</p>
		<p>{if $discount.value != 0.00}{l s='-'}{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</p>
		{if $return_allowed}
		<p>&nbsp;</p>
		{/if}
	</li>
{/foreach}
{* / TO CHECK ==========================*}
	{if $priceDisplay && $use_tax}
		<li data-theme="b" class="item">
			{l s='Total products (tax excl.)'} <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithoutTaxes() currency=$currency}</span>
		</tr>
	{/if}
	<li data-theme="b" class="item">
		{l s='Total products'} {if $use_tax}{l s='(tax incl.)'}{/if}: <span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithTaxes() currency=$currency}</span>
	</li>
	{if $order->total_discounts > 0}
	<li data-theme="b" class="item">
		{l s='Total vouchers:'} <span class="price-discount">{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
	</li>
	{/if}
	{if $order->total_wrapping > 0}
	<li data-theme="b" class="item">
		{l s='Total gift wrapping cost:'} <span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
	</li>
	{/if}
	<li data-theme="b" class="item">
		{l s='Total shipping'} {if $use_tax}{l s='(tax incl.)'}{/if}: <span class="price-shipping">{displayWtPriceWithCurrency price=$order->total_shipping currency=$currency}</span>
	</li>
	<li data-theme="a" class="totalprice item">
		{l s='Total'} <span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
	</li>
</ul>
<!-- /order details -->

{if $order->getShipping()|count > 0}
<h3 class="bg">{l s='Carrier'}</h3>
<ul data-role="listview" >
	{foreach from=$order->getShipping() item=line}
	<li>
		<h3>{$line.carrier_name}</h3>
		<p><strong>{l s='Weight'}</strong> {$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</p>
		<p><strong>{l s='Shipping cost'}</strong> {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}{else}{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}{/if}</p>
		<p><strong>{l s='Tracking number'}</strong> {if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}" data-ajax="false">{$line.tracking_number}</a>{elseif $line.tracking_number != ''}{$line.tracking_number}{else}----{/if}</p>
		<span class="ui-li-aside">{$line.date_add}</span>
	</li>
	{/foreach}
</ul>
{/if}

{* > TO CHECK ==========================*}
{if !$is_guest}
	{if $return_allowed}
	<div id="returnOrderMessage">
		<h3>{l s='Merchandise return'}</h3>
		<p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>
		<fieldset>
			<textarea cols="67" rows="3" name="returnText"></textarea>
		</fieldset>
		<fieldset>
			<input type="submit" data-theme="a" value="{l s='Make an RMA slip'}" name="submitReturnMerchandise" class="button_large" />
			<input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
		</fieldset>
	</div>
	<br />
	{/if}
	</form>

	{if count($messages)}
	<h3>{l s='Messages'}</h3>
	<div class="table_block">
		<table class="detail_step_by_step std">
			<thead>
				<tr>
					<th class="first_item" style="width:150px;">{l s='From'}</th>
					<th class="last_item">{l s='Message'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$messages item=message name="messageList"}
				<tr class="{if $smarty.foreach.messageList.first}first_item{elseif $smarty.foreach.messageList.last}last_item{/if} {if $smarty.foreach.messageList.index % 2}alternate_item{else}item{/if}">
					<td>
						{if isset($message.ename) && $message.ename}
							{$message.efirstname|escape:'htmlall':'UTF-8'} {$message.elastname|escape:'htmlall':'UTF-8'}
						{elseif $message.clastname}
							{$message.cfirstname|escape:'htmlall':'UTF-8'} {$message.clastname|escape:'htmlall':'UTF-8'}
						{else}
							<b>{$shop_name|escape:'htmlall':'UTF-8'}</b>
						{/if}
						<br />
						{dateFormat date=$message.date_add full=1}
					</td>
					<td>{$message.message|nl2br}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}
	{if isset($errors) && $errors}
		<div class="error">
			<p>{if $errors|@count > 1}{l s='There are %d errors' sprintf=$errors|@count}{else}{l s='There is %d error' sprintf=$errors|@count}{/if} :</p>
			<ol>
			{foreach from=$errors key=k item=error}
				<li>{$error}</li>
			{/foreach}
			</ol>
		</div>
	{/if}
	{* / TO CHECK ==========================*}
	<form action="{$link->getPageLink('order-detail', true)}" method="post" class="std" id="sendOrderMessage">
		<h3 class="bg">{l s='Add a message'}</h3>
		<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
		<fieldset>
			<label for="id_product">{l s='Product'}</label>
			<select name="id_product" style="width:300px;">
				<option value="0">{l s='-- Choose --'}</option>
				{foreach from=$products item=product name=products}
					<option value="{$product.product_id}">{$product.product_name}</option>
				{/foreach}
			</select>
		</fieldset>
		<fieldset>
			<textarea name="msgText"></textarea>
		</fieldset>
		<input type="hidden" name="id_order" value="{$order->id|intval}" />
		<input type="submit" data-role="button" data-theme="a" name="submitMessage" value="{l s='Send'}"/>
	</form>
{else}
<p><img src="{$img_dir}icon/infos.gif" alt="" class="icon" />&nbsp;{l s='You cannot return merchandise with a guest account'}</p>
{/if}
</div><!-- #content -->
