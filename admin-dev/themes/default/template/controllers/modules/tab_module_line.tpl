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

<tr class="{$class_row}">
<td>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
				<img class="imgm" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}">
			</td>
			<td>
				<div class="moduleDesc" id="anchor{$module->name|ucfirst}">
					<h3>{$module->displayName} {$module->version}
						{if isset($module->type) && $module->type == 'addonsMustHave'}
							<span class="setup must-have">{l s='Must Have'}</span>
						{/if}
					</h3>
					<p class="desc">
						{if isset($module->description) && $module->description ne ''}
							{$module->description|truncate:100:'…'}
						{else}
							&nbsp;
						{/if}
					</p>
				</div>
			</td>
		</tr>
		<tr>
			<td class="tab_modules_actions_row">
				{if isset($module->type) && $module->type == 'addonsMustHave'}
					<a href="{$module->addons_buy_url}" target="_blank" class="button updated">
					<span><img src="../img/admin/cart_addons.png">&nbsp;&nbsp;{displayPrice price=$module->price currency=$module->id_currency}</span></a>
				{else}
					<a {if isset($module->id) && $module->id gt 0 && !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{if isset($module->id) && $module->id gt 0}{$module->options.uninstall_url}{else}{$module->options.install_url}{/if}" class="button installed">
						<span>{if isset($module->id) && $module->id gt 0}{l s='Uninstall'}{else}{l s='Install'}{/if}</span>
					</a>
			    {/if}
			</td>
			<td  class="tab_modules_actions_row">
				<div class="row-actions-module">
					{if !isset($module->not_on_disk)}
						{$module->optionsHtml}
						<a href="#" class="button action_tab_module" data-option="select_{$module->name}" class="button">Valider</a>
					{else}&nbsp;{/if}
				</div>
			</td>
		</tr>
	</table>
</td>

<!--

	<td>
		<img class="imgm" alt="" src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}">
		<div>
			{if isset($module->type) && $module->type == 'addonsMustHave'}
				<a href="{$module->addons_buy_url}" target="_blank" class="button updated">
				<span><img src="../img/admin/cart_addons.png">&nbsp;&nbsp;{displayPrice price=$module->price currency=$module->id_currency}</span></a>
			{else}
				<a {if isset($module->id) && $module->id gt 0 && !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{if isset($module->id) && $module->id gt 0}{$module->options.uninstall_url}{else}{$module->options.install_url}{/if}" class="button installed">
					<span>{if isset($module->id) && $module->id gt 0}{l s='Uninstall'}{else}{l s='Install'}{/if}</span>
				</a>
		    {/if}
	    </div>
	</td>
	<td>
		<div class="moduleDesc" id="anchor{$module->name|ucfirst}">
			<h3>{$module->displayName} {$module->version}
				{if isset($module->type) && $module->type == 'addonsMustHave'}
					<span class="setup must-have">{l s='Must Have'}</span>
				{/if}
			</h3>
			<p class="desc">
				{if isset($module->description) && $module->description ne ''}
					{$module->description|truncate:100:'…'}
				{else}
					&nbsp;
				{/if}
			</p>
			<div class="row-actions-module" style="float:right">
				{if !isset($module->not_on_disk)}
					{$module->optionsHtml}
					<a href="#" class="button action_tab_module" data-option="select_{$module->name}" class="button">Valider</a>
				{else}&nbsp;{/if}
			</div>
		</div>
	</td>
</tr>
-->