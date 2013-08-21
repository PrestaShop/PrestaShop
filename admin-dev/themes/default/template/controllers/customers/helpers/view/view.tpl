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

<div id="container-customer" class="row">

{*left*}
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-heading panel-heading-big">
				<i class="icon-user"></i> {$customer->firstname} {$customer->lastname}
			</div>
			<ul class="list-unstyled col-lg-6">
				<li>{l s='Gender:'} <span class="label"><img src="{$gender_image}"/></span></li>
				<li><i class="icon-envelope-alt"></i> {l s='Email:'} <a class="label" href="mailto:{$customer->email}">{$customer->email}</a></li>
				<li>{l s='ID:'} <span class="label">{$customer->id|string_format:"%06d"}</span></li>
				<li>{l s='Registration date:'} <span class="label">{$registration_date}</span></li>
				<li>{l s='Last visit:'} <span class="label">{if $customer_stats['last_visit']}{$last_visit}{else}{l s='Never'}{/if}</span></li>
				<li>{if $count_better_customers != '-'}{l s='Rank:'} <span class="label"># {$count_better_customers}</span>{/if}</li>
				<li>{if $shop_is_feature_active}{l s='Shop:'} <span class="label">{$name_shop}</span>{/if}</li>
			</ul>
			<ul class="list-unstyled col-lg-6">
				<li>{l s='Language:'} <span class="label">{if isset($customerLanguage)}{$customerLanguage->name}{else}{l s='undefined'}{/if}</span></li>
				<li>{l s='Newsletter:'} {if $customer->newsletter}<span class="label label-success"><i class="icon-check-sign"></i> {l s='Yes'}</span>{else}<span class="label label-warning"><i class="icon-ban-circle"></i> {l s='No'}</span>{/if}</li>
				<li>{l s='Opt in:'} {if $customer->optin}<span class="label label-success"><i class="icon-check-sign"></i> {l s='Yes'}</span>{else}<span class="label label-warning"><i class="icon-ban-circle"></i> {l s='No'}</span>{/if}</li>
				<li>{l s='Age:'} {$customer_stats['age']} {if isset($customer->birthday['age'])}({$customer_birthday}){else}{l s='Unknown'}{/if}</li>
				<li>{l s='Last update:'} <span class="label">{$last_update}</span></li>
				<li>{l s='Status:'} {if $customer->active}<span class="label label-success"><i class="icon-check-sign"></i> {l s='Yes'}</span>{else}<span class="label label-warning"><i class="icon-ban-circle"></i> {l s='No'}</span>{/if}</li>
			</ul>

			{if $customer->isGuest()}
			<div>
				{l s='This customer is registered as.'} <b>{l s='Guest'}</b>
				{if !$customer_exists}
				<form method="post" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
					<input type="hidden" name="id_lang" value="{$id_lang}" />
					<p class="center">
						<input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform to a customer account'}" />
					</p>
					{l s='This feature generates a random password before sending an email to your customer.'}
				</form>
				{else}
			</div>
			<div>
				{l s='A registered customer account using the defined email address already exists. '}
				{/if}
			</div>
			{/if}
			<a class="btn btn-default" href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
				<i class="icon-edit"></i> {l s='Edit'}
			</a>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-file"></i> {l s='Orders'} <span class="badge">{count($orders)}</span>
			</div>
			{if $orders AND count($orders)}

				{assign var=count_ok value=count($orders_ok)}
				{assign var=count_ko value=count($orders_ko)}

				<div class="panel">
					<div class="row">
						<div class="col-lg-6">
							<i class="icon-ok-circle icon-big"></i>
							{l s='Valid orders:'} <span class="badge badge-success">{$count_ok}</span> {l s='for'} {$total_ok}
						</div>
						<div class="col-lg-6">
							<i class="icon-exclamation-sign icon-big"></i>
							{l s='Invalid orders:'} <span class="badge badge-warning">{$count_ko}</span>
						</div>
					</div>
				</div>
				
				{if $count_ok}
					<table class="table">
						<thead>
							<tr>
								<th class="center">{l s='ID'}</th>
								<th>{l s='Date'}</th>
								<th>{l s='Payment: '}</th>
								<th>{l s='State'}</th>
								<th>{l s='Products'}</th>
								<th>{l s='Total spent'}</th>
								<th class="center">{l s='Actions'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach $orders_ok AS $key => $order}
							<tr onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
								<td>{$order['id_order']}</td>
								<td>{dateFormat date=$order['date_add'] full=0}</td>
								<td>{$order['payment']}</td>
								<td>{$order['order_state']}</td>
								<td align="right">{$order['nb_products']}</td>
								<td align="right">{$order['total_paid_real']}</td>
								<td align="center">
									<a class="btn btn-default" href="?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}">
										<i class='icon-eye-open'></i> {l s='View'}
									</a>
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				{/if}

				{if $count_ko}
					<table class="table">
						<thead>
							<tr>
								<th>{l s='ID'}</th>
								<th>{l s='Date'}</th>
								<th>{l s='Payment: '}</th>
								<th>{l s='State'}</th>
								<th>{l s='Products'}</th>
								<th>{l s='Total spent'}</th>
								<th class="center">{l s='Actions'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $orders_ko AS $key => $order}
							<tr onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
								<td>{$order['id_order']}</td>
								<td>{dateFormat date=$order['date_add'] full=0}</td>
								<td>{$order['payment']}</td>
								<td>{$order['order_state']}</td>
								<td align="right">{$order['nb_products']}</td>
								<td align="right">{$order['total_paid_real']}</td>
								<td align="center">
									<a class="btn btn-default" href="?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}">
										<i class='icon-eye-open'></i> {l s='View'}
									</a>
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>	
				{/if}
			{else}
				{l s='%1$s %2$s has not placed any orders yet' sprintf=[$customer->firstname, $customer->lastname]}
			{/if}
		</div>

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-shopping-cart"></i> {l s='Carts'} <span class="badge">{count($carts)}</span>
			</div>
			{if $carts AND count($carts)}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='ID'}</th>
							<th>{l s='Date'}</th>
							<th>{l s='Carrier'}</th>
							<th>{l s='Total'}</th>
							<th class="center">{l s='Actions'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach $carts AS $key => $cart}
						<tr onclick="document.location = '?tab=AdminCarts&id_cart={$cart['id_cart']}&viewcart&token={getAdminToken tab='AdminCarts'}'">
							<td>{$cart['id_cart']}</td>
							<td>{dateFormat date=$order['date_add'] full=0}</td>
							<td>{$cart['name']}</td>
							<td align="right">{$cart['total_price']}</td>
							<td align="center">
								<a class="btn btn-default" href="index.php?tab=AdminCarts&id_cart={$cart['id_cart']}&viewcart&token={getAdminToken tab='AdminCarts'}">
									<i class='icon-eye-open'></i> {l s='View'}
								</a>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			{else}
				{l s='No cart is available'}.
			{/if}
		</div>

		{if $products AND count($products)}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-archive"></i> {l s='Products:'} <span class="badge">{count($products)}</span>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th class="center">{l s='Date'}</th>
						<th class="center">{l s='Name'}</th>
						<th class="center">{l s='Quantity'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $products AS $key => $product}
					<tr onclick="document.location = '?tab=AdminOrders&id_order={$product['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
						<td>{dateFormat date=$order['date_add'] full=0}</td>
						<td>{$product['product_name']}</td>
						<td align="right">{$product['product_quantity']}</td>
						<td align="center">
							<a class="btn btn-default" href="?tab=AdminOrders&id_order={$product['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}">
								<i class='icon-eye-open'></i> {l s='View'}
							</a>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{/if}

	</div>

