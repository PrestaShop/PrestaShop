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

{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Credit slips'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Credit slips'}</h1>
<p>{l s='Credit slips you have received after cancelled orders'}.</p>
<div class="block-center" id="block-history">
	{if $ordersSlip && count($ordersSlip)}
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Credit slip'}</th>
				<th class="item">{l s='Order'}</th>
				<th class="item">{l s='Date issued'}</th>
				<th class="last_item">{l s='View credit slip'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$ordersSlip item=slip name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="bold"><span class="color-myaccount">{l s='#%s' sprintf=$slip.id_order_slip|string_format:"%06d"}</span></td>
				<td class="history_method"><a class="color-myaccount" href="javascript:showOrder(1, {$slip.id_order|intval}, '{$link->getPageLink('order-detail')}');">{l s='#%s' sprintf=$slip.id_order|string_format:"%06d"}</a></td>
				<td class="bold">{dateFormat date=$slip.date_add full=0}</td>
				<td class="history_invoice">
					<a href="{$link->getPageLink('pdf-order-slip', true, NULL, "id_order_slip={$slip.id_order_slip|intval}")}" title="{l s='Credit slip'} {l s='#%s' sprintf=$slip.id_order_slip|string_format:"%06d"}"><img src="{$img_dir}icon/pdf.gif" alt="{l s='Order slip'} {l s='#%s' sprintf=$slip.id_order_slip|string_format:"%06d"}" class="icon" /></a>
					<a href="{$link->getPageLink('pdf-order-slip', true, NULL, "id_order_slip={$slip.id_order_slip|intval}")}" title="{l s='Credit slip'} {l s='#%s' sprintf=$slip.id_order_slip|string_format:"%06d"}">{l s='PDF'}</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
	{else}
		<p class="warning">{l s='You have not received any credit slips.'}</p>
	{/if}
</div>
<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account', true)}">{l s='Back to your account'}</a></li>
	<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>
