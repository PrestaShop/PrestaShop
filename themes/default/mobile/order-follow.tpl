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

{capture assign='page_title'}{l s='Return Merchandise Authorization (RMA)'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
	<a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="{$link->getPageLink('my-account', true)}" data-ajax="false">{l s='My account'}</a>

	{if isset($errorQuantity) && $errorQuantity}<p class="error">{l s='You do not have enough products to request an additional merchandise return.'}</p>{/if}
	{if isset($errorMsg) && $errorMsg}<p class="error">{l s='Please provide an explanation for your RMA.'}</p>{/if}
	{if isset($errorDetail1) && $errorDetail1}<p class="error">{l s='Please check at least one product you would like to return.'}</p>{/if}
	{if isset($errorDetail2) && $errorDetail2}<p class="error">{l s='For each product you wish to add, please specify the desired quantity.'}</p>{/if}
	{if isset($errorNotReturnable) && $errorNotReturnable}<p class="error">{l s='This order cannot be returned.'}</p>{/if}
	
	<p>{l s='Here is a list of pending merchandise returns'}.</p>
	<div class="block-center" id="block-history">
		{if $ordersReturn && count($ordersReturn)}
		<table id="order-list" class="std">
			<thead>
				<tr>
					<th class="first_item">{l s='Return'}</th>
					<th class="item">{l s='Order'}</th>
					<th class="item">{l s='Package status'}</th>
					<th class="item">{l s='Date issued'}</th>
					<th class="last_item">{l s='Return slip'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$ordersReturn item=return name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
					<td class="bold"><a class="color-myaccount" href="javascript:showOrder(0, {$return.id_order_return|intval}, '{$link->getPageLink('order-return')}');" data-ajax="false">{l s='#'}{$return.id_order_return|string_format:"%06d"}</a></td>
					<td class="history_method"><a class="color-myaccount" href="javascript:showOrder(1, {$return.id_order|intval}, '{$link->getPageLink('order-detail')}');" data-ajax="false">{l s='#'}{$return.id_order|string_format:"%06d"}</a></td>
					<td class="history_method"><span class="bold">{$return.state_name|escape:'htmlall':'UTF-8'}</span></td>
					<td class="bold">{dateFormat date=$return.date_add full=0}</td>
					<td class="history_invoice">
					{if $return.state == 2}
						<a href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$return.id_order_return|intval}")}" title="{l s='Order return'} {l s='#'}{$return.id_order_return|string_format:"%06d"}" data-ajax="false"><img src="{$img_dir}icon/pdf.gif" alt="{l s='Order return'} {l s='#'}{$return.id_order_return|string_format:"%06d"}" class="icon" /></a>
						<a href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$return.id_order_return|intval}")}" title="{l s='Order return'} {l s='#'}{$return.id_order_return|string_format:"%06d"}" data-ajax="false">{l s='Print out'}</a>
					{else}
						--
					{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		<div id="block-order-detail" class="hidden">&nbsp;</div>
		{else}
			<p class="warning">{l s='You have no merchandise return authorizations.'}</p>
		{/if}
	</div>
</div><!-- /content -->