{*right*}
	<div class="col-lg-6">

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-eye-close"></i> {l s='Add a private note'}
			</div>
			<div class="alert alert-info">{l s='This note will be displayed to all employees but not to customers.'}</div>
			<form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote();return false;" >
				<div class="row">
					<div class="col-lg-12">
						<textarea name="note" id="noteContent" onkeydown="$('#submitCustomerNote').removeAttr('disabled');">{$customer_note}</textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<button type="submit" id="submitCustomerNote" class="btn btn-primary pull-right" disabled="disabled" /><i class="icon-save"></i> {l s='Save'}</button>
					</div>
				</div>
				<span id="note_feedback"></span>
			</form>
		</div>

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-envelope"></i> {l s='Messages'} <span class="badge">{count($messages)}</span>
			</div>
			{if count($messages)}
				<table class="table">
					<thead>
						<th>{l s='Status'}</th>
						<th>{l s='Message'}</th>
						<th>{l s='Sent on'}</th>
					</thead>
					{foreach $messages AS $message}
						<tr>
							<td>{$message['status']}</td>
							<td>
								<a href="index.php?tab=AdminCustomerThreads&id_customer_thread={$message.id_customer_thread}&viewcustomer_thread&token={getAdminToken tab='AdminCustomerThreads'}">
									{$message['message']}...
								</a>
							</td>
							<td>{$message['date_add']}</td>
						</tr>
					{/foreach}
				</table>
			{else}
				{l s='%1$s %2$s has never contacted you' sprintf=[$customer->firstname, $customer->lastname]}
			{/if}
		</div>

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-ticket"></i> {l s='Vouchers'} <span class="badge">{count($discounts)}</span>
			</div>
			{if count($discounts)}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='ID'}</th>
							<th>{l s='Code'}</th>
							<th>{l s='Name'}</th>
							<th>{l s='Status'}</th>
							<th>{l s='Actions'}</th>
						<tr/>
					</thead>
					<tbody>
				{foreach $discounts AS $key => $discount}
						<tr>
							<td align="center">{$discount['id_cart_rule']}</td>
							<td>{$discount['code']}</td>
							<td>{$discount['name']}</td>
							<td align="center">
								<img src="../img/admin/{if $discount['active']}enabled.gif{else}disabled.gif{/if}" alt="{l s='Status'}" title="{l s='Status'}" />
							</td>
							<td align="center">
								<a href="?tab=AdminCartRules&id_cart_rule={$discount['id_cart_rule']}&addcart_rule&token={getAdminToken tab='AdminCartRules'}">
									<img src="../img/admin/edit.gif" />
								</a>
								<a href="?tab=AdminCartRules&id_cart_rule={$discount['id_cart_rule']}&deletecart_rule&token={getAdminToken tab='AdminCartRules'}">
									<img src="../img/admin/delete.gif" />
								</a>
							</td>
						</tr>
					</tbody>
				{/foreach}
				</table>
			{else}
				{l s='%1$s %2$s has no discount vouchers' sprintf=[$customer->firstname, $customer->lastname]}.
			{/if}
		</div>

		

		{if count($connections)}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-time"></i> {l s='Last connections'}
			</div>
			<table class="table">
				<thead>
				<tr>
					<th>{l s='Date'}</th>
					<th>{l s='Pages viewed'}</th>
					<th>{l s='Total time'}</th>
					<th>{l s='Origin'}</th>
					<th>{l s='IP Address'}</th>
				</tr>
				</thead>
				<tbody>
				{foreach $connections as $connection}
					<tr>
						<td>{dateFormat date=$order['date_add'] full=0}</td>
						<td>{$connection['pages']}</td>
						<td>{$connection['time']}</td>
						<td>{$connection['http_referer']}</td>
						<td>{$connection['ipaddress']}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
		{/if}

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-group"></i> {l s='Groups'} <span class="badge">{count($groups)}</span>
			</div>
			{if $groups AND count($groups)}
			<table class="table">
				<thead>
					<tr>
						<th>{l s='ID'}</th>
						<th>{l s='Name'}</th>
						<th class="center">{l s='Actions'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $groups AS $key => $group}
					<tr onclick="document.location = '?tab=AdminGroups&id_group={$group['id_group']}&viewgroup&token={getAdminToken tab='AdminGroups'}'">
						<td>{$group['id_group']}</td>
						<td>{$group['name']}</td>
						<td class="center">
							<a class="btn btn-default" href="?tab=AdminGroups&id_group={$group['id_group']}&viewgroup&token={getAdminToken tab='AdminGroups'}">
								<i class='icon-eye-open'></i> {l s='View'}
							</a>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			{/if}
			<a class="btn btn-default" href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
				<i class="icon-edit"></i> {l s='Edit'}
			</a>
		</div>
	</div>


