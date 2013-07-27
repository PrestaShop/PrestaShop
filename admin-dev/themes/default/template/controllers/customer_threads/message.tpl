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



{if !$email}

	<fieldset style="margin-top:10px;{if !empty($message.id_employee)}background-color:#F0F8E6;border:1px solid #88D254{/if}">
		<legend {if !empty($message.id_employee)}style="background-color:#F0F8E6;color:#000;border:1px solid #88D254;"{/if}>
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

		<div class="infoCustomer">
			{if !empty($message.id_customer) && empty($message.id_employee)}
			<dl>
				<dt>{l s='Customer ID:'}</dd> 
				<dd><a href="index.php?tab=AdminCustomers&id_customer={$message.id_customer}&viewcustomer&token={getAdminToken tab='AdminCustomers'}" title="{l s='View customer'}">
					{$message.id_customer} <img src="../img/admin/search.gif" alt="{l s='View'}" />
				</a>
				</dd>
			</dl>
			{/if}
			
			<dl>			
				<dt>{l s='Sent on:'}</dt>
				<dd>{$message.date_add}</dd> 
			
			</dl>

			{if empty($message.id_employee)}
			<dl>
				<dt>{l s='Browser:'}</dt>
				<dd>{$message.user_agent}</dd>
			</dl>
			{/if}

			{if !empty($message.file_name) && $file_name}
			<dl>
				<dt>{l s='File attachment'}</dt> 
				<dd><a href="index.php?tab=AdminCustomerThreads&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread&token={getAdminToken tab='AdminCustomerThreads'}&filename={$message.file_name}"
					title="{l s='View file'}">
						<img src="../img/admin/search.gif" alt="{l s='View'}" />
				</a>
				</dd>
			</dl>
			{/if}

			{if !empty($message.id_order) && empty($message.id_employee)}
				<dl>
					<dt>{l s='Order #'}</dt> 
					<dd><a href="index.php?tab=AdminOrders&id_order={$message.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}" title="{l s='View order'}">
					{$message.id_order} <img src="../img/admin/search.gif" alt="{l s='View'}" />
				</a></dd>
				</dl>
			{/if}

			{if !empty($message.id_product) && empty($message.id_employee)}
				<dl>
					<dt>{l s='Product #'}</dt> 
					<dd><a href="index.php?tab=AdminProducts&id_product={$message.id_product}&updateproduct&token={getAdminToken tab='AdminProducts'}" title="{l s='View order'}">
					{$message.id_product} <img src="../img/admin/search.gif" alt="{l s='View'}" />
				</a></dd>
				</dl>
			{/if}
			
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

	<div class="infoEmployee">
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

		{if !empty($message.id_customer) && empty($message.id_employee)}
			<br /><b>{l s='Customer ID:'}</b> {$message.id_customer}<br />
		{/if}

		{if !empty($message.id_order) && empty($message.id_employee)}
			<br /><b>{l s='Order #'}:</b> {$message.id_order}<br />
		{/if}

		{if !empty($message.id_product) && empty($message.id_employee)}
			<br /><b>{l s='Product #'}:</b> {$message.id_product}<br />
		{/if}

		<br /><b>{l s='Subject:'}</b> {$message.subject}

{/if}
		<dl>
			<dt>{l s='Thread ID:'}</dt>
			<dd>{$message.id_customer_thread}</dd>
		</dl>
		<dl>
			<dt>{l s='Message ID:'}</dt>
			<dd>{$message.id_customer_message}</dd>
		</dl>
		<dl>
			<dt>{l s='Message:'}</dt>
			<dd>{$message.message|escape:'htmlall':'UTF-8'|nl2br}</dd>
		</dl>
	</div>

{if !$email}
	{if empty($message.id_employee)}
			<button class="button" style="font-size:12px;"
				onclick="$('#reply_to_{$message.id_customer_message}').show(500); $(this).hide();">
				<img src="../img/admin/contact.gif" alt=""/>{l s='Reply to this message'}
			</button>
	{/if}

	<div id="reply_to_{$message.id_customer_message}" style="display: none; margin-top: 20px;">
		<form action="{$current}&token={getAdminToken tab='AdminCustomerThreads'}&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread" method="post" enctype="multipart/form-data">
			<p>{l s='Please type your reply below:'}</p>
			<textarea style="width: 450px; height: 175px;" name="reply_message">{$PS_CUSTOMER_SERVICE_SIGNATURE}</textarea>
			<div style="width: 450px; text-align: right; font-style: italic; font-size: 9px; margin-top: 2px;">
				{l s='Your reply will be sent to:'} {$message.email}
			</div>
			<div style="width: 450px; margin-top: 0px;">
				<input type="file" name="joinFile"/>
			</div>
			<div>
				<input type="submit" class="button" name="submitReply" value="{l s='Send my reply'}" style="margin-top:20px;" />
				<input type="hidden" name="id_customer_thread" value="{$message.id_customer_thread}" />
				<input type="hidden" name="msg_email" value="{$message.email}" />
			</div>					
		</form>
	</div>

	</fieldset>

{/if}

	</fieldset>
