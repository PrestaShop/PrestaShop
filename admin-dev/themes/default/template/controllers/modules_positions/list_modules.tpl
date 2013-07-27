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

<script type="text/javascript">
	var token = '{$token}';
	var come_from = 'AdminModulesPositions';
</script>
<script type="text/javascript" src="../js/admin-dnd.js"></script>

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>

<div class="filter-module">
<form>
	{l s='Show'} :
	<select id="show_modules" onChange="autoUrl('show_modules', '{$url_show_modules}')">
		<option value="all">{l s='All modules'}&nbsp;</option>
		<option>---------------</option>

		{foreach $modules as $module}
			<option value="{$module->id|intval}" {if $display_key == $module->id}selected="selected"{/if}>{$module->displayName}</option>
		{/foreach}
	</select>
	<br /><br />
	<input type="checkbox" id="hook_position" onclick="autoUrlNoList('hook_position', '{$url_show_invisible}')" {if $hook_position}checked="checked"{/if} />&nbsp;
	<label class="t" for="hook_position">{l s='Display non-positionable hooks'}</label>
</form>
</div>
<br/>
<div>

<div id="modulePosition">
<div class="blocLiveEdit"><h2>{l s='LiveEdit'}</h2>
{if $live_edit}
	<p>{l s='You have to select a shop to use LiveEdit'}</p>
{else}
	<p>{l s='Click here to be redirected to the Front Office of your shop where you can move and delete modules directly.'}</p>
		<a href="{$url_live_edit}" target="_blank" class="button">{l s='Run LiveEdit'}</a>
{/if}
</div>
<form method="post" action="{$url_submit}">
<div id="unhook_button_position_top">
	<input class="button floatr" type="submit" name="unhookform" value="{l s='Unhook the selection'}"/></div>

{if !$can_move}
	<br /><div><b>{l s='If you want to order/move the following data, please select a shop from the shop list.'}</b></div>
{/if}
{foreach $hooks as $hook}
	<a name="{$hook['name']}"/>
	<table cellpadding="0" cellspacing="0" class="table widthfull space {if $hook['module_count'] >= 2} tableDnD{/if}" id="{$hook['id_hook']}">
		<colgroup>
			<col width="10">
			<col width="30">
			<col width="40">
			<col width="">
			<col width="50">
		</colgroup>
	<tr class="nodrag nodrop"><th colspan="5">	{if $hook['module_count'] && $can_move}
		<input type="checkbox" id="Ghook{$hook['id_hook']}" style="margin-right: 2px;" onclick="hookCheckboxes({$hook['id_hook']}, 0, this)"/>
	{/if}{$hook['title']} - <span style="color: red">{$hook['module_count']}</span> {if $hook['module_count'] > 1}{l s='Modules'}{else}{l s='Module'}{/if}

	{if !empty($hook['description'])}
		&nbsp;<span style="font-size:0.8em; font-weight: normal">[{$hook['description']}]</span>
	{/if}
	<span style="color:grey;">({l s='Technical name: '}{$hook['name']})</span></th></tr>
	{if $hook['module_count']}
		{foreach $hook['modules'] as $position => $module}
			{if isset($module['instance'])}
			<tr id="{$hook['id_hook']}_{$module['instance']->id}" {cycle values='class="alt_row",'} style="height: 42px;">
			<td align=center ><input type="checkbox" id="mod{$hook['id_hook']}_{$module['instance']->id}" class="hook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 1, this)" name="unhooks[]" value="{$hook['id_hook']}_{$module['instance']->id}"/></td>
			{if !$display_key}
				<td align=center  class="positions">{$module@iteration}</td>
				<td {if $can_move && $hook['module_count'] >= 2} align=center class="dragHandle"{/if} id="td_{$hook['id_hook']}_{$module['instance']->id}">
					{if $can_move}
						<a {if {$module@iteration} == 1} style="display: none;"{/if} href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&direction=0&token={$token}&changePosition#{$hook['name']}">
							<img src="../img/admin/up.gif" alt="{l s='Up'}" title="{l s='Up'}" />
						</a><br />
						<a {if {$module@iteration} == count($hook['modules'])} style="display: none;"{/if} href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&direction=1&token={$token}&changePosition#{$hook['name']}">
							<img src="../img/admin/down.gif" alt="{l s='Down'}" title="{l s='Down'}" />
						</a>
					{/if}
				</td>
				<td><div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}">
			{else}
				<td colspan="3"><div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}">
			{/if}
			<img src="../modules/{$module['instance']->name}/logo.png" alt="{$module['instance']->name|stripslashes}" /> <h3>{$module['instance']->displayName|stripslashes}</h3>
				<span>{if $module['instance']->version}v{if $module['instance']->version|intval == $module['instance']->version}{sprintf('%.1f', $module['instance']->version)}{else}{$module['instance']->version|floatval}{/if}{/if}</span><p>{$module['instance']->description}</p>
			</div></td>
				<td>
					<a href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&editGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
						<img src="../img/admin/edit.gif" border="0" alt="{l s='Edit'}" title="{l s='Edit'}" />
					</a>
					<a href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&deleteGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
						<img src="../img/admin/delete.gif" border="0" alt="{l s='Delete'}" title="{l s='Delete'}" />
					</a>
				</td>
			</tr>
			{/if}
		{/foreach}
	{else}
		<tr><td colspan="5">{l s='No module was found for this hook.'}</td></tr>
	{/if}
	</table>
{/foreach}
<div id="unhook_button_position_bottom"><input class="button floatr" type="submit" name="unhookform" value="{l s='Unhook the selection'}"/></div>

</div>
</div>
</form>