{*todo add next elements*}
	{if count($interested)}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-archive"></i> {l s='Products:'} <span class="badge">{count($interested)}</span>
		</div>
		<table class="table">
			<thead>
				<tr>
					<th>{l s='ID'}</th>
					<th>{l s='Name'}</th>
					<th class="center">{l s='Actions'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach $interested as $key => $p}
				<tr onclick="document.location = '{$p['url']}'">
					<td>{$p['id']}</td>
					<td>{$p['name']}</td>
					<td align="center">
						<a class="btn btn-default" href="{$p['url']}">
							<i class='icon-eye-open'></i> {l s='View'}
						</a>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}

	{if count($referrers)}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-share-alt"></i> {l s='Referrers'}
		</div>
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Date'}</th>
					<th>{l s='Name'}</th>
					{if $shop_is_feature_active}<th>{l s='Shop'}</th>{/if}
				</tr>
			</thead>
			<tbody>
				{foreach $referrers as $referrer}
				<tr>
					<td>{dateFormat date=$order['date_add'] full=0}</td>
					<td>{$referrer['name']}</td>
					{if $shop_is_feature_active}<td>{$referrer['shop_name']}</td>{/if}
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	{/if}

	{* display hook specified to this page : AdminCustomers *}
	{hook h="displayAdminCustomers" id_customer=$customer->id}

	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-map-marker"></i> {l s='Addresses'} <span class="badge">{count($addresses)}</span>
			</div>
			{if count($addresses)}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='Company'}</th>
							<th>{l s='Name'}</th>
							<th>{l s='Address'}</th>
							<th>{l s='Country'}</th>
							<th>{l s='Phone number(s)'}</th>
							<th class="center">{l s='Actions'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $addresses AS $key => $address}
						<tr>
							<td>{if $address['company']}{$address['company']}{else}--{/if}</td>
							<td>{$address['firstname']} {$address['lastname']}</td>
							<td>{$address['address1']} {if $address['address2']}{$address['address2']}{/if} {$address['postcode']} {$address['city']}</td>
							<td>{$address['country']}</td>
							<td>
								{if $address['phone']}
									{$address['phone']}
									{if $address['phone_mobile']}<br />{$address['phone_mobile']}{/if}
								{else}
									{if $address['phone_mobile']}<br />{$address['phone_mobile']}{else}--{/if}
								{/if}
							</td>
							<td>
								<div class="btn-group">
									<a class="btn btn-default" data-toggle="dropdown" href="?tab=AdminAddresses&id_address={$address['id_address']}&addaddress&token={getAdminToken tab='AdminAddresses'}">
										<i class="icon-edit"></i> {l s='Edit'}
									</a>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="?tab=AdminAddresses&id_address={$address['id_address']}&deleteaddress&token={getAdminToken tab='AdminAddresses'}">
										<i class="icon-trash"></i> {l s='Delete'}
									</a></li>
									</ul>
								</div>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			{else}
				{l s='%1$s %2$s has not registered any addresses yet' sprintf=[$customer->firstname, $customer->lastname]}
			{/if}
		</div>
	</div>
</div>
{/block}