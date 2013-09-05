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

<script>
	var dashboard_ajax_url = '{$link->getAdminLink('AdminDashboard')}';
	var adminstats_ajax_url = '{$link->getAdminLink('AdminStats')}';
	var no_results_translation = '{l s='No result'}';
	var dashboard_use_push = '{$dashboard_use_push|intval}';
</script>

<div class="page-head">
	<h2 class="page-title">
		{l s='Dashboard'}
	</h2>
</div>

<div id="dashboard">
	<div class="row">
		<div class="col-lg-12">
			{include file="../../../../form_date_range_picker.tpl"}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3">
			{$hookDashboardZoneOne}
		</div>
		<div class="col-lg-7">
			{$hookDashboardZoneTwo}
		</div>
		<div class="col-lg-2">
			<section class="dash_news panel">
				<h4><i class="icon-rss"></i> PrestaShop News</h4>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
				<br/>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
				<br/>
				<article>
				<strong>Important it is to focus marketing efforts.</strong><br/>
				Let’s go over how to use newsletters to increase traffic to your online store and we’ll review the benefits, what to include and how to get subscribers.
				</article>
			</section>
			<section class="dash_links panel">
				<h4><i class="icon-link"></i> Useful Links</h4>
					<ul>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
						<li><a href="#">link</a></li>
					</ul>
			</section>
		</div>
	</div>
</div>
<script>
var testdata = [
	{
		key: "Direct Link",
		y: 5
	},
	{
		key: "Google.com",
		y: 2
	},
	{
		key: "Facebook.com",
		y: 9
	}
  ];

nv.addGraph(function() {
	var chart = nv.models.pieChart()
		.x(function(d) { return d.key })
		.y(function(d) { return d.y })
		.color(d3.scale.category10().range())
		.donut(true)
		.showLabels(false)
		.showLegend(false);
	  d3.select("#dash_traffic_chart2 svg")
		.datum(testdata)
		.transition().duration(1200)
		.call(chart);
	nv.utils.windowResize(chart.update);
	return chart;
});
</script>
