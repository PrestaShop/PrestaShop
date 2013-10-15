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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

<div class="panel">
	<h3>
		<i class="icon-envelope"></i>
		{l s='Message status'}
	</h3>
	<form action="{$current}&token={$token}&viewcustomer_thread&id_customer_thread={$id_customer_thread}" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div id="ChangeStatus" class="row row-margin-bottom">
			<label class="control-label col-lg-3">{l s='Change status of message:'}</label>
			<div class="col-lg-3">
				<select onchange="quickSelect(this);">
					{foreach $actions as $action}
						<option value="{$action.href}">&gt; {$action.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="row row-margin-bottom">
			<label class="control-label col-lg-3">{l s='Forward this discussion to an employee:'}</label>
			<div class="col-lg-3">
				<select name="id_employee_forward">
					<option value="-1">{l s='-- Choose --'}</option>
					{foreach $employees as $employee}
						<option value="{$employee.id_employee}"> {Tools::substr($employee.firstname, 0, 1)}. {$employee.lastname}</option>
					{/foreach}
					<option value="0">{l s='Someone else'}</option>
				</select>
			</div>
		</div>

		<div id="message_forward_email" class="row row-margin-bottom" style="display:none">
			<label class="control-label col-lg-3">{l s='Email'}</label>
			<div class="col-lg-3"> 
				<input type="text" name="email" />
			</div>
		</div>

		<div id="message_forward" style="display:none;">
			<div class="row row-margin-bottom">
				<label class="control-label col-lg-3">{l s='Comment:'}</label>
				<div class="col-lg-7"> 
					<textarea name="message_forward" rows="6">{l s='You can add a comment here.'}</textarea>
				</div>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-offset-3"> 
					<button type="Submit" name="submitForward" class="btn btn-default">
						<i class="icon-mail-forward"></i> {l s='Forward this discussion.'}
					</button>
				</div>
			</div>
		</div>
	</form>
</div>


	{if $thread->id_customer}

		<div class="panel">
		{if $orders && count($orders)}
			{if $count_ok}
				<h3>
					<i class="icon-shopping-cart"></i> {l s='Orders'}
				</h3>
				<div class="table-responsive clearfix">
					<table class="table">
						<tr>
							<th class="center">
								<span class="title_box">{l s='ID'}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='Date'}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='Products:'}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='Total paid'}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='Payment: '}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='State'}</span>
							</th>
							<th class="center">
								<span class="title_box">{l s='Actions'}</span>
							</th>
						</tr>
						{assign var=irow value=0}
						{foreach $orders_ok as $order}
							<tr onclick="document.location='?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
								<td class="center">{$order.id_order}</td>
								<td class="center">{$order.date_add}</td>
								<td class="center">{$order.nb_products}</td>
								<td class="center">{$order.total_paid_real}</td>
								<td class="center">{$order.payment}</td>
								<td class="center">{$order.order_state}</td>
								<td class="center">
									<a class=" btn btn-default" href="?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
										<i class="icon-eye-open"></i> {l s='View'}
									</a>
								</td>
							</tr>
						{/foreach}
					</table>
				</div>
				<p class="pull-right">
					{l s='Validated Orders:'} {$count_ok} {l s='for'} <strong>{$total_ok}</strong>
				</p>
			{/if}
		{/if}
		</div>
		<div class="panel">
		{if $products && count($products)}
			<h3>
				<i class="icon-archive"></i> {l s='Products:'}
			</h3>
			<div class="table-responsive clearfix">
				<table class="table">
					<tr>
						<th class="center">
							<span class="title_box">{l s='Date'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='ID'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Name'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Quantity'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Actions'}</span>
						</th>
					</tr>
					{assign var=irow value=0}
					{foreach $products as $product}
						<tr onclick="document.location = '?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}'">
							<td class="center">{$product.date_add}</td>
							<td class="center">{$product.product_id}</td>
							<td class="center">{$product.product_name}</td>
							<td class="center">{$product.product_quantity}</td>
							<td class="center">
								<a class=" btn btn-default" href="?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
									<i class="icon-eye-open"></i> {l s='View'}
								</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		{/if}
		</div>
	{/if}

	<div class="row row-margin-bottom">
		{foreach $messages as $message}
			{$message}
		{/foreach}
	</div>

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
			$('teaxtrea[name=message_forward]').click(function(){
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
				url: 'ajax-tab.php',
				async: true,
				dataType: 'json',
				data: {
					controller: 'AdminCustomerThreads',
					action: 'markAsRead',
					token : '{$token}',
					id_thread: {$id_customer_thread}
				}
			});
			clearInterval(timer);
			timer = null;
   		}
	</script>

{/block}

