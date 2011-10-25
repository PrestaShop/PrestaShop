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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
{literal}
	function showWarehouseList()
	{
		{/literal}{if (count($warehouse_list) > 1)}{literal}
			$('#warehouse').show();
		{/literal}{/if}{literal}
	}
	
	function hideWarehouseList()
	{
		$('#warehouse').hide();
	}
	$(document).ready(function()
	{
		hideWarehouseList();
	});
{/literal}
</script>

{if ($order->total_paid != $order->total_paid_real)}
<center><span class="warning" style="font-size: 16px">{l s='Warning:'} {displayPrice price=$order->total_paid_real currency=$currency->id} {l s='paid instead of'} {displayPrice price=$order->total_paid currency=$currency->id} !</span></center><div class="clear"><br /><br /></div>
{/if}

{if ($HOOK_INVOICE)}
<div style="float: right; margin: -40px 40px 10px 0;">{$HOOK_INVOICE}</div><br class="clear" />';
{/if}

<div style="float:left" style="width:440px">
<h2>
	{if $previousOrder}<a href="{$currentIndex}&token={$smarty.get.token}&vieworder&id_order={$previousOrder}"><img style="width:24px;height:24px" src="../img/admin/arrow-left.png" /></a>{/if}
	{if ($customer->id)}{$customer->firstname} {$customer->lastname} - {/if}{l s='Order #'}{"%06d"|sprintf:$order->id}
	{if $nextOrder}<a href="{$currentIndex}&token={$smarty.get.token}&vieworder&id_order={$nextOrder}"><img style="width:24px;height:24px" src="../img/admin/arrow-right.png" /></a>{/if}
</h2>
<div style="width:429px">
	{if (($currentState->invoice || $order->invoice_number) && count($products))}
	<a href="pdf.php?id_order={$order->id}&pdf"><img src="../img/admin/charged_ok.gif" alt="{l s='View invoice'}" /> {l s='View invoice'}</a>
	{else}
	<img src="../img/admin/charged_ko.gif" alt="{l s='No invoice'}" /> {l s='No invoice'}
	{/if}
	 -
	{if ($currentState->delivery || $order->delivery_number)}
	<a href="pdf.php?id_delivery={$order->delivery_number}"><img src="../img/admin/delivery.gif" alt="{l s='View delivery slip'}" /> {l s='View delivery slip'}</a>
	{else}
	<img src="../img/admin/delivery_ko.gif" alt="{l s='No delivery slip'}" /> {l s='No delivery slip'}
	{/if}
	 -
	<a href="javascript:window.print()"><img src="../img/admin/printer.gif" alt="{l s='Print order'}" title="{l s='Print order'}" /> {l s='Print order'}</a>
</div>
<div class="clear">&nbsp;</div>

<table cellspacing="0" cellpadding="0" class="table" style="width: 429px">
{foreach from=$history item=row key=key}
	{if ($key == 0)}
	<tr>
		<th>{dateFormat date=$row['date_add'] full=true}</th>
		<th><img src="../img/os/{$row['id_order_state']}.gif" /></th>
		<th>{$row['ostate_name']|stripslashes}</th>
		<th>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</th>
	</tr>
	{else}
	<tr class="{if ($key % 2)}alt_row{/if}">
		<td>{dateFormat date=$row['date_add'] full=true}</td>
		<td><img src="../img/os/{$row['id_order_state']}.gif" /></td>
		<td>{$row['ostate_name']|stripslashes}</td>
		<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
	</tr>
	{/if}
{/foreach}
</table>
<br />

<form action="{$currentIndex}&viewOrder&token={$smarty.get.token}" method="post" style="text-align:center;">
	<select name="id_order_state">
	{foreach from=$states item=state}
		<option onclick="{if (!$currentState->shipped && $state['shipped'])}showWarehouseList(){else}hideWarehouseList(){/if}" value="{$state['id_order_state']}" {if $state['id_order_state'] == $currentState->id}selected="selected"{/if}>{$state['name']|stripslashes}</option>
	{/foreach}
	</select>
	<select name="id_warehouse" id="warehouse">
	{foreach from=$warehouse_list item=warehouse}
		<option value="{$warehouse['id_warehouse']}">{$warehouse['name']}</option>
	{/foreach}
	</select>
	<input type="hidden" name="id_order" value="{$order->id}" />
	<input type="submit" name="submitState" value="{l s='Change'}" class="button" />
