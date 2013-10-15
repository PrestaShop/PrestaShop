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
					<div class='form-date-body'>
						<label>{l s='From'}</label>
						<input class='date-input form-control' id='date-start' placeholder='Start' type='text' name="date_from" value="{$date_from}" />
						<label>{l s='to'}</label>
						<input class='date-input form-control' id='date-end' placeholder='End' type='text' name="date_to" value="{$date_to}" />
					</div>
				</div>
				<div id="date-compare" class='form-date-group'>
					<div class='form-date-heading clearfix'>
						<span class="checkbox-title">
							<label >
								<input type='checkbox'>
								{l s='Compare to'}
							</label>
						</span>
						<button class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown'>
							Custom
							<i class='icon-angle-down'></i>
						</button>

					</div>
					<div class="form-date-body">
						<label>{l s='From'}</label>
						<input id="compare-date-start" class="date-input form-control" type="text" placeholder="Start" name="compare_date_from" value="{$compare_date_from}" />
						<label>{l s='to'}</label>
						<input id="compare-date-end" class="date-input form-control" type="text" placeholder="End" name="compare_date_to" value="{$compare_date_to}" />
					</div>
				</div>
				<div class='form-date-actions'>
					<button class='btn btn-default' type='button'>
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
	$(function() {
		$('.datepicker1').datepicker();
		$('.datepicker2').datepicker();
		$("#date-start").focus().addClass("input-selected");
	});
</script>