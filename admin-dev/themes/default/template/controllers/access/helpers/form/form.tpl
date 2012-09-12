{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	$(document).ready(function() {

		$('div.productTabs').find('a').each(function() {
			$(this).attr('href', '#');
		});

		$('div.productTabs a').click(function() {
			var id = $(this).attr('id');
			$('.nav-profile').removeClass('selected');
			$(this).addClass('selected');
			$('.tab-profile').hide()
			$('.'+id).show();
		});

		$('.ajaxPower').change(function(){
			var tout = $(this).attr('rel').split('||');
			var id_tab = tout[0];
			var id_profile = tout[1];
			var perm = tout[2];
			var enabled = $(this).is(':checked')? 1 : 0;
			var tabsize = tout[3];
			var tabnumber = tout[4];
			var table = 'table#table_'+id_profile;

			if (perm == 'all' && $(this).parent().parent().hasClass('parent'))
			{
				checked = enabled ? 'checked': '';
				$(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').attr('checked', checked);
				$.ajax({
					url: "{$link->getAdminLink('AdminAccess')}",
					cache: false,
					data : {
						ajaxMode : '1',
						id_tab: id_tab,
						id_profile: id_profile,
						perm: perm,
						enabled: enabled,
						submitAddAccess: '1',
						addFromParent: '1',
						action: 'updateAccess',
						ajax: '1',
						token: '{getAdminToken tab='AdminAccess'}'
					},
					success : function(res,textStatus,jqXHR)
					{
						try
						{
							if (res == 'ok')
								showSuccessMessage("{l s='Update successful'}");
							else
								showErrorMessage("{l s='Update error'}");
						}
						catch(e)
						{
							jAlert('Technical error');
						}
					}
				});
			}
			perfect_access_js_gestion(this, perm, id_tab, tabsize, tabnumber, table);

			$.ajax({
				url: "{$link->getAdminLink('AdminAccess')}",
				cache: false,
				data : {
					ajaxMode : '1',
					id_tab: id_tab,
					id_profile: id_profile,
					perm: perm,
					enabled: enabled,
					submitAddAccess: '1',
					action: 'updateAccess',
					ajax: '1',
					token: '{getAdminToken tab='AdminAccess'}'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("{l s='Update successful'}");
						else
							showErrorMessage("{l s='Update error'}");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

		$(".changeModuleAccess").change(function(){
			var tout = $(this).attr('rel').split('||');
			var id_module = tout[0];
			var perm = tout[1];
			var id_profile = tout[2];
			var enabled = $(this).is(':checked') ? 1 : 0;
			var enabled_attr = $(this).is(':checked') ? true : false;
			var table = 'table#table_module_'+id_profile;

			if (id_module == -1)
				$(table+' .ajax-ma-'+perm).each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});
			else if (!enabled)
				$(table+' #ajax-ma-'+perm+'-master').each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});

			$.ajax({
				url: "{$link->getAdminLink('AdminAccess')}",
				cache: false,
				data : {
					ajaxMode: '1',
					id_module: id_module,
					perm: perm,
					enabled: enabled,
					id_profile: id_profile,
					changeModuleAccess: '1',
					action: 'updateModuleAccess',
					ajax: '1',
					token: '{getAdminToken tab='AdminAccess'}'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("{l s='Update successful'}");
						else
							showErrorMessage("{l s='Update error'}");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

	});

</script>

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

<div class="productTabs">
	<ul class="tab">
	{foreach $profiles as $profile}
		<li class="tab-row">
			<a class="nav-profile {if $profile.id_profile == $current_profile}selected{/if}" id="profile-{$profile.id_profile}" href="{$current}&token={$token}&id_profile={$profile.id_profile}">{$profile.name}</a>
		</li>
	{/foreach}
	</ul>
</div>

<form id="{$table}_form" class="defaultForm" action="{$current}&{$submit_action}=1&token={$token}" method="post" enctype="multipart/form-data">
	{if $form_id}
		<input type="hidden" name="{$identifier}" id="{$identifier}" value="{$form_id}" />
	{/if}

	{assign var=tabsize value=count($tabs)}
	{foreach $tabs AS $tab}
		{if $tab.id_tab > $tabsize}
			{assign var=tabsize value=$tab.id_tab}
		{/if}
	{/foreach}

	{foreach $profiles as $profile}

		<div class="profile-{$profile.id_profile} tab-profile" style="display:{if $profile.id_profile == $current_profile}block{else}none{/if}">

			{if $profile.id_profile != $admin_profile}
				<table class="table float" cellspacing="0" style="margin-right:50px" id="table_{$profile.id_profile}">
					<tr>
						<th class="center">
							{l s='Menus'}
						</th>
						<th class="center">
							<input type="checkbox" name="1" id="viewall"
								{if $access_edit == 1}
									rel="-1||{$profile.id_profile}||view||{$tabsize}||{count($tabs)}" class="ajaxPower"
								{else}
									disabled="disabled"
								{/if} />
							{l s='View'}
						</th>
						<th class="center">
							<input type="checkbox" name="1" id="addall"
								{if $access_edit == 1}
									rel="-1||{$profile.id_profile}||add||{$tabsize}||{count($tabs)}" class="ajaxPower"
								{else}
									disabled="disabled"
								{/if} />
							{l s='Add'}
						</th>
						<th class="center">
							<input type="checkbox" name="1" id="editall"
								{if $access_edit == 1}
									rel="-1||{$profile.id_profile}||edit||{$tabsize}||{count($tabs)}" class="ajaxPower"
								{else}
									disabled="disabled"
								{/if} />
							{l s='Edit'}
						</th>
						<th class="center">
							<input type="checkbox" name="1" id="deleteall"
								{if $access_edit == 1}
									rel="-1||{$profile.id_profile}||delete||{$tabsize}||{count($tabs)}" class="ajaxPower"
								{else}
									disabled="disabled"
								{/if} />
							{l s='Delete'}
						</th>
						<th class="center">
							<input type="checkbox" name="1" id="allall"
								{if $access_edit == 1}
									rel="-1||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}" class="ajaxPower"
								{else}
									disabled="disabled"
								{/if} />
							{l s='All'}
						</th>
					</tr>
					{if !count($tabs)}
						<tr>
							<td colspan="6">{l s='No menu'}</td>
						</tr>
					{else}
						{foreach $tabs AS $tab}

							{assign var=access value=$accesses[$profile.id_profile]}

							{if !$tab.id_parent OR $tab.id_parent == -1}
								{assign var=is_child value=false}
								{assign var=result_accesses value=0}
								<tr{if !$is_child} class="parent"{/if}>
									<td{if !$is_child} class="bold"{/if}>{if $is_child} &raquo; {/if}<strong>{$tab.name}</strong></td>
									{foreach $perms as $perm}
										{if $access_edit == 1}
											<td>
												<input type="checkbox"
													id="{$perm}{$access[$tab.id_tab]['id_tab']}"
													rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
													class="ajaxPower {$perm} {$access[$tab.id_tab]['id_tab']}"
													{if $access[$tab.id_tab][$perm] == 1}checked="checked"{/if}/>
											</td>
										{else}
											<td>
												<input type="checkbox"
													disabled="disabled"
													{if $access[$tab.id_tab][$perm] == 1}checked="checked"{/if}/>
											</td>
										{/if}
										{assign var=result_accesses value=$result_accesses + $access[$tab.id_tab][$perm]}
									{/foreach}
									<td>
										<input type="checkbox"
											id='all{$access[$tab.id_tab]['id_tab']}'
											{if $access_edit == 1}
												rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
												class="ajaxPower all {$access[$tab.id_tab]['id_tab']}"
											{else}
												class="all {$access[$tab.id_tab]['id_tab']}"
												disabled="disabled"
											{/if}
											{if $result_accesses == 4}checked="checked"{/if}/>
									</td>
								</tr>

								{foreach $tabs AS $child}
									{if $child.id_parent === $tab.id_tab}
										{if isset($access[$child.id_tab])}
											{assign var=is_child value=true}
											{assign var=result_accesses value=0}
											<tr class="child-{$child.id_parent}">
												<td{if !$is_child} class="bold"{/if}>{if $is_child} &raquo; {/if}<strong>{$child.name}</strong></td>
												{foreach $perms as $perm}
													{if $access_edit == 1}
														<td>
															<input type="checkbox"
																id="{$perm}{$access[$child.id_tab]['id_tab']}"
																rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}"
																class="ajaxPower {$perm} {$access[$child.id_tab]['id_tab']}"
																{if $access[$child.id_tab][$perm] == 1}checked="checked"{/if}/>
														</td>
													{else}
														<td>
															<input type="checkbox"
																disabled="disabled"
																{if $access[$child.id_tab][$perm] == 1}checked="checked"{/if}/>
														</td>
													{/if}
													{assign var=result_accesses value=$result_accesses + $access[$child.id_tab][$perm]}
												{/foreach}
												<td>
													<input type="checkbox"
														id='all{$access[$child.id_tab]['id_tab']}'
														{if $access_edit == 1}
															rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"
															class="ajaxPower all {$access[$child.id_tab]['id_tab']}"
														{else}
															class="all {$access[$child.id_tab]['id_tab']}"
															disabled="disabled"
														{/if}
														{if $result_accesses == 4}checked="checked"{/if}/>
												</td>
											</tr>
										{/if}
									{/if}
								{/foreach}

							{/if}

						{/foreach}
					{/if}
				</table>

				<table class="table" cellspacing="0" style="margin-left:20px" id="table_module_{$profile.id_profile}">
					<tr>
						<th>{l s='Modules'}</th>
						<th class="center">
							<input type="checkbox"
								id="ajax-ma-view-master"
								{if $access_edit == 1}
									class="changeModuleAccess" rel="-1||view||{$profile.id_profile}"
								{else}
									disabled="disabled"
								{/if} /> {l s='View'}
						</th>
						<th class="center">
							<input type="checkbox"
								id="ajax-ma-configure-master"
								{if $access_edit == 1}
									class="changeModuleAccess" rel="-1||configure||{$profile.id_profile}"
								{else}
									disabled="disabled"
								{/if} /> {l s='Configure'}</th>
					</tr>
					
					{if !count($modules)}
						<tr>
							<td colspan="3">{l s='No modules installed'}</td>
						</tr>
					{else}
						{foreach $modules[$profile.id_profile] AS $module}
							<tr>
								<td>&raquo; {$module.name}</td>
								<td>
									<input type="checkbox"
										value="1"
										{if $module.view == true}checked="checked"{/if}
										{if $access_edit == 1}
											class="ajax-ma-view changeModuleAccess"
											rel="{$module.id_module}||view||{$profile.id_profile}"
										{else}
											class="ajax-ma-view"
											disabled="disabled"
										{/if}
									/>
								</td>
								<td>
									<input type="checkbox"
										value="1"
										{if $module.configure == true}checked="checked"{/if}
										{if $access_edit == 1}
											class="ajax-ma-configure changeModuleAccess"
											rel="{$module.id_module}||configure||{$profile.id_profile}"
										{else}
											class="ajax-ma-configure"
											disabled="disabled"
										{/if}
									/>
								</td>
							</tr>
						{/foreach}
					{/if}
				</table>
				
				<div class="clear">&nbsp;</div>

			{else}
				{l s='Administrator permissions cannot be modified.'}
			{/if}

		</div>

	{/foreach}
</form>
