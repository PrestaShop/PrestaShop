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


	<table id="module-list" class="table">
		<thead>
			<tr>
				<th width="1%">
					<!-- <input type="checkbox" rel="false" class="noborder" id="checkme"> -->
				</th>
				<th colspan="3">
					<div class="pull-left">
						{include file='controllers/modules/filters.tpl'}
					</div>
					<div class="btn-group pull-right">
						<a class="btn btn-default {if !isset($smarty.get.select)} active{/if}" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}">
							<i class="icon-list"></i>
							{l s='Normal view'} 
						</a>
						<a class="btn btn-default {if isset($smarty.get.select) && $smarty.get.select == 'favorites'} active{/if}" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites">
							<i class="icon-star"></i> 
							{l s='Favorites view'}
						</a>
					</div>
				</th>
			</tr>
		</thead>
{if count($modules)}
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
				<td>
					<img width="57" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" />
				</td>
				<td>
					<div id="anchor{$module->name|ucfirst}">
						<h3 class="module-title">
							{$module->displayName}
							{if isset($module->type) && $module->type == 'addonsMustHave'}
								<span class="label label-info">{l s='Must Have'}</span>
							{elseif isset($module->id) && $module->id gt 0}
								{if isset($module->version_addons) && $module->version_addons}
									<span class="label label-warning">{l s='Need update'}</span>
								{elseif $module->active == 1}
									<span class="label label-info">{l s='Active'}</span>
								{elseif $module->active == 0}
									<span class="label label-warning">{l s='Inactive'}</span>
								{/if}
							{else}
								<span class="label label-warning">{l s='Not installed'}</span>
							{/if}
<!-- 						{if isset($module->id) && $module->id gt 0}
								<span class="label label-success{if isset($module->active) && $module->active eq 0} off{/if}">{l s='Installed'}</span>
							{else}
								<span class="label label-warning">{l s='Not installed'}</span>
							{/if} -->
						</h3>
						<div class="row">
							<p class="module-infos">
								<strong>{$module->categoryName}</strong> - 
								{if isset($module->author) && !empty($module->author)}
									{l s='Developed by'} {$module->author|truncate:20:'...'}
								{/if}
									&nbsp;- {l s='Version'} : {$module->version} 
										{if isset($module->version_addons)}
											({l s='Update'} {$module->version_addons} {l s='Available on PrestaShop Addons'})
										{/if}
							</p>
							<p class="module-description">
								{if isset($module->description) && $module->description ne ''}
									{$module->description}
								{/if}
							</p>
						</div>
						{if isset($module->message) && (!isset($module->type) || ($module->type != 'addonsMustHave' || $module->type !== 'addonsNative'))}<div class="alert alert-success">{$module->message}</div>{/if}
					</div>
				</td>
				<td>
					<div class="btn-group list-action-button">
						{if isset($module->type) && $module->type == 'addonsMustHave'}
							<a class="btn btn-default" href="{$module->addons_buy_url}" target="_blank">
								<i class="icon-shopping-cart"></i> &nbsp;{if isset($module->id_currency) && isset($module->price)}{displayPrice price=$module->price currency=$module->id_currency}{/if}
							</a>
						{else}
							{if isset($module->id) && $module->id gt 0}
								{if isset($module->version_addons) && $module->version_addons}
									<a class="btn btn-warning" href="{$module->options.update_url}">
										<i class="icon-refresh"></i> {l s='Update it!'}
									</a>
								{elseif !isset($module->not_on_disk)}
									{if $module->optionsHtml|count > 0}
										{assign var=option value=$module->optionsHtml[0]}
										{$option}
									{/if}
								{else}
									<a class="btn btn-danger" {if !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{$module->options.uninstall_url}">
										<i class="icon-minus-sign-alt"></i>&nbsp;{l s='Uninstall'}
									</a>
								{/if}
							{else}
								<a class="btn btn-success" href="{$module->options.install_url}">
									<i class="icon-plus-sign-alt"></i>&nbsp;{l s='Install'}
								</a>
							{/if}
							{if !isset($module->not_on_disk)}
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
								<span class="caret">&nbsp;</span>
							</button>

							<ul class="dropdown-menu">
								{foreach $module->optionsHtml key=key item=option}
									{if $key != 0}
										<li>{$option}</li>
									{/if}
								{/foreach}
								{if isset($module->preferences) && isset($module->preferences['favorite']) && $module->preferences['favorite'] == 1}
								<li>
									<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#">
										<i class="icon-star"></i> {l s='Remove from Favorites'}
									</a>
								</li>
								<li>
									<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#" style="display: none;">
										<i class="icon-star"></i> {l s='Mark as Favorite'}
									</a>
								</li>
								{else}
								<li>
									<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#" style="display: none;">
										<i class="icon-star"></i> {l s='Remove from Favorites'}
									</a>
								</li>
								<li>
									<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#">
										<i class="icon-star"></i> {l s='Mark as Favorite'}
									</a>
								</li>
								{/if}
							</ul>
							{else}
								&nbsp;
							{/if}
						{/if}
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<div class="btn-group pull-left">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{l s='bulk actions'}
							 <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li>
							 	<a href="#" onclick="modules_management('install')">
									<i class="icon-plus-sign-alt"></i>&nbsp;
									{l s='Install the selection'}
								</a>
							</li>
							<li>
								<a href="#" onclick="modules_management('uninstall')">
									<i class="icon-minus-sign-alt"></i>&nbsp;
									{l s='Uninstall the selection'}
								</a>
							</li>
						</ul>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
{else}
		<tbody>
			<tr>
				<td colspan="4" class="text-center"><i class="icon-warning-sign"></i> {l s='No modules available in this section.'}</td>
			</tr>
		</tbody>
	</table>
{/if}
