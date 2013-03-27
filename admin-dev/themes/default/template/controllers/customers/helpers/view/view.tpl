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
		function saveCustomerNote()
		{
			$('#note_feedback').html('<img src="../img/loader.gif" alt="" />').show();
			var noteContent = $('#noteContent').val();
	
			$.ajax({
				type: "POST",
				url: "index.php",
				data: "token={getAdminToken tab='AdminCustomers'}&tab=AdminCustomers&ajax=1&action=updateCustomerNote&id_customer={$customer->id}&note="+noteContent,
				async : true,
				success: function(r) {
					$('#note_feedback').html('').hide();
					if (r == 'ok')
					{
						$('#note_feedback').html("<b style='color:green'>{l s='Your note has been saved.'}</b>").fadeIn(400);
						$('#submitCustomerNote').attr('disabled', true);
					}
					else if (r == 'error:validation')
						$('#note_feedback').html("<b style='color:red'>({l s='Error: Your note is not valid.'}</b>").fadeIn(400);
					else if (r == 'error:update')
						$('#note_feedback').html("<b style='color:red'>{l s='Error: Your note cannot be saved.'}</b>").fadeIn(400);
					$('#note_feedback').fadeOut(3000);
				}
			});
		}
	</script>

<div id="container-customer">

	<div class="info-customer-left">
			<div style="float: right">
			<a href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
				<img src="../img/admin/edit.gif" />
			</a>
		</div>
		<span style="font-size: 14px;">
			{$customer->firstname} {$customer->lastname}
		</span>
		<img src="{$gender_image}" style="margin-bottom: 5px" /><br />
		<a href="mailto:{$customer->email}" style="text-decoration: underline; color:#268CCD;">{$customer->email}</a>
		<br /><br />
		{l s='ID:'} {$customer->id|string_format:"%06d"}<br />
		{l s='Registration date:'} {$registration_date}<br />
		{l s='Last visit:'} {if $customer_stats['last_visit']}{$last_visit}{else}{l s='Never'}{/if}<br />
		{if $count_better_customers != '-'}{l s='Rank: #'} {$count_better_customers}<br />{/if}
		{if $shop_is_feature_active}{l s='Shop:'} {$name_shop}<br />{/if}
	</div>
	
	<div class="info-customer-right">
		<div style="float: right">
			<a href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
				<img src="../img/admin/edit.gif" />
			</a>
		</div>
		{l s='Language:'} {if isset($customerLanguage)}{$customerLanguage->name}{else}{l s='undefined'}{/if}<br />
		{l s='Newsletter:'} {if $customer->newsletter}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}<br />
		{l s='Opt in:'} {if $customer->optin}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}<br />
		{l s='Age:'} {$customer_stats['age']} {if isset($customer->birthday['age'])}({$customer_birthday}){else}{l s='Unknown'}{/if}<br /><br />
		{l s='Last update:'} {$last_update}<br />
		{l s='Status:'} {if $customer->active}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}
	
		{if $customer->isGuest()}
			<div>
				{l s='This customer is registered as.'} <b>{l s='Guest'}</b>
				{if !$customer_exists}
					<form method="post" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
						<input type="hidden" name="id_lang" value="{$id_lang}" />
						<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform to a customer account'}" /></p>
						{l s='This feature generates a random password before sending an email to your customer.'}
					</form>
				{else}
					</div><div><b style="color:red;">{l s='A registered customer account using the defined email address already exists. '}</b>
				{/if}
			</div>
		{/if}

