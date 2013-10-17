<div id="datepicker" class="datepicker-days">
	<div class="row">	
		{if $is_rtl}
		<div class="col-sm-6 col-lg-4">
			<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}"></div>
		</div>
		<div class="col-sm-6 col-lg-4">
			<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>
		</div>
		{else}
		<div class="col-sm-6 col-lg-4">
			<div class="datepicker1" data-date="{$date_from}" data-date-format="{$date_format}"></div>
		</div>
		<div class="col-sm-6 col-lg-4">
			<div class="datepicker2" data-date="{$date_to}" data-date-format="{$date_format}"></div>
		</div>
		{/if}
		<div class="col-lg-4 clearfix">
			<div id='datepicker-form' class='form-inline'>

				<div id='date-range' class='form-date-group'>
					<div  class='form-date-heading'>
						<span class="title">{l s='Date range'}</span>
						{if isset($actions) && $actions|count > 0}
							{if $actions|count > 1}
							<button class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown' type="button">
								{l s='Custom'}
								<i class='icon-angle-down'></i>
							</button>
							<ul class='dropdown-menu'>
								{foreach from=$actions item=action}
								<li><a{if isset($action.href)} href="{$action.href}"{/if}{if isset($action.class)} class="{$action.class}"{/if}>{if isset($action.icon)}<i class="{$action.icon}"></i> {/if}{$action.label}</a></li>
								{/foreach}
							</ul>
							{else}
							<a{if isset($actions[0].href)} href="{$actions[0].href}"{/if} class="btn btn-default btn-xs pull-right{if isset($actions[0].class)} {$actions[0].class}{/if}">{if isset($actions[0].icon)}<i class="{$actions[0].icon}"></i> {/if}{$actions[0].label}</a>
							{/if}
						{/if}
					</div>
					<div class='form-date-body'>
						<label>{l s='From'}</label>
						<input class='date-input form-control' id='date-start' placeholder='Start' type='text' name="date_from" value="{$date_from}" />
						<label>{l s='to'}</label>
						<input class='date-input form-control' id='date-end' placeholder='End' type='text' name="date_to" value="{$date_to}" />
					</div>
				</div>

				<div id="date-compare" class='form-date-group'>
					<div class='form-date-heading'>
						<span class="checkbox-title">
							<label >
								<input type='checkbox' id="datepicker-compare">
								{l s='Compare to'}
							</label>
						</span>
						<button class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown' type="button">
							{l s='Select'}
							<i class='icon-angle-down'></i>							
						</button>
						<ul class='dropdown-menu'>
							<li>
								<a href="javascript:void(0);" onclick="$('#date-start-compare').focus();"><i class="icon-cogs"></i> {l s='Custom'}</a>
							</li>
							<li>
								<a href="javascript:void(0);" onclick="setPreviousPeriod()"><i class="icon-cogs"></i> {l s='Previous period'}</a>
							</li>
							<li>
								<a href="javascript:void(0);" onclick="setPreviousYear()"><i class="icon-cogs"></i> {l s='Previous Year'}</a>
							</li>
						</ul>
					</div>
					<div class="form-date-body" id="form-date-body-compare" style="display: none;">
						<label>{l s='From'}</label>
						<input id="date-start-compare" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="{$compare_date_from}" />
						<label>{l s='to'}</label>
						<input id="date-end-compare" class="date-input form-control" type="text" placeholder="End" name="compare_date_to" value="{$compare_date_to}" />
					</div>
				</div>
				<div class='form-date-actions'>
					<button class='btn btn-default' type='button' id="datepicker-cancel">
						<i class='icon-remove text-danger'></i>
						{l s='Cancel'}
					</button>
					<button class='btn btn-default pull-right' type='submit' name="submitDateRange">
						<i class='icon-ok text-success'></i>
						{l s='Apply'}
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	{literal}
	function parseFormat(format){
		var separator = format.match(/[.\/\-\s].*?/),
			parts = format.split(/\W+/);
		if (!separator || !parts || parts.length === 0){
			throw new Error("Invalid date format.");
		}
		return {separator: separator, parts: parts};
	}

	function parseDate(date, format) {
		var parts = date.split(format.separator),
			date = new Date(),
			val;
		date.setHours(0);
		date.setMinutes(0);
		date.setSeconds(0);
		date.setMilliseconds(0);
		if (parts.length === format.parts.length) {
			var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				val = parseInt(parts[i], 10)||1;
				switch(format.parts[i]) {
					case 'dd':
					case 'd':
						day = val;
						date.setDate(val);
						break;
					case 'mm':
					case 'm':
						month = val - 1;
						date.setMonth(val - 1);
						break;
					case 'yy':
					case 'y':
						year = 2000 + val;
						date.setFullYear(2000 + val);
						break;
					case 'yyyy':
					case 'Y':
						year = val;
						date.setFullYear(val);
						break;
				}
			}
			date = new Date(year, month, day, 0 ,0 ,0);
		}
		return date;
	}

	function formatDate(date, format){
		var val = {
			d: date.getDate(),
			m: date.getMonth() + 1,
			yy: date.getFullYear().toString().substring(2),
			y: date.getFullYear().toString().substring(2),
			yyyy: date.getFullYear(),
			Y: date.getFullYear()
		};
		val.d = (val.d < 10 ? '0' : '') + val.d;
		val.m = (val.m < 10 ? '0' : '') + val.m;
		var date = [];
		for (var i=0, cnt = format.parts.length; i < cnt; i++) {
			date.push(val[format.parts[i]]);
		}
		return date.join(format.separator);
	}
	{/literal}

	function setPreviousPeriod() {
		startDate = parseDate($("#date-start").val(), parseFormat('{$date_format}'));
		endDate = parseDate($("#date-end").val(), parseFormat('{$date_format}'));
		diff = endDate.getTime() - startDate.getTime();
		startDateCompare = startDate.getTime()-diff;
		$("#date-end-compare").val($("#date-start").val());
		$("#date-start-compare").val(formatDate(new Date(startDateCompare), parseFormat('{$date_format}')));
	}

	function setPreviousYear() {
		startDate = parseDate($("#date-start").val(), parseFormat('{$date_format}'));
		startDate = startDate.setFullYear(startDate.getFullYear() - 1);
		endDate = parseDate($("#date-end").val(), parseFormat('{$date_format}'));
		endDate = endDate.setFullYear(endDate.getFullYear() - 1);
		$("#date-start-compare").val(formatDate(new Date(startDate), parseFormat('{$date_format}')));
		$("#date-end-compare").val(formatDate(new Date(endDate), parseFormat('{$date_format}')));
	}

	$(function() {
		var translated_dates = {
			days: ["{l s='Sunday'}", "{l s='Monday'}", "{l s='Tuesday'}", "{l s='Wednesday'}", "{l s='Thursday'}", "{l s='Friday'}", "{l s='Saturday'}", "{l s='Sunday'}"],
			daysShort: ["{l s='Sun'}", "{l s='Mon'}", "{l s='Tue'}", "{l s='Wed'}", "{l s='Thu'}", "{l s='Fri'}", "{l s='Sat'}", "{l s='Sun'}"],
			daysMin: ["{l s='Su'}", "{l s='Mo'}", "{l s='Tu'}", "{l s='We'}", "{l s='Th'}", "{l s='Fr'}", "{l s='Sa'}", "{l s='Su'}"],
			months: ["{l s='January'}", "{l s='February'}", "{l s='March'}", "{l s='April'}", "{l s='May'}", "{l s='June'}", "{l s='July'}", "{l s='August'}", "{l s='September'}", "{l s='October'}", "{l s='November'}", "{l s='December'}"],
			monthsShort: ["{l s='Jan'}", "{l s='Feb'}", "{l s='Mar'}", "{l s='Apr'}", "{l s='May'}", "{l s='Jun'}", "{l s='Jul'}", "{l s='Aug'}", "{l s='Sep'}", "{l s='Oct'}", "{l s='Nov'}", "{l s='Dec'}"]
		};

	{literal}
		var datepickerStart = $('.datepicker1').datepicker({
			"dates": translated_dates,
			"weekStart": 1,
			"start": $("#date-start").val(),
			"end": $("#date-end").val()
		}).on('changeDate', function(ev){
			if (ev.date.valueOf() >= datepickerEnd.date.valueOf()){
				datepickerEnd.setValue(ev.date.setMonth(ev.date.getMonth()+1));
			}
		}).data('datepicker');

		var datepickerEnd = $('.datepicker2').datepicker({
			"dates": translated_dates,
			"weekStart": 1,
			"start": $("#date-start").val(),
			"end": $("#date-end").val()
		}).on('changeDate', function(ev){
			if (ev.date.valueOf() <= datepickerStart.date.valueOf()){
				datepickerStart.setValue(ev.date.setMonth(ev.date.getMonth()-1));
			}
		}).data('datepicker');

		$("#date-start").focus(function() {
			datepickerStart.setCompare(false);
			datepickerEnd.setCompare(false);
			$(".date-input").removeClass("input-selected");
			$(this).addClass("input-selected");
		});

		$("#date-end").focus(function() {
			datepickerStart.setCompare(false);
			datepickerEnd.setCompare(false);
			$(".date-input").removeClass("input-selected");
			$(this).addClass("input-selected");
		});

		$("#date-start-compare").focus(function() {
			datepickerStart.setCompare(true);
			datepickerEnd.setCompare(true);
			$(".date-input").removeClass("input-selected");
			$(this).addClass("input-selected");
		});

		$("#date-end-compare").focus(function() {
			datepickerStart.setCompare(true);
			datepickerEnd.setCompare(true);
			$(".date-input").removeClass("input-selected");
			$(this).addClass("input-selected");
		});
		
		$('#datepicker-cancel').click(function() {
			$('#datepicker').slideUp(200);
		});

		$('#datepicker').show(function() {
			$('#date-start').focus();
		});

		$('#datepicker-compare').click(function() {
			if ($(this).attr("checked")) {
				datepickerStart.setStartCompare($("#date-start-compare").val());
				datepickerStart.setEndCompare($("#date-end-compare").val());
				datepickerEnd.setStartCompare($("#date-start-compare").val());
				datepickerEnd.setEndCompare($("#date-end-compare").val());
				$('#form-date-body-compare').show();
				$('#date-start-compare').focus();
			} else {
				datepickerStart.setStartCompare(null);
				datepickerStart.setEndCompare(null);
				datepickerEnd.setStartCompare(null);
				datepickerEnd.setEndCompare(null);
				$('#form-date-body-compare').hide();
				$('#date-start').focus();
			}
		})

		{/literal}
	});
</script>