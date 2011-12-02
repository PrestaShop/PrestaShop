{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}



{if !$email}

	<fieldset style="margin-top:10px;{if !empty($message.employee_name)}background: rgb(255,236,242){/if}">
		<legend {if !empty($message.employee_name)}style="background:rgb(255,210,225)"{/if}>
			{if !empty($message.employee_name)}
				<img src="../img/t/AdminCustomers.gif" alt="{$PS_SHOP_NAME}" />
					{$PS_SHOP_NAME} - {$message.employee_name}
			{else}
				<img src="../img/admin/tab-customers.gif" alt="{$PS_SHOP_NAME}" />
				{if !empty($message.id_customer)}
					<a href="index.php?tab=AdminCustomers&id_customer={$message.id_customer}&viewcustomer&token={getAdminToken tab='AdminCustomers'}" title="{l s='View customer'}">
						{$message.customer_name}
					</a>
				{else}
					{$message.email}
				{/if}
			{/if}
		</legend>

		<div style="font-size:11px">
			{if !empty($message.id_customer) && empty($message.employee_name)}
				<b>{l s='Customer ID:'}</b> 
				<a href="index.php?tab=AdminCustomers&id_customer={$message.id_customer}&viewcustomer&token={getAdminToken tab='AdminCustomers'}" title="{l s='View customer'}">
					{$message.id_customer} <img src="../img/admin/search.gif" alt="{l s='view'}" />
				</a><br />
			{/if}

			<b>{l s='Sent on:'}</b> {$message.date_add}<br />
			{if empty($message.employee_name)}
				<b>{l s='Browser:'}</b> {$message.user_agent}<br />
			{/if}

			{if !empty($message.file_name) && $file_name}
				<b>{l s='File attachment'}</b> 
				<a href="index.php?tab=AdminCustomerThreads&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread&token={getAdminToken tab='AdminCustomerThreads'}&filename={$message.file_name}"
					title="{l s='View file'}">
						<img src="../img/admin/search.gif" alt="{l s='view'}" />
				</a><br />
			{/if}

			{if !empty($message.id_order) && empty($message.employee_name)}
				<b>{l s='Order #'}</b> 
				<a href="index.php?tab=AdminOrders&id_order={$message.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}" title="{l s='View order'}">
					{$message.id_order} <img src="../img/admin/search.gif" alt="{l s='view'}" />
				</a><br />
			{/if}

			{if !empty($message.id_product) && empty($message.employee_name)}
				<b>{l s='Product #'}</b> 
				<a href="index.php?tab=AdminOrders&id_order={$id_order_product}&vieworder&token={getAdminToken tab='AdminOrders'}" title="{l s='View order'}">
					{$message.id_product} <img src="../img/admin/search.gif" alt="{l s='view'}" />
				</a><br />
			{/if}
			<br />

			<form action="{$current}&token={$token}&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread" method="post">
				<b>{l s='Subject:'}</b>
				<input type="hidden" name="id_customer_message" value="{$message.id_customer_message}" />
				<select name="id_contact" onchange="this.form.submit();">
					{foreach $contacts as $contact}
						<option value="{$contact.id_contact}" {if $contact.id_contact == $message.id_contact}selected="selected"{/if}>
							{$contact.name}
						</option>
					{/foreach}
				</select>
			</form>

{else}

	<div style="font-size:11px">
		{if $id_employee}
			<a href="{$current}&token={getAdminToken tab='AdminCustomerThreads'}&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread">'.
				{l s='View this thread'}
			</a><br />
		{/if}
		<b>{l s='Sent by:'}</b>

		{if !empty($message.customer_name)}
			{$message.customer_name} ({$message.email})
		{else}
			{$message.email}
		{/if}

		{if !empty($message.id_customer) && empty($message.employee_name)}
			<br /><b>{l s='Customer ID:'}</b> {$message.id_customer}<br />
		{/if}

		{if !empty($message.id_order) && empty($message.employee_name)}
			<br /><b>{l s='Order #'}:</b> {$message.id_order}<br />
		{/if}

		{if !empty($message.id_product) && empty($message.employee_name)}
			<br /><b>{l s='Product #'}:</b> {$message.id_product}<br />
		{/if}

		<br /><b>{l s='Subject:'}</b> {$message.subject}

{/if}
		<br /><br />
		<b>{l s='Thread ID:'}</b> {$message.id_customer_thread}<br />
		<b>{l s='Message ID:'}</b> {$message.id_customer_message}<br />
		<b>{l s='Message:'}</b><br />
		{$message.message}
	</div>


{if !$email}

	{if empty($message.employee_name)}

		<p style="text-align:right">
			<button style="font-family: Verdana; font-size: 11px; font-weight:bold; height: 65px; width: 120px;" 
				onclick="$('#reply_to_{$message.id_customer_message}').show(500); $(this).hide();">
				<img src="../img/admin/contact.gif" alt="" style="margin-bottom: 5px;" /><br />{l s='Reply to this message'}
			</button>
		</p>

	{/if}

	<div id="reply_to_{$message.id_customer_message}" style="display: none; margin-top: 20px;"">
		<form action="{$current}&token={getAdminToken tab='AdminCustomerThreads'}&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread" method="post" enctype="multipart/form-data">
			<p>{l s='Please type your reply below:'}</p>
			<textarea style="width: 450px; height: 175px;" name="reply_message">'.
				{$PS_CUSTOMER_SERVICE_SIGNATURE}
			</textarea>
			<div style="width: 450px; text-align: right; font-style: italic; font-size: 9px; margin-top: 2px;">
				{l s='Your reply will be sent to:'} {$message.email}
			</div>
			<div style="width: 450px; margin-top: 0px;">
				<input type="file" name="joinFile"/>
			<div>
			<div style="width: 450px; text-align: center;">
				<input type="submit" class="button" name="submitReply" value="{l s='Send my reply'}" style="margin-top:20px;" />
				<input type="hidden" name="id_customer_thread" value="{$message.id_customer_thread}" />
				<input type="hidden" name="msg_email" value="{$message.email}" />
			</div>					
		</form>
	</div>

	</fieldset>

{/if}

		</div>





	</fieldset>