</div>
<div class="clear"></div>
	<div class="separation"></div>
	
	<div>
		<h2>
			<img src="../img/admin/cms.gif" /> {l s='Add a private note'}
		</h2>
		<p>{l s='This note will be displayed to all employees but not to customers.'}</p>
		<form action="ajax.php" method="post" onsubmit="saveCustomerNote();return false;" id="customer_note">
			<textarea name="note" id="noteContent" style="width:600px;height:100px" onkeydown="$('#submitCustomerNote').removeAttr('disabled');">{$customer_note}</textarea><br />
			<input type="submit" id="submitCustomerNote" class="button" value="{l s='Save   '}" style="float:left;margin-top:5px" disabled="disabled" />
			<span id="note_feedback" style="position:relative; top:10px; left:10px;"></span>
		</form>
	</div>
	<div class="clear"></div>
	<div class="separation"></div>
	
	<div style="width:50%;float:left;">
		<h2>{l s='Messages'} ({count($messages)})</h2>
		{if count($messages)}
			<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
				<tr>
					<th class="left">{l s='Status'}</th>
					<th class="left">{l s='Message'}</th>
					<th class="left">{l s='Sent on'}</th>
				</tr>
				{foreach $messages AS $message}
					<tr>
						<td class="left">{$message['status']}</td>
						<td class="left">
							<a href="index.php?tab=AdminCustomerThreads&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread&token={getAdminToken tab='AdminCustomerThreads'}">
								{$message['message']}...
							</a>
						</td>
						<td class="left">{$message['date_add']}</td>
					</tr>
				{/foreach}
			</table>
			<div class="clear">&nbsp;</div>
		{else}
			{l s='%1$s %2$s has never contacted you' sprintf=[$customer->firstname, $customer->lastname]}
		{/if}
	</div>
	
	<div style="width:50%;float:left;">
		<div style="margin-left:15px;"
			<h2>{l s='Vouchers'} ({count($discounts)})</h2>
			{if count($discounts)}
				<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th>{l s='ID'}</th>
						<th>{l s='Code'}</th>
						<th>{l s='Name'}</th>
						<th>{l s='Status'}</th>
						<th>{l s='Actions'}</th>
					</tr>
				{foreach $discounts AS $key => $discount}
					<tr {if $key %2}class="alt_row"{/if}>
						<td align="center">{$discount['id_cart_rule']}</td>
						<td>{$discount['code']}</td>
						<td>{$discount['name']}</td>
						<td align="center"><img src="../img/admin/{if $discount['active']}enabled.gif{else}disabled.gif{/if}" alt="{l s='Status'}" title="{l s='Status'}" /></td>
						<td align="center">
							<a href="?tab=AdminCartRules&id_cart_rule={$discount['id_cart_rule']}&addcart_rule&token={getAdminToken tab='AdminCartRules'}"><img src="../img/admin/edit.gif" /></a>
							<a href="?tab=AdminCartRules&id_cart_rule={$discount['id_cart_rule']}&deletecart_rule&token={getAdminToken tab='AdminCartRules'}"><img src="../img/admin/delete.gif" /></a>
						</td>
					</tr>
				{/foreach}
				</table>
			{else}
				{l s='%1$s %2$s has no discount vouchers' sprintf=[$customer->firstname, $customer->lastname]}.
			{/if}
		</div>
	</div>
	
	{* display hook specified to this page : AdminCustomers *}
	<div>{hook h="displayAdminCustomers" id_customer=$customer->id}</div>
	
	<div class="clear">&nbsp;</div>
	
	<div style="width:50%;float:left;">
	<h2>{l s='Orders'} ({count($orders)})</h2>
	{if $orders AND count($orders)}
		{assign var=count_ok value=count($orders_ok)}
		{if $count_ok}
			<div>
				<h3 style="color:green;font-weight:700;clear:both;">
					{l s='Valid orders:'} {$count_ok} {l s='for'} {$total_ok}
				</h3>
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%; text-align:left;">
					<colgroup>
						<col width="10px">
						<col width="100px">
						<col width="100px">
						<col width="">
						<col width="50px">
						<col width="80px">
						<col width="70px">
					</colgroup>
					<tr>
						<th height="39px" class="center">{l s='ID'}</th>
						<th class="left">{l s='Date'}</th>
						<th class="left">{l s='Payment: '}</th>
						<th class="left">{l s='State'}</th>
						<th class="right">{l s='Products'}</th>
						<th class="right">{l s='Total spent'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
					{foreach $orders_ok AS $key => $order}
						<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
							<td class="left">{$order['id_order']}</td>
							<td class="left">{$order['date_add']}</td>
							<td class="left">{$order['payment']}</td>
							<td class="left">{$order['order_state']}</td>
							<td align="right">{$order['nb_products']}</td>
							<td align="right">{$order['total_paid_real']}</td>
							<td align="center"><a href="?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}"><img src="../img/admin/details.gif" /></a></td>
						</tr>
					{/foreach}
				</table>
			</div>
		{/if}
		{assign var=count_ko value=count($orders_ko)}
		{if $count_ko}
			<div>
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="10px">
						<col width="100px">
						<col width="">
						<col width="">
						<col width="100px">
						<col width="100px">
						<col width="52px">
					</colgroup>
					<tr>
						<th height="39px" class="left">{l s='ID'}</th>
						<th class="left">{l s='Date'}</th>
						<th class="left">{l s='Payment: '}</th>
						<th class="left">{l s='State'}</th>
						<th class="right">{l s='Products'}</th>
						<th class="right">{l s='Total spent'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
					{foreach $orders_ko AS $key => $order}
						<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
							<td class="left">{$order['id_order']}</td>
							<td class="left">{$order['date_add']}</td>
							<td class="left">{$order['payment']}</td>
							<td class="left">{$order['order_state']}</td>
							<td align="right">{$order['nb_products']}</td>
							<td align="right">{$order['total_paid_real']}</td>
							<td align="center"><a href="?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}"><img src="../img/admin/details.gif" /></a></td>
						</tr>
					{/foreach}
				</table>
				<h3 style="color:red;font-weight:normal;">{l s='Invalid orders:'} {$count_ko}</h3>
			</div>
		{/if}
	{else}
		{l s='%1$s %2$s has not placed any orders yet' sprintf=[$customer->firstname, $customer->lastname]}
	{/if}
	</div>
	
	<div style="float:left;width:50%">
		<div style="margin-left:15px;">
			<h2>{l s='Carts'} ({count($carts)})</h2>
			{if $carts AND count($carts)}
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
					<colgroup>
						<col width="50px">
						<col width="150px">
						<col width="">
						<col width="70px">
						<col width="50px">
					</colgroup>
					<tr>
						<th height="39px" class="left">{l s='ID'}</th>
						<th class="left">{l s='Date'}</th>
						<th class="left">{l s='Carrier'}</th>
						<th class="right">{l s='Total'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
					{foreach $carts AS $key => $cart}
						<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminCarts&id_cart={$cart['id_cart']}&viewcart&token={getAdminToken tab='AdminCarts'}'">
							<td class="left">{$cart['id_cart']}</td>
							<td class="left">{$cart['date_add']}</td>
							<td class="left">{$cart['name']}</td>
							<td align="right">{$cart['total_price']}</td>
							<td align="center"><a href="index.php?tab=AdminCarts&id_cart={$cart['id_cart']}&viewcart&token={getAdminToken tab='AdminCarts'}"><img src="../img/admin/details.gif" /></a></td>
						</tr>
					{/foreach}
				</table>
			{else}
				{l s='No cart is available'}.
			{/if}
		</div>
	</div>

	<div class="clear">&nbsp;</div>	
	{if $products AND count($products)}
	<div class="clear">&nbsp;</div>
		<h2>{l s='Products:'} ({count($products)})</h2>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="">
						<col width="60px">
						<col width="70px">
					</colgroup>
			<tr>
				<th height="39px" class="center">{l s='Date'}</th>
				<th class="center">{l s='Name'}</th>
				<th class="center">{l s='Quantity'}</th>
				<th class="center">{l s='Actions'}</th>
			</tr>
			{foreach $products AS $key => $product}
				<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order={$product['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
					<td>{$product['date_add']}</td>
					<td>{$product['product_name']}</td>
					<td align="right">{$product['product_quantity']}</td>
					<td align="center"><a href="?tab=AdminOrders&id_order={$product['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}"><img src="../img/admin/details.gif" /></a></td>
				</tr>
			{/foreach}
		</table>
	{/if}
	<div class="clear">&nbsp;</div>

	<div style="float:left;width:50%">
		<h2>{l s='Addresses'} ({count($addresses)})</h2>
		{if count($addresses)}
			<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
						<colgroup>
							<col width="120px">
							<col width="120px">
							<col width="">
							<col width="100px">
							<col width="170px">
							<col width="70px">
						</colgroup>
				<tr>
					<th class="left" height="39px">{l s='Company'}</th>
					<th class="left">{l s='Name'}</th>
					<th class="left">{l s='Address'}</th>
					<th class="left">{l s='Country'}</th>
					<th class="right">{l s='Phone number(s)'}</th>
					<th class="center">{l s='Actions'}</th>
				</tr>
				{foreach $addresses AS $key => $address}
					<tr {if $key %2}class="alt_row"{/if}>
						<td class="left">{if $address['company']}{$address['company']}{else}--{/if}</td>
						<td class="left">{$address['firstname']} {$address['lastname']}</td>
						<td class="left">{$address['address1']} {if $address['address2']}{$address['address2']}{/if} {$address['postcode']} {$address['city']}</td>
						<td class="left">{$address['country']}</td>
						<td class="right">
							{if $address['phone']}
								{$address['phone']}
								{if $address['phone_mobile']}<br />{$address['phone_mobile']}{/if}
							{else}
								{if $address['phone_mobile']}<br />{$address['phone_mobile']}{else}--{/if}
							{/if}
						</td>
						<td class="center">
							<a href="?tab=AdminAddresses&id_address={$address['id_address']}&addaddress&token={getAdminToken tab='AdminAddresses'}"><img src="../img/admin/edit.gif" /></a>
							<a href="?tab=AdminAddresses&id_address={$address['id_address']}&deleteaddress&token={getAdminToken tab='AdminAddresses'}"><img src="../img/admin/delete.gif" /></a>
						</td>
					</tr>
				{/foreach}
			</table>
		{else}
			{l s='%1$s %2$s has not registered any addresses yet' sprintf=[$customer->firstname, $customer->lastname]}
		{/if}
	</div>

	<div style="float:left;width:50%">
		<div style="margin-left:15px">
			<h2>
				{l s='Groups'} ({count($groups)})
				<a href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
					<img src="../img/admin/edit.gif" />
				</a>
			</h2>
			{if $groups AND count($groups)}
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
					<colgroup>
						<col width="10px">
						<col width="">
						<col width="70px">
					</colgroup>
					<tr>
						<th height="39px" class="left">{l s='ID'}</th>
						<th class="left">{l s='Name'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
				{foreach $groups AS $key => $group}
					<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminGroups&id_group={$group['id_group']}&viewgroup&token={getAdminToken tab='AdminGroups'}'">
						<td class="left">{$group['id_group']}</td>
						<td class="left">{$group['name']}</td>
						<td class="center"><a href="?tab=AdminGroups&id_group={$group['id_group']}&viewgroup&token={getAdminToken tab='AdminGroups'}"><img src="../img/admin/details.gif" /></a></td>
					</tr>
				{/foreach}
				</table>
			{/if}
		</div>
	</div>

	<div class="clear">&nbsp;</div>

	{if count($interested)}
		<div>
		<h2>{l s='Products:'} ({count($interested)})</h2>
			<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
				<colgroup>
					<col width="10px">
					<col width="">
					<col width="50px">
				</colgroup>
				{foreach $interested as $key => $p}
					<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '{$p['url']}'">
						<td>{$p['id']}</td>
						<td>{$p['name']}</td>
						<td align="center"><a href="{$p['url']}"><img src="../img/admin/details.gif" /></a></td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/if}

	<div class="clear">&nbsp;</div>
	<div style="float:left;width:50%">
	{* Last connections *}
	{if count($connections)}
		<h2>{l s='Last connections'}</h2>
		<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
				<colgroup>
					<col width="150px">
					<col width="100px">
					<col width="100px">
					<col width="">
					<col width="150px">
				</colgroup>
			<tr>
				<th class="left" height="39px;">{l s='Date'}</th>
				<th class="left">{l s='Pages viewed'}</th>
				<th class="left">{l s='Total time'}</th>
				<th class="left">{l s='Origin'}</th>
				<th class="left">{l s='IP Address'}</th>
			</tr>
			{foreach $connections as $connection}
				<tr>
					<td class="left">{$connection['date_add']}</td>
					<td class="left">{$connection['pages']}</td>
					<td class="left">{$connection['time']}</td>
					<td class="left">{$connection['http_referer']}</td>
					<td class="left">{$connection['ipaddress']}</td>
				</tr>
			{/foreach}
		</table>
		<div class="clear">&nbsp;</div>
	{/if}
	</div>
	<div style="float:left;width:50%">
		<div style="margin-left:15px">
			{if count($referrers)}
				<h2>{l s='Referrers'}</h2>
				<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
					<tr>
						<th class="left">{l s='Date'}</th>
						<th class="left">{l s='Name'}</th>
						{if $shop_is_feature_active}<th class="left">{l s='Shop'}</th>{/if}
					</tr>
					{foreach $referrers as $referrer}
						<tr>
							<td class="left">{$referrer['date_add']}</td>
							<td class="left">{$referrer['name']}</td>
							{if $shop_is_feature_active}<td class="left">{$referrer['shop_name']}</td>{/if}
						</tr>
					{/foreach}
				</table>
			{/if}
		</div>
	</div>
{/block}
</div>	
<div class="clear">&nbsp;</div>