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

<section id="dashgoals" class="panel widget">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Goals' mod='dashgoals'}
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="javascript:toggleDashConfig('dashgoals');" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#"  onclick="refreshDashboard('dashgoals');" title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	<section id="dashgoals_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> {l s='Configuration' mod='dashgoals'}</header>
		<form class="defaultForm form-horizontal" method="post" action="{$link->getAdminLink('AdminDashboard')}">
			<table class="table table-condensed table-striped table-bordered">
				<thead>
					<tr>
						<th>{$goals_year}</th>
						<th>{l s='Traffic' mod='dashgoals'}</th>
						<th>{l s='Conversion Rate' mod='dashgoals'}</th>
						<th>{l s='Average Cart Value' mod='dashgoals'}</th>
						<th>{l s='Sales' mod='dashgoals'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $goals_months as $month}
					<tr>
						<td>
							{$month.label}
						</td>
						<td>
							<input id="dashgoals_traffic_{$month@key}" name="dashgoals_traffic_{$month@key}" class="dashgoals_config_input form-control"
								value="{$month.values.traffic|intval}" />
						</td>
						<td>
							<div class="input-group">
								<input id="dashgoals_conversion_{$month@key}" name="dashgoals_conversion_{$month@key}" class="dashgoals_config_input form-control"
									value="{$month.values.conversion|floatval}" />
								<span class="input-group-addon">%</span>
							</div>
						</td>
						<td>
							<div class="input-group">
								<span class="input-group-addon">{$currency_code|escape}</span>
								<input id="dashgoals_avg_cart_value_{$month@key}" name="dashgoals_avg_cart_value_{$month@key}" class="dashgoals_config_input form-control"
									value="{$month.values.avg_cart_value|intval}" />
							</div>
						</td>
						<td id="dashgoals_sales_{$month@key}" class="dashgoals_sales">
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
			<input class="btn btn-default" name="submitDashGoals" type="submit" value="{l s='Save' mod='dashgoals'}" />
		</form>
	</section>
	<section class="loading">
		<div id="dash_goals_chart1" class="chart with-transitions">
			<svg></svg>
		</div>
	</section>
</section>