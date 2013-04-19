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

{capture name=path}{l s='Guest tracking'}{/capture}
{include file="./breadcrumb.tpl"}

<h1>{l s='Guest Tracking'}</h1>

{if isset($order_collection)}
	{foreach $order_collection as $order}
		{assign var=order_state value=$order->getCurrentState()}
		{assign var=invoice value=$order->invoice}
		{assign var=order_history value=$order->order_history}
		{assign var=carrier value=$order->carrier}
		{assign var=address_invoice value=$order->address_invoice}
		{assign var=address_delivery value=$order->address_delivery}
		{assign var=inv_adr_fields value=$order->inv_adr_fields}
		{assign var=dlv_adr_fields value=$order->dlv_adr_fields}
		{assign var=invoiceAddressFormatedValues value=$order->invoiceAddressFormatedValues}
		{assign var=deliveryAddressFormatedValues value=$order->deliveryAddressFormatedValues}
		{assign var=currency value=$order->currency}
		{assign var=discounts value=$order->discounts}
		{assign var=invoiceState value=$order->invoiceState}
		{assign var=deliveryState value=$order->deliveryState}
		{assign var=products value=$order->products}
		{assign var=customizedDatas value=$order->customizedDatas}
		{assign var=HOOK_ORDERDETAILDISPLAYED value=$order->hook_orderdetaildisplayed}
		{if isset($order->total_old)}
			{assign var=total_old value=$order->total_old}
		{/if}
		{if isset($order->followup)}
			{assign var=followup value=$order->followup}
		{/if}
		
		<div id="block-history">
			<div id="block-order-detail" class="std" style="zoom:1">
			{include file="./order-detail.tpl"}
			</div>
		</div>
	{/foreach}

	<h2 id="guestToCustomer">{l s='For more advantages...'}</h2>

	{include file="$tpl_dir./errors.tpl"}
	
	{if isset($transformSuccess)}
		<p class="success">{l s='Your guest account has been successfully transformed into a customer account. You can now login as a registered shopper. '} <a href="{$link->getPageLink('authentication', true)}">{l s='page.'}</a></p>
	{else}
		<form method="post" action="{$action|escape:'htmlall':'UTF-8'}#guestToCustomer" class="std">
			<fieldset class="description_box">
				<p class="bold">{l s='Transform your guest account into a customer account and enjoy:'}</p>
				<ul class="bullet">
					<li>{l s='Personalized and secure access'}</li>
					<li>{l s='Fast and easy checkout'}</li>
					<li>{l s='Easier merchandise return'}</li>
				</ul>
				<p class="text">
					<label>{l s='Set your password:'}</label>
					<input type="password" name="password" />
				</p>
				
				<input type="hidden" name="id_order" value="{if isset($order->id)}{$order->id}{else}{if isset($smarty.get.id_order)}{$smarty.get.id_order|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.id_order)}{$smarty.post.id_order|escape:'htmlall':'UTF-8'}{/if}{/if}{/if}" />
				<input type="hidden" name="order_reference" value="{if isset($smarty.get.order_reference)}{$smarty.get.order_reference|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.order_reference)}{$smarty.post.order_reference|escape:'htmlall':'UTF-8'}{/if}{/if}" />
				<input type="hidden" name="email" value="{if isset($smarty.get.email)}{$smarty.get.email|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'}{/if}{/if}" />
				
				<p class="center"><input type="submit" class="exclusive_large" name="submitTransformGuestToCustomer" value="{l s='Send'}" /></p>
			</fieldset>
		</form>
	{/if}
{else}
	{include file="$tpl_dir./errors.tpl"}
	{if isset($show_login_link) && $show_login_link}
		<p><img src="{$img_dir}icon/userinfo.gif" alt="{l s='Information'}" class="icon" /><a href="{$link->getPageLink('my-account', true)}">{l s='Click here to login to your customer account.'}</a><br /><br /></p>
	{/if}
	<form method="post" action="{$action|escape:'htmlall':'UTF-8'}" class="std">
		<fieldset class="description_box">
			<p>{l s='To track your order, please enter the following information:'}</p>
			<p class="text">
				<label>{l s='Order Reference:'} </label>
				<input type="text" name="order_reference" value="{if isset($smarty.get.id_order)}{$smarty.get.id_order|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.id_order)}{$smarty.post.id_order|escape:'htmlall':'UTF-8'}{/if}{/if}" size="8" />
				<i>{l s='For example: QIIXJXNUI or QIIXJXNUI#1'}</i>
			</p>

			<p class="text">
				<label>{l s='Email'}</label>
				<input type="text" name="email" value="{if isset($smarty.get.email)}{$smarty.get.email|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'}{/if}{/if}" />
			</p>

			<p class="center"><input type="submit" class="button" name="submitGuestTracking" value="{l s='Send'}" /></p>
		</fieldset>
	</form>
{/if}
