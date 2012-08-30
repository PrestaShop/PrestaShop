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
*  @version  Release: $Revision: 6599 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='Order history'}{/capture}
{include file='./page-title.tpl'}
{include file="$tpl_dir./errors.tpl"}
<div data-role="content" id="content">
	<a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="{$link->getPageLink('my-account', true)}" data-ajax="false">{l s='My account'}</a>

	<p>{l s='Here are the orders you have placed since the creation of your account'}.</p>

	{if $slowValidation}<p class="warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.'}</p>{/if}

	<div class="block-center" id="block-history">
		{if $orders && count($orders)}
		<ul data-role="listview" data-theme="c" data-inset="true" data-split-theme="c" data-split-icon="prestashop-pdf">
		{foreach from=$orders item=order name=myLoop}
			<li>
				{assign var="id_order" value={$order.id_order|intval}}
				<a class="color-myaccount" id="order-{$id_order}" href="{$link->getPageLink('order-detail', true, null, "id_order=$id_order")}" data-ajax="false">
					{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Products to download'}" title="{l s='Products to download'}" />{/if}
					<h3>{l s='#'}{$order.id_order|string_format:"%06d"}</h3>
					<p><strong>{l s='Total price'}</strong> {displayPrice price=$order.total_paid currency=$order.id_currency no_utf8=false convert=false}</p>
					<p><strong>{l s='Payment'}</strong> {$order.payment|escape:'htmlall':'UTF-8'}</p>
					<p><strong>{l s='Status'}</strong> {if isset($order.order_state)}{$order.order_state|escape:'htmlall':'UTF-8'}{/if}</p>
					<span class="ui-li-aside">{dateFormat date=$order.date_add full=0}</span>
				</a>
				{if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
				<a rel="external" data-iconshadow="false" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")}" title="{l s='Invoice'}" data-ajax="false">
					{l s='PDF'}
				</a>
				{/if}
			</li>
		{/foreach}
		</ul>
		{else}
			<p class="warning">{l s='You have not placed any orders.'}</p>
		{/if}
	</div>

</div><!-- /content -->
