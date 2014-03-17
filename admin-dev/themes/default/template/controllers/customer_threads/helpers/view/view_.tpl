{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

<!--



<div class="panel">
	<div class="panel-heading text-center">
			<button class="btn btn-default pull-left">Archiver</button>
			<button class="btn btn-default pull-left">
				<i class="icon-star"></i>
				Important
			</button>
		<div class="btn-group pull-left">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				</i>&nbsp;Assigner <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'orderBox[]', true);return false;">
						<i class="icon-user"></i>&nbsp;Employee
					</a>
				</li>
			</ul>
		</div>
		<i class="icon-comments"></i>
		Discussion : #<strong>7</strong>
		<div class="btn-group pull-right">
			<a class="btn btn-default pull-left" href="#">
				<i class="icon-chevron-left"></i>
			</a>
			<a class="btn btn-default pull-left" href="#" disabled="">
				<i class="icon-chevron-right"></i>
			</a> 
		</div>
	</div>
	<div class="row">
		<div class="message-item-initial media">
			<div class="avatar-lg pull-left"><i class="icon-user icon-3x"></i></div>
			<div class="media-body">
				<div class="row">
					<div class="col-sm-6">
						<h2>John Doe <small>(pub@prestashop.com)</small></h2>
						<span>TO: </span><span class="badge">Webmaster</span>
					</div>
					<div class="col-sm-6">
						<span class="badge">2</span> Commandes validées pour <span class="badge">42 €</span><br>
						<span>Client depuis le 01/02/2014</span>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="message-item-initial-body">
							<span class="message-date"><i class="icon-time"></i> 17/01/2014 - 14:00</span>
							<p class="message-item-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue fermentum leo et porta. Pellentesque a quam dui. Pellentesque sed augue id sem aliquet faucibus eu vel odio. Nullam non libero volutpat, pulvinar turpis non, gravida mauris.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="message-item">
			<div class="message-avatar">
				<div class="avatar-md">
					<img src="../img/tmp/employee_1.jpg">
				</div>
			</div>
			<div class="message-body">
				<h4 class="message-item-heading"><i class="icon-mail-reply text-muted"></i> Kevin</h4>
				<span class="message-date">- 17/01/2014 - <i class="icon-time"></i> 14:00</span>
				<p class="message-item-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue fermentum leo et porta. Pellentesque a quam dui. Pellentesque sed augue id sem aliquet faucibus eu vel odio. Nullam non libero volutpat, pulvinar turpis non, gravida mauris.</p>
			</div>
		</div>
		<div class="message-item">
			<div class="message-avatar">
				<div class="avatar-md">
					<i class="icon-user icon-3x"></i>
				</div>
			</div>
			<div class="message-body">
				<h4 class="message-item-heading"><i class="icon-mail-reply text-muted"></i> John Doe</h4>
				<span class="message-date">- 17/01/2014 - <i class="icon-time"></i> 14:00</span>
				<p class="message-item-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue fermentum leo et porta. Pellentesque a quam dui. Pellentesque sed augue id sem aliquet faucibus eu vel odio. Nullam non libero volutpat, pulvinar turpis non, gravida mauris.</p>
			</div>
		</div>
	</div>
</div>
<div class="panel">
	<h3>Répondre à John Doe</h3>
	<div class="row">
		<div class="media">
			<div class="pull-left">
				<span class="avatar-md"><img src="../img/tmp/employee_1.jpg?time=1389957919" alt=""></span>
			</div>
			<div class="media-body">
				<textarea name="" id="" cols="30" rows="7"></textarea>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<i class="icon-magic icon-2x"></i><br>
			Utiliser un modèle
		</button>
		<button class="btn btn-default pull-right">
			<i class="icon-mail-reply icon-2x"></i><br>
			Envoyer
		</button>
	</div>
</div>
<div class="panel">
	<h3>
		<i class="icon-clock-o"></i>
		Historiques des commandes et messages
	</h3>
	<div class="timeline">
		<article class="timeline-item alt">
			<div class="timeline-caption">
				<div class="timeline-panel arrow arrow-right">
					<span class="timeline-icon command-danger"><i class="icon-credit-card"></i></span>
					<span class="timeline-date">17/01/2014 - 14:00</span>
					<a class="badge" href="#">Commande #4242</a><br>
					<span>Status : En attente</span><br>
					<span>Paiement : Paiement par chèque</span><br><br>
					<button class="btn btn-default">Voir le détails</button>
				</div>
			</div>
		</article>
		<article class="timeline-item">
			<div class="timeline-caption">
				<div class="timeline-panel arrow arrow-left">
					<span class="timeline-icon"><i class="icon-envelope"></i></span>
					<span class="timeline-date">17/01/2014 - 12:25</span>
					Message à <span class="badge">Webmaster</span>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue fermentum leo et porta. Pellentesque a quam dui...</p>
				</div>
			</div>
		</article>

		<article class="timeline-item alt">
			<div class="timeline-caption">
				<div class="timeline-panel arrow arrow-right">
					<span class="timeline-icon command-success"><i class="icon-credit-card"></i></span>
					<span class="timeline-date">17/01/2014 - 14:00</span>
					<a class="badge" href="#">Commande #4242</a><br>
					<span>Status :</span><br>
					<span>Paiement :</span><br><br>
					<button class="btn btn-default">Voir le détails</button>
				</div>
			</div>
		</article>
	</div>
