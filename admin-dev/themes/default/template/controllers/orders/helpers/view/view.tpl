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
	<script type="text/javascript">
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|escape:'html'}";
	var id_order = {$order->id};
	var id_lang = {$current_id_lang};
	var id_currency = {$order->id_currency};
	var id_customer = {$order->id_customer|intval};
	{assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
	var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
	var currency_sign = "{$currency->sign}";
	var currency_format = "{$currency->format}";
	var currency_blank = "{$currency->blank}";
	var priceDisplayPrecision = 2;
	var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
	var token = "{$smarty.get.token|escape:'htmlall':'UTF-8'}";
	var stock_management = {$stock_management|intval};

	var txt_add_product_stock_issue = "{l s='Are you sure you want to add this quantity?' js=1}";
	var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' js=1}";
	var txt_add_product_no_product = "{l s='Error: No product has been selected' js=1}";
	var txt_add_product_no_product_quantity = "{l s='Error: Quantity of products must be set' js=1}";
	var txt_add_product_no_product_price = "{l s='Error: Product price must be set' js=1}";
	var txt_confirm = "{l s='Are you sure?' js=1}";

	var statesShipped = new Array();
	{foreach from=$states item=state}
		{if (!$currentState->shipped && $state['shipped'])}
			statesShipped.push({$state['id_order_state']});
		{/if}
	{/foreach}
	</script>

	{assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
	{if ($hook_invoice)}
	<div>{$hook_invoice}</div>
	{/if}

<div class="row">
	<!-- Global informations -->
	<div class="col-lg-12">
		<div class="well">
			<span>
				{l s='Date:'} {dateFormat date=$order->date_add full=true} 
			</span>
			<span>
				| {l s='Messages:'} {sizeof($messages)}
			</span>
			<span>
				| <a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}">{l s='New Customer Messages:'}</a> 
				<a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}">{sizeof($customer_thread_message)}</a>
			</span>
			<span>
				| {l s='Products:'} {sizeof($products)}
			</span>
			<span>
				| {l s='Total:'} {displayPrice price=$order->total_paid_tax_incl currency=$currency->id}
			</span>
			<span class="pull-right">
				{if (count($invoices_collection))}
					<a href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}" target="_blank">
						<i class="icon-file"></i> 
						{l s='View invoice'}
					</a>
				{else}
					<i class="icon-file"></i> {l s='No invoice'}
				{/if}
				 |
				{if (($currentState && $currentState->delivery) || $order->delivery_number)}
					<a href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}" target="_blank">
						<i class="icon-suitcase"></i> {l s='View delivery slip'}
					</a>
				{else}
					<i class="icon-suitcase"></i> {l s='No delivery slip'}
				{/if}
				 |
				<a href="javascript:window.print()">
					<i class="icon-print"></i> 
					{l s='Print order'}
				</a>
			</span>
		</div>
	</div>

	<!-- Breadcrumb -->
	<div class="col-lg-12">
		<div class="breadcrumb pull-right">
			<span><strong>{l s='Orders'}</strong></span> :
			{if $previousOrder}
				<a class="button" href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$previousOrder}">
					{l s='< Prev'}
				</a>
			{/if}
			{if $nextOrder}
				<a class="button" href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$nextOrder}">
					{l s='Next >'}
				</a>
			{/if}
		</div>
	</div>

	<!-- Status -->
	<div class="col-lg-12">
		<!-- Change status form -->
		<form action="{$currentIndex}&vieworder&token={$smarty.get.token}" method="post">
			<fieldset>
				<legend>
					<i class="icon-table"></i>
					{l s='Order status'}
				</legend>
				<label class="control-label col-lg-1 text-right">
					{l s='Status:'}
				</label>
				<div class="col-lg-10">
					<select id="id_order_state" name="id_order_state">
						{foreach from=$states item=state}
							{if $state['id_order_state'] != $currentState->id}
							<option value="{$state['id_order_state']}">{$state['name']|stripslashes}</option>
							{/if}
						{/foreach}
					</select>
				</div>
				<input type="hidden" name="id_order" value="{$order->id}" />
				<button type="submit" name="submitState" class="btn btn-default"> 
					{l s='Add'}
				</button>

				<!-- History of status -->
				<table class="table">
					<colgroup>
						<col width="1%">
						<col width="">
						<col width="20%">
						<col width="20%">
					</colgroup>
					{foreach from=$history item=row key=key}
						{if ($key == 0)}
						<thead>
							<tr>
								<th>
									<img src="../img/os/{$row['id_order_state']}.gif" />
								</th>
								<th>
									<span class="title_box ">{$row['ostate_name']|stripslashes}</span>
								</th>
								<th>
									<span class="title_box ">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</span>
								</th>
								<th>
									<span class="title_box ">{dateFormat date=$row['date_add'] full=true}</span>
								</th>
							</tr>
						</thead>
						{else}
						<tbody>
							<tr class="{if ($key % 2)}alt_row{/if}">
								<td><img src="../img/os/{$row['id_order_state']}.gif" /></td>
								<td>{$row['ostate_name']|stripslashes}</td>
								<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{else}&nbsp;{/if}</td>
								<td>{dateFormat date=$row['date_add'] full=true}</td>
							</tr>
						</tbody>
						{/if}
					{/foreach}
				</table>
			</fieldset>
		</form>
	</div>

	{if $customer->id}
	<!-- Customer informations -->
	<div class="col-lg-6">
		<fieldset>
			<legend>
				<i class="icon-user"></i>
				{l s='Customer information'}
			</legend>
			<span><a href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={getAdminToken tab='AdminCustomers'}"> {$customer->firstname} {$customer->lastname}</a></span> ({l s='#'}{$customer->id})<br />
			(<a href="mailto:{$customer->email}">{$customer->email}</a>)<br /><br />
			{if ($customer->isGuest())}
				{l s='This order has been placed by a guest.'}
				{if (!Customer::customerExists($customer->email))}
				<form method="post" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
					<input type="hidden" name="id_lang" value="{$order->id_lang}" />
					<p class="text-center"><input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer'}" /></p>
					{l s='This feature will generate a random password and send an email to the customer.'}
				</form>
				{else}
					<div><b style="color:red;">{l s='A registered customer account has already claimed this email address'}</strong></div>
				{/if}
			{else}
				{l s='Account registered:'} <strong>{dateFormat date=$customer->date_add full=true}</strong><br />
				{l s='Valid orders placed:'} <strong>{$customerStats['nb_orders']}</strong><br />
				{l s='Total spent since registration:'} <strong>{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</strong><br />
			{/if}
		</fieldset>

		<!-- Sources block -->
		{if (sizeof($sources))}
			<fieldset>
				<legend>
					<i class="icon-signal"></i>
					{l s='Sources'}
				</legend>
				<ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
				{foreach from=$sources item=source}
					<li>
						{dateFormat date=$source['date_add'] full=true}<br />
						<strong>{l s='From'}</strong>{if $source['http_referer'] != ''}<a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
						<strong>{l s='To'}</strong> <a href="http://{$source['request_uri']}">{$source['request_uri']|truncate:100:'...'}</a><br />
						{if $source['keywords']}<strong>{l s='Keywords'}</strong> {$source['keywords']}<br />{/if}<br />
					</li>
				{/foreach}
				</ul>
			</fieldset>
		{/if}

		<!-- Admin order hook -->
		{hook h="displayAdminOrder" id_order=$order->id}
	</div>
	{/if}

	<!-- New message -->
	<div class="col-lg-6">
		<form action="{$smarty.server.REQUEST_URI}&token={$smarty.get.token}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?'}');">
			<fieldset>
				<legend onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false">
					<i class="icon-envelope"></i>
					{l s='New message'}
				</legend>
				<p id="message_m" style="display: {if Tools::getValue('message')}none{else}block{/if};">
					<a href="#" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false">
						<strong>{l s='Click here'}</strong> {l s='to add a comment or send a message to the customer.'}
					</a>
				</p>
				<p>
					<a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}">
						<strong>{l s='Click here'}</strong> {l s='to see all messages.'}
					</a>
				</p>
				<div id="message" style="display: {if Tools::getValue('message')}block{else}none{/if}">
					<p>
						<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
							<option value="0" selected="selected">-- {l s='Choose a standard message'} --</option>
							{foreach from=$orderMessages item=orderMessage}
								<option value="{$orderMessage['message']|escape:'htmlall':'UTF-8'}">{$orderMessage['name']}</option>
							{/foreach}
						</select>
					</p>
					<div class="row">
						<label class="control-label col-lg-4">{l s='Display to customer?'}</label>
						<div class="col-lg-8">
							<label for="visibility" class="radio-inline">
								<input type="radio" name="visibility" id="visibility" value="0" /> {l s='Yes'}
							</label>
							<label for="visibility" class="radio-inline">
								<input type="radio" name="visibility" value="1" checked="checked" /> {l s='No'}
							</label>
						</div>
					</div>

					<p id="nbchars" style="display:inline;font-size:10px;color:#666;"></p>
					<textarea id="txt_msg" name="message" cols="50" rows="8" onKeyUp="var length = document.getElementById('txt_msg').value.length; if (length > 600) length = '600+'; document.getElementById('nbchars').innerHTML = '{l s='600 characters, max.'} (' + length + ')';">{Tools::getValue('message')|escape:'htmlall':'UTF-8'}</textarea>
					<input type="hidden" name="id_order" value="{$order->id}" />
					<input type="hidden" name="id_customer" value="{$order->id_customer}" />
					<button type="submit" class="btn btn-default pull-right" name="submitMessage">
						{l s='Send'}
					</button>
				</div>
			</fieldset>
		</form>

		{if (sizeof($messages))}
			<fieldset>
				<legend>
					<i class="icon-envelope"></i>
					{l s='Messages'}
				</legend>
				{foreach from=$messages item=message}
					<div {if $message['is_new_for_me']}class="new_message"{/if}>
						{if ($message['is_new_for_me'])}
							<a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.server.REQUEST_URI}&token={$smarty.get.token}&messageReaded={$message['id_message']}">
								<i class="icon-ok"></i>
								{l s='Mark this message as \'viewed\''}
							</a>
						{/if}
						<p>
							{l s='At'} 
							<i>{dateFormat date=$message['date_add']}</i> 
							{l s='from'} 
							<strong>{if ($message['elastname']|escape:'htmlall':'UTF-8')}{$message['efirstname']|escape:'htmlall':'UTF-8'} {$message['elastname']|escape:'htmlall':'UTF-8'}{else}{$message['cfirstname']|escape:'htmlall':'UTF-8'} {$message['clastname']|escape:'htmlall':'UTF-8'}{/if}</strong>
						</p>
						{if ($message['private'] == 1)}<span>{l s='Private'}</span>{/if}
						<p>{$message['message']|escape:'htmlall':'UTF-8'|nl2br}</p>
					</div>
				{/foreach}
			</fieldset>
		{/if}
	</div>

	<div class="col-lg-12"></div>

	<!-- Addresses -->
	<div class="col-lg-6">
		{if !$order->isVirtual()}
			<!-- Shipping address -->
			<fieldset>
				<legend>
					<i class="icon-truck"></i>
					{l s='Shipping address'}
				</legend>
				{if $can_edit}
				<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}">
					<div class="col-lg-10">
						<select name="id_address">
							{foreach from=$customer_addresses item=address}
							<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_delivery} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
							{/foreach}
						</select>
					</div>
					<button class="btn btn-default" type="submit" name="submitAddressShipping">
						{l s='Change'}
					</button>
				</form>
				{/if}
				<hr/>
				<div class="col-lg-6">
					{displayAddressDetail address=$addresses.delivery newLine='<br />'}
					{if $addresses.delivery->other}<hr />{$addresses.delivery->other}<br />{/if}
				</div>
				<div class="col-lg-6 btn-group btn-group-action">
					<span class="btn btn-default btn-small">{l s='Choose an action'}</span>
					<button class="btn btn-default btn-small dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>&nbsp;
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="?tab=AdminAddresses&id_address={$addresses.delivery->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=1{/if}&token={getAdminToken tab='AdminAddresses'}&back={$smarty.server.REQUEST_URI|urlencode}">
								<i class="icon-pencil"></i>
								{l s='Edit'}
							</a>
						</li>
						<li>
							<a href="http://maps.google.com/maps?f=q&hl={$iso_code_lang}&geocode=&q={$addresses.delivery->address1} {$addresses.delivery->postcode} {$addresses.delivery->city} {if ($addresses.delivery->id_state)} {$addresses.deliveryState->name}{/if}" target="_blank">
								<i class="icon-map-marker"></i>
								{l s='Map'}
							</a>
						</li>
					</ul>
				</div>
			</fieldset>
		{/if}
	</div>

	<!-- Invoice address -->
	<div class="col-lg-6">
		<fieldset>
			<legend>
				<i class="icon-file"></i>
				{l s='Invoice address'}
			</legend>
			{if $can_edit}
			<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}">
				<div class="col-lg-10">
					<select name="id_address">
						{foreach from=$customer_addresses item=address}
						<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_invoice} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
						{/foreach}
					</select>
				</div>
					<button class="btn btn-default" type="submit" name="submitAddressInvoice">
						{l s='Change'}
					</button>
			</form>
			{/if}
			<hr/>
			<div class="col-lg-6">
				{displayAddressDetail address=$addresses.invoice newLine='<br />'}
				{if $addresses.invoice->other}<hr />{$addresses.invoice->other}<br />{/if}
			</div>
			<div class="col-lg-6">
				<a class="btn btn-default"  href="?tab=AdminAddresses&id_address={$addresses.invoice->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=2{/if}&back={$smarty.server.REQUEST_URI|urlencode}&token={getAdminToken tab='AdminAddresses'}">
					<i class="icon-pencil"></i>
					{l s='Edit'}
				</a>
				</div>
		</fieldset>
	</div>

	<div class="col-lg-12"></div>

	<!-- linked orders block -->
	{if count($order->getBrother()) > 0}
		<div class="col-lg-6">
			<fieldset>
				<legend>
					<i class="icon-shopping-cart"></i>
					{l s='Linked orders'}
				</legend>
				<table class="table">
					<thead>
						<tr>
							<th width="15%">
								<span class="title_box ">{l s='Order no. '}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Status'}</span>
							</th>
							<th width="10%">
								<span class="title_box ">{l s='Amount'}</span>
							</th>
							<th width="5%">
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach $order->getBrother() as $brother_order}
							<tr>
								<td>
									<a href="{$current_index}&vieworder&id_order={$brother_order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">#{'%06d'|sprintf:$brother_order->id}</a>
								</td>
								<td>
									{$brother_order->getCurrentOrderState()->name[$current_id_lang]}
								</td>
								<td>
									{displayPrice price=$brother_order->total_paid_tax_incl currency=$currency->id}
								</td>
								<td>
									<a href="{$current_index}&vieworder&id_order={$brother_order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}"><img alt="{l s='See the order'}" src="../img/admin/details.gif"></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</fieldset>
		</div>
	{/if}

	<!-- Documents block -->
	<div class="col-lg-12">
		<fieldset>
			<legend>
				<i class="icon-file-text"></i>
				{l s='Documents'}
			</legend>
			{* Include document template *}
			{include file='controllers/orders/_documents.tpl'}
		</fieldset>
	</div>

	<!-- Payments block -->
	<div class="col-lg-12">
		<fieldset>
			<legend>
				<i class="icon-money"></i>
				{l s='Payment'}
			</legend>

			{if (!$order->valid && sizeof($currencies) > 1)}
			<form method="post" action="{$currentIndex}&vieworder&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
				<p class="alert alert-block">{l s='Don\'t forget to update your conversion rate before making this change.'}</p>
				<div class="col-lg-10">
					<select name="new_currency">
						{foreach from=$currencies item=currency_change}
							{if $currency_change['id_currency'] != $order->id_currency}
							<option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
							{/if}
						{/foreach}
					</select>
				</div>
				<input type="submit" class="btn btn-default" name="submitChangeCurrency" value="{l s='Change'}" />
			</form>
			<hr/>
			{/if}
			
			{if count($order->getOrderPayments()) > 0}
			<p class="alert alert-block" style="{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || $currentState->id == 6}display: none;{/if}">
				{l s='Warning'} {displayPrice price=$total_paid currency=$currency->id} {l s='paid instead of'} <strong>{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</strong>
				{foreach $order->getBrother() as $brother_order}
					{if $brother_order@first}
						{if count($order->getBrother()) == 1}
							<br />{l s='This warning also concerns order '}
						{else}
							<br />{l s='This warning also concerns the next orders:'}
						{/if}
					{/if}
					<a href="{$current_index}&vieworder&id_order={$brother_order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">#{'%06d'|sprintf:$brother_order->id}</a>
				{/foreach}
			</p>
			{/if}
			<form id="formAddPayment" method="post" action="{$current_index}&vieworder&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
				<table class="table">
					<colgroup>
						<col width="15%">
						<col width="39%">
						<col width="20%">
						<col width="15%">
						<col width="10%">
						<col width="1%">
					</colgroup>
					<thead>
						<tr>
							<th>
								<span class="title_box ">{l s='Date'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Payment method'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Transaction ID'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Amount'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Invoice'}</span>
							</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$order->getOrderPaymentCollection() item=payment}
						<tr>
							<td>{dateFormat date=$payment->date_add full=true}</td>
							<td>{$payment->payment_method}</td>
							<td>{$payment->transaction_id}</td>
							<td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
							<td>
							{if $invoice = $payment->getOrderInvoice($order->id)}
								{$invoice->getInvoiceNumberFormatted($current_id_lang)}
							{else}
								{l s='No invoice'}
							{/if}
							</td>
							<td class="text-right">
								<a href="#" class="open_payment_information btn btn-default">
									<i class="icon-search"></i>
									{l s='See payment information'}
								</a>
							</td>
						</tr>
						<tr class="payment_information" style="display: none;">
							<td colspan="6">
								<p>
									<strong>{l s='Card Number'}</strong>&nbsp;
									{if $payment->card_number}
										{$payment->card_number}
									{else}
										<i>{l s='Not defined'}</i>
									{/if}
								</p>

								<p>
									<strong>{l s='Card Brand'}</strong>&nbsp;
									{if $payment->card_brand}
										{$payment->card_brand}
									{else}
										<i>{l s='Not defined'}</i>
									{/if}
								</p>

								<p>
									<strong>{l s='Card Expiration'}</strong>&nbsp;
									{if $payment->card_expiration}
										{$payment->card_expiration}
									{else}
										<i>{l s='Not defined'}</i>
									{/if}
								</p>

								<p>
									<strong>{l s='Card Holder'}</strong>&nbsp;
									{if $payment->card_holder}
										{$payment->card_holder}
									{else}
										<i>{l s='Not defined'}</i>
									{/if}
								</p>
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="6" class="text-center">
								<h4>{l s='No payments are available'}</h4>
							</td>
						</tr>
						{/foreach}
						<tr>
							<td>
								<input type="text" name="payment_date" class="datepicker" size="17" value="{date('Y-m-d H:i:s')}" />
							</td>
							<td>
								<select name="payment_method" class="payment_method">
								{foreach from=$payment_methods item=payment_method}
									<option value="{$payment_method}">{$payment_method}</option>
								{/foreach}
								</select>
							</td>
							<td>
								<input type="text" name="payment_transaction_id" value="" />
							</td>
							<td>
								<input type="text" name="payment_amount" size="5" value="" />
								<select name="payment_currency" class="payment_currency">
								{foreach from=$currencies item=current_currency}
									<option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
								{/foreach}
								</select>
							</td>
							{if count($invoices_collection) > 0}
							<td>
								<select name="payment_invoice" id="payment_invoice">
								{foreach from=$invoices_collection item=invoice}
									<option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang)}</option>
								{/foreach}
								</select>
							</td>
							{/if}
							<td>
								<input class="btn btn-default" type="submit" name="submitAddPayment" value="{l s='Add'}" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</fieldset>
	</div>

	{if !$order->isVirtual()}
		<!-- Shipping block -->
			<fieldset>
				<legend>
					<i class="icon-truck"></i>
					{l s='Shipping'}
				</legend>
				{if $order->recyclable}
					<p>
						<span class="label label-success">
							<i class="icon-ok"></i>
							{l s='Recycled packaging'}
						</span>
					</p>
				{else}
					<p>
						<span class="label label-danger">
							<i class="icon-remove"></i>
							{l s='Recycled packaging'}
						</span>
					</p>
				{/if}
				{if $order->gift}
					<p>
						<span class="label label-success">
							<i class="icon-ok"></i>
							{l s='Gift wrapping'}
						</span>
						{if $order->gift_message}
							<div class="alert alert-block">
								<p>
									<strong>{l s='Message'}</strong>
								</p>
								<p>
									{$order->gift_message|nl2br}
								</p>
							</div>
						{/if}
					</p>
				{else}
					<p>
						<span class="label label-danger">
							<i class="icon-remove"></i>
							{l s='Gift wrapping'}
						</span>
					</p>
				{/if}

				{include file='controllers/orders/_shipping.tpl'}

				{if $carrierModuleCall}
					{$carrierModuleCall}
				{/if}
			</fieldset>
		</div>

		<!-- Return block -->
		<div class="col-lg-6">	
			<fieldset>
				<legend>
					<i class="icon-suitcase"></i>
					{l s='Merchandise returns'}
				</legend>

				{if $order->getReturn()|count > 0}
				<table class="table">
					<thead>
						<tr>
							<th width="30%">
								<span class="title_box ">{l s='Date'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Type'}</span>
							</th>
							<th width="20%">
								<span class="title_box ">{l s='Carrier'}</span>
							</th>
							<th width="30%">
								<span class="title_box ">{l s='Tracking number'}</span>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$order->getReturn() item=line}
						<tr>
							<td>{$line.date_add}</td>
							<td>{$line.type}</td>
							<td>{$line.state_name}</td>
							<td>
								<span id="shipping_number_show">
									{if isset($line.url) && isset($line.tracking_number)}
									<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>
									{elseif isset($line.tracking_number)}{$line.tracking_number}{/if}
								</span>
								{if $line.can_edit}
								<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'htmlall':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'htmlall':'UTF-8'}{else}0{/if}">
									<span class="shipping_number_edit" style="display:none;">
										<input type="text" name="tracking_number" value="{$line.tracking_number|htmlentities}" />
										<input type="submit" class="button" name="submitShippingNumber" value="{l s='Update'}" />
									</span>
									<a href="#" class="edit_shipping_number_link btn btn-default">
										<i class="icon-pencil"></i>
										{l s='Edit'}
									</a>
									<a href="#" class="cancel_shipping_number_link btn btn-default" style="display: none;">
										<i class="icon-remove"></i>
										{l s='Cancel'}
									</a>
								</form>
								{/if}
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
				{else}
				{l s='No merchandise returned yet.'}
				{/if}

				{if $carrierModuleCall}
					{$carrierModuleCall}
				{/if}
			</fieldset>
		</div>
	{/if}
	
	<div class="col-lg-12">
		<form class="container-command-top-spacing" action="{$current_index}&vieworder&token={$smarty.get.token}&id_order={$order->id}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.'}', '{l s='Quantity to cancel is greater than quantity available.'}');">
			<input type="hidden" name="id_order" value="{$order->id}" />
			<fieldset>
				<div style="display: none">
					<input type="hidden" value="{$order->getWarehouseList()|implode}" id="warehouse_list" />
				</div>
				<legend>
					<i class="icon-shopping-cart"></i>
					{l s='Products:'}
				</legend>
				<div class="col-lg-12">
					{if $can_edit}
						{if !$order->hasBeenDelivered()}
							<a href="#" class="add_product btn btn-default">
								<i class="icon-plus-sign-alt"></i>
								{l s='Add a product'}
							</a>
						{/if}
						<div id="refundForm">
							<!--<a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Process a standard refund'}" /> {l s='Process a standard refund'}</a>
							<a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Process a partial refund'}" /> {l s='Process a partial refund'}</a>-->
						</div>
					{/if}
				</div>
				<div class="col-lg-12">
					<table class="table" id="orderProducts">
						<thead>
							<tr>
								<th height="39" width="7%">&nbsp;</th>
								<th>
									<span class="title_box ">{l s='Product'}</span>
								</th>
								<th width="15%">
									<span class="title_box ">{l s='Unit Price'} <sup>*</sup></span>
								</th>
								<th width="4%">
									<span class="title_box ">{l s='Qty'}</span>
								</th>
								{if ($order->hasBeenPaid())}
									<th width="3%">
										<span class="title_box ">{l s='Refunded'}</span>
									</th>
								{/if}
								{if ($order->hasBeenDelivered() || $order->hasProductReturned())}
									<th width="3%">
										<span class="title_box ">{l s='Returned'}</span>
									</th>
								{/if}
								{if $stock_management}
									<th width="10%">
										<span class="title_box ">{l s='Available quantity'}</span>
									</th>
								{/if}
								<th width="10%" class="text-right">
									<span class="title_box ">{l s='Total'} <sup>*</sup></span>
								</th>
								<th colspan="2" style="display: none;" class="add_product_fields">&nbsp;</th>
								<th colspan="2" style="display: none;" class="edit_product_fields">&nbsp;</th>
								<th colspan="2" style="display: none;" class="standard_refund_fields">
									<span class="title_box ">
										<i class="icon-remove"></i>
										{if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
											{l s='Return'}
										{elseif ($order->hasBeenPaid())}
											{l s='Refund'}
										{else}
											{l s='Cancel'}
										{/if}
									</span>
								</th>
								<th widht="12%" style="display:none" class="partial_refund_fields right">
									<span class="title_box ">{l s='Partial refund'}</span>
								</th>
								{if !$order->hasBeenDelivered()}
								<th width="8%" class="text-center">
									<span class="title_box ">{l s='Action'}</span>
								</th>
								{/if}
							</tr>
						</thead>
						<tbody>
							{foreach from=$products item=product key=k}
								{* Include customized datas partial *}
								{include file='controllers/orders/_customized_data.tpl'}

								{* Include product line partial *}
								{include file='controllers/orders/_product_line.tpl'}
							{/foreach}
							{if $can_edit}
								{include file='controllers/orders/_new_product.tpl'}
							{/if}
						</tbody>
					</table>
				</div>
				<div class="col-lg-12">
					<p class="text-muted">
						<sup>*</sup> {l s='For this customer group, prices are displayed as:'}
						{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
							{l s='tax excluded.'}
						{else}
							{l s='tax included.'}
						{/if}
					</p>
					<p class="text-danger">
						{if !Configuration::get('PS_ORDER_RETURN')}
							{l s='Merchandise returns are disabled'}
						{/if}
					</p>
				</div>
				<div class="col-lg-7 pull-right">
					<table class="table">
						<tr id="total_products">
							<td width="150">
								<span><strong>{l s='Products:'}</strong></span>
							</td>
							<td class="text-right">
								<span>{displayPrice price=$order->total_products_wt currency=$currency->id}</span>
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
							<td>
								<span><strong>{l s='Discounts'}</strong></span>
							</td>
							<td class="text-right">
								<span>-{displayPrice price=$order->total_discounts_tax_incl currency=$currency->id}</span>
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
							<td>
								<span><strong>{l s='Wrapping'}</strong></span>
							</td>
							<td class="text-right">
								<span>{displayPrice price=$order->total_wrapping_tax_incl currency=$currency->id}</span>
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_shipping">
							<td>
								<span><strong>{l s='Shipping'}</strong></span>
							</td>
							<td class="text-right">
								<span>{displayPrice price=$order->total_shipping_tax_incl currency=$currency->id}</span>
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">
								<span class="col-lg-2">{$currency->prefix}</span>
								<span class="col-lg-8"><input type="text" size="3" name="partialRefundShippingCost" value="0" /></span>
								<span class="col-lg-2">{$currency->suffix}</span>
							</td>
						</tr>
						<tr id="total_order">
							<td>
								<span>{l s='Total'}</span>
							</td>
							<td class="text-right">
								<span>{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
					</table>
				</div>
				
				<!-- Partial refund -->
				<div style="display:none;" class="partial_refund_fields col-lg-7 pull-right">
					<div class="checkbox">
						<label for="reinjectQuantities" class="control-label">
							<input type="checkbox" name="reinjectQuantities" class="button" />
							{l s='Re-stock products'}
						</label>
					</div>
					<div class="checkbox">
						<label for="generateDiscount" class="control-label">
							<input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" class="button" onclick="toggleShippingCost(this)" />
							{l s='Generate a voucher'}
						</label>
					</div>
					<input type="submit" name="partialRefund" value="{l s='Partial refund'}" class="btn btn-default"/>
				</div>
				
				<!-- Standard refund -->
				<div style="display: none;" class="standard_refund_fields col-lg-7 pull-right">
					{if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
						<div class="checkbox">
							<label for="reinjectQuantities" class="control-label">
								<input type="checkbox" name="reinjectQuantities" class="button" />
								{l s='Re-stock products'}
							</label>
						</div>
					{/if}
					{if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
							
						<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost(this)" />
						<label for="generateCreditSlip" class="control-label">{l s='Generate a credit card slip'}</label>

						<input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost(this)" />
						<label for="generateCreditSlip" class="control-label">{l s='Generate a voucher'}</label>

						<span id="spanShippingBack" style="display:none;">
							<input type="checkbox" id="shippingBack" name="shippingBack" />
							<label for="shippingBack" class="control-label">{l s='Repay shipping costs'}</label>
						</span>
					{/if}
					{if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
						<div>
							<input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="btn btn-default" />
						</div>
					{/if}
				</div>

				<!-- Discount block -->
				{if (sizeof($discounts) || $can_edit)}
				<div class="col-lg-12">
					<legend>
						<i class="icon-tag"></i>
						{l s='Discounts'}
					</legend>
					{*
						TO DO - Je voudrais placer le bouton ici mais l'apparition du formulaire ne fonctionne plus....
						<a class="btn btn-default" href="#" id="add_voucher">
							<i class="icon-plus-sign-alt"></i>
							{l s='Add a new discount'}
						</a>
		
					*}
					<table class="table">
						<thead>
							<tr>
								<th>
									<span class="title_box ">{l s='Discount name'}</span>
								</th>
								<th class="text-center" width="100">
									<span class="title_box ">{l s='Value'}</span>
								</th>
								{if $can_edit}
									<th class="text-center" width="30">
										<span class="title_box ">{l s='Action'}</span>
									</th>
								{/if}
							</tr>
						</thead>
						<tbody>
							{foreach from=$discounts item=discount}
								<tr>
									<td>{$discount['name']}</td>
									<td class="text-center">
										{if $discount['value'] != 0.00}
											-
										{/if}
										{displayPrice price=$discount['value'] currency=$currency->id}
									</td>
									{if $can_edit}
									<td class="text-center">
										<a class="btn btn-default btn-small" href="{$current_index}&submitDeleteVoucher&id_order_cart_rule={$discount['id_order_cart_rule']}&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
											<i class="icon-remove"></i>
											{l s='Delete voucher'}
										</a>
									</td>
									{/if}
								</tr>
							{/foreach}
							{if $can_edit}
								<tr>
									<td colspan="3" class="text-right">
										<a class="btn btn-default" href="#" id="add_voucher">
											<i class="icon-plus-sign-alt"></i>
											{l s='Add a new discount'}
										</a>
									</td>
								</tr>
								<tr style="display: none" >
									<td colspan="3" class="current-edit" id="voucher_form">
										{include file='controllers/orders/_discount_form.tpl'}
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
				{/if}
			</fieldset>
		</form>
	</div>
</div>
<a class="btn btn-default" href="{$current_index}&token={$smarty.get.token}">
	<i class="icon-arrow-left"></i>
	{l s='Back to list'}
</a>
{/block}
