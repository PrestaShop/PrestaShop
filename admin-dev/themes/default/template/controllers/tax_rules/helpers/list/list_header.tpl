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

{hook h='displayAdminListBefore'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}

<form method="post" action="{$currentIndex}&{$identifier}&token={$token}&id_tax_rules_group={$id_tax_rules_group}&updatetax_rules_group#{$table}" class="form">
	<input type="hidden" id="submitFilter{$table}" name="submitFilter{$table}" value="0"/>
	<table class="table_grid">
		<tr>
			<td style="vertical-align: bottom;">
				<span style="float: left;">
					{if $page > 1}
						<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter{$table}').value=1"/>&nbsp;
						<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter{$table}').value={$page - 1}"/>
					{/if}
					{l s='Page'} <b>{$page}</b> / {$total_pages}
					{if $page < $total_pages}
						<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter{$table}').value={$page + 1};"/>&nbsp;
						<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter{$table}').value={$total_pages}"/>
					{/if}
					| {l s='Display'}
					<select name="pagination" onchange="submit()">
						{* Choose number of results per page *}
						{foreach $pagination AS $value}
							<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
						{/foreach}
					</select>
					/ {$list_total} {l s='result(s)'}
				</span>
				<span style="float: right;">
					<input type="submit" name="submitReset{$table}" value="{l s='Reset'}" class="button" />
				</span>
				<span class="clear"></span>
			</td>
		</tr>
		<tr>
			<td>
				<table
				{if $table_id} id={$table_id}{/if}
				class="table {if $table_dnd}tableDnD{/if} {$table}"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;"
				>
					<col width="10px" />
					{foreach $fields_display AS $key => $params}
						<col {if isset($params.width) && $params.width != 'auto'}width="{$params.width}px"{/if}/>
					{/foreach}
					{if $shop_link_type}
						<col width="80px" />
					{/if}
					{if $has_actions}
						<col width="52px" />
					{/if}
					<thead>
						<tr class="nodrag nodrop">
							<th class="center">
								{if $has_bulk_actions}
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$table}Box[]', this.checked)" />
								{/if}
							</th>
							{foreach $fields_display AS $key => $params}
								<th {if isset($params.align)} class="{$params.align}"{/if}>
									{if isset($params.hint)}<span class="hint" name="help_box">{$params.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
									<span class="title_box">
										{$params.title}
									</span>
										<br />&nbsp;
								</th>
							{/foreach}
							{if $shop_link_type}
								<th>
									{if $shop_link_type == 'shop'}
										{l s='Shop'}
									{else}
										{l s='Group shop'}
									{/if}
									<br />&nbsp;
								</th>
							{/if}
							{if $has_actions}
								<th class="center">{l s='Actions'}<br />&nbsp;</th>
							{/if}
						</tr>
						</thead>
