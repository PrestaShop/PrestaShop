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


<div id="container-customer"></div>

<div class="row">
<div class="col-lg-6">
<fieldset>
		<a class="btn btn-default pull-right" href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
			<i class="icon-edit"></i> {l s='Edit'}
		</a>
		<h2>
			{$customer->firstname} {$customer->lastname} <img src="{$gender_image}"/>
		</h2>
				<ul>
					<li><a href="mailto:{$customer->email}">{$customer->email}</a></li>
					<li>{l s='ID:'} {$customer->id|string_format:"%06d"}</li>
					<li>{l s='Registration date:'} {$registration_date}</li>
					<li>{l s='Last visit:'} {if $customer_stats['last_visit']}{$last_visit}{else}{l s='Never'}{/if}</li>
					<li>{if $count_better_customers != '-'}{l s='Rank: #'} {$count_better_customers}{/if}</li>
					<li>{if $shop_is_feature_active}{l s='Shop:'} {$name_shop}{/if}</li>
					<li>{l s='Language:'} {if isset($customerLanguage)}{$customerLanguage->name}{else}{l s='undefined'}{/if}</li>
					<li>{l s='Newsletter:'} {if $customer->newsletter}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}</li>
					<li>{l s='Opt in:'} {if $customer->optin}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}</li>
					<li>{l s='Age:'} {$customer_stats['age']} {if isset($customer->birthday['age'])}({$customer_birthday}){else}{l s='Unknown'}{/if}</li>
					<li>{l s='Last update:'} {$last_update}</li>
					<li>{l s='Status:'} {if $customer->active}<img src="../img/admin/enabled.gif" />{else}<img src="../img/admin/disabled.gif" />{/if}</li>
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
</fieldset>
</div>
<div class="col-lg-6">
<fieldset>
			<h3>
				<i class="icon-eye-close"></i> {l s='Add a private note'}
			</h3>

			<div class="alert alert-info">{l s='This note will be displayed to all employees but not to customers.'}</div>

			<form action="ajax.php" method="post" onsubmit="saveCustomerNote();return false;" id="customer_note">
				<textarea name="note" id="noteContent" onkeydown="$('#submitCustomerNote').removeAttr('disabled');">{$customer_note}</textarea>

				<button type="submit" id="submitCustomerNote" class="btn btn-primary" disabled="disabled" />{l s='Save'}</button>

				<span id="note_feedback"></span>
			</form>
</fieldset>
</div>
</div>






<fieldset>
		<div class="col col-lg-12">
			<h3><i class="icon-envelope-alt"></i> {l s='Messages'} <span class="badge">{count($messages)}</span></h3>
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
</fieldset>


<fieldset>
		<div class="col col-lg-12">
			<h3><i class="icon-gift"></i> {l s='Vouchers'} <span class="badge">{count($discounts)}</span></h3>
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
</fieldset>


<fieldset>
		{* display hook specified to this page : AdminCustomers *}
		<div>{hook h="displayAdminCustomers" id_customer=$customer->id}</div>
</fieldset>


<fieldset>
		<div class="col col-lg-12">
			<h3>{l s='Orders'} <span class="badge">{count($orders)}</span></h3>
			{if $orders AND count($orders)}
				{assign var=count_ok value=count($orders_ok)}
				{if $count_ok}
					<h3>
						<i class="icon-ok-circle"></i> {l s='Valid orders:'} {$count_ok} {l s='for'} {$total_ok}
					</h3>
					<table class="table">
						<thead>
							<tr>
								<th class="center">{l s='ID'}</th>
								<th>{l s='Date'}</th>
								<th>{l s='Payment: '}</th>
								<th>{l s='State'}</th>
								<th class="right">{l s='Products'}</th>
								<th class="right">{l s='Total spent'}</th>
								<th class="center">{l s='Actions'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach $orders_ok AS $key => $order}
							<tr onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
								<td>{$order['id_order']}</td>
								<td>{$order['date_add']}</td>
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
				{assign var=count_ko value=count($orders_ko)}
				{if $count_ko}
					<table class="table">
						<thead>
							<tr>
								<th>{l s='ID'}</th>
								<th>{l s='Date'}</th>
								<th>{l s='Payment: '}</th>
								<th>{l s='State'}</th>
								<th class="right">{l s='Products'}</th>
								<th class="right">{l s='Total spent'}</th>
								<th class="center">{l s='Actions'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $orders_ko AS $key => $order}
							<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']}&vieworder&token={getAdminToken tab='AdminOrders'}'">
								<td>{$order['id_order']}</td>
								<td>{$order['date_add']}</td>
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
					<div class="alert alert-info">{l s='Invalid orders:'} <span class="badge">{$count_ko}</span></div>
				{/if}
			{else}
				{l s='%1$s %2$s has not placed any orders yet' sprintf=[$customer->firstname, $customer->lastname]}
			{/if}
		</div>
