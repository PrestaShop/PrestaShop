{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
{include file="./modal.tpl" }
<div class="panel">
	<div class="panel-heading">
		<i class="icon-comments"></i>
		{l s="Thread" d='Admin.Orderscustomers.Feature'}: <span class="badge">#{$id_customer_thread|intval}</span>
		{if isset($next_thread) && $next_thread}
			<a class="btn btn-default pull-right" href="{$next_thread.href|escape:'html':'UTF-8'}">
				{$next_thread.name} <i class="icon-forward"></i>
			</a>
		{/if}
	</div>
	<div class="well">
		<form action="{$link->getAdminLink('AdminCustomerThreads', true, [], ['id_customer_thread' => $id_customer_thread|intval, 'viewcustomer_thread' => 1])|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
			{foreach $actions as $action}
				<button class="btn btn-default" name="{$action.name|escape:'html':'UTF-8'}" value="{$action.value|intval}">
					{if isset($action.icon)}<i class="{$action.icon|escape:'html':'UTF-8'}"></i>{/if}{$action.label}
				</button>
			{/foreach}
			<button class="btn btn-default" type="button" data-toggle="modal" data-target="#myModal">
				{l s="Forward this discussion to another employee" d='Admin.Orderscustomers.Feature'}
			</button>
		</form>
	</div>
	<div class="row">
		<div class="message-item-initial media">
			<a href="{if isset($customer->id)}{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer->id|intval, 'viewcustomer' => 1])|escape:'html':'UTF-8'}{else}#{/if}" class="avatar-lg pull-left"><i class="icon-user icon-3x"></i></a>
			<div class="media-body">
				<div class="row">
					<div class="col-sm-6">
					{if isset($customer->firstname)}
						<h2>
							<a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer->id|intval, 'viewcustomer' => 1])|escape:'html':'UTF-8'}">
							{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'} <small>({$customer->email|escape:'html':'UTF-8'})</small>
							</a>
						</h2>
					{else}
						<h2>{$thread->email|escape:'html':'UTF-8'}</h2>
					{/if}
					{if isset($contact) && trim($contact) != ''}
						<span>{l s="To:" d='Admin.Orderscustomers.Feature'} </span><span class="badge">{$contact|escape:'html':'UTF-8'}</span>
					{/if}
					</div>
					{if isset($customer->firstname)}
						<div class="col-sm-6">
							<p>
							{if $count_ok}
								{l s='[1]%count%[/1] order(s) validated for a total amount of [2]%total%[/2]' html=true sprintf=['%count%' => $count_ok, '%total%' => $total_ok, '[1]' => '<span class="badge">', '[/1]' => '</span>', '[2]' => '<span class="badge badge-success">', '[/2]' => '</span>'] d='Admin.Orderscustomers.Feature'}
							{else}
								{l s="No orders validated for the moment" d='Admin.Orderscustomers.Feature'}
							{/if}
							</p>
							<p class="text-muted">{l s="Customer since: %s" sprintf=[{dateFormat date=$customer->date_add full=0}] d='Admin.Orderscustomers.Feature'}</p>
						</div>
					{/if}
				</div>
				{if !$first_message.id_employee}
					{include file="./message.tpl" message=$first_message initial=true}
				{/if}
			</div>
		</div>
	</div>
	<div class="row" data-role="thread-messages">
		{foreach $messages as $message}
			{include file="./message.tpl" message=$message initial=false}
		{/foreach}
	</div>
</div>
<div class="panel">
	<h3 id="reply-form-title">{l s="Your answer to" d='Admin.Orderscustomers.Feature'} {if isset($customer->firstname)}{$customer->firstname|escape:'html':'UTF-8'} {$customer->lastname|escape:'html':'UTF-8'} {else} {$thread->email}{/if}</h3>
	<form action="{$link->getAdminLink('AdminCustomerThreads', true, [], ['id_customer_thread' => $thread->id|intval, 'viewcustomer_thread' => 1])|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="row">
		<div class="media">
			<div class="pull-left">
				<span class="avatar-md">{if isset($current_employee->firstname)}<img src="{$current_employee->getImage()}" alt="">{/if}</span>
			</div>
			<div class="media-body">
				<textarea id="reply_message" cols="30" rows="7" name="reply_message">{$PS_CUSTOMER_SERVICE_SIGNATURE|escape:'html':'UTF-8'}</textarea>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<!--
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<i class="icon-magic icon-2x"></i><br>
			{l s="Choose a template"}
		</button>
		-->
		<button class="btn btn-default pull-right" name="submitReply"><i class="process-icon-mail-reply"></i> {l s="Send"}</button>
		<input type="hidden" name="id_customer_thread" value="{$thread->id|intval}" />
		<input type="hidden" name="msg_email" value="{$thread->email}" />
	</div>
	</form>
</div>

{if count($timeline_items)}
<div class="panel" id="orders-and-messages-block">
	<h3>
		<i class="icon-clock-o"></i>
		{l s="Orders and messages timeline" d='Admin.Orderscustomers.Feature'}
	</h3>
	<div class="timeline">
		{foreach $timeline_items as $dates}
			{foreach from=$dates key=date item=timeline_item}
				{include file="controllers/customer_threads/helpers/view/timeline_item.tpl" timeline_item=$timeline_item}
			{/foreach}
		{/foreach}
	</div>
</div>
{/if}
<script type="text/javascript">
	var timer;
		$(document).ready(function(){
			$('select[name=id_employee_forward]').change(function(){
				if ($(this).val() >= 0)
					$('#message_forward').show(400);
				else
					$('#message_forward').hide(200);
				if ($(this).val() == 0)
					$('#message_forward_email').show(200);
				else
					$('#message_forward_email').hide(200);
			});
			$('textarea[name=message_forward]').click(function(){
				if($(this).val() == '{l s='You can add a comment here.'}')
				{
					$(this).val('');
				}
			});
			timer = setInterval("markAsRead()", 3000);
		});

	function markAsRead()
	{
		$.ajax({
			type: 'POST',
			url: 'index.php',
			async: true,
			dataType: 'json',
			data: {
        ajax: 1,
				controller: 'AdminCustomerThreads',
				action: 'markAsRead',
				token : '{$token|escape:'html':'UTF-8'}',
				id_thread: {$id_customer_thread}
			}
		});
		clearInterval(timer);
		timer = null;
	}
</script>

{/block}


