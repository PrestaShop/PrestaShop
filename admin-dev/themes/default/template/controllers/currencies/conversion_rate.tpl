
<div class="row">
	<div class="col-lg-12">
		{if Module::isInstalled("cronjobs")}
			{assign var='PS_ACTIVE_CRONJOB_EXCHANGE_RATE' value=Configuration::get('PS_ACTIVE_CRONJOB_EXCHANGE_RATE')}
			<div id="currencyCronjobLiveExchangeRate" class="panel">
				<div class="panel-heading">
					{l s='Live exchange rates'}
					<div class="pull-right checkbox titatoggle unchecked-red checkbox-slider--b-flat">
						<label>
							<input type="checkbox" {(0 != $PS_ACTIVE_CRONJOB_EXCHANGE_RATE)?'checked="checked"':''}><span></span>
						</label>
					</div>
					<div class="clearfix"></div>
				</div>
				<span class="status disabled {(0 == $PS_ACTIVE_CRONJOB_EXCHANGE_RATE)?'':'hide'}">{l s="The exchange rates aren't automaticly updated"}</span>
				<span class="status enabled {(0 != $PS_ACTIVE_CRONJOB_EXCHANGE_RATE)?'':'hide'}">{l s="The exchange rates are automaticly updated"}</span>
			</div>
		{/if}
		<div class="panel">
			<div class="panel-heading">{l s='Update exchange rates'}</div>
			<form action="{$link->getAdminLink('AdminCurrencies')|escape:'html':'UTF-8'}" id="currency_form" method="post">
				<button type="submit" class="btn btn-default col-lg-12 col-xs-4" name="SubmitExchangesRates">{l s="Update"|upper}</button>
			</form>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
