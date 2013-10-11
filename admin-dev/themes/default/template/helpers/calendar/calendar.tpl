<form class='form-inline' id='datepicker-form'>
	<fieldset class='form-date-group' id='date-range'>
		<div class='form-date-heading'>
			Date range
			<div class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown'>
				Custom
				<i class='icon-angle-down'></i>
				<ul class='dropdown-menu'>
					<li>
						<a>test</a>
					</li>
				</ul>
			</div>
		</div>
		<div class='form-date-body form-group'>
			<label>From</label>
			<input class='date-input form-control' id='date-start' placeholder='Start' type='text'>
			<label>to</label>
			<input class='date-input form-control' id='date-end' placeholder='End' type='text'>
		</div>
	</fieldset>
	<fieldset class='form-date-group' disabled='' id='date-compare'>
		<div class='form-date-heading'>
			<input type='checkbox'>
			Compare to
			<div class='btn btn-default btn-xs pull-right dropdown-toggle' data-toggle='dropdown'>
				Custom
				<i class='icon-angle-down'></i>
			</div>
		</div>
		<div class='form-date-body form-group'>
			<label>From</label>
			<input class='date-input form-control' id='date-start' placeholder='Start' type='text'>
			<label>to</label>
			<input class='date-input form-control' id='date-end' placeholder='End' type='text'>
		</div>
	</fieldset>
	<div class='actions'>
		<button class='btn-default' type='button'>
			<i class='icon-remove'></i>
			Cancel
		</button>
		<button class='btn-primary pull-right' type='submit'>
			<i class='icon-ok'></i>
			Apply
		</button>
	</div>
</form>