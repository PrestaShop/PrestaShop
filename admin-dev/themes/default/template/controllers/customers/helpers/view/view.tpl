{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div id="container-customer">
	<div class="row">
		{*left*}
		<div class="col-lg-6">
			<div class="panel clearfix">
				<div class="panel-heading">
					<i class="icon-user"></i>
					{$customer->firstname}
					{$customer->lastname}
					[{$customer->id|string_format:"%06d"}]
					-
					<a href="mailto:{$customer->email}"><i class="icon-envelope"></i>
						{$customer->email}
					</a>
					<div class="panel-heading-action">
						<a class="btn btn-default" href="{$current|escape:'html':'UTF-8'}&amp;updatecustomer&amp;id_customer={$customer->id|intval}&amp;token={$token|escape:'html':'UTF-8'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
							<i class="icon-edit"></i>
							{l s='Edit' d='Admin.Actions'}
						</a>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="row">
						<label class="control-label col-lg-3">{l s='Social Title' d='Admin.Global'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{if $gender->name}{$gender->name}{else}{l s='Unknown' d='Admin.Orderscustomers.Feature'}{/if}</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Age' d='Admin.Orderscustomers.Feature'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if isset($customer->birthday) && $customer->birthday != '0000-00-00'}
									{l s='%1$d years old (birth date: %2$s)' sprintf=[$customer_stats['age'], $customer_birthday] d='Admin.Orderscustomers.Feature'}
								{else}
									{l s='Unknown' d='Admin.Orderscustomers.Feature'}
								{/if}
							</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Registration Date' d='Admin.Orderscustomers.Feature'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{$registration_date}</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Last Visit' d='Admin.Orderscustomers.Feature'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{if $customer_stats['last_visit']}{$last_visit}{else}{l s='Never' d='Admin.Global'}{/if}</p>
						</div>
					</div>
					{if $count_better_customers != '-'}
						<div class="row">
							<label class="control-label col-lg-3">{l s='Best Customer Rank' d='Admin.Orderscustomers.Feature'}</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$count_better_customers}</p>
							</div>
						</div>
					{/if}
					{if $shop_is_feature_active}
						<div class="row">
							<label class="control-label col-lg-3">{l s='Shop' d='Admin.Global'}</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$name_shop}</p>
							</div>
						</div>
					{/if}
					<div class="row">
						<label class="control-label col-lg-3">{l s='Language' d='Admin.Global'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if isset($customerLanguage)}
									{$customerLanguage->name}
								{else}
									{l s='Unknown' d='Admin.Orderscustomers.Feature'}
								{/if}
							</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Registrations' d='Admin.Orderscustomers.Feature'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if $customer->newsletter}
									<span class="label label-success">
										<i class="icon-check"></i>
										{l s='Newsletter' d='Admin.Global'}
									</span>
								{else}
									<span class="label label-danger">
										<i class="icon-remove"></i>
										{l s='Newsletter' d='Admin.Global'}
									</span>
								{/if}
								&nbsp;
								{if $customer->optin}
									<span class="label label-success">
										<i class="icon-check"></i>
										{l s='Partner offers' d='Admin.Orderscustomers.Feature'}
									</span>
									{else}
									<span class="label label-danger">
										<i class="icon-remove"></i>
										{l s='Partner offers' d='Admin.Orderscustomers.Feature'}
									</span>
								{/if}
							</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Latest Update' d='Admin.Orderscustomers.Feature'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{$last_update}</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Status' d='Admin.Global'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if $customer->active}
									<span class="label label-success">
										<i class="icon-check"></i>
										{l s='Active' d='Admin.Global'}
									</span>
								{else}
									<span class="label label-danger">
										<i class="icon-remove"></i>
										{l s='Inactive' d='Admin.Global'}
									</span>
								{/if}
							</p>
						</div>
					</div>
				</div>
				{if $customer->isGuest()}
					{l s='This customer is registered as a Guest.' d='Admin.Orderscustomers.Feature'}
					{if !$customer_exists}
					<form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;token={getAdminToken tab='AdminCustomers'}">
						<input type="hidden" name="id_lang" value="{$id_lang}" />
						<p class="text-center">
							<input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform to a customer account' d='Admin.Orderscustomers.Feature'}" />
						</p>
						{l s='This feature generates a random password before sending an email to your customer.' d='Admin.Orderscustomers.Help'}
					</form>
					{else}
					<p class="text-muted text-center">
						{l s='A registered customer account using the defined email address already exists. ' d='Admin.Orderscustomers.Notification'}
					</p>
					{/if}
				{/if}
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-file"></i> {l s='Orders' d='Admin.Global'} <span class="badge">{count($orders)}</span>
				</div>
				{if $orders AND count($orders)}
					{assign var=count_ok value=count($orders_ok)}
					{assign var=count_ko value=count($orders_ko)}
					<div class="panel">
						<div class="row">
							<div class="col-lg-6">
								<i class="icon-ok-circle icon-big"></i>
								{l s='Valid orders:'}
								<span class="label label-success">{$count_ok}</span>
								{l s='for a total amount of %s' sprintf=[$total_ok] d='Admin.Orderscustomers.Feature'}
							</div>
							<div class="col-lg-6">
								<i class="icon-exclamation-sign icon-big"></i>
								{l s='Invalid orders:' d='Admin.Orderscustomers.Feature'}
								<span class="label label-danger">{$count_ko}</span>
							</div>
						</div>
					</div>

					{if $count_ok}
						<table class="table">
							<thead>
								<tr>
									<th class="center"><span class="title_box ">{l s='ID' d='Admin.Global'}</span></th>
									<th><span class="title_box">{l s='Date' d='Admin.Global'}</span></th>
									<th><span class="title_box">{l s='Payment' d='Admin.Global'}</span></th>
									<th><span class="title_box">{l s='Status' d='Admin.Global'}</span></th>
									<th><span class="title_box">{l s='Products' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Total spent' d='Admin.Orderscustomers.Feature'}</span></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							{foreach $orders_ok AS $key => $order}
								<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
									<td>{$order['id_order']}</td>
									<td>{dateFormat date=$order['date_add'] full=0}</td>
									<td>{$order['payment']}</td>
									<td>{$order['order_state']}</td>
									<td>{$order['nb_products']}</td>
									<td>{$order['total_paid_real']}</td>
									<td>
										<a class="btn btn-default" href="?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">
											<i class='icon-search'></i> {l s='View' d='Admin.Actions'}
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
									<th><span class="title_box ">{l s='ID' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Date' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Payment' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Status' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Products' d='Admin.Global'}</span></th>
									<th><span class="title_box ">{l s='Total spent' d='Admin.Orderscustomers.Feature'}</span></th>
								</tr>
							</thead>
							<tbody>
								{foreach $orders_ko AS $key => $order}
								<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
									<td>{$order['id_order']}</td>
									<td><a href="?tab=AdminOrders&amp;id_order={$order['id_order']}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">{dateFormat date=$order['date_add'] full=0}</a></td>
									<td>{$order['payment']}</td>
									<td>{$order['order_state']}</td>
									<td>{$order['nb_products']}</td>
									<td>{$order['total_paid_real']}</td>
								</tr>
								{/foreach}
							</tbody>
						</table>
					{/if}
				{else}
				<p class="text-muted text-center">
					{l s='%firstname% %lastname% has not placed any orders yet' sprintf=['%firstname%' => $customer->firstname, '%lastname%' => $customer->lastname] d='Admin.Orderscustomers.Feature'}
				</p>
				{/if}
			</div>

			<div class="panel">
				<div class="panel-heading">
					<i class="icon-shopping-cart"></i> {l s='Carts' d='Admin.Global'} <span class="badge">{count($carts)}</span>
				</div>
				{if $carts AND count($carts)}
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box ">{l s='ID' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Date' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Carrier' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Total' d='Admin.Global'}</span></th>
							</tr>
						</thead>
						<tbody>
						{foreach $carts AS $key => $cart}
							<tr onclick="document.location = '?tab=AdminCarts&amp;id_cart={$cart['id_cart']|intval}&amp;viewcart&amp;token={getAdminToken tab='AdminCarts'}'">
								<td>{$cart['id_cart']}</td>
								<td>
									<a href="index.php?tab=AdminCarts&amp;id_cart={$cart['id_cart']}&amp;viewcart&amp;token={getAdminToken tab='AdminCarts'}">
										{dateFormat date=$cart['date_upd'] full=0}
									</a>
								</td>
								<td>{$cart['name']}</td>
								<td>{$cart['total_price']}</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				{else}
				<p class="text-muted text-center">
					{l s='No cart is available' d='Admin.Orderscustomers.Notification'}
				</p>
				{/if}
			</div>
			{if $products AND count($products)}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-archive"></i> {l s='Purchased products' d='Admin.Orderscustomers.Feature'} <span class="badge">{count($products)}</span>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Date' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='Name' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='Quantity' d='Admin.Global'}</span></th>
						</tr>
					</thead>
					<tbody>
						{foreach $products AS $key => $product}
						<tr onclick="document.location = '?tab=AdminOrders&amp;id_order={$product['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}'">
							<td>{dateFormat date=$product['date_add'] full=0}</td>
							<td>
								<a href="?tab=AdminOrders&amp;id_order={$product['id_order']}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">
									{$product['product_name']}
								</a>
							</td>
							<td>{$product['product_quantity']}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			{/if}
			{if count($interested)}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-eye"></i> {l s='Viewed products' d='Admin.Orderscustomers.Feature'} <span class="badge">{count($interested)}</span>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box ">{l s='ID' d='Admin.Global'}</span></th>
							<th><span class="title_box ">{l s='Name' d='Admin.Global'}</span></th>
						</tr>
					</thead>
					<tbody>
					{foreach $interested as $key => $p}
						<tr onclick="document.location = '{$p['url']|escape:'html':'UTF-8'}'">
							<td>{$p['id']}</td>
							<td><a href="{$p['url']|escape:'html':'UTF-8'}">{$p['name']}</a></td>
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
					<i class="icon-eye-close"></i> {l s='Add a private note' d='Admin.Orderscustomers.Feature'}
				</div>
				<div class="alert alert-info">{l s='This note will be displayed to all employees but not to customers.' d='Admin.Orderscustomers.Help'}</div>
				<form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id|intval});return false;" >
					<div class="form-group">
						<div class="col-lg-12">
							<textarea name="note" id="noteContent" onkeyup="$('#submitCustomerNote').removeAttr('disabled');">{$customer_note}</textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
								<i class="icon-save"></i>
								{l s='Save' d='Admin.Actions'}
							</button>
						</div>
					</div>
					<span id="note_feedback"></span>
				</form>
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-envelope"></i> {l s='Messages' d='Admin.Global'} <span class="badge">{count($messages)}</span>
				</div>
				{if count($messages)}
					<table class="table">
						<thead>
							<th><span class="title_box">{l s='Status' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='Message' d='Admin.Global'}</span></th>
							<th><span class="title_box">{l s='Sent on' d='Admin.Orderscustomers.Feature'}</span></th>
						</thead>
						{foreach $messages AS $message}
							<tr>
								<td>{$message['status']}</td>
								<td>
									<a href="index.php?tab=AdminCustomerThreads&amp;id_customer_thread={$message.id_customer_thread}&amp;viewcustomer_thread&amp;token={getAdminToken tab='AdminCustomerThreads'}">
										{$message['message']}...
									</a>
								</td>
								<td>{$message['date_add']}</td>
							</tr>
						{/foreach}
					</table>
				{else}
				<p class="text-muted text-center">
					{l s='%firstname% %lastname% has never contacted you' sprintf=['%firstname%' => $customer->firstname, '%lastname%' => $customer->lastname] d='Admin.Orderscustomers.Feature'}
				</p>
				{/if}
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-ticket"></i> {l s='Vouchers' d='Admin.Orderscustomers.Feature'} <span class="badge">{count($discounts)}</span>
				</div>
				{if count($discounts)}
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box">{l s='ID' d='Admin.Global'}</span></th>
								<th><span class="title_box">{l s='Code' d='Admin.Global'}</span></th>
								<th><span class="title_box">{l s='Name' d='Admin.Global'}</span></th>
								<th><span class="title_box">{l s='Status' d='Admin.Global'}</span></th>
								<th><span class="title_box">{l s='Qty available' d='Admin.Orderscustomers.Feature'}</span></th>
								<th><span class="title_box">{l s='Actions' d='Admin.Global'}</span></th>
							<tr/>
						</thead>
						<tbody>
					{foreach $discounts AS $key => $discount}
							<tr>
								<td>{$discount['id_cart_rule']}</td>
								<td>{$discount['code']}</td>
								<td>{$discount['name']}</td>
								<td>
									{if $discount['active']}
										<i class="icon-check"></i>
									{else}
										<i class="icon-remove"></i>
									{/if}
								</td>
								<td>{if $discount['quantity'] > 0}{$discount['quantity_for_user']|intval}{else}0{/if}</td>
								<td>
									<a href="?tab=AdminCartRules&amp;id_cart_rule={$discount['id_cart_rule']|intval}&amp;addcart_rule&amp;token={getAdminToken tab='AdminCartRules'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
										<i class="icon-pencil"></i>
									</a>
									<a href="?tab=AdminCartRules&amp;id_cart_rule={$discount['id_cart_rule']|intval}&amp;deletecart_rule&amp;token={getAdminToken tab='AdminCartRules'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
										<i class="icon-remove"></i>
									</a>
								</td>
							</tr>
						</tbody>
					{/foreach}
					</table>
				{else}
				<p class="text-muted text-center">
					{l s='%firstname% %lastname% has no discount vouchers' sprintf=['%firstname%' => $customer->firstname, '%lastname%' => $customer->lastname] d='Admin.Orderscustomers.Feature'}
				</p>
				{/if}
			</div>

			{if count($emails)}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-envelope"></i> {l s='Last emails' d='Admin.Orderscustomers.Feature'}
				</div>
				<table class="table">
					<thead>
					<tr>
						<th><span class="title_box">{l s='Date' d='Admin.Global'}</span></th>
						<th><span class="title_box">{l s='Language' d='Admin.Global'}</span></th>
						<th><span class="title_box">{l s='Subject' d='Admin.Global'}</span></th>
						<th><span class="title_box">{l s='Template' d='Admin.Global'}</span></th>
					</tr>
					</thead>
					<tbody>
					{foreach $emails as $email}
						<tr>
							<td>{dateFormat date=$email['date_add'] full=1}</td>
							<td>{$email['language']}</td>
							<td>{$email['subject']}</td>
							<td>{$email['template']}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			{/if}

			{if count($connections)}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-time"></i> {l s='Last connections' d='Admin.Orderscustomers.Feature'}
				</div>
				<table class="table">
					<thead>
					<tr>
						<th><span class="title_box">{l s='ID' d='Admin.Global'}</span></th>
						<th><span class="title_box">{l s='Date' d='Admin.Global'}</span></th>
						<th><span class="title_box">{l s='Pages viewed' d='Admin.Orderscustomers.Feature'}</span></th>
						<th><span class="title_box">{l s='Total time' d='Admin.Orderscustomers.Feature'}</span></th>
						<th><span class="title_box">{l s='Origin' d='Admin.Orderscustomers.Feature'}</span></th>
						<th><span class="title_box">{l s='IP Address' d='Admin.Orderscustomers.Feature'}</span></th>
					</tr>
					</thead>
					<tbody>
					{foreach $connections as $connection}
						<tr>
							<td>{$connection['id_connections']}</td>
							<td>{dateFormat date=$connection['date_add'] full=0}</td>
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
					<i class="icon-group"></i>
					{l s='Groups' d='Admin.Global'}
					<span class="badge">{count($groups)}</span>
					<a class="btn btn-default pull-right" href="{$current|escape:'html':'UTF-8'}&amp;updatecustomer&amp;id_customer={$customer->id|intval}&amp;token={$token|escape:'html':'UTF-8'}">
						<i class="icon-edit"></i> {l s='Edit' d='Admin.Actions'}
					</a>
				</div>
				{if $groups AND count($groups)}
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box ">{l s='ID' d='Admin.Global'}</span></th>
							<th><span class="title_box ">{l s='Name' d='Admin.Global'}</span></th>
						</tr>
					</thead>
					<tbody>
						{foreach $groups AS $key => $group}
						<tr onclick="document.location = '?tab=AdminGroups&amp;id_group={$group['id_group']|intval}&amp;viewgroup&amp;token={getAdminToken tab='AdminGroups'}'">
							<td>{$group['id_group']}</td>
							<td>
								<a href="?tab=AdminGroups&amp;id_group={$group['id_group']}&amp;viewgroup&amp;token={getAdminToken tab='AdminGroups'}">
									{$group['name']}
								</a>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
				{/if}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6">

		</div>
		<div class="col-lg-6">
			{if count($referrers)}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-share-alt"></i> {l s='Referrers' d='Admin.Orderscustomers.Feature'}
				</div>
				<table class="table">
					<thead>
						<tr>
							<th><span class="title_box ">{l s='Date' d='Admin.Global'}</span></th>
							<th><span class="title_box ">{l s='Name' d='Admin.Global'}</span></th>
							{if $shop_is_feature_active}<th>{l s='Shop' d='Admin.Global'}</th>{/if}
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
		</div>
	</div>

	<div class="row">
		{* display hook specified to this page : AdminCustomers *}
		{hook h="displayAdminCustomers" id_customer=$customer->id|intval}
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-map-marker"></i> {l s='Addresses' d='Admin.Global'} <span class="badge">{count($addresses)}</span>
					<div class="panel-heading-action">
						<a class="btn btn-default" href="?controller=AdminAddresses&amp;addaddress&amp;id_customer={$customer->id|intval}&amp;token={getAdminToken tab='AdminAddresses'}">
							<i class="icon-plus-sign"></i>
							{l s='Add' d='Admin.Actions'}
						</a>
					</div>
				</div>
				{if count($addresses)}
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box ">{l s='Company' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Name' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Address' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Country' d='Admin.Global'}</span></th>
								<th><span class="title_box ">{l s='Phone number(s)' d='Admin.Orderscustomers.Feature'}</span></th>
								<th></th>
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
								<td class="text-right">
									<div class="btn-group">
										<a class="btn btn-default" href="?tab=AdminAddresses&amp;id_address={$address['id_address']}&amp;addaddress=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
											<i class="icon-edit"></i> {l s='Edit' d='Admin.Actions'}
										</a>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a href="?tab=AdminAddresses&amp;id_address={$address['id_address']}&amp;deleteaddress&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
													<i class="icon-trash"></i>
													{l s='Delete' d='Admin.Actions'}
												</a>
											</li>
                      {hook h="displayAdminCustomersAddressesItemAction" id_address=$address['id_address']|intval}
										</ul>
									</div>
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<p class="text-muted text-center">
						{l s='%firstname% %lastname% has not registered any addresses yet' sprintf=['%firstname%' => $customer->firstname, '%lastname%' => $customer->lastname]}
					</p>
				{/if}
			</div>
		</div>
	</div>
</div>
{/block}
