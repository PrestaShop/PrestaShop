{**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<script>
	var dashboard_ajax_url = '{$link->getAdminLink('AdminDashboard')}';
	var adminstats_ajax_url = '{$link->getAdminLink('AdminStats')}';
	var no_results_translation = '{l s='No result' js=1}';
	var dashboard_use_push = '{$dashboard_use_push|intval}';
	var read_more = '{l s='Read more' js=1}';
</script>

<div id="dashboard">
	<div class="row">
		<div class="col-lg-12">
{if $warning}
			<div class="alert alert-warning">{$warning}</div>
{/if}
			<div id="calendar" class="panel">
				<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="form-inline">
					<div class="btn-group">
						<button type="button" name="submitDateDay" class="btn btn-default submitDateDay{if isset($preselect_date_range) && $preselect_date_range == 'day'} active{/if}">
							{l s='Day' d='Admin.Global'}
						</button>
						<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth{if (!isset($preselect_date_range) || !$preselect_date_range) || (isset($preselect_date_range) && $preselect_date_range == 'month')} active{/if}">
							{l s='Month' d='Admin.Global'}
						</button>
						<button type="button" name="submitDateYear" class="btn btn-default submitDateYear{if isset($preselect_date_range) && $preselect_date_range == 'year'} active{/if}">
							{l s='Year' d='Admin.Global'}
						</button>
						<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-day'} active{/if}">
							{l s='Day' d='Admin.Global'}-1
						</button>
						<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-month'} active{/if}">
							{l s='Month' d='Admin.Global'}-1
						</button>
						<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-year'} active{/if}">
							{l s='Year' d='Admin.Global'}-1
						</button>
						<!--
						<button type="submit" name="submitDateRealTime" class="hide btn btn-default submitDateRealTime {if $dashboard_use_push}active{/if}" value="{!$dashboard_use_push|intval}">
							{l s='Real Time'}
						</button> -->
					</div>
					<input type="hidden" name="datepickerFrom" id="datepickerFrom" value="{$date_from|escape}" class="form-control">
					<input type="hidden" name="datepickerTo" id="datepickerTo" value="{$date_to|escape}" class="form-control">
					<input type="hidden" name="preselectDateRange" id="preselectDateRange" value="{if isset($preselect_date_range)}{$preselect_date_range|escape:'html'}{/if}" class="form-control">
					<div class="form-group pull-right">
						<button id="datepickerExpand" class="btn btn-default" type="button">
							<i class="icon-calendar-empty"></i>
							<span class="hidden-xs">
								{l s='From' d='Admin.Global'}
								<strong class="text-info" id="datepicker-from-info">{$date_from|escape}</strong>
								{l s='To' d='Admin.Global'}
								<strong class="text-info" id="datepicker-to-info">{$date_to|escape}</strong>
								<strong class="text-info" id="datepicker-diff-info"></strong>
							</span>
							<i class="icon-caret-down"></i>
						</button>
					</div>
					{$calendar}
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4 col-lg-3" id="hookDashboardZoneOne">
			{$hookDashboardZoneOne}
		</div>
		<div class="col-md-8 col-lg-7" id="hookDashboardZoneTwo">
			{$hookDashboardZoneTwo}
			<div id="dashaddons" class="row-margin-bottom">
				<a href="https://addons.prestashop.com/en/209-dashboards?utm_source=back-office&amp;utm_medium=dashboard&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="_blank">
					<i class="icon-plus"></i> {l s='Add more dashboard modules' d='Admin.Dashboard.Feature'}
				</a>
			</div>
		</div>
		<div class="col-md-12 col-lg-2">
			<section class="dash_news panel">
				<h3><i class="icon-rss"></i> {l s='PrestaShop News' d='Admin.Dashboard.Feature'}</h3>
				<div class="dash_news_content"></div>
				<div class="text-center"><h4><a href="http://www.prestashop.com/blog/" onclick="return !window.open(this.href);">{l s='Find more news' d='Admin.Dashboard.Feature'}</a></h4></div>
			</section>
			<section id="dash_version" class="visible-lg">
				<iframe style="overflow:hidden;border:none" src="{$new_version_url|escape:'html':'UTF-8'}" ></iframe>
			</section>
			<section class="dash_links panel">
				<h3><i class="icon-link"></i> {l s="We stay by your side!" d='Admin.Dashboard.Feature'}</h3>
					<dl>
						<dt><a href="{$help_center_link}" class="_blank">{l s="Help Center" d='Admin.Dashboard.Feature'}</a></dt>
						<dd>{l s="Documentation, support, experts, training... PrestaShop and all of its community are here to guide you" d='Admin.Dashboard.Feature'}</dd>
					</dl>
					<dl>
						<dt><a href="https://addons.prestashop.com?utm_source=back-office&amp;utm_medium=links&amp;utm_campaign=addons-{$lang_iso}&amp;utm_content=download17" class="_blank">{l s="PrestaShop Marketplace" d='Admin.Dashboard.Feature'}</a></dt>
						<dd>{l s="Traffic, conversion rate, customer loyalty... Increase your sales with all of the PrestaShop modules and themes" d='Admin.Dashboard.Feature'}</dd>
					</dl>
			</section>
		</div>
	</div>
</div>