</div>

-->

{block name="override_tpl"}
<div class="panel">
	<h3>
		<i class="icon-envelope"></i>
		{l s='Message status'}
	</h3>
	<form action="{$current}&token={$token}&viewcustomer_thread&id_customer_thread={$id_customer_thread}" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div id="ChangeStatus" class="row row-margin-bottom">
			<label class="control-label col-lg-3">{l s='Change status of message:'}</label>
			<div class="col-lg-3">
				<select onchange="quickSelect(this);">
					{foreach $actions as $action}
						<option value="{$action.href}">&gt; {$action.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="row row-margin-bottom">
			<label class="control-label col-lg-3">{l s='Forward this discussion to an employee:'}</label>
			<div class="col-lg-3">
				<select name="id_employee_forward">
					<option value="-1">{l s='-- Choose --'}</option>
					{foreach $employees as $employee}
						<option value="{$employee.id_employee}"> {Tools::substr($employee.firstname, 0, 1)}. {$employee.lastname}</option>
					{/foreach}
					<option value="0">{l s='Someone else'}</option>
				</select>
			</div>
		</div>
		<div id="message_forward_email" class="row row-margin-bottom" style="display:none">
			<label class="control-label col-lg-3">{l s='Email'}</label>
			<div class="col-lg-3"> 
				<input type="text" name="email" />
			</div>
		</div>
		<div id="message_forward" style="display:none;">
			<div class="row row-margin-bottom">
				<label class="control-label col-lg-3">{l s='Comment:'}</label>
				<div class="col-lg-7"> 
					<textarea name="message_forward" rows="6">{l s='You can add a comment here.'}</textarea>
				</div>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-offset-3"> 
					<button type="Submit" name="submitForward" class="btn btn-default">
						<i class="icon-mail-forward"></i> {l s='Forward this discussion.'}
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
	{if $thread->id_customer}
		<div class="panel">
			<h3>
				<i class="icon-shopping-cart"></i> {l s='Orders'}
				<p class="pull-right">
					{l s='Validated Orders:'} {$count_ok} {l s='for'} <span class="badge">{$total_ok}</span>
				</p>
			</h3>
			<div class="table-responsive clearfix">
				<table class="table">
					<tr>
						<th class="center">
							<span class="title_box">{l s='ID'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Date'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Products:'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Total paid'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Payment: '}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='State'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Actions'}</span>
						</th>
					</tr>
					{assign var=irow value=0}
					{foreach $orders as $order}
						<tr onclick="document.location='?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
							<td class="center">{$order.id_order}</td>
							<td class="center">{$order.date_add}</td>
							<td class="center">{$order.nb_products}</td>
							<td class="center">{$order.total_paid_real}</td>
							<td class="center">{$order.payment}</td>
							<td class="center">{$order.order_state}</td>
							<td class="center">
								<a class=" btn btn-default" href="?tab=AdminOrders&id_order={$order.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
									<i class="icon-eye-open"></i> {l s='View'}
								</a>
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="7">{l s="No orders"}</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		{/if}
		{if $products && count($products)}
		<div class="panel">
			<h3>
				<i class="icon-archive"></i> {l s='Products:'}
			</h3>
			<div class="table-responsive clearfix">
				<table class="table">
					<tr>
						<th class="center">
							<span class="title_box">{l s='Date'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='ID'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Name'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Quantity'}</span>
						</th>
						<th class="center">
							<span class="title_box">{l s='Actions'}</span>
						</th>
					</tr>
					{assign var=irow value=0}
					{foreach $products as $product}
						<tr onclick="document.location = '?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}'">
							<td class="center">{$product.date_add}</td>
							<td class="center">{$product.product_id}</td>
							<td class="center">{$product.product_name}</td>
							<td class="center">{$product.product_quantity}</td>
							<td class="center">
								<a class=" btn btn-default" href="?tab=AdminOrders&id_order={$product.id_order}&vieworder&token={getAdminToken tab='AdminOrders'}">
									<i class="icon-eye-open"></i> {l s='View'}
								</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
	{/if}

	<div class="row row-margin-bottom">
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
