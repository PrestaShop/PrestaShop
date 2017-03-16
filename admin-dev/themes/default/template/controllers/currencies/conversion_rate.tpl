{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

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
				<span class="status disabled {(0 == $PS_ACTIVE_CRONJOB_EXCHANGE_RATE)?'':'hide'}">{l s="The exchange rates are not automatically updated"}</span>
				<span class="status enabled {(0 != $PS_ACTIVE_CRONJOB_EXCHANGE_RATE)?'':'hide'}">{l s="The exchange rates are automatically updated"}</span>
			</div>
		{/if}
		<div class="panel">
			<div class="panel-heading">{l s='Update exchange rates'}</div>
			<form action="{$link->getAdminLink('AdminCurrencies')|escape:'html':'UTF-8'}" id="currency_form" method="post">
				<button type="submit" class="btn btn-default col-lg-12 col-xs-4" name="SubmitExchangesRates">{l s="Update"}</button>
			</form>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
