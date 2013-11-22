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

<section id="dashproducts" class="panel widget">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Product and Sales'}
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashproducts'); return false;" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#"  onclick="refreshDashboard('dashproducts'); return false;"  title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	<section id="dashproducts_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> {l s='Configuration' mod='dashactivity'}</header>
		{$dashproducts_config_form}
	</section>
	<section>
		<nav>
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#dash_recent_orders" data-toggle="tab">
						<i class="icon-fire"></i> {l s='Recent Orders'}
					</a>
				</li>
				<li>
					<a href="#dash_best_sellers" data-toggle="tab">
						<i class="icon-trophy"></i> {l s='Best Sellers'}
					</a>
				</li>
				<li>
					<a href="#dash_most_viewed" data-toggle="tab">
						<i class="icon-eye-open"></i>  {l s='Most Viewed'}
					</a>
				</li>
				<li>
					<a href="#dash_top_search" data-toggle="tab">
						<i class="icon-search"></i> {l s='Top Search'}
					</a>
				</li>
			</ul>
		</nav>
		<div class="tab-content panel">
			<div class="tab-pane  active" id="dash_recent_orders">
				<h3>{l s="Last 10 orders"}</h3>
				<table class="table data_table" id="table_recent_orders">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="dash_best_sellers">
				<h3>{l s="Top 10 products"} - {l s="From:"} {$date_from} {l s="to:"} {$date_to}</h3>
				<table class="table data_table" id="table_best_sellers">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="dash_most_viewed">
				<h3>{l s="Most Viewed"} - {l s="From:"} {$date_from} {l s="to:"} {$date_to}</h3>
				<table class="table data_table" id="table_most_viewed">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="tab-pane" id="dash_top_search">
				<h3>{l s="Top 10 most search terms"} - {l s="From:"} {$date_from} {l s="to:"} {$date_to}</h3>
				<table class="table data_table" id="table_top_10_most_search">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</section>