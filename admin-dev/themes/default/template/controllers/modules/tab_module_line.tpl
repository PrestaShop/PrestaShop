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


<tr>
	<td class="center">
		<img width="57" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}">
	</td>
	<td>
		<div id="anchor{$module->name|ucfirst}">
			<h4>
				{$module->displayName|truncate:40:'…'} {$module->version}
				{if isset($module->id) && $module->id gt 0 }
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
				{/if}
			</h4>
			<p>
				{if isset($module->description) && $module->description ne ''}
					{$module->description|truncate:100:'…'}
				{else}
					&nbsp;
				{/if}
			</p>
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
				<a href="{$module->options.install_url}" class="btn btn-default">
					<i class="icon-plus-sign-alt"></i>
					{l s='Install'}
				</a>
			</p>
		</td>
	{/if}
</tr>
