{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

</div>

<div id="settings_referrers" class="row">
	<div class="col-lg-3">
		<form action="{$current}&token={$token}" method="post" id="settings_form" name="settings_form" class="form-horizontal">
			<div class="panel">
				<h3>
					<i class="icon-cog"></i> {l s='Settings'}
				</h3>
				<div class="row">
					<label class="control-label col-lg-5">{l s='Save direct traffic?'}</label>
					<div class="col-lg-7">
						<div class="row">
							<div class="input-group col-lg-2">
								<span class="switch prestashop-switch">
									<input type="radio" name="tracking_dt" id="tracking_dt_on" value="1" {if $tracking_dt}checked="checked"{/if} />
									<label class="t radio" for="tracking_dt_on">
										<i class="icon-check-sign color_success"></i> {l s='Yes'}
									</label>
									<input type="radio" name="tracking_dt" id="tracking_dt_off" value="0" {if !$tracking_dt}checked="checked"{/if}  />
									<label class="t radio" for="tracking_dt_off"> 
										<i class="icon-ban-circle color_danger"></i> {l s='No'}
									</label>
									<span class="slide-button btn btn-default"></span>
								</span>
							</div>
						</div>
					</div>
				</div>
					
				<p>{l s='Direct traffic can be quite resource-intensive. You should consider enabling it only if you have a strong need for it.'}</p>
				<button type="submit" class="btn btn-default pull-right" name="submitSettings" id="submitSettings">
					<i class="icon-save"></i> {l s='Save'}
				</button>
			</div>
		</form>
	</div>
	<div class="col-lg-6">
		<form action="{$current}&token={$token}" method="post" id="refresh_index_form" name="refresh_index_form" class="form-horizontal">
			<div class="panel">
				<h3>
					<i class="icon-fullscreen"></i> {l s='Indexation'}
				</h3>
				<p>{l s='There is a huge quantity of data, so each connection corresponding to a referrer is indexed. You can also refresh this index by clicking the button above. This process may take awhile, and it\'s only needed if you modified or added a referrer, or if you want changes to be retroactive.'}</p>
				<button type="submit" class="btn btn-default pull-right" name="submitRefreshIndex" id="submitRefreshIndex">
					<i class="icon-refresh"></i> {l s='Refresh index'}
				</button>
			</div>
		</form>
	</div>
	<div class="col-lg-6">
		<form action="{$current}&token={$token}" method="post" id="refresh_cache_form" name="refresh_cache_form" class="form-horizontal">
			<div class="panel">
				<h3>
					<i class="icon-briefcase"></i> {l s='Cache'}
				</h3>
				<p>{l s='In order to sort and filter your data, it\'s cached. You can refresh the cache by clicking on the button above.'}</p>
				<button type="submit" class="btn btn-default pull-right" name="submitRefreshCache" id="submitRefreshCache">
					<i class="icon-refresh"></i> {l s='Refresh cache'}
				</button>
			</div>
		</form>
	</div>
</div>


	