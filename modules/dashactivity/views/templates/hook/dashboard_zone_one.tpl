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

<section id="dash_activity" class="panel">
	<div class="panel-heading">
		<i class="icon-time"></i> {l s='Recent Activity'}
		<span class="panel-heading-action">
			<a class="list-tooolbar-btn" href="javascript:void(0);" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-tooolbar-btn" href="javascript:location.reload();" title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</div>
	<section id="dash_orders">
		<ul class="data_list_large">
			<li>
				<span class="data_label size_l">
					{l s='Orders'}<br/>
					<small class="text-muted">{l s='Within the last seven days'}</small>
				</span>
				<span class="data_value size_xxl">
					365<br/>
					<small class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</small>
				</span>
			</li>
		</ul>			
	</section>
	<section id="dash_pending">
		<header><i class="icon-time"></i> {l s='Pending'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='Pending Orders'}</span>
				<span class="data_value size_l">120</span>
			</li>
			<li>
				<span class="data_label">{l s='Return/Exchanges'}</span>
				<span class="data_value size_l">35</span>
			</li>
			<li>
				<span class="data_label">{l s='Abandonned Carts'}</span>
				<span class="data_value size_l">12</span>
			</li>
			<li>
				<span class="data_label">{l s='Products Out of Stock'}</span>
				<span class="data_value size_l">4</span>
			</li>
		</ul>
	</section>
	<section id="dash_notifications">
		<header><i class="icon-exclamation-sign"></i> {l s='Notification'}</header>
		<ul class="data_list_vertical">
			<li>
				<span class="data_label">{l s='New Messages'}</span>
				<span class="data_value size_l">42</span>
			</li>
			<li>
				<span class="data_label">{l s='Order Inquires'}</span>
				<span class="data_value size_l">13</span>
			</li>
			<li>
				<span class="data_label">{l s='Product Reviews'}</span>
				<span class="data_value size_l">56</span>
			</li>
		</ul>
	</section>
	<section id="dash_customers">
		<header><i class="icon-user"></i> {l s='Customers'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='New Customers'}</span>
				<span class="data_value size_md">42</span>
			</li>
			<li>
				<span class="data_label">{l s='Online Visitor'}</span>
				<span class="data_value size_md">200</span>
			</li>
			<li>
				<span class="data_label">
					{l s='Active Shopping Carts'}
					<small class="text-muted"><br/>
						{l s='In the last 30 minutes'}
					</small>
				</span>
				<span class="data_value size_md">36</span>
			</li>
		</ul>
	</section>
	<section id="dash_newsletter">
		<header><i class="icon-envelope"></i> {l s='Newsletter'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='New Registrations'}</span>
				<span class="data_value size_md">125</span>
			</li>
			<li>
				<span class="data_label">{l s='Total Subscribers'}</span>
				<span class="data_value size_md">13,500</span>
			</li>
		</ul>		
	</section>
	<section id="dash_traffic">
		<header><i class="icon-globe"></i> {l s='Traffic'}</header>
		<ul class="data_list">
			<li>
				<span class="data_label">{l s='Visits'}</span>
				<span class="data_value size_md">10,000</span>
			</li>
			<li>
				<span class="data_label">{l s='Unique Visitors'}</span>
				<span class="data_value size_md">3,500</span>
			</li>
			<li>
				<span class="data_label">{l s='Traffic Sources'}</span>
				<ul class="data_list_small">
					<li>
						<span class="data_label">{l s='Direct Link'}</span>
						<span class="data_value size_s">120</span>
					</li>
					<li>
						<span class="data_label">google.com</span>
						<span class="data_value size_s">75</span>
					</li>
					<li>
						<span class="data_label">facebook.com</span>
						<span class="data_value size_s">32</span>
					</li>
				</ul>
				<div id="dash_traffic_chart2" class='chart with-transitions'>
					<svg></svg>
				</div>
			</li>
		</ul>		
	</section>
</section>
