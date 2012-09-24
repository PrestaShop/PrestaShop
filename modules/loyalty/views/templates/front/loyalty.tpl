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
*  @version  Release: $Revision: 8084 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='loyalty'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My loyalty points' mod='loyalty'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='My loyalty points' mod='loyalty'}</h2>

{if $orders}
<div class="block-center" id="block-history">
	{if $orders && count($orders)}
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Order' mod='loyalty'}</th>
				<th class="item">{l s='Date' mod='loyalty'}</th>
				<th class="item">{l s='Points' mod='loyalty'}</th>
				<th class="last_item">{l s='Points Status' mod='loyalty'}</th>
			</tr>
		</thead>
		<tfoot>
			<tr class="alternate_item">
				<td colspan="2" class="history_method bold" style="text-align:center;">{l s='Total points available:' mod='loyalty'}</td>
				<td class="history_method" style="text-align:left;">{$totalPoints|intval}</td>
				<td class="history_method">&nbsp;</td>
			</tr>
		</tfoot>
		<tbody>
		{foreach from=$displayorders item='order'}
			<tr class="alternate_item">
				<td class="history_link bold">{l s='#' mod='loyalty'}{$order.id|string_format:"%06d"}</td>
				<td class="history_date">{dateFormat date=$order.date full=1}</td>
				<td class="history_method">{$order.points|intval}</td>
				<td class="history_method">{$order.state|escape:'htmlall':'UTF-8'}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
	{else}
		<p class="warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>
<div id="pagination" class="pagination">
	{if $nbpagination < $orders|@count}
		<ul class="pagination">
		{if $page != 1}
			{assign var='p_previous' value=$page-1}
			<li id="pagination_previous"><a href="{summarypaginationlink p=$p_previous n=$nbpagination}">
			&laquo;&nbsp;{l s='Previous' mod='loyalty'}</a></li>
		{else}
			<li id="pagination_previous" class="disabled"><span>&laquo;&nbsp;{l s='Previous' mod='loyalty'}</span></li>
		{/if}
		{if $page > 2}
			<li><a href="{summarypaginationlink p='1' n=$nbpagination}">1</a></li>
			{if $page > 3}
				<li class="truncate">...</li>
			{/if}
		{/if}
		{section name=pagination start=$page-1 loop=$page+2 step=1}
			{if $page == $smarty.section.pagination.index}
				<li class="current"><span>{$page|escape:'htmlall':'UTF-8'}</span></li>
			{elseif $smarty.section.pagination.index > 0 && $orders|@count+$nbpagination > ($smarty.section.pagination.index)*($nbpagination)}
				<li><a href="{summarypaginationlink p=$smarty.section.pagination.index n=$nbpagination}">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a></li>
			{/if}
		{/section}
		{if $max_page-$page > 1}
			{if $max_page-$page > 2}
				<li class="truncate">...</li>
			{/if}
			<li><a href="{summarypaginationlink p=$max_page n=$nbpagination}">{$max_page}</a></li>
		{/if}
		{if $orders|@count > $page * $nbpagination}
			{assign var='p_next' value=$page+1}
			<li id="pagination_next"><a href="{summarypaginationlink p=$p_next n=$nbpagination}">{l s='Next' mod='loyalty'}&nbsp;&raquo;</a></li>
		{else}
			<li id="pagination_next" class="disabled"><span>{l s='Next' mod='loyalty'}&nbsp;&raquo;</span></li>
		{/if}
		</ul>
	{/if}
	{if $orders|@count > 10}
		<form action="{$pagination_link}" method="get" class="pagination">
			<p>
				<input type="submit" class="button_mini" value="{l s='OK'}" />
				<label for="nb_item">{l s='items:' mod='loyalty'}</label>
				<select name="n" id="nb_item">
				{foreach from=$nArray item=nValue}
					{if $nValue <= $orders|@count}
						<option value="{$nValue|escape:'htmlall':'UTF-8'}" {if $nbpagination == $nValue}selected="selected"{/if}>{$nValue|escape:'htmlall':'UTF-8'}</option>
					{/if}
				{/foreach}
				</select>
				<input type="hidden" name="p" value="1" />
			</p>
		</form>
	{/if}
	</div>

