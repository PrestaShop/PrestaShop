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

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Return Merchandise Authorization (RMA)'}</span>{/capture}

<h1 class="page-heading bottom-indent">{l s='Return Merchandise Authorization (RMA)'}</h1>
{if isset($errorQuantity) && $errorQuantity}<p class="error">{l s='You do not have enough products to request an additional merchandise return.'}</p>{/if}
{if isset($errorMsg) && $errorMsg}
	<p class="alert alert-danger">
		{l s='Please provide an explanation for your RMA.'}
	</p>
	<p>
		<form method="POST"  id="returnOrderMessage">
			<p class="textarea form-group">
            	<label>{l s='Please provide an explanation for your RMA:'}</label>
				<textarea name="returnText" class="form-control"></textarea>
			</p>
			{foreach $ids_order_detail as $id_order_detail}
				<input type="hidden" name="ids_order_detail[{$id_order_detail}]" value="{$id_order_detail}"/>
			{/foreach}
			{foreach $order_qte_input as $key => $value}
				<input type="hidden" name="order_qte_input[{$key}]" value="{$value}"/>
			{/foreach}
			<input type="hidden" name="id_order" value="{$id_order}"/>
			<input class="unvisible" type="submit" name="submitReturnMerchandise" value="{l s='Make an RMA slip'}"/>
            <button type="submit" name="submitReturnMerchandise" class="btn btn-default button button-small"><span>{l s='Make an RMA slip'}<i class="icon-chevron-right right"></i></span></button>
		</form>
	</p>
{/if}
{if isset($errorDetail1) && $errorDetail1}<p class="alert alert-danger">{l s='Please check at least one product you would like to return.'}</p>{/if}
{if isset($errorDetail2) && $errorDetail2}<p class="alert alert-danger">{l s='For each product you wish to add, please specify the desired quantity.'}</p>{/if}
{if isset($errorNotReturnable) && $errorNotReturnable}<p class="alert alert-danger">{l s='This order cannot be returned.'}</p>{/if}

<p class="info-title">{l s='Here is a list of pending merchandise returns'}.</p>
<div class="block-center" id="block-history">
	{if $ordersReturn && count($ordersReturn)}
	<table id="order-list" class="table table-bordered footab">
		<thead>
			<tr>
				<th class="first_item">{l s='Return'}</th>
				<th class="item">{l s='Order'}</th>
				<th data-hide="phone"class="item">{l s='Package status'}</th>
				<th data-hide="phone,tablet" class="item">{l s='Date issued'}</th>
				<th data-hide="phone,tablet" class="last_item">{l s='Return slip'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$ordersReturn item=return name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="bold"><a class="color-myaccount" href="javascript:showOrder(0, {$return.id_order_return|intval}, '{$link->getPageLink('order-return', true)|escape:'html'}');">{l s='#'}{$return.id_order_return|string_format:"%06d"}</a></td>
				<td class="history_method"><a class="color-myaccount" href="javascript:showOrder(1, {$return.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html'}');">{l s='#'}{$return.id_order|string_format:"%06d"}</a></td>
				<td class="history_method"><span class="label label-info">{$return.state_name|escape:'htmlall':'UTF-8'}</span></td>
				<td class="bold">{dateFormat date=$return.date_add full=0}</td>
				<td class="history_invoice">
				{if $return.state == 2}
					<a class="link-button" href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$return.id_order_return|intval}")|escape:'html'}" title="{l s='Order return'} {l s='#'}{$return.id_order_return|string_format:"%06d"}"><i class="icon-file-text"></i> {l s='Print out'}</a>
				{else}
					--
				{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{else}
		<p class="alert alert-warning">{l s='You have no merchandise return authorizations.'}</p>
	{/if}
</div>

<ul class="footer_links clearfix">
	<li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account'}</span></a></li>
	<li><a class="btn btn-default button button-small" href="{$base_dir}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>
