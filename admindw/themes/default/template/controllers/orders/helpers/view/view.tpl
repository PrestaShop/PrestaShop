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
*  @version  Release: $Revision: 17822 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	<script type="text/javascript">
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')}";
	var id_order = {$order->id};
	var id_lang = {$current_id_lang};
	var id_currency = {$order->id_currency};
	{assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
	var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
	var currency_sign = "{$currency->sign}";
	var currency_format = "{$currency->format}";
	var currency_blank = "{$currency->blank}";
	var priceDisplayPrecision = 2;
	var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
	var token = "{$smarty.get.token|escape:'htmlall':'UTF-8'}";

	var txt_add_product_stock_issue = "{l s='You want to add more product than are available in stock, are you sure you want to add this quantity?' js=1}";
	var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' js=1}";
	var txt_add_product_no_product = "{l s='Error: No product has been selected' js=1}";
	var txt_add_product_no_product_quantity = "{l s='Error: Quantity of product must be set' js=1}";
	var txt_add_product_no_product_price = "{l s='Error: Price of product must be set' js=1}";
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
	<div style="float: right; margin: -40px 40px 10px 0;">{$hook_invoice}</div><br class="clear" />';
	{/if}

<div class="bloc-command">
	<div class="button-command">
			{if (count($invoices_collection))}
				<a class="button" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}" target="_blank">
					<img src="../img/admin/charged_ok.gif" alt="{l s='View invoice'}" /> {l s='View invoice'}
				</a>
			{else}
				<img src="../img/admin/charged_ko.gif" alt="{l s='No invoice'}" /> {l s='No invoice'}
			{/if}
			 |
			{if (($currentState && $currentState->delivery) || $order->delivery_number)}
				<a class="button"  href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}" target="_blank">
					<img src="../img/admin/delivery.gif" alt="{l s='View delivery slip'}" /> {l s='View delivery slip'}
				</a>
			{else}
				<img src="../img/admin/delivery_ko.gif" alt="{l s='No delivery slip'}" /> {l s='No delivery slip'}
			{/if}
			 |
			<a class="button" href="javascript:window.print()"><img src="../img/admin/printer.gif" alt="{l s='Print order'}" title="{l s='Print order'}" /> {l s='Print order'}</a>
		</div>
		<div class="metadata-command">
			<dl>
				<dt>{l s='Date:'} </dt>
				<dd>{dateFormat date=$order->date_add full=true}</dd>
			|</dl>
			<dl>
				<dt>{l s='Messages:'}</dt>
				<dd>{sizeof($messages)}</dd>
			|</dl>
			<dl>
				<dt><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}">{l s='New Customer Messages:'}</a></dt>
				<dd><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}">{sizeof($customer_thread_message)}</a></dd>
			|</dl>
			<dl>
				<dt>{l s='Products:'}</dt>
				<dd id="product_number">{sizeof($products)}</dd>
			|</dl>
			<dl>
				<dt>{l s='Total:'}</dt>
				<dd class="total_paid">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</dd>
			</dl>
		<div class="clear"></div>
	</div>
