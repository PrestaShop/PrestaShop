{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !$module_active}
	{if empty($errors)}
		<div class="row">
			<div class="col-lg-3">&nbsp;</div>
			<div id="not-active" class="col-lg-6">
				<div class="center-text"><span id='not-active-alert'>{l s='Woops! The module is not active!' mod='translatools2'}</span></div>
				<br>
				<p>{l s='Don\'t panic though, you are just one click away.' mod='translatools2'} {l s='We just need your confirmation before activating the module, because it may cause harm on a production server.' mod='translatools2'}</p>
				<p>{l s='Activating this module will add a new, special language called Aragonese to your shop. It does not contain any Aragonese texts, just strange looking data that Live Translation needs to do its magic.' mod='translatools2'}</p>
				<p>{l s='Your customers should never see this language, that\'s why we want to make sure you are not activating the module on your real shop, but only on a testing version or on a copy of your real shop.' mod='translatools2'}</p>
				<p>{l s='If all of the above made sense to you, click the activate button below.' mod='translatools2'}</p>
				<br>
				<form method="POST" action="{$ctrl_url}&amp;action=activate">
					<div class="row">
						<div class="col-lg-2">&nbsp;</div>
						<div class="col-lg-8">
							<button id='activate' data-confirm type="submit" class="btn btn-default btn-block">{l s='Activate!' mod='translatools'}</button>
						</div>
						<div class="col-lg-2">&nbsp;</div>
					</div>
				</form>
			</div>
			<div class="col-lg-3">&nbsp;</div>
		</div>
	{/if}
{else}
	<div class="panel">
		<div class="panel-heading">{l s='Configuration' mod='translatools2'}</div>
		<div class="panel-body">
			<div class="form-horizontal">
				<label class="control-label col-lg-3">
					{l s='Deactivate'}
				</label>
				<div class="col-lg-9">
					<form method="POST" action="{$ctrl_url}&amp;action=deactivate">
						<button class='btn btn-default' type="submit">{l s='Deactivate Translatools2' mod='translatools2'}</button>
					</form>
				</div>
			</form>
		</div>
	</div>
{/if}


<script>
	$(document).ready(function(){
		$(document).on('click', '[data-confirm]', function(event) {
			var msg = $(event.target).attr('data-confirm');
			if (!msg)
			{
				msg = "{l s='Really proceed with this action?' mod='translatools2' js=1}";
			}
			if (!confirm(msg)) {
				event.preventDefault();
			}
		});
	});
</script>
