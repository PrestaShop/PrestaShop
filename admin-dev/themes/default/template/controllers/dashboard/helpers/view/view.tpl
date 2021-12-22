{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<script>
	var dashboard_ajax_url = '{$link->getAdminLink('AdminDashboard')}';
	var adminstats_ajax_url = '{$link->getAdminLink('AdminStats')}';
	var no_results_translation = '{l s='No result' js=1}';
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
		<div class="col-md-8 col-lg-{if $hookDashboardZoneThree}7{else}9{/if}" id="hookDashboardZoneTwo">
			{$hookDashboardZoneTwo}
		</div>
    {if $hookDashboardZoneThree}
      <div class="col-md-12 col-lg-2" id="hookDashboardZoneThree">
          {$hookDashboardZoneThree}
      </div>
    {/if}
	</div>
</div>