</form>

{if $customer->id}
<br />
<fieldset style="width: 400px">
	<legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information'}</legend>
	<span style="font-weight: bold; font-size: 14px;"><a href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={getAdminToken tab='AdminCustomers'}"> {$customer->firstname} {$customer->lastname}</a></span> ({l s='#'}{$customer->id})<br />
	(<a href="mailto:{$customer->email}">{$customer->email}</a>)<br /><br />
	{if ($customer->isGuest())}
		{l s='This order has been placed by a'} <b>{l s='guest'}</b>
		{if (!Customer::customerExists($customer->email))}
		<form method="POST" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
			<input type="hidden" name="id_lang" value="{$order->id_lang}" />
			<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform to customer'}" /></p>
			{l s='This feature will generate a random password and send an e-mail to the customer'}
		</form>
		{else}
			<div><b style="color:red;">{l s='A registered customer account exists with the same email address'}</b></div>
		{/if}
	{else}
		{l s='Account registered:'} <b>{dateFormat date=$customer->date_add full=true}</b><br />
		{l s='Valid orders placed:'} <b>{$customerStats['nb_orders']}</b><br />
		{l s='Total paid since registration:'} <b>{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</b><br />
	</fieldset>
	{/if}
{/if}

{if (sizeof($sources))}
<br />
<fieldset style="width: 400px;">
	<legend><img src="../img/admin/tab-stats.gif" /> {l s='Sources'}</legend>
	<ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll; width: 360px;"{/if}>
	{foreach from=$sources item=source}
		<li>
			{dateFormat date=$source['date_add'] full=true}<br />
			<b>{l s='From:'}</b> <a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a><br />
			<b>{l s='To:'}</b> {$source['request_uri']}<br />
			{if $source['keywords']}<b>{l s='Keywords:'}</b> {$source['keywords']}<br />{/if}<br />
		</li>
	{/foreach}
	</ul>
</fieldset>
{/if}

{if $HOOK_ADMIN_ORDER}
	{$HOOK_ADMIN_ORDER}
{/if}

</div>
<div style="float: left; margin-left: 40px">
	<fieldset style="width: 400px">
	{if (($currentState->invoice OR $order->invoice_number) AND count($products))}
		<legend><a href="pdf.php?id_order={$order->id}&pdf"><img src="../img/admin/charged_ok.gif" /> {l s='Invoice'}</a></legend>
		<a href="pdf.php?id_order={$order->id}&pdf">{l s='Invoice #'}<b>{Configuration::get('PS_INVOICE_PREFIX', $id_lang)}{"%06d"|sprintf:$order->invoice_number}</b></a>
		<br />{l s='Created on:'} {dateFormat date=$order->invoice_date full=true}
	{else}
		<legend><img src="../img/admin/charged_ko.gif" /> {l s='Invoice'}</legend>
		{l s='No invoice yet.'}
	{/if}
	</fieldset>
	<br />

	<fieldset style="width:400px">
		<legend><img src="../img/admin/delivery.gif" /> {l s='Shipping information'}</legend>
		{l s='Total weight:'} <b>{$order->getTotalWeight()|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</b><br />
		{l s='Carrier:'} <b>{if $carrier->name == '0'}{Configuration::get('PS_SHOP_NAME')}{else}{$carrier->name}{/if}</b><br />

		{if ($currentState->delivery || $order->delivery_number)}
		<br /><a href="pdf.php?id_delivery={$order->delivery_number}">{l s='Delivery slip #'}<b>{Configuration::get('PS_DELIVERY_PREFIX', $id_lang)}{"%06d"|sprintf:$order->delivery_number}</b></a><br />
		{/if}

		{if $order->shipping_number}
			{l s='Tracking number:'} <b>{$order->shipping_number}</b>
			{if $carrier->url}
			<a href="{$carrier->url|replace:'@':$order->shipping_number}" target="_blank">{l s='Track the shipment'}</a>
			{/if}
		{/if}

		{if $carrierModuleCall}
			{$carrierModuleCall}
		{/if}

		{if ($carrier->url && $order->hasBeenShipped())}
		<form action="{$currentIndex}&viewOrder&token={$smarty.get.token}" method="post" style="margin-top:10px;">
			<input type="text" name="shipping_number" value="{$order->shipping_number}" />
			<input type="hidden" name="id_order" value="{$order->id}" />
			<input type="submit" name="submitShippingNumber" value="{l s='Set shipping number'}" class="button" />
		</form>
		{/if}
	</fieldset>

<br />
<fieldset>
	<legend>
		<img widtdh="20" height="16" src="../img/admin/order-detail-icone.png" />
		{l s='Payment detail'}
	</legend>
	<ul style="list-style:none; display:block; line-height: 1.5em; padding 4px 0;">
		<li style="margin-bottom:10px;">
			<form method="post" action="{$smarty.server.REQUEST_URI}">
				<font style="font-weight:bolder;">{l s='Set the transaction id:'}</font> 
				<input type="text" name="transaction_id" value="{if $paymentCCDetails}{$paymentCCDetails['transaction_id']}{/if}" />
				<input type="hidden" name="id_payment_cc" value="{if $paymentCCDetails}{$paymentCCDetails['id_payment_cc']}{/if}" />
				<input class="button" type="submit" name="setTransactionId" value="{l s='Update'}"/>
			</form>
		</li>
		{if $paymentCCDetails}
			{if $paymentCCDetails['card_holder'] != ''}
				<li>
					<font style="font-weight:bolder;">{l s='Card Holder:'} </font>
					{$paymentCCDetails['card_holder']}
				</li>
			{/if}
			{if $paymentCCDetails['card_number'] != ''}
				<li>
					<font style="font-weight:bolder;">{l s='Card Number:'} </font>						
					****{$paymentCCDetails['card_number']|substr:-4}
				</li>
			{/if}
			{if $paymentCCDetails['card_brand'] != ''}
				<li>
					<font style="font-weight:bolder;">{l s='Card Brand:'} </font>
						{$paymentCCDetails['card_brand']}
				</li>
			{/if}
			{if $paymentCCDetails['card_expiration'] != ''}
				<li>
					<font style="font-weight:bolder;">{l s='Card expiration:'} </font>
					{$paymentCCDetails['card_expiration']}
				</li>
			{/if}
		{/if}
	</ul>
</fieldset>

	<br />
	<fieldset style="width: 400px">
		<legend><img src="../img/admin/details.gif" /> {l s='Order details'}</legend>
		{if (Shop::isFeatureActive())}
		<label>{l s='Shop:'}</label>
		<div style="margin: 2px 0 1em 190px;">{Shop::getInstance($order->id_shop)->name}</div>
		{/if}

		<label>{l s='Original cart:'}</label>
		<div style="margin: 2px 0 1em 190px;"><a href="?tab=AdminCarts&id_cart={$cart->id}&viewcart&token={getAdminToken tab='AdminCarts'}">{l s='Cart #'}{"%06d"|sprintf:$cart->id}</a></div>
		<label>{l s='Payment mode:'}</label>
		<div style="margin: 2px 0 1em 190px; padding: 2px 0px;">{substr($order->payment, 0, 32)}{if $order->module} ({$order->module}){/if}</div>
		<div style="margin: 2px 0 1em 50px;">
			<table class="table" width="300px;" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150px;">{l s='Products'}</td>
					<td align="right">{displayPrice price=$order->getTotalProductsWithTaxes() currency=$currency->id}</td>
				</tr>
				{if $order->total_discounts > 0}
				<tr>
					<td>{l s='Discounts'}</td>
					<td align="right">-{displayPrice price=$order->total_discounts currency=$currency->id}</td>
				</tr>
				{/if}
				{if $order->total_wrapping > 0}
				<tr>
					<td>{l s='Wrapping'}</td>
					<td align="right">{displayPrice price=$order->total_wrapping currency=$currency->id}</td>
				</tr>
				{/if}
				<tr>
					<td>{l s='Shipping'}</td>
					<td align="right">{displayPrice price=$order->total_shipping currency=$currency->id}</td>
				</tr>
				<tr style="font-size: 20px">
					<td>{l s='Total'}</td>
					<td align="right">
						{displayPrice price=$order->total_paid currency=$currency->id}
						{if $order->total_paid != $order->total_paid_real}
							<br />
							<font color="red">{l s='Paid:'} {displayPrice price=$order->total_paid_real currency=$currency->id}</font>
						{/if}
					</td>
				</tr>
			</table>
		</div>
		<div style="float: left; margin-right: 10px; margin-left: 42px;">
			<span class="bold">{l s='Recycled package:'}</span>
			{if $order->recyclable}
			<img src="../img/admin/enabled.gif" />
			{else}
			<img src="../img/admin/disabled.gif" />
			{/if}
		</div>
		<div style="float: left; margin-right: 10px;">
			<span class="bold">{l s='Gift wrapping:'}</span>
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
		</fieldset>
</div>
<div class="clear">&nbsp;</div>

<div class="clear">&nbsp;</div>
<div style="float: left">
	<fieldset style="width: 400px;">
		<legend><img src="../img/admin/delivery.gif" alt="{l s='Shipping address'}" />{l s='Shipping address'}</legend>
		<div style="float: right">
			<a href="?tab=AdminAddresses&id_address={$addresses.delivery->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=1{/if}&token={getAdminToken tab='AdminAddresses'}&back={$smarty.server.REQUEST_URI}"><img src="../img/admin/edit.gif" /></a>
			<a href="http://maps.google.com/maps?f=q&hl={$iso_code_lang}&geocode=&q={$addresses.delivery->address1} {$addresses.delivery->postcode} {$addresses.delivery->city} {if ($addresses.delivery->id_state)} {$addresses.deliveryState->name}{/if}" target="_blank"><img src="../img/admin/google.gif" alt="" class="middle" /></a>
		</div>
		{displayAddressDetail address=$addresses.delivery newLine='<br />'}
		{if $addresses.delivery->other}<hr />{$addresses.delivery->other}<br />{/if}
	</fieldset>
</div>
<div style="float: left; margin-left: 40px">
	<fieldset style="width: 400px;">
		<legend><img src="../img/admin/invoice.gif" alt="{l s='Invoice address'}" />{l s='Invoice address'}</legend>
		<div style="float: right"><a href="?tab=AdminAddresses&id_address={$addresses.invoice->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=2{/if}&back={$smarty.server.REQUEST_URI}&token={getAdminToken tab='AdminAddresses'}"><img src="../img/admin/edit.gif" /></a></div>
		{displayAddressDetail address=$addresses.invoice newLine='<br />'}
		{if $addresses.invoice->other}<hr />{$addresses.invoice->other}<br />{/if}
	</fieldset>
</div>
<div class="clear">&nbsp;</div>

<form action="{$currentIndex}&submitCreditSlip&vieworder&token={$smarty.get.token}" method="post" onsubmit="return orderDeleteProduct('{l s='Cannot return this product'}', '{l s='Quantity to cancel is greater than quantity available'}');">
	<input type="hidden" name="id_order" value="{$order->id}" />
	<fieldset style="width: 868px; ">
		<legend><img src="../img/admin/cart.gif" alt="{l s='Products'}" />{l s='Products'}</legend>
		<div style="float:left;">
			<table style="width: 868px;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
				<tr>
					<th align="center" style="width: 60px">&nbsp;</th>
					<th>{l s='Product'}</th>
					<th style="width: 80px; text-align: center">{l s='UP'} <sup>*</sup></th>
					<th style="width: 20px; text-align: center">{l s='Qty'}</th>
					{if ($order->hasBeenPaid())}<th style="width: 20px; text-align: center">{l s='Refunded'}</th>{/if}
					{if ($order->hasBeenDelivered())}<th style="width: 20px; text-align: center">{l s='Returned'}</th>{/if}
					<th style="width: 30px; text-align: center">{l s='Stock'}</th>
					<th style="width: 90px; text-align: center">{l s='Total'} <sup>*</sup></th>
					<th colspan="2" style="width: 120px;"><img src="../img/admin/delete.gif" alt="{l s='Products'}" />
						{if ($order->hasBeenDelivered())}
							{l s='Return'}
						{elseif ($order->hasBeenPaid())}
							{l s='Refund'}
						{else}
							{l s='Cancel'}
						{/if}
					</th>
				</tr>

				{foreach from=$products item=product key=k}
					{* Include customized datas partial *}
					{include file='orders/_customized_data.tpl'}

					{* Include product line partial *}
					{include file='orders/_product_line.tpl'}
				{/foreach}
			</table>

			<div style="float:left; width:280px; margin-top:15px;">
				<sup>*</sup> {l s='According to the group of this customer, prices are printed:'}
				{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
					{l s='tax excluded.'}
				{else}
					{l s='tax included.'}
				{/if}

				{if Configuration::get('PS_ORDER_RETURN')}
					<br /><br />{l s='Merchandise returns are disabled'}
				{/if}
			</div>

			{if (sizeof($discounts))}
			<div style="float:right; width:280px; margin-top:15px;">
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<tr>
						<th><img src="../img/admin/coupon.gif" alt="{l s='Discounts'}" />{l s='Discount name'}</th>
						<th align="center" style="width: 100px">{l s='Value'}</th>
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
					</tr>
					{/foreach}
				</table>
			</div>
			{/if}
		</div>

		<div style="clear:both; height:15px;">&nbsp;</div>
		<div style="float: right; width: 160px;">
		{if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
			<input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products'}</label><br />
		{/if}
		{if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
			<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" class="button" onclick="toogleShippingCost(this)" />&nbsp;<label for="generateCreditSlip" style="float:none; font-weight:normal;">{l s='Generate a credit slip'}</label><br />
			<input type="checkbox" id="generateDiscount" name="generateDiscount" class="button" onclick="toogleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher'}</label><br />
			<span id="spanShippingBack" style="display:none;"><input type="checkbox" id="shippingBack" name="shippingBack" class="button" />&nbsp;<label for="shippingBack" style="float:none; font-weight:normal;">{l s='Repay shipping costs'}</label><br /></span>
		{/if}
		{if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
			<div style="text-align:center; margin-top:5px;">
				<input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="button" style="margin-top:8px;" />
			</div>
		{/if}
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
		<div id="message" style="display: {if Tools::getValue('message')}block{else}none{/if}">
					<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
						<option value="0" selected="selected">-- {l s='Choose a standard message'} --</option>
		{foreach from=$orderMessages item=orderMessage}
			<option value="{$orderMessage['message']|escape:'htmlall':'UTF-8'}">{$orderMessage['name']}</option>
		{/foreach}
					</select><br /><br />
					<b>{l s='Display to consumer?'}</b>
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
			<a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.get.REQUEST_URI}&token={$smarty.get.token}&messageReaded={$message['id_message']}"><img src="../img/admin/enabled.gif" alt="" /></a>
		{/if}
		{l s='At'} <i>{dateFormat date=$message['date_add']}
		</i> {l s='from'} <b>{if ($message['elastname'])}{$message['efirstname']} {$message['elastname']}{else}{$message['cfirstname']} {$message['clastname']}{/if}</b>
		{if ($message['private'] == 1)}<span style="color:red; font-weight:bold;">{l s='Private:'}</span>{/if}
		<p>{$message['message']|nl2br}</p>
		</div>
		<br />
	{/foreach}
	<p class="info">{l s='When you read a message, please click on the green check.'}</p>
	</fieldset>
{/if}
</div>

<div style="float: left; margin-left: 40px">
	<fieldset style="width: 400px;">
		<legend><img src="../img/admin/return.gif" alt="{l s='Merchandise returns'}" />{l s='Merchandise returns'}</legend>
{if (!sizeof($returns))}
	{l s='No merchandise return for this order.'}
{else}
	{foreach from=$returns item=return}
		({dateFormat date=$return['date_upd']}) :
		<b><a href="index.php?tab=AdminReturn&id_order_return={$return['id_order_return']}&updateorder_return&token={getAdminToken tab='AdminReturn'}">{l s='#'}{'%06d'|sprintf:$return['id_order_return']}</a></b> -
		{$return['state_name']}<br />
	{/foreach}
{/if}
	</fieldset>

	<br />
	<fieldset style="width: 400px;">
		<legend><img src="../img/admin/slip.gif" alt="{l s='Credit slip'}" />{l s='Credit slip'}</legend>
{if (!sizeof($slips))}
	{l s='No slip for this order.'}
{else}
	{foreach from=$slips item=slip}
		({dateFormat date=$slip['date_upd']}) : <b><a href="pdf.php?id_order_slip={$slip['id_order_slip']}">{l s='#'}{'%06d'|sprintf:$slip['id_order_slip']}</a></b><br />
	{/foreach}
{/if}
	</fieldset>
</div>
<div class="clear">&nbsp;</div>
<br /><br /><a href="{$currentIndex}&token={$smarty.get.token}"><img src="../img/admin/arrow2.gif" /> {l s='Back to list'}</a><br />