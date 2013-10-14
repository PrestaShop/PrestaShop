<div id="datepicker" class="datepicker-days">
	<div class="row">
		<div class="col-lg-8">
			<div class="datepickers-container">
				{if $is_rtl}
				<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}"></div>
				<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>				
				{else}
				<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>
				<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}"></div>
				{/if}
			</div>
		</div>
		<div class="col-lg-4">
			<form class='form-inline' id='datepicker-form'>
				<fieldset class='form-date-group' id='date-range'>
					<div class='form-date-heading'>
						{l s='Date range'}
						{if isset($actions) && $actions|count > 0}
							{if $actions|count > 1}
							<div class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown'>
								{l s='Custom'}
								<i class='icon-angle-down'></i>
								<ul class='dropdown-menu'>
									{foreach from=$actions item=action}
									<li><a{if isset($action.href)} href="{$action.href}"{/if}{if isset($action.class)} class="{$action.class}"{/if}>{if isset($action.icon)}<i class="{$action.icon}"> {/if}{$action.label}</a></li>
									{/foreach}
								</ul>
							</div>
							{else}
							<a{if isset($actions[0].href)} href="{$actions[0].href}"{/if} class="btn btn-default btn-xs pull-right{if isset($actions[0].class)} {$actions[0].class}{/if}">{if isset($actions[0].icon)}<i class="{$actions[0].icon}"></i> {/if}{$actions[0].label}</a>
							{/if}
						{/if}
					</div>
					<div class='form-date-body form-group'>
						<label>{l s='From'}</label>
						<input class='date-input form-control' id='date-start' placeholder='Start' type='text' name="date_from" value="{$date_from}" />
						<label>{l s='to'}</label>
						<input class='date-input form-control' id='date-end' placeholder='End' type='text' name="date_to" value="{$date_to}" />
					</div>
					<hr/>
					<div class='form-date-heading'>
						<div>{l s='Compare to'}</div>
					</div>
					<div class="form-date-body form-group">
						<label>{l s='From'}</label>
						<input id="compare-date-start" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="{$compare_date_from}" />
						<label>{l s='to'}</label>
						<input id="compare-date-end" class="date-input form-control" type="text" placeholder="End" name="compare_date_to" value="{$compare_date_to}" />
					</div>
					<hr/>
					<div class='form-group'>
						<button class='btn btn-default' type='button'>
							<i class='icon-remove text-danger'></i>
							{l s='Cancel'}
						</button>

						<button class='btn btn-default' type='submit' name="submitDateRange">
							<i class='icon-ok text-success'></i>
							{l s='Apply'}
						</button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<hr/>
</div>
<script type="text/javascript">
	$(function() {
		var translated_dates = {
			days: ["{l s='Sunday'}", "{l s='Monday'}", "{l s='Tuesday'}", "{l s='Wednesday'}", "{l s='Thursday'}", "{l s='Friday'}", "{l s='Saturday'}", "{l s='Sunday'}"],
			daysShort: ["{l s='Sun'}", "{l s='Mon'}", "{l s='Tue'}", "{l s='Wed'}", "{l s='Thu'}", "{l s='Fri'}", "{l s='Sat'}", "{l s='Sun'}"],
			daysMin: ["{l s='Su'}", "{l s='Mo'}", "{l s='Tu'}", "{l s='We'}", "{l s='Th'}", "{l s='Fr'}", "{l s='Sa'}", "{l s='Su'}"],
			months: ["{l s='January'}", "{l s='February'}", "{l s='March'}", "{l s='April'}", "{l s='May'}", "{l s='June'}", "{l s='July'}", "{l s='August'}", "{l s='September'}", "{l s='October'}", "{l s='November'}", "{l s='December'}"],
			monthsShort: ["{l s='Jan'}", "{l s='Feb'}", "{l s='Mar'}", "{l s='Apr'}", "{l s='May'}", "{l s='Jun'}", "{l s='Jul'}", "{l s='Aug'}", "{l s='Sep'}", "{l s='Oct'}", "{l s='Nov'}", "{l s='Dec'}"]
		}
	{literal}
		$('.datepicker1').datepicker({"dates": translated_dates, "weekStart": 1});
		$('.datepicker2').datepicker({"dates": translated_dates, "weekStart": 1});
	{/literal}
		$("#date-start").focus().addClass("input-selected");
	});
</script>