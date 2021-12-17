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

<div id="referrersContainer">
	<div id="calendar">
			<form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}{if $action && $table}&amp;{$action}{$table}{/if}{if $identifier && $id}&amp;{$identifier}={$id|escape:'html':'UTF-8'}{/if}" method="post" id="calendar_form" name="calendar_form" class="form-horizontal">
				<div class="panel">
					<input type="submit" name="submitDateDay" class="btn btn-default submitDateDay" value="{$translations.Day}" />
					<input type="submit" name="submitDateMonth" class="btn btn-default submitDateMonth" value="{$translations.Month}" />
					<input type="submit" name="submitDateYear" class="btn btn-default submitDateYear" value="{$translations.Year}" />
					<input type="submit" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev" value="{$translations.Day}-1" />
					<input type="submit" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev" value="{$translations.Month}-1" />
					<input type="submit" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev" value="{$translations.Year}-1" />
					<p>
						<span>{if isset($translations.From)}{$translations.From}{else}{l s='From:' d='Admin.Global'}{/if}</span>
            <input type="text" class="datepicker" data-target="#datepickerFrom"/>
            <input type="hidden" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" />
					</p>
					<p>
						<span>{if isset($translations.To)}{$translations.To}{else}<span>{l s='To:' d='Admin.Global'}</span>{/if}</span>
            <input type="text" class="datepicker" data-target="#datepickerTo"/>
						<input type="hidden" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" />
					</p>
					<button type="submit" name="submitDatePicker" id="submitDatePicker" class="btn btn-default">
						<i class="icon-save"></i> {if isset($translations.Save)}{$translations.Save}{else}{l s='Save' d='Admin.Actions'}{/if}
					</button>
				</div>
			</form>

			<script type="text/javascript">
				$(document).ready(function() {
          if ($("form#calendar_form .datepicker").length > 0) {
            const dateFormat = $.datepicker.regional[window.full_language_code]
                ? $.datepicker.regional[window.full_language_code].dateFormat
                : 'yy-mm-dd';
            $("form#calendar_form .datepicker").each(function() {
              var altField = $(this).data('target');
              $(this).datepicker({
                altField: altField,
                altFormat: 'yy-mm-dd',
                prevText: '',
                nextText: '',
                dateFormat: dateFormat,
              });
              $(this).datepicker(
                'setDate',
                new Date($(altField).val())
              );
            });
          }
        });
			</script>
	</div>
