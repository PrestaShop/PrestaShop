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
<div>{block name="leadin"}{/block}</div>

<div class="row">
	<div class="col-lg-9">
		<div class="panel">
			<form class="form-inline well">
				<label>{l s='Show'} :</label>
				<span>
					<select id="show_modules" onChange="autoUrl('show_modules', '{$url_show_modules}')" class="filter fixed-width-XL">
						<option value="all">{l s='All modules'}&nbsp;</option>
						<option>---------------</option>
						{foreach $modules as $module}
							<option value="{$module->id|intval}" {if $display_key == $module->id}selected="selected"{/if}>{$module->displayName}</option>
						{/foreach}
					</select>
				</span>
				<p class="checkbox">
					<label class="control-label" for="hook_position">
						<input type="checkbox" id="hook_position" onclick="autoUrlNoList('hook_position', '{$url_show_invisible}')" {if $hook_position}checked="checked"{/if} />
						{l s='Display non-positionable hooks'}
					</label>
				</p>
			</form>

			<div id="modulePosition">
				<form method="post" action="{$url_submit}" >

{if !$can_move}
					<span class="alert">
						{l s='If you want to order/move the following data, please select a shop from the shop list.'}
					</span>
{/if}
{foreach $hooks as $hook}
					<section class="hook_panel">
						<a name="{$hook['name']}"></a>
						<header class="hook_panel_header">
							<span class="hook_name">{$hook['name']}</span>
							<span class="hook_title">{$hook['title']}</span>

							<span class="badge pull-right">
	{if $hook['module_count'] && $can_move}
								<input type="checkbox" id="Ghook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 0, this)"/>
	{/if}
								{$hook['module_count']} {if $hook['module_count'] > 1}{l s='Modules'}{else}{l s='Module'}{/if}
							</span>
	{if !empty($hook['description'])}
							<div class="hook_description">{$hook['description']}</div>
	{/if}
						</header>

	{if $hook['module_count']}
						<section class="module_list">
		{foreach $hook['modules'] as $position => $module}
			{if isset($module['instance'])}
							<div id="{$hook['id_hook']}_{$module['instance']->id}" class="module_list_item">
								<div class="module_col_select">
									<input type="checkbox" id="mod{$hook['id_hook']}_{$module['instance']->id}" class="hook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 1, this)" name="unhooks[]" value="{$hook['id_hook']}_{$module['instance']->id}"/>
								</div>
				{if !$display_key}
								<div class="module_col_position" {if $can_move && $hook['module_count'] >= 2}class="dragHandle"{/if} id="td_{$hook['id_hook']}_{$module['instance']->id}">
									<span class="positions">{$module@iteration}</span>
					{if $can_move}
									<a class="btn btn-default" {if {$module@iteration} == 1} style="display: none;"{/if} href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&direction=0&token={$token}&changePosition#{$hook['name']}">
										<i class="icon-chevron-up"></i>
									</a>

									<a class="btn btn-default" {if {$module@iteration} == count($hook['modules'])} style="display: none;"{/if} href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&direction=1&token={$token}&changePosition#{$hook['name']}">
										<i class="icon-chevron-down"></i>
									</a>
					{/if}
								</div>
				{/if}
<div class="module_col_icon">
	<img src="../modules/{$module['instance']->name}/logo.png" alt="{$module['instance']->name|stripslashes}" />
</div>
<div class="module_col_infos"><span class="module_name">
											{$module['instance']->displayName|stripslashes} {if $module['instance']->version}
											<small class="text-muted">&nbsp;-&nbsp;v{if $module['instance']->version|intval == $module['instance']->version}{sprintf('%.1f', $module['instance']->version)}{else}{$module['instance']->version|floatval}{/if}</small>{/if}
										</span>
										<div class="module_description">{$module['instance']->description}</div></div>
<div class="module_col_actions">
									<!-- <div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}"></div> -->
										
										
									

									<div class="btn-group">
										<a class="btn btn-default" href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&editGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
											<i class="icon-pencil"></i>
											{l s='Edit'}
										</a>
										<a class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>&nbsp;
										</a>
										<ul class="dropdown-menu">
											<li>
												<a href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&deleteGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
													<i class="icon-minus-sign-alt"></i>
													{l s='Unhook'}
												</a>
											</li>
										</ul>
									</div>
								</div>

								</div>
							
			{/if}
		{/foreach}
						</section>
	{else}
							<!-- <p>{l s='No module was found for this hook.'}</p> -->
	{/if}
					</section>
{/foreach}
					<div id="unhook_button_position_bottom">
						<button type="submit" class="btn btn-default" name="unhookform">
							<i class="icon-minus-sign-alt"></i>
							{l s='Unhook the selection'}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="panel">
			<h3><i class="icon-eye-open"></i> {l s='LiveEdit'}</h3>
			{if $live_edit}
				<p>{l s='You have to select a shop to use LiveEdit'}</p>
			{else}
				<p>{l s='Click here to be redirected to the Front Office of your shop where you can move and delete modules directly.'}</p>
					<a class="btn btn-default" href="{$url_live_edit}" target="_blank">
						<i class="icon-edit"></i>
						{l s='Run LiveEdit'}
					</a>
			{/if}
		</div>
	</div>
</div>
