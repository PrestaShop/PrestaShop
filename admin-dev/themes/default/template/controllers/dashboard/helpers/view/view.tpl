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
			</section>
			<section class="dash_links panel">
				<h4><i class="icon-link"></i>{l s="Useful PrestaShop Links"}</h4>
					<dl>
						<dt>{l s="Discover the latest official documentation :"}</dt>
						<dd><a href="http://doc.prestashop.com/display/PS16?utm_source=backoffice_dashboard" target="_blank">{l s="PrestaShop documentation"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Use the PrestaShop forum & discover a great community :"}</dt>
						<dd><a href="http://www.prestashop.com/forums?utm_source=backoffice_dashboard" target="_blank">{l s="Go to forums.prestashop.com"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Enhance your Shop with new templates & modules :"}</dt>
						<dd><a href="http://addons.prestashop.com?utm_source=backoffice_dashboard" target="_blank">{l s="Go to addons.prestashop.com"}</a></dd>
					</dl>
			</section>
		</div>
	</div>
</div>