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

<div class="well">
	<form class="form-inline">
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
</div>

<div class="row">
	<div id="modulePosition">
		<div class="col-lg-9">
			<form method="post" action="{$url_submit}">
				<span class="pull-right">
					<button type="submit" class="btn btn-default" name="unhookform" onclick="modules_management('install')">
						<i class="icon-minus-sign-alt"></i>
						{l s='Unhook the selection'}
					</button>
				</span>
				{if !$can_move}
					<span class="alert">
						{l s='If you want to order/move the following data, please select a shop from the shop list.'}
					</span>
				{/if}

				{foreach $hooks as $hook}
					<div class="row-light clearfix">
						<a name="{$hook['name']}"></a>
						<table class="table {if $hook['module_count'] >= 2} tableDnD{/if}" id="{$hook['id_hook']}">
							<colgroup>
								<col width="10">
								<col width="30">
								<col width="40">
								<col width="">
								<col width="50">
							</colgroup>
							<thead>
								<tr class="nodrag nodrop">
									{if $hook['module_count'] && $can_move}
										<th>
											<input type="checkbox" id="Ghook{$hook['id_hook']}" style="margin-right: 2px;" onclick="hookCheckboxes({$hook['id_hook']}, 0, this)"/>
										</th>
									{/if}
									<th colspan="5">	
										<h3>
											{$hook['title']} - 
											<span class="badge">{$hook['module_count']} {if $hook['module_count'] > 1}{l s='Modules'}{else}{l s='Module'}{/if} </span>
											<span>({l s='Technical name: '}{$hook['name']})</span>
										</h3>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !empty($hook['description'])}
									<tr>
										<td colspan="5">
											<p class="text-muted">
												[{$hook['description']}]
											</p>
										</td>
									</tr>
								{/if}
								{if $hook['module_count']}
									{foreach $hook['modules'] as $position => $module}
										{if isset($module['instance'])}
											<tr id="{$hook['id_hook']}_{$module['instance']->id}" {cycle values='class="alt_row",'}>
												<td align="center">
													<input type="checkbox" id="mod{$hook['id_hook']}_{$module['instance']->id}" class="hook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 1, this)" name="unhooks[]" value="{$hook['id_hook']}_{$module['instance']->id}"/>
												</td>
												{if !$display_key}
												<td align="center" class="positions">{$module@iteration}</td>
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
												<td>
													<div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}">
											{else}
												<td colspan="3">
													<div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}">
											{/if}
														<h4>
															<img src="../modules/{$module['instance']->name}/logo.png" alt="{$module['instance']->name|stripslashes}" /> 
															{$module['instance']->displayName|stripslashes}
														</h4>
														<dl class="dl-horizontal">
															<dt>{l s='Version:'}</dt>
															<dd>
																{if $module['instance']->version}v{if $module['instance']->version|intval == $module['instance']->version}{sprintf('%.1f', $module['instance']->version)}{else}{$module['instance']->version|floatval}{/if}{/if}
															</dd>
															<dt>{l s='Description:'}</dt>
															<dd>
																{$module['instance']->description}
															</dd>
														</dl>
													</div>
												</td>
												<td>
													<div class="btn-group btn-group-action">
														<span class="btn btn-default">{l s='Choose an action'}</span>
														<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
															<span class="caret"></span>&nbsp;
														</button>
														<ul class="dropdown-menu">
															<li>
																<a href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&editGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
																	<i class="icon-pencil"></i>
																	{l s='Edit'}
																</a>
															</li>
															<li>
																<a href="{$current}&id_module={$module['instance']->id}&id_hook={$hook['id_hook']}&deleteGraft{if $display_key}&show_modules={$display_key}{/if}&token={$token}">
																	<i class="icon-trash"></i>
																	{l s='Delete'}
																</a>
															</li>
														</ul>
													</div>
												</td>
											</tr>
										{/if}
									{/foreach}
								{else}
									<tr>
										<td colspan="5">
											<p class="text-warning">{l s='No module was found for this hook.'}</p>
										</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
				{/foreach}
				<div id="unhook_button_position_bottom">
					<span class="pull-right">
					<button type="submit" class="btn btn-default" name="unhookform">
						<i class="icon-minus-sign-alt"></i>
						{l s='Unhook the selection'}
					</button>
					</span>
				</div>
			</form>
		</div>
		<div class="col-lg-3">
			<h2>{l s='LiveEdit'}</h2>
			{if $live_edit}
				<p>{l s='You have to select a shop to use LiveEdit'}</p>
			{else}
				<p>{l s='Click here to be redirected to the Front Office of your shop where you can move and delete modules directly.'}</p>
					<a class="btn btn-default pull-right" href="{$url_live_edit}" target="_blank">
						<i class="icon-edit"></i>
						{l s='Run LiveEdit'}
					</a>
			{/if}
		</div>
	</div>
</div>