<div id="productBox">
	{include file='controllers/modules/header.tpl'}
	<ul class="view-modules">
		<li class="button normal-view"><a href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}"><img src="themes/default/img/modules_view_layout_sidebar.png" alt="{l s='Normal view'}" border="0" /><span>{l s='Normal view'}</span></a></li>
		<li class="button favorites-view-disabled"><img src="themes/default/img/modules_view_table_select_row.png" alt="{l s='Favorites view'}" border="0" /><span>{l s='Favorites view'}</span></li>
	</ul>

	<div id="container">

		<div id="moduleContainer" style="padding:0px;margin:0px;padding-top:15px">

			<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table" id="">
				<col width="30px">
				<col width="240px">
				<col width="">
				<col width="140px">
				<col width="250px">
				<col width="180px">
				<col width="70px">
				<col width="70px">
				<col width="130px">
				</colgroup>
				<thead>
					<tr class="nodrag nodrop">
						<th class="center">{l s='Logo'}</th>
						<th>{l s='Module Name'}</th>
						<th>{l s='Description'}</th>
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
					<tr height="32" {if $km % 2 eq 0} class="alt_row"{/if}>
						<td><img src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" width="16" height="16" /></td>
						<td><span class="moduleName">{$module->displayName}</span></td>
						<td><span class="moduleFavDesc">{$module->description|truncate:80:'...'}</span></td>
						<td>{if isset($module->id) && $module->id gt 0}<span class="setup">{l s='Installed'}</span>{else}<span class="setup non-install">{l s='Not Installed'}</span>{/if}</td>
						<td>
							{assign var="module_name" value=$module->name}
							<select class="chosen moduleTabPreferencesChoise" name="t_{$module->name}" multiple="multiple">
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
						<td>{$module->categoryName}</td>
						<td>
						<select name="i_{$module->name}" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" {if isset($module->preferences.interest) && $module->preferences.interest eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
							<option value="0" {if isset($module->preferences.interest) && $module->preferences.interest eq '0'}selected="selected"{/if}>{l s='No'}</option>
						</select>
						</td>
						<td>
						<select name="f_{$module->name}" class="moduleFavorite" style="width:50px">
							<option value="" selected="selected">---</option>
							<option value="1" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
							<option value="0" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '0'}selected="selected"{/if}>{l s='No'}</option>
						</select>
						</td>
						<td id="r_{$module->name}">&nbsp;</td>
					</tr>
				{/foreach}
				</tbody>
			</table>

		</div>
	</div>
</div>