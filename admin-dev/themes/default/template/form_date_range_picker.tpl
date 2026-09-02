{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div id="calendar" class="panel">
	<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="calendar-form form-inline">
		<div class="calendar-form__left">		
			<div class="btn-group">
				<button type="submit" name="submitDateDay" class="btn btn-default submitDateDay">{$translations.Day}</button>
				<button type="submit" name="submitDateMonth" class="btn btn-default submitDateMonth">{$translations.Month}</button>
				<button type="submit" name="submitDateYear" class="btn btn-default submitDateYear">{$translations.Year}</button>
				<button type="submit" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev">{$translations.Day}-1</button>
				<button type="submit" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev">{$translations.Month}-1</button>
				<button type="submit" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev">{$translations.Year}-1</button>
			</div>
		</div>

		<div class="calendar-form__right">
			<div class="input-group">
				<label class="input-group-addon">{if isset($translations.From)}{$translations.From}{else}{l s='From:' d='Admin.Global'}{/if}</label>
				<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="datepicker form-control">
			</div>

			<div class="input-group">
				<label class="input-group-addon">{if isset($translations.To)}{$translations.To}{else}{l s='From:' d='Admin.Global'}{/if}</label>
				<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="datepicker form-control">
			</div>

			<button type="submit" name="submitDatePicker" id="submitDatePicker" class="btn btn-default"><i class="icon-save"></i> {l s='Save' d='Admin.Actions'}</button>
		</div>

	</form>
</div>
<script type="text/javascript">
	$(function() {
		if ($("form#calendar_form .datepicker").length > 0)
			$("form#calendar_form .datepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
	});
</script>