<br />{l s='Vouchers generated here are usable in the following categories : ' mod='loyalty'}
{if $categories}{$categories}{else}{l s='All' mod='loyalty'}{/if}

{if $transformation_allowed}
<p style="text-align:center; margin-top:20px">
	<a href="{$link->getModuleLink('loyalty', 'default', ['process' => 'transformpoints'])}" onclick="return confirm('{l s='Are you sure you want to transform your points into vouchers?' mod='loyalty' js=1}');">{l s='Transform my points into a voucher of' mod='loyalty'} <span class="price">{convertPrice price=$voucher}</span>.</a>
</p>
{/if}

<br />
<br />
<br />

<h2>{l s='My vouchers from loyalty points' mod='loyalty'}</h2>

{if $nbDiscounts}
<div class="block-center" id="block-history">
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Created' mod='loyalty'}</th>
				<th class="item">{l s='Value' mod='loyalty'}</th>
				<th class="item">{l s='Code' mod='loyalty'}</th>
				<th class="item">{l s='Valid from' mod='loyalty'}</th>
				<th class="item">{l s='Valid until' mod='loyalty'}</th>
				<th class="item">{l s='Status' mod='loyalty'}</th>
				<th class="last_item">{l s='Details' mod='loyalty'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$discounts item=discount name=myLoop}
			<tr class="alternate_item">
				<td class="history_date">{dateFormat date=$discount->date_add}</td>
				<td class="history_price"><span class="price">{if $discount->reduction_percent > 0}
						{$discount->reduction_percent}%
					{elseif $discount->reduction_amount}
						{displayPrice price=$discount->reduction_amount currency=$discount->reduction_currency}
					{else}
						{l s='Free shipping' mod='loyalty'}
					{/if}</span></td>
				<td class="history_method bold">{$discount->code}</td>
				<td class="history_date">{dateFormat date=$discount->date_from}</td>
				<td class="history_date">{dateFormat date=$discount->date_to}</td>
				<td class="history_method bold">{if $discount->quantity > 0}{l s='To use' mod='loyalty'}{else}{l s='Used' mod='loyalty'}{/if}</td>
				<td class="history_method">
					<a href="{$smarty.server.SCRIPT_NAME|escape:'htmlall':'UTF-8'}" onclick="return false" class="tips" title="
					{l s='Generated by these following orders' mod='loyalty'}|{foreach from=$discount->orders item=myorder name=myLoop}
					{l s='Order #%d' sprintf=$myorder.id_order mod='loyalty'}
					({displayPrice price=$myorder.total_paid currency=$myorder.id_currency}) :
					{if $myorder.points > 0}{l s='%d points.' sprintf=$myorder.points mod='loyalty'}{else}{l s='Cancelled' mod='loyalty'}{/if}
					{if !$smarty.foreach.myLoop.last}|{/if}{/foreach}">{l s='more...' mod='loyalty'}</a></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
</div>

{if $minimalLoyalty > 0}<p>{l s='The minimum order amount in order to use these vouchers is:'} {convertPrice price=$minimalLoyalty}</p>{/if}

<script type="text/javascript">
{literal}
$(document).ready(function()
{
	$('a.tips').cluetip({
		showTitle: false,
		splitTitle: '|',
		arrows: false,
		fx: {
			open: 'fadeIn',
			openSpeed: 'fast'
		}
	});
});
{/literal}
</script>
{else}
<p class="warning">{l s='No vouchers yet.' mod='loyalty'}</p>
{/if}
{else}
<p class="warning">{l s='No reward points yet.' mod='loyalty'}</p>
{/if}

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account', true)}">{l s='Back to Your Account' mod='loyalty'}</a></li>
	<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home' mod='loyalty'}</a></li>
</ul>
