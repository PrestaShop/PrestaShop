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
	<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table" id="">
		<thead>
			<tr class="nodrag nodrop">
				<th class="center">
					<input type="checkbox" rel="false" class="noborder" id="checkme"><br>
				</th>
				<th class="center"></th>
				<th>{l s='Module name'}</th>
				<th></th>
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
				<td><img class="imgm" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}"></td>
				<td>
					<div class="moduleDesc" id="anchor{$module->name|ucfirst}">
						<h3>{$module->displayName}
							{if isset($module->type) && $module->type == 'addonsMustHave'}
								<span class="setup must-have">{l s='Must Have'}</span>
							{else}
								{if isset($module->id) && $module->id gt 0}
									<span class="setup{if isset($module->active) && $module->active eq 0} off{/if}">{l s='Installed'}</span>
								{else}
									<span class="setup non-install">{l s='Not installed'}</span>
								{/if}
							{/if}
						</h3>
						<div class="metadata">
							{if isset($module->author) && !empty($module->author)}
							<dl class="">
								<dt>{l s='Developed by'} :</dt>
								<dd>{$module->author|truncate:20:'...'}</dd>|
							</dl>
							{/if}
							<dl class="">
								<dt>{l s='Version'} :</dt>
								<dd>{$module->version} 
									{if isset($module->version_addons)}({l s='Update'} {$module->version_addons} {l s='Available on PrestaShop Addons'}){/if}
								</dd>|
							</dl>
							<dl class="">
								<dt>{l s='Category'} :</dt>
								<dd>{$module->categoryName}</dd>
							</dl>
						</div>
						<p class="desc">{if isset($module->description) && $module->description ne ''}{l s='Description'} : {$module->description}{else}&nbsp;{/if}</p>
						{if isset($module->message)}<div class="conf">{$module->message}</div>{/if}
						<div class="row-actions-module">
							{if !isset($module->not_on_disk)}
								{$module->optionsHtml}
								{if isset($module->preferences) && $module->preferences['favorite'] == 1}
									<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#">{l s='Remove from Favorites'}</a>
									<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#" style="display: none;">{l s='Mark as Favorite'}</a>
								{else}
									<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#" style="display: none;">{l s='Remove from Favorites'}</a>
									<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#">{l s='Mark as Favorite'}</a>
								{/if}
							{else}
								&nbsp;
							{/if}
						</div>
					</div>
				</td>
				<td>
					<ul id="list-action-button">
						{if isset($module->type) && $module->type == 'addonsMustHave'}
							<li>
								<a href="{$module->addons_buy_url}" target="_blank" class="button updated"><span><img src="../img/admin/cart_addons.png">&nbsp;&nbsp;{if isset($module->id_currency) && isset($module->price)}{displayPrice price=$module->price currency=$module->id_currency}{/if}</span></a>
							</li>
						{else}
							{if $module->id && isset($module->version_addons) && $module->version_addons}
								<li><a href="{$module->options.update_url}" class="button updated"><span>{l s='Update it!'}</span></a></li>
							{/if}
								<li>
									<a {if isset($module->id) && $module->id gt 0 && !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{if isset($module->id) && $module->id gt 0}{$module->options.uninstall_url}{else}{$module->options.install_url}{/if}" class="button installed">
										<span>{if isset($module->id) && $module->id gt 0}{l s='Uninstall'}{else}{l s='Install'}{/if}</span>
									</a>
								</li>
						 {/if}
					</ul>
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
	<div style="margin-top: 12px;color: #585A69;font-size: 16px;"><p align="center">{l s='No modules available in this section.'}</p></div>
{/if}
