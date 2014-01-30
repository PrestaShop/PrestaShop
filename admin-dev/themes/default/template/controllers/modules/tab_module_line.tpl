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

<tr>
	<td class="fixed-width-sm center">
		<img class="img-thumbnail" alt="{$module->name}" src="{if isset($module->image)}{$module->image}{else}{$modules_uri}/{$module->name}/{$module->logo}{/if}">
	</td>
	<td>
		<div id="anchor{$module->name|ucfirst}">
			<span>
				{$module->displayName|truncate:40:'…'} {$module->version}
				{if isset($module->type) && $module->type == 'addonsPartner'}
					- <a href="#" class="module-badge-partner help-tooltip text-warning" data-title="{l s="This module is available for free thanks to our partner."}"><i class="icon-pushpin"></i> <small>{l s="Partner"}</small></a>
				{/if}
				{*if isset($module->id) && $module->id gt 0 }
					{if $module->active}
						<span class="label label-success">{l s='Enabled'}</span>
					{else}
						<span class="label label-warning">{l s='Disabled'}</span>
					{/if}
				{else}
					{if isset($module->type) && $module->type == 'addonsMustHave'}
						<span class="label label-danger">{l s='Must Have'}</span>
					{else}
						<span class="label label-warning">{l s='Not installed'}</span>
					{/if}
				{/if*}
			</span>
			{if isset($module->description) && $module->description ne ''}
			<p class="text-muted">
				{$module->description|truncate:100:'…'}
			</p>
			{/if}
		</div>
	</td>
	{if isset($module->type) && $module->type == 'addonsMustHave'}
		<td>&nbsp;</td>
		<td align="right">
			<p>
				<a href="{$module->addons_buy_url}" target="_blank" class="button updated">
					<span class="btn btn-default">
						<i class="icon-shopping-cart"></i> &nbsp;&nbsp;{displayPrice price=$module->price currency=$module->id_currency}
					</span>
				</a>
			</p>
		</td>
	{else if !isset($module->not_on_disk)}
		<td>&nbsp;</td>
		<td align="right">
			{if $module->optionsHtml|count > 0}
			<div id="list-action-button" class="btn-group">
				{assign var=option value=$module->optionsHtml[0]}
				{$option}
				{if $module->optionsHtml|count > 1}
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
					<span class="caret">&nbsp;</span>
				</button>
				<ul class="dropdown-menu">
				{foreach $module->optionsHtml key=key item=option}
					{if $key != 0}
						<li>{$option}</li>
					{/if}
				{/foreach}
				</ul>
				{/if}
			</div>
			{/if}
		</td>
	{else}
		<td>&nbsp;</td>
		<td align="right">
			<p>
				<a href="{$module->options.install_url}" class="btn btn-success">
					<i class="icon-plus-sign-alt"></i>
					{l s='Install'}
				</a>
			</p>
		</td>
	{/if}
</tr>