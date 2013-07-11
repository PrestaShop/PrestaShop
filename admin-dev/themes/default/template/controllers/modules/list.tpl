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

{if count($modules)}
	<table cellspacing="0" cellpadding="0" class="table table-striped table-hover">
		<thead>
			<tr class="nodrag nodrop">
				<th class="center">
					<input type="checkbox" rel="false" class="noborder" id="checkme">
				</th>
				<th>{l s='Logo'}</th>
				<th class="center">{l s='Module name'}</th>
				<th>&nbsp;</th>
			</tr>			
		<tbody>
		{foreach from=$modules item=module}
			<tr>
				<td>
					{if (isset($module->id) && $module->id > 0) || !isset($module->type) || $module->type != 'addonsMustHave'}
					<input type="checkbox" name="modules" value="{$module->name}"
						{if !isset($module->confirmUninstall) OR empty($module->confirmUninstall)}rel="false"{else}rel="{$module->confirmUninstall|addslashes}"{/if}
						class="noborder">
					{/if}
				</td>
				<td class="center">
					<img width="57" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" />
				</td>
				<td>
					<div id="anchor{$module->name|ucfirst}">
						<h4>{$module->displayName}
							{if isset($module->type) && $module->type == 'addonsMustHave'}
								<span class="label label-danger">{l s='Must Have'}</span>
							{else}
								{if isset($module->id) && $module->id gt 0}
									<span class="label label-success{if isset($module->active) && $module->active eq 0} off{/if}">{l s='Installed'}</span>
								{else}
									<span class="label label-warning">{l s='Not installed'}</span>
								{/if}
							{/if}
						</h4>
						<div class="row-fluid">
							<dl class="dl-horizontal">
								{if isset($module->author) && !empty($module->author)}
									<dt>{l s='Developed by'} :</dt>
									<dd>{$module->author|truncate:20:'...'}</dd>
								{/if}
									<dt>{l s='Version'} :</dt>
									<dd>{$module->version} 
										{if isset($module->version_addons)}({l s='Update'} {$module->version_addons} {l s='Available on PrestaShop Addons'}){/if}
									</dd>
									<dt>{l s='Category'} :</dt>
									<dd>{$module->categoryName}</dd>
								{if isset($module->description) && $module->description ne ''}
									<dt>{l s='Description'} :</dt>
									<dd>{$module->description}</dd>
								{else}
									&nbsp;
								{/if}
							</dl>
						</div>
						{if isset($module->message)}<div class="alert">{$module->message}</div>{/if}
						<div class="row-fluid">
							{if !isset($module->not_on_disk)}
								{$module->optionsHtml}
								{if isset($module->preferences) && $module->preferences['favorite'] == 1}
									<a class="btn btn-default btn-small action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#">
										<i class="icon-star"></i> {l s='Remove from Favorites'}
									</a>
									<a class="btn btn-default btn-small action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#" style="display: none;">
										<i class="icon-star"></i> {l s='Mark as Favorite'}
									</a>
								{else}
									<a class="btn btn-default btn-small action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#" style="display: none;">
										<i class="icon-star"></i> {l s='Remove from Favorites'}
									</a>
									<a class="btn btn-default btn-small action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#">
										<i class="icon-star"></i> {l s='Mark as Favorite'}
									</a>
								{/if}
							{else}
								&nbsp;
							{/if}
						</div>
					</div>
				</td>
				<td class="center">
					<div id="list-action-button" class="row-light">
						{if isset($module->type) && $module->type == 'addonsMustHave'}
							<p>
								<a href="{$module->addons_buy_url}" target="_blank">
									<span class="btn btn-default">
										<i class="icon-shopping-cart"></i> &nbsp;&nbsp;{if isset($module->id_currency) && isset($module->price)}{displayPrice price=$module->price currency=$module->id_currency}{/if}
									</span>
								</a>
							</p>
						{else}
							{if $module->id && isset($module->version_addons) && $module->version_addons}
								<p>
									<a href="{$module->options.update_url}">
										<span class="btn btn-default btn-warning">
											<i class="icon-refresh"></i> {l s='Update it!'}
										</span>
									</a>
								</p>
							{/if}
							<p>
								<a {if isset($module->id) && $module->id gt 0 && !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{if isset($module->id) && $module->id gt 0}{$module->options.uninstall_url}{else}{$module->options.install_url}{/if}" class="button installed">
									{if isset($module->id) && $module->id gt 0}
										<p>
											<span class="btn btn-default btn-danger">
												<i class="icon-minus-sign-alt"></i> 
												{l s='Uninstall'}
											</span>
										</p>
									{else}
										<p>
											<span class="btn btn-default btn-success">
												<i class="icon-plus-sign-alt"></i> 
												{l s='Install'}
											</span>
										</p>
									{/if}
								</a>
							</p>
						 {/if}
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div style="margin-top: 12px;">
		<input type="button" class="button big" value="{l s='Install the selection'}" onclick="modules_management('install')"/>
		<input type="button" class="button big" value="{l s='Uninstall the selection'}" onclick="modules_management('uninstall')" />
	</div>
{else}
	<p class="help-block">{l s='No modules available in this section.'}</p>
{/if}
