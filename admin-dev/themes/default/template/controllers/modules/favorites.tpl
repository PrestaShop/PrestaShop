<div id="productBox">
	{include file='controllers/modules/header.tpl'}
	<div class="row">
		<div class="col-lg-12">
			<span class="pull-right">
				<a class="btn btn-default" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}">
					<i class="icon-list"></i>
					{l s='Normal view'} 
				</a>
				<a class="btn btn-default" href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites">
					<i class="icon-star"></i> 
					{l s='Favorites view'}
				</a>
			</span>
		</div>
		<div class="col-lg-12">&nbsp;</div>
		<div id="container" class="col-lg-12">

		<div id="moduleContainer">
			<table class="table table-striped table-hover" cellspacing="0" cellpadding="0" id="">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="5%">
					<col width="20%">
					<col width="5%">
					<col width="7%">
					<col width="7%">
					<col width="7%">
				</colgroup>
				<thead>
					<tr class="nodrag nodrop">
						<th class="center">{l s='Logo'}</th>
						<th class="center">{l s='Module Name'}</th>
						<th>{l s='Description'}</th>
						<th class="center">{l s='Status'}</th>
						<th>{l s='Tab'}</th>
						<th class="center">{l s='Categories'}</th>
						<th>{l s='Interest'}</th>
						<th>{l s='Favorite'}</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$modules key=km item=module}
					<tr height="32" {if $km % 2 eq 0} class="alt_row"{/if}>
						<td class="center">
							<img src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" width="57" height="57" />
						</td>
						<td class="center moduleName">
							<h5>{$module->displayName}</h5>
						</td>
						<td>
							<span class="moduleFavDesc">{$module->description|truncate:80:'...'}</span>
						</td>
						<td class="center setup">
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
						<td class="center">
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
						<td class="center" id="r_{$module->name}">
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