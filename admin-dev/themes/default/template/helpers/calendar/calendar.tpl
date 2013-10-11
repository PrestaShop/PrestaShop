<div id="datepicker" class="datepicker-days">
	<div class="datepickers-container">
		<div class="datepicker1" data-date="{$from_date}" data-date-format="{$date_format}"></div>
		<div class="datepicker2" data-date="{$to_date}" data-date-format="{$date_format}"></div>
	</div>
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
				<input class='date-input form-control' id='date-start' placeholder='Start' type='text'>
				<label>{l s='to'}</label>
				<input class='date-input form-control' id='date-end' placeholder='End' type='text'>
			</div>
		</fieldset>
		<div class='actions'>
			<button class='btn-default' type='button'>
				<i class='icon-remove'></i>
				{l s='Cancel'}
			</button>
			<button class='btn-primary pull-right' type='submit'>
				<i class='icon-ok'></i>
				{l s='Apply'}
			</button>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$('.datepicker1').datepicker();
		$('.datepicker2').datepicker();
		$("#date-start").focus().addClass("input-selected");
	});
</script>