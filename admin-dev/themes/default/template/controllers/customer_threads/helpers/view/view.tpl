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
	<form action="{$current}&token={$token}&viewcustomer_thread&id_customer_thread={$id_customer_thread}" method="post" enctype="multipart/form-data">
		<fieldset>

			<div id="ChangeStatus">
				<select onchange="quickSelect(this);">
					<option value="0">{l s='Change status of message:'}</option>
					{foreach $actions as $action}
						<option value="{$action.href}">&gt; {$action.name}</option>
					{/foreach}
				</select>
			</div>

			<p>
				<img src="../img/admin/email_go.png" alt="" style="vertical-align: middle;" /> 
				{l s='Forward this discussion to an employee:'}
				<select name="id_employee_forward" style="vertical-align: middle;">
					<option value="-1">{l s='-- Choose --'}</option>
					{foreach $employees as $employee}
						<option value="{$employee.id_employee}"> {Tools::substr($employee.firstname, 0, 1)}. {$employee.lastname}</option>
					{/foreach}
					<option value="0">{l s='Someone else'}</option>
				</select>
			</p>

			<div id="message_forward_email" style="display:none">
				<b>{l s='Email'}</b> <input type="text" name="email" />
			</div>

			<div id="message_forward" style="display:none;margin-bottom:10px">
				<textarea name="message_forward" style="width:500px;height:80px;margin-top:15px;">{l s='You can add a comment here.'}</textarea><br />
				<input type="Submit" name="submitForward" class="button" value="{l s='Forward this discussion.'}" style="margin-top: 10px;" />
			</div>

		</fieldset>
	</form>
	<div class="clear">&nbsp;</div>

	{if $thread->id_customer}

		<div style="float:right;margin-left:20px;">
		{if $orders && count($orders)}
			{if $count_ok}
				<div>
					<h2>{l s='Orders'}</h2>
					<table cellspacing="0" cellpadding="0" class="table float">
						<tr>
							<th class="center">{l s='ID'}</th>
							<th class="center">{l s='Date'}</th>
							<th class="center">{l s='Products:'}</th>
							<th class="center">{l s='Total paid'}</th>
							<th class="center">{l s='Payment: '}</th>
							<th class="center">{l s='State'}</th>
							<th class="center">{l s='Actions'}</th>
						</tr>
						{assign var=irow value=0}
						{foreach $orders_ok as $order}
							<tr {if $irow++ % 2}class="alt_row"{/if} style="cursor: pointer" 
											onclick="document.location='?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
								<td class="center">{$order.id_order}</td>
								<td>{$order.date_add}</td>
								<td align="right">{$order.nb_products}</td>
								<td align="right">{$order.total_paid_real}</td>
								<td>{$order.payment}</td>
								<td>{$order.order_state}</td>
								<td align="center">
									<a href="?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
										<img src="../img/admin/details.gif" />
									</a>
								</td>
							</tr>
						{/foreach}
					</table>
					<h3 style="color:green;font-weight:700;margin-top:10px">
						{l s='Validated Orders:'} {$count_ok} {l s='for'} {$total_ok}
					</h3>
				</div>
			{/if}
		{/if}
		{if $products && count($products)}
			<div>
				<h2>{l s='Products:'}</h2>
				<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th class="center">{l s='Date'}</th>
						<th class="center">{l s='ID'}</th>
						<th class="center">{l s='Name'}</th>
						<th class="center">{l s='Quantity'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
					{assign var=irow value=0}
					{foreach $products as $product}
						<tr {if $irow++ % 2}class="alt_row"{/if} style="cursor: pointer" 
							onclick="document.location = '?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}'">
							<td>{$product.date_add}</td>
							<td>{$product.product_id}</td>
							<td>{$product.product_name}</td>
							<td align="right">{$product.product_quantity}</td>
							<td align="center">
								<a href="?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
									<img src="../img/admin/details.gif" />
								</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		{/if}
		</div>
	{/if}

	<div style="margin-top:10px">
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

