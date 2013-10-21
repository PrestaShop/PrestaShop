<div id="calendar" class="panel">
	<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="form-inline">
		<div class="row">
			<div class="col-lg-6">
				<div class="btn-group">
					<button type="button" name="submitDateDay" class="btn btn-default submitDateDay" onclick="setDayPeriod()">{$translations.Day}</button>
					<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth" onclick="setMonthPeriod()">{$translations.Month}</button>
					<button type="button" name="submitDateYear" class="btn btn-default submitDateYear" onclick="setYearPeriod()">{$translations.Year}</button>
					<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev" onclick="setPreviousDayPeriod()">{$translations.Day}-1</button>
					<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev" onclick="setPreviousMonthPeriod()">{$translations.Month}-1</button>
					<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev" onclick="setPreviousYearPeriod()">{$translations.Year}-1</button>
				</div>					
			</div>
			<div class="col-lg-6">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-xs-6">
								<div class="input-group">
									<label class="input-group-addon">{if isset($translations.From)}{$translations.From}{else}{l s='From:'}{/if}</label>
									<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="form-control">
								</div>
							</div>
							<div class="col-xs-6">
								<div class="input-group">
									<label class="input-group-addon">{if isset($translations.To)}{$translations.To}{else}{l s='From:'}{/if}</label>
									<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				{$calendar}
			</div>
		</div>
	</form>	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#datepickerFrom').click(function() {
				$('#datepicker').slideDown(200);
				$('#date-start').focus();
			});

			$('#datepickerTo').click(function() {
				$('#datepicker').slideDown(200);
				$('#date-end').focus();
			});
		});
	</script>
</div>