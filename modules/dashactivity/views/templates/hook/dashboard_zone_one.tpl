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
<section id="dashactivity" class="panel widget{if $allow_push} allow_push{/if}">
	<div class="panel-heading">
		<i class="icon-time"></i> {l s='Recent Activity'}
		<span class="panel-heading-action">
			<a class="list-tooolbar-btn" href="#" onclick="toggleDashConfig('dashactivity'); return false;" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-tooolbar-btn" href="#" onclick="refreshDashboard('dashactivity'); return false;" title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</div>
	<section id="dashactivity_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> {l s='Configuration'}</header>
		{$dashactivity_config_form}
	</section>
	<section id="dash_orders" class="loading">
		<ul class="data_list_large">
			<li>
				<span class="data_label size_l">
					{l s='Orders'}<br/>
					<small class="text-muted">{l s='Within the last seven days'}</small>
				</span>
				<span class="data_value size_xxl">
					<span id="order_nbr">
					</span><br/>
					<small class="dash_trend dash_trend_up"><span id="orders_trends"></span>%</small>
				</span>
			</li>
		</ul>			
	</section>
	{if $DASHACTIVITY_SHOW_PENDING}
	<section id="dash_pending" class="loading">
		<header><i class="icon-time"></i> {l s='Pending'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='Pending Orders'}</span>
				<span class="data_value size_l">
					<span id="pending_orders"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Return/Exchanges'}</span>
				<span class="data_value size_l">
					<span id="return_exchanges"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Abandoned Carts'}</span>
				<span class="data_value size_l">
					<span id="abandoned_cart"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Products Out of Stock'}</span>
				<span class="data_value size_l">
					<span id="products_out_of_stock"></span>
				</span>
			</li>
		</ul>
	</section>
	{/if}
	{if $DASHACTIVITY_SHOW_NOTIFICATION}
	<section id="dash_notifications" class="loading">
		<header><i class="icon-exclamation-sign"></i> {l s='Notification'}</header>
		<ul class="data_list_vertical">
			<li>
				<span class="data_label">{l s='New Messages'}</span>
				<span class="data_value size_l">
					<span id="new_messages"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Order Inquires'}</span>
				<span class="data_value size_l">
					<span id="order_inquires"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Product Reviews'}</span>
				<span class="data_value size_l">
					<span id="product_reviews"></span>
				</span>
			</li>
		</ul>
	</section>
	{/if}
	{if $DASHACTIVITY_SHOW_CUSTOMERS}
	<section id="dash_customers" class="loading">
		<header><i class="icon-user"></i> {l s='Customers'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='New Customers'}</span>
				<span class="data_value size_md">
					<span id="new_customers"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Online Visitor'}</span>
				<span class="data_value size_md">
					<span id="online_visitor"></span>
				</span>
			</li>
			<li>
				<span class="data_label">
					{l s='Active Shopping Carts'}
					<small class="text-muted"><br/>
						{l s='In the last 30 minutes'}
					</small>
				</span>
				<span class="data_value size_md">
					<span id="active_shopping_cart"></span>
				</span>
			</li>
		</ul>
	</section>
	{/if}
	{if $DASHACTIVITY_SHOW_NEWSLETTER}
	<section id="dash_newsletter" class="loading">
		<header><i class="icon-envelope"></i> {l s='Newsletter'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='New Registrations'}</span>
				<span class="data_value size_md">
					<span id="new_registrations"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Total Subscribers'}</span>
				<span class="data_value size_md">
					<span id="total_suscribers"></span>
				</span>
			</li>
		</ul>		
	</section>
	{/if}
	{if $DASHACTIVITY_SHOW_TRAFFIC}
	<section id="dash_traffic" class="loading">
		<header><i class="icon-globe"></i> {l s='Traffic'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='Visits'}</span>
				<span class="data_value size_md">
					<span id="visits"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Unique Visitors'}</span>
				<span class="data_value size_md">
					<span id="unique_visitors"></span>
				</span>
			</li>
			<li>
				<span class="data_label">{l s='Traffic Sources'}</span>
				<ul class="data_list_small" id="dash_traffic_source">
				</ul>
				<div id="dash_traffic_chart2" class='chart with-transitions'>
					<svg></svg>
				</div>
			</li>
		</ul>		
	</section>
	{/if}
</section>