</div>
	<div class="container-command">
		<!-- Left column -->
		<div style="width: 49%; float:left;">
			<!-- Change status form -->
			<form action="{$currentIndex}&vieworder&token={$smarty.get.token}" method="post">
				<select id="id_order_state" name="id_order_state">
				{foreach from=$states item=state}
					{if $state['id_order_state'] != $currentState->id}
					<option value="{$state['id_order_state']}">{$state['name']|stripslashes}</option>
					{/if}
				{/foreach}
				</select>
				<input type="hidden" name="id_order" value="{$order->id}" />
				<input type="submit" name="submitState" value="{l s='Add'}" class="button" />
			</form>
			<br />

			<!-- History of status -->
			<table cellspacing="0" cellpadding="0" class="table history-status" style="width: 100%;">
				<colgroup>
					<col width="1%">
					<col width="">
					<col width="20%">
					<col width="20%">
				</colgroup>
			{foreach from=$history item=row key=key}
				{if ($key == 0)}
				<tr>
					<th><img src="../img/os/{$row['id_order_state']}.gif" /></th>
					<th>{$row['ostate_name']|stripslashes}</th>
					<th>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</th>
					<th>{dateFormat date=$row['date_add'] full=true}</th>
				</tr>
				{else}
				<tr class="{if ($key % 2)}alt_row{/if}">
					<td><img src="../img/os/{$row['id_order_state']}.gif" /></td>
					<td>{$row['ostate_name']|stripslashes}</td>
					<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{else}&nbsp;{/if}</td>
					<td>{dateFormat date=$row['date_add'] full=true}</td>
				</tr>
				{/if}
			{/foreach}
			</table>

			{if $customer->id}
			<!-- Customer informations -->
			<br />
			<fieldset>
				<legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information'}</legend>
				<span style="font-weight: bold; font-size: 14px;"><a href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={getAdminToken tab='AdminCustomers'}"> {$customer->firstname} {$customer->lastname}</a></span> ({l s='#'}{$customer->id})<br />
				(<a href="mailto:{$customer->email}">{$customer->email}</a>)<br /><br />
				{if ($customer->isGuest())}
					{l s='This order has been placed by a guest.'}
					{if (!Customer::customerExists($customer->email))}
					<form method="post" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
						<input type="hidden" name="id_lang" value="{$order->id_lang}" />
						<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform guest into customer'}" /></p>
						{l s='This feature will generate a random password and send an e-mail to the customer'}
					</form>
					{else}
						<div><b style="color:red;">{l s='A registered customer account already exists with this e-mail address'}</b></div>
					{/if}
				{else}
					{l s='Account registered:'} <b>{dateFormat date=$customer->date_add full=true}</b><br />
					{l s='Valid orders placed:'} <b>{$customerStats['nb_orders']}</b><br />
					{l s='Total spent since registration:'} <b>{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</b><br />
			</fieldset>
				{/if}
			{/if}

			<!-- Sources block -->
			{if (sizeof($sources))}
			<br />
			<fieldset>
				<legend><img src="../img/admin/tab-stats.gif" /> {l s='Sources'}</legend>
				<ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
				{foreach from=$sources item=source}
					<li>
						{dateFormat date=$source['date_add'] full=true}<br />
						<b>{l s='From:'}</b>{if $source['http_referer'] != ''}<a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
						<b>{l s='To:'}</b> <a href="http://{$source['request_uri']}">{$source['request_uri']|truncate:100:'...'}</a><br />
						{if $source['keywords']}<b>{l s='Keywords:'}</b> {$source['keywords']}<br />{/if}<br />
					</li>
				{/foreach}
				</ul>
			</fieldset>
			{/if}

			<!-- Admin order hook -->
			{hook h="displayAdminOrder" id_order=$order->id}
		</div>
		<!-- END Left column -->

		<!-- Right column -->
		<div style="width: 49%; float:right;">
			<div class="button-command-prev-next">
				<b>{l s='Orders'}</b> :
				{if $previousOrder}<a class="button" href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$previousOrder}">{l s='< Prev'}</a>{/if}
				{if $nextOrder}<a class="button" href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$nextOrder}">{l s='Next >'}</a>{/if}
			</div>
			<div class="clear"></div>
			
			<!-- linked orders block -->
			{if count($order->getBrother()) > 0}
				<fieldset>
					<legend><img src="../img/admin/tab-orders.gif" /> {l s='Linked orders'}</legend>
					<table class="table" width="100%;" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th width="10%">
									{l s='Order no.'}
								</th>
								<th>
									{l s='Status'}
								</th>
								<th width="10%">
									{l s='Amount'}
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
				<br />
			{/if}
			
			<!-- Documents block -->
			<fieldset>
				<legend><img src="../img/admin/details.gif" /> {l s='Documents'}</legend>

				{* Include document template *}
				{include file='controllers/orders/_documents.tpl'}
			</fieldset>
			<br />

			<!-- Payments block -->
			<fieldset>
				<legend><img src="../img/admin/money.gif" /> {l s='Payment'}</legend>

				{if (!$order->valid && sizeof($currencies) > 1)}
				<form method="post" action="{$currentIndex}&vieworder&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
					<p class="warn">{l s='Don\'t forget to update your conversion rate before make this change.'}</p>
					<label>{l s='Don\'t forget to update your conversion rate before make this change.'}</label>
					<select name="new_currency">
						{foreach from=$currencies item=currency_change}
							{if $currency_change['id_currency'] != $order->id_currency}
							<option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
							{/if}
						{/foreach}
					</select>
					<input type="submit" class="button" name="submitChangeCurrency" value="{l s='Change'}" />
				</form>
				<hr class="clear"/>
				{/if}
				
				{if count($order->getOrderPayments()) > 0}
				<p class="error" style="{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || $currentState->id == 6}display: none;{/if}">
					{l s='Warning:'} {displayPrice price=$total_paid currency=$currency->id}
					{l s='paid instead of'} <span class="total_paid">{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</span>
					
					{foreach $order->getBrother() as $brother_order}
						{if $brother_order@first}
							{if count($order->getBrother()) == 1}
								<br />{l s='This warning also concerns the order '}
							{else}
								<br />{l s='This warning also concerns the next orders:'}
							{/if}
						{/if}
						<a href="{$current_index}&vieworder&id_order={$brother_order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">#{'%06d'|sprintf:$brother_order->id}</a>
					{/foreach}
				</p>
				{/if}

				<form id="formAddPayment" method="post" action="{$current_index}&vieworder&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
					<table class="table" width="100%" cellspacing="0" cellpadding="0">
						<colgroup>
							<col width="15%">
							<col width="">
							<col width="20%">
							<col width="10%">
							<col width="10%">
							<col width="1%">
						</colgroup>
						<thead>
							<tr>
								<th>{l s='Date'}</th>
								<th>{l s='Payment method'}</th>
								<th>{l s='Transaction ID'}</th>
								<th>{l s='Amount'}</th>
								<th>{l s='Invoice'}</th>
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
								<td class="right">
									<a href="#" class="open_payment_information"><img src="../img/admin/details.gif" title="{l s='See payment informations'}" alt="{l s='See payment informations'}" /></a>
								</td>
							</tr>
							<tr class="payment_information" style="display: none;">
								<td colspan="6">
									<p>
										<b>{l s='Card Number:'}</b>&nbsp;
										{if $payment->card_number}
											{$payment->card_number}
										{else}
											<i>{l s='Not defined'}</i>
										{/if}
									</p>

									<p>
										<b>{l s='Card Brand:'}</b>&nbsp;
										{if $payment->card_brand}
											{$payment->card_brand}
										{else}
											<i>{l s='Not defined'}</i>
										{/if}
									</p>

									<p>
										<b>{l s='Card Expiration:'}</b>&nbsp;
										{if $payment->card_expiration}
											{$payment->card_expiration}
										{else}
											<i>{l s='Not defined'}</i>
										{/if}
									</p>

									<p>
										<b>{l s='Card Holder:'}</b>&nbsp;
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
								<td colspan="6" class="center">
									<h3>{l s='No payment available'}</h3>
								</td>
							</tr>
							{/foreach}
							<tr class="current-edit">
								<td><input type="text" name="payment_date" class="datepicker" size="17" value="{date('Y-m-d H:i:s')}" /></td>
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
								<td><input class="button" type="submit" name="submitAddPayment" value="{l s='Add'}" /></td>
							</tr>
						</tbody>
					</table>
				</form>
			</fieldset>
			<br />

			<!-- Shipping block -->
			{if !$order->isVirtual()}
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" /> {l s='Shipping'}</legend>

					<div class="clear" style="float: left; margin-right: 10px;">
						<span>{l s='Recycled packaging:'}</span>
						{if $order->recyclable}
						<img src="../img/admin/enabled.gif" />
						{else}
						<img src="../img/admin/disabled.gif" />
						{/if}
					</div>
					<div style="float: left;">
						<span>{l s='Gift-wrapping:'}</span>
						{if $order->gift}
						<img src="../img/admin/enabled.gif" />
						</div>
						<div style="clear: left; margin: 0px 42px 0px 42px; padding-top: 2px;">
							{if $order->gift_message}
							<div style="border: 1px dashed #999; padding: 5px; margin-top: 8px;"><b>{l s='Message:'}</b><br />{$order->gift_message|nl2br}</div>
							{/if}
						{else}
						<img src="../img/admin/disabled.gif" />
						{/if}
					</div>
					<div class="clear" style="margin-bottom: 10px;"></div>

					{include file='controllers/orders/_shipping.tpl'}

					{if $carrierModuleCall}
						{$carrierModuleCall}
					{/if}
				</fieldset>
				<br />

				<!-- Return block -->
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" /> {l s='Merchandise returns'}</legend>

					{if $order->getReturn()|count > 0}
					<table class="table" width="100%" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th style="width:30%">Date</th>
								<th>Type</th>
								<th style="width:20%">Carrier</th>
								<th style="width:30%">Tracking number</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$order->getReturn() item=line}
							<tr>
								<td>{$line.date_add}</td>
								<td>{$line.type}</td>
								<td>{$line.state_name}</td>
								<td>
									<span id="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{elseif isset($line.tracking_number)}{$line.tracking_number}{/if}</span>
									{if $line.can_edit}
									<form style="display: inline;" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'htmlall':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'htmlall':'UTF-8'}{else}0{/if}">
										<span class="shipping_number_edit" style="display:none;">
											<input type="text" name="tracking_number" value="{$line.tracking_number|htmlentities}" />
											<input type="submit" class="button" name="submitShippingNumber" value="{l s='Update'}" />
										</span>
										<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="{l s='Edit'}" /></a>
										<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel'}" /></a>
									</form>
									{/if}
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
					{else}
					{l s='No merchandise returns yet.'}
					{/if}

					{if $carrierModuleCall}
						{$carrierModuleCall}
					{/if}
				</fieldset>
			{/if}
		</div>
		<!-- END Right column -->
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<div class="container-command container-command-top-spacing">
		<!-- Addresses -->
		{if !$order->isVirtual()}
			<div style="width: 49%; float:left;">
				<!-- Shipping address -->
				<fieldset>
					<legend><img src="../img/admin/delivery.gif" alt="{l s='Shipping address'}" />{l s='Shipping address'}</legend>

					{if $can_edit}
					<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}">
						<div style="margin-bottom:5px;">
							<p>
								<select name="id_address">
									{foreach from=$customer_addresses item=address}
									<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_delivery} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
									{/foreach}
								</select>
								<input class="button" type="submit" name="submitAddressShipping" value="{l s='Change'}" />
							</p>
						</div>
					</form>
					{/if}

					<div style="float: right">
						<a href="?tab=AdminAddresses&id_address={$addresses.delivery->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=1{/if}&token={getAdminToken tab='AdminAddresses'}&back={$smarty.server.REQUEST_URI|urlencode}"><img src="../img/admin/edit.gif" /></a>
						<a href="http://maps.google.com/maps?f=q&hl={$iso_code_lang}&geocode=&q={$addresses.delivery->address1} {$addresses.delivery->postcode} {$addresses.delivery->city} {if ($addresses.delivery->id_state)} {$addresses.deliveryState->name}{/if}" target="_blank"><img src="../img/admin/google.gif" alt="" class="middle" /></a>
					</div>

					{displayAddressDetail address=$addresses.delivery newLine='<br />'}
					{if $addresses.delivery->other}<hr />{$addresses.delivery->other}<br />{/if}
				</fieldset>
			</div>
		{/if}
		<div style="width: 49%; float:right;">
			<!-- Invoice address -->
			<fieldset>
				<legend><img src="../img/admin/invoice.gif" alt="{l s='Invoice address'}" />{l s='Invoice address'}</legend>

				{if $can_edit}
				<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}">
					<div style="margin-bottom:5px;">
						<p>
							<select name="id_address">
								{foreach from=$customer_addresses item=address}
								<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_invoice} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
								{/foreach}
							</select>
							<input class="button" type="submit" name="submitAddressInvoice" value="{l s='Change'}" />
						</p>
					</div>
				</form>
				{/if}

				<div style="float: right">
					<a href="?tab=AdminAddresses&id_address={$addresses.invoice->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=2{/if}&back={$smarty.server.REQUEST_URI|urlencode}&token={getAdminToken tab='AdminAddresses'}"><img src="../img/admin/edit.gif" /></a>
				</div>

				{displayAddressDetail address=$addresses.invoice newLine='<br />'}
				{if $addresses.invoice->other}<hr />{$addresses.invoice->other}<br />{/if}
			</fieldset>
		</div>
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<form style="width: 98%" class="container-command-top-spacing" action="{$current_index}&vieworder&token={$smarty.get.token}&id_order={$order->id}" method="post" onsubmit="return orderDeleteProduct('{l s='Cannot return this product'}', '{l s='Quantity to cancel is greater than quantity available'}');">
		<input type="hidden" name="id_order" value="{$order->id}" />
		<fieldset style="width: 100%; ">
			<div style="display: none">
				<input type="hidden" value="{$order->getWarehouseList()|implode}" id="warehouse_list" />
			</div>
			<legend><img src="../img/admin/cart.gif" alt="{l s='Products'}" />{l s='Products'}</legend>
			<div style="float:left;width: 100%;">
				{if $can_edit}
				{if !$order->hasBeenDelivered()}<div style="float: left;"><a href="#" class="add_product button"><img src="../img/admin/add.gif" alt="{l s='Add a product'}" /> {l s='Add a product'}</a></div>{/if}
				<div style="float: right; margin-right: 10px" id="refundForm">
				<!--
					<a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Process a standard refund'}" /> {l s='Process a standard refund'}</a>
					<a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Process a partial refund'}" /> {l s='Process a partial refund'}</a>
				-->
				</div>
				<br clear="left" /><br />
				{/if}
				<table style="width: 100%;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
					<tr>
						<th height="39" align="center" style="width: 7%">&nbsp;</th>
						<th>{l s='Product'}</th>
						<th style="width: 15%; text-align: center">{l s='Unit Price'} <sup>*</sup></th>
						<th style="width: 4%; text-align: center">{l s='Qty'}</th>
						{if ($order->hasBeenPaid())}<th style="width: 3%; text-align: center">{l s='Refunded'}</th>{/if}
						{if ($order->hasBeenDelivered() || $order->hasProductReturned())}<th style="width: 3%; text-align: center">{l s='Returned'}</th>{/if}
						<th style="width: 10%; text-align: center">{l s='Available quantity'}</th>
						<th style="width: 10%; text-align: center">{l s='Total'} <sup>*</sup></th>
						<th colspan="2" style="display: none;" class="add_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="edit_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="standard_refund_fields"><img src="../img/admin/delete.gif" alt="{l s='Products'}" />
							{if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
								{l s='Return'}
							{elseif ($order->hasBeenPaid())}
								{l s='Refund'}
							{else}
								{l s='Cancel'}
							{/if}
						</th>
						<th style="width: 12%;text-align:right;display:none" class="partial_refund_fields">
							{l s='Partial refund'}
						</th>
						{if !$order->hasBeenDelivered()}
						<th style="width: 8%;text-align:center;">
							{l s='Action'}
						</th>
						{/if}
					</tr>

					{foreach from=$products item=product key=k}
						{* Include customized datas partial *}
						{include file='controllers/orders/_customized_data.tpl'}

						{* Include product line partial *}
						{include file='controllers/orders/_product_line.tpl'}
					{/foreach}
					{if $can_edit}
						{include file='controllers/orders/_new_product.tpl'}
					{/if}
				</table>

				<div style="float:left; width:280px; margin-top:15px;">
					<sup>*</sup> {l s='For this customer group, prices are displayed as:'}
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{l s='tax excluded.'}
					{else}
						{l s='tax included.'}
					{/if}

					{if !Configuration::get('PS_ORDER_RETURN')}
						<br /><br />{l s='Merchandise returns are disabled'}
					{/if}
				</div>
				<div style="float:right; margin-top: 20px;">
					<table class="table" width="450px;" style="border-radius:0px;"cellspacing="0" cellpadding="0">
						<tr id="total_products">
							<td width="150px;"><b>{l s='Products'}</b></td>
							<td class="amount" align="right">{displayPrice price=$order->total_products_wt currency=$currency->id}</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
							<td><b>{l s='Discounts'}</b></td>
							<td class="amount" align="right">-{displayPrice price=$order->total_discounts_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
							<td><b>{l s='Wrapping'}</b></td>
							<td class="amount" align="right">{displayPrice price=$order->total_wrapping_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
						<tr id="total_shipping">
							<td><b>{l s='Shipping'}</b></td>
							<td class="amount" align="right">{displayPrice price=$order->total_shipping_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields current-edit" style="display:none;">{$currency->prefix}<input type="text" size="3" name="partialRefundShippingCost" value="0" />{$currency->suffix}</td>
						</tr>
						<tr style="font-size: 20px" id="total_order">
							<td style="font-size: 20px">{l s='Total'}</td>
							<td class="amount" style="font-size: 20px" align="right">
								{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}
							</td>
							<td class="partial_refund_fields current-edit" style="display:none;">&nbsp;</td>
						</tr>
					</table>
				</div>
				<div class="clear"></div>

				{if (sizeof($discounts) || $can_edit)}
				<div style="float:right; width:450px; margin-top:15px;">
					<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
						<tr>
							<th><img src="../img/admin/coupon.gif" alt="{l s='Discounts'}" />{l s='Discount name'}</th>
							<th align="center" style="width: 100px">{l s='Value'}</th>
							{if $can_edit}<th align="center" style="width: 30px">{l s='Action'}</th>{/if}
						</tr>
						{foreach from=$discounts item=discount}
						<tr>
							<td>{$discount['name']}</td>
							<td align="center">
							{if $discount['value'] != 0.00}
								-
							{/if}
							{displayPrice price=$discount['value'] currency=$currency->id}
							</td>
							{if $can_edit}
							<td class="center">
								<a href="{$current_index}&submitDeleteVoucher&id_order_cart_rule={$discount['id_order_cart_rule']}&id_order={$order->id}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}"><img src="../img/admin/delete.gif" alt="{l s='Delete voucher'}" /></a>
							</td>
							{/if}
						</tr>
						{/foreach}
					{if $can_edit}
						<tr>
							<td colspan="3" class="center">
								<a class="button" href="#" id="add_voucher"><img src="../img/admin/add.gif" alt="{l s='Add'}" /> {l s='Add a new discount'}</a>
							</td>
						</tr>
						<tr style="display: none" >
							<td colspan="3" class="current-edit" id="voucher_form">
								{include file='controllers/orders/_discount_form.tpl'}
							</td>
						</tr>
					{/if}
					</table>
				</div>
				{/if}
			</div>

			<div style="clear:both; height:15px;">&nbsp;</div>
			<div style="float: right; width: 160px; display: none;" class="standard_refund_fields">
				{if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
					<input type="checkbox" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products'}</label><br />
				{/if}
				{if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
					<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateCreditSlip" style="float:none; font-weight:normal;">{l s='Generate a credit slip'}</label><br />
					<input type="checkbox" id="generateDiscount" name="generateDiscount" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher'}</label><br />
					<span id="spanShippingBack" style="display:none;"><input type="checkbox" id="shippingBack" name="shippingBack" class="button" />&nbsp;<label for="shippingBack" style="float:none; font-weight:normal;">{l s='Repay shipping costs'}</label><br /></span>
				{/if}
				{if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
					<div style="text-align:center; margin-top:5px;">
						<input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="button" style="margin-top:8px;" />
					</div>
				{/if}
			</div>
			<div style="float: right; width: 160px; display:none;" class="partial_refund_fields">
				<input type="checkbox" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products'}</label><br />
				<input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" class="button" onclick="toggleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher'}</label><br />
				<input type="submit" name="partialRefund" value="{l s='Partial refund'}" class="button" style="margin-top:8px;" />
			</div>
		</fieldset>
	</form>
	<div class="clear" style="height:20px;">&nbsp;</div>

	<div style="float: left">
		<form action="{$smarty.server.REQUEST_URI}&token={$smarty.get.token}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?'}');">
		<fieldset style="width: 400px;">
			<legend style="cursor: pointer;" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><img src="../img/admin/email_edit.gif" /> {l s='New message'}</legend>
			<div id="message_m" style="display: {if Tools::getValue('message')}none{else}block{/if}; overflow: auto; width: 400px;">
				<a href="#" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><b>{l s='Click here'}</b> {l s='to add a comment or send a message to the customer'}</a>
			</div>
			<a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'htmlall':'UTF-8'}"><b>{l s='Click here'}</b> {l s='to see all messages'}</a><br>
			<div id="message" style="display: {if Tools::getValue('message')}block{else}none{/if}">
						<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
							<option value="0" selected="selected">-- {l s='Choose a standard message'} --</option>
			{foreach from=$orderMessages item=orderMessage}
				<option value="{$orderMessage['message']|escape:'htmlall':'UTF-8'}">{$orderMessage['name']}</option>
			{/foreach}
						</select><br /><br />
						<b>{l s='Display to customer?'}</b>
						<input type="radio" name="visibility" id="visibility" value="0" /> {l s='Yes'}
						<input type="radio" name="visibility" value="1" checked="checked" /> {l s='No'}
						<p id="nbchars" style="display:inline;font-size:10px;color:#666;"></p><br /><br />
				<textarea id="txt_msg" name="message" cols="50" rows="8" onKeyUp="var length = document.getElementById('txt_msg').value.length; if (length > 600) length = '600+'; document.getElementById('nbchars').innerHTML = '{l s='600 chars max'} (' + length + ')';">{Tools::getValue('message')|escape:'htmlall':'UTF-8'}</textarea><br /><br />
				<input type="hidden" name="id_order" value="{$order->id}" />
				<input type="hidden" name="id_customer" value="{$order->id_customer}" />
				<input type="submit" class="button" name="submitMessage" value="{l s='Send'}" />
			</div>
		</fieldset>
		</form>

	{if (sizeof($messages))}
		<br />
		<fieldset style="width: 400px;">
		<legend><img src="../img/admin/email.gif" /> {l s='Messages'}</legend>
		{foreach from=$messages item=message}
			<div style="overflow:auto; width:400px;" {if $message['is_new_for_me']}class="new_message"{/if}>
			{if ($message['is_new_for_me'])}
				<a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.server.REQUEST_URI}&token={$smarty.get.token}&messageReaded={$message['id_message']}"><img src="../img/admin/enabled.gif" alt="" /></a>
			{/if}
			{l s='At'} <i>{dateFormat date=$message['date_add']}
			</i> {l s='from'} <b>{if ($message['elastname'])}{$message['efirstname']} {$message['elastname']}{else}{$message['cfirstname']} {$message['clastname']}{/if}</b>
			{if ($message['private'] == 1)}<span style="color:red; font-weight:bold;">{l s='Private:'}</span>{/if}
			<p>{$message['message']|nl2br}</p>
			</div>
			<br />
		{/foreach}
		</fieldset>
	{/if}
	</div>


	<div class="clear">&nbsp;</div>
	<br /><br /><a href="{$current_index}&token={$smarty.get.token}"><img src="../img/admin/arrow2.gif" /> {l s='Back to list'}</a><br />
{/block}
