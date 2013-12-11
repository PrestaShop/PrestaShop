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
	var currency_format = {$currency->format|intval};
	var currency_sign = '{$currency->sign|addslashes}';
	var currency_blank = {$currency->blank|intval};
	var priceDisplayPrecision = 0;
	
	var dashgoals_year = {$goals_year|intval};
	
	var dashgoals_ajax_link = '{$dashgoals_ajax_link|addslashes}';
</script>

<section id="dashgoals" class="panel widget">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Your forecast' mod='dashgoals'}
		<a href="javascript:void(0);" onclick="dashgoals_changeYear('backward');" class="icon-backward"></a>
		<span id="dashgoals_title">{$goals_year}</span>
		<a href="javascript:void(0);" onclick="dashgoals_changeYear('forward');" class="icon-forward"></a>
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="javascript:void(0);" onclick="toggleDashConfig('dashgoals');" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="javascript:void(0);" onclick="refreshDashboard('dashgoals');" title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	{include file='./config.tpl'}
	<section class="loading">
		<div id="dash_goals_chart1" class="chart with-transitions">
			<svg></svg>
		</div>
	</section>
</section>