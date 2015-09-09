<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				{l s='Status'}
				<div id="currencyStatus" class="pull-right checkbox titatoggle unchecked-red checkbox-slider--b-flat">
					<label>
						<input type="checkbox" {(1 == $status)?'checked="checked"':''}><span></span>
					</label>
				</div>
				<div class="clearfix"></div>
			</div>
			<span class="status disabled {(0 == $status)?'':'hide'}">{l s="This currency is disabled"}</span>
			<span class="status enabled {(1 == $status)?'':'hide'}">{l s="This currency is enabled"}</span>
		</div>
	</div>
</div>