</fieldset>


<fieldset>
		<div class="col-lg-12">
			<h3><i class="icon-shopping-cart"></i> {l s='Carts'} <span class="badge">{count($carts)}</span></h3>
			{if $carts AND count($carts)}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='ID'}</th>
							<th>{l s='Date'}</th>
							<th>{l s='Carrier'}</th>
							<th class="right">{l s='Total'}</th>
							<th class="center">{l s='Actions'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach $carts AS $key => $cart}
						<tr onclick="document.location = '?tab=AdminCarts&id_cart={$cart['id_cart']}&viewcart&token={getAdminToken tab='AdminCarts'}'">
							<td>{$cart['id_cart']}</td>
							<td>{$cart['date_add']}</td>
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
</fieldset>


<fieldset>
		{if $products AND count($products)}
		<div class="col-lg-12">
			<h3><i class="icon-archive"></i> {l s='Products:'} <span class="badge">{count($products)}</span></h3>
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
						<td>{$product['date_add']}</td>
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
</fieldset>


<fieldset>
		<div class="col-lg-12">
			<h3><i class="icon-building"></i> {l s='Addresses'} <span class="badge">{count($addresses)}</span></h3>
			{if count($addresses)}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='Company'}</th>
							<th>{l s='Name'}</th>
							<th>{l s='Address'}</th>
							<th>{l s='Country'}</th>
							<th class="right">{l s='Phone number(s)'}</th>
							<th class="center">{l s='Actions'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $addresses AS $key => $address}
						<tr {if $key %2}class="alt_row"{/if}>
							<td>{if $address['company']}{$address['company']}{else}--{/if}</td>
							<td>{$address['firstname']} {$address['lastname']}</td>
							<td>{$address['address1']} {if $address['address2']}{$address['address2']}{/if} {$address['postcode']} {$address['city']}</td>
							<td>{$address['country']}</td>
							<td class="right">
								{if $address['phone']}
									{$address['phone']}
									{if $address['phone_mobile']}<br />{$address['phone_mobile']}{/if}
								{else}
									{if $address['phone_mobile']}<br />{$address['phone_mobile']}{else}--{/if}
								{/if}
							</td>
							<td class="center">
								<a href="?tab=AdminAddresses&id_address={$address['id_address']}&addaddress&token={getAdminToken tab='AdminAddresses'}">
									<img src="../img/admin/edit.gif" />
								</a>
								<a href="?tab=AdminAddresses&id_address={$address['id_address']}&deleteaddress&token={getAdminToken tab='AdminAddresses'}">
									<img src="../img/admin/delete.gif" />
								</a>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			{else}
				{l s='%1$s %2$s has not registered any addresses yet' sprintf=[$customer->firstname, $customer->lastname]}
			{/if}
		</div>
</fieldset>


<fieldset>
		<div class="col-lg-12">
			<h3>
				<i class="icon-group"></i> {l s='Groups'} <span class="badge">{count($groups)}</span>
				<a class="btn btn-default pull-right" href="{$current}&updatecustomer&id_customer={$customer->id}&token={$token}">
					<i class="icon-edit"></i> {l s='Edit'}
				</a>
			</h3>
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
					<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '?tab=AdminGroups&id_group={$group['id_group']}&viewgroup&token={getAdminToken tab='AdminGroups'}'">
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
		</div>
</fieldset>


<fieldset>
		{if count($interested)}
		<div class="col-lg-12">
			<h3><i class="icon-archive"></i> {l s='Products:'} <span class="badge">{count($interested)}</span></h3>
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
						<tr {if $key %2}class="alt_row"{/if} style="cursor: pointer" onclick="document.location = '{$p['url']}'">
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
		<div >
		{/if}
</fieldset>

<fieldset>
		{if count($connections)}
		<div class="col-lg-12">
			<h3><i class="icon-time"></i> {l s='Last connections'}</h3>
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
						<td>{$connection['date_add']}</td>
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
</fieldset>


<fieldset>
		<div class="col-lg-12">
		{if count($referrers)}
			<h3><i class="icon-share-alt"></i> {l s='Referrers'}</h3>
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
						<td>{$referrer['date_add']}</td>
						<td>{$referrer['name']}</td>
						{if $shop_is_feature_active}<td>{$referrer['shop_name']}</td>{/if}
					</tr>
					{/foreach}
				</tbody>
			</table>
		{/if}
		</div>
</fieldset>

	</div>
{/block}
