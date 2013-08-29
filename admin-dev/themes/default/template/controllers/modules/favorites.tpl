	{include file='controllers/modules/header.tpl'}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-list-ul"></i>
		{l s='Modules list'}
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group pull-right">
				<a class="btn btn-default {if !isset($smarty.get.select)} active{/if}" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}">
					<i class="icon-list"></i>
					{l s='Normal view'} 
				</a>
				<a class="btn btn-default {if $smarty.get.select == 'favorites'} active{/if}" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites">
					<i class="icon-star"></i> 
					{l s='Favorites view'}
				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div id="container" class="col-lg-12">
			<div id="moduleContainer">
				<table class="table">
					<thead>
						<tr class="nodrag nodrop">
							<th></th>
							<th>{l s='Module'}</th>
							<th>{l s='Status'}</th>
							<th>{l s='Tab'}</th>
							<th>{l s='Categories'}</th>
							<th>{l s='Interest'}</th>
							<th>{l s='Favorite'}</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$modules key=km item=module}
						<tr>
							<td>
								<img src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" width="32" height="32" />
							</td>
							<td class="moduleName">
								<h4>{$module->displayName}</h4>
								<span class="moduleFavDesc text-muted">{$module->description|truncate:80:'...'}</span>
							</td>
							<td class="setup">
								{if isset($module->id) && $module->id gt 0}
									<span class="label label-success">{l s='Installed'}</span>
								{else}
									<span class="label label-warning">{l s='Not Installed'}</span>
								{/if}
							</td>
							<td>
								{assign var="module_name" value=$module->name}
								<select name="t_{$module->name}" multiple="multiple">
									{foreach $tabs AS $t}
										{if $t.active}
											<option {if isset($tab_modules_preferences.$module_name) && in_array($t.id_tab, $tab_modules_preferences.$module_name)} selected="selected" {/if} class="group" value="{$t.id_tab}">{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}</option>
											{foreach from=$t.sub_tabs item=t2}
												{if $t2.active}
													{assign var="id_tab" value=$t.id_tab}
													<option {if isset($tab_modules_preferences.$module_name) && in_array($t2.id_tab, $tab_modules_preferences.$module_name)} selected="selected" {/if} value="{$t2.id_tab}">&nbsp;&nbsp;&nbsp;{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'htmlall':'UTF-8'}{/if}</option>
												{/if}
											{/foreach}
										{/if}
									{/foreach}
								</select>
							</td>
							<td>
								<span>{$module->categoryName}</span>
							</td>
							<td>
								<select name="i_{$module->name}" class="moduleFavorite">
									<option value="" selected="selected">---</option>
									<option value="1" {if isset($module->preferences.interest) && $module->preferences.interest eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
									<option value="0" {if isset($module->preferences.interest) && $module->preferences.interest eq '0'}selected="selected"{/if}>{l s='No'}</option>
								</select>
							</td>
							<td>
								<select name="f_{$module->name}" class="moduleFavorite">
									<option value="" selected="selected">---</option>
									<option value="1" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
									<option value="0" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '0'}selected="selected"{/if}>{l s='No'}</option>
								</select>
							</td>
							<td  id="r_{$module->name}">
								<span>&nbsp;</span>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
