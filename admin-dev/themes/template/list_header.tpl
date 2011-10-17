{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<link href="../css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	$(document).ready(function() {
		$('.filter').keypress(function(event){
			formSubmit(event, 'submitFilterButton{$table}')
		})
	});
</script>
{* Display column names and arrows for ordering (ASC, DESC) *}
{if $is_order_position}
	<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
	<script type="text/javascript">
		var token = '{$token}';
		var come_from = '{$table}';
		var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
	</script>
	<script type="text/javascript" src="../js/admin-dnd.js"></script>
{/if}

<script type="text/javascript">
	$(function() {
		if ($(".datepicker").length > 0)
			$(".datepicker").datepicker({
				prevText: '',
				nextText: ''
			});
	});
</script>

{if $add_button}
	<br /><a href="{$currentIndex}&add{$table}&token={$token}"><img src="../img/admin/add.gif" border="0" /> {l s='Add new'}</a><br /><br />
{/if}
<a name="{$table}">&nbsp;</a>
<form method="post" action="{$action}" class="form">
	<input type="hidden" id="submitFilter{$table}" name="submitFilter{$table}" value="0"/>
	<table>
		<tr>
			<td style="vertical-align: bottom;">
				<span style="float: left;">
					{if $page > 1}
						<input type="image" src="../img/admin/list-prev2.gif" onclick="getE(\'submitFilter{$table}\').value=1"/>&nbsp;
						<input type="image" src="../img/admin/list-prev.gif" onclick="getE(\'submitFilter{$table}\').value={$page - 1}"/>
					{/if}
					{l s='Page '}<b>{$page}</b> / {$total_pages}
					{if $page < $total_pages}
						<input type="image" src="../img/admin/list-next.gif" onclick="getE(\'submitFilter{$table}\').value={$page + 1}"/>&nbsp;
						<input type="image" src="../img/admin/list-next2.gif" onclick="getE(\'submitFilter{$table}'\').value={$total_pages}"/>
					{/if}
					| {l s='Display'}
					<select name="pagination">
						{* Choose number of results per page *}
						{foreach $pagination AS $value}
							<option value="{$value|intval}"{if $selected_pagination == $value} selected="selected" {elseif $selected_pagination == NULL && $value == $pagination[1]} selected="selected2"{/if}>{$value|intval}</option>
						{/foreach}
					</select>
					/ {$list_total} {l s='result(s)'}
				</span>
				<span style="float: right;">
					<input type="submit" name="submitReset{$table}" value="{l s='Reset'}" class="button" />
					<input type="submit" id="submitFilterButton{$table}" name="submitFilter" value="{l s='Filter'}" class="button" />
				</span>
				<span class="clear"></span>
			</td>
		</tr>
		<tr>
			<td>
				<table
				{if $table_id} id={$table_id}{/if}
				class="table {if $table_dnd}tableDnd{/if}"
				cellpadding="0" cellspacing="0">
					<thead>
						<tr class="nodrag nodrop">
							<th>
								{if $delete}
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$table}Box[]', this.checked)" />
								{/if}
							</th>
							{foreach $fields_display AS $key => $params}
								<th {if isset($params.widthColumn)} style="width: {$params.widthColumn}px"{/if}>{$params.title}
									{if !isset($params.orderby) || $params.orderby}
										<br />
										<a href="{$currentIndex}&{$identifier}={$id_cat}&{$table}Orderby={$key|urlencode}&{$table}Orderway=desc&token={$token}">
											<img border="0" src="../img/admin/down{if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}'_d'{/if}.gif" />
										</a>
										<a href="{$currentIndex}&{$identifier}={$id_cat}&{$table}Orderby={$key|urlencode}&{$table}Orderway=asc&token={$token}">
											<img border="0" src="../img/admin/up{if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}'_d'{/if}.gif" />
										</a>
									{/if}
								</th>
							{/foreach}

							{if $shop_link_type}
								<th style="width: 80px">
									{if $shop_link_type == 'shop'}
										{l s='shop'}
									{else}
										{l s='Group shop'}
									{/if}
								</th>
							{/if}
							{if $has_actions}
								<th style="width: 52px">{l s='Actions'}</th>
							{/if}
						</tr>
						<tr class="nodrag nodrop" style="height: 35px;">
							<td class="center">
								{if $delete}
									--
								{/if}
							</td>

							{* Filters (input, select, date or bool) *}
							{foreach $fields_display AS $key => $params}
								<td {if isset($params.align)} class="{$params.align}" {/if}>
									{if isset($params.search) && !$params.search}
										--
									{else}
										{if $params.type == 'bool'}
											<select onchange="$('#submitFilterButton{$table}').focus();$('#submitFilterButton{$table}').click();" name="{$table}Filter_{$key}">
												<option value="">--</option>
												<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'}</option>
												<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No'}</option>
											</select>
										{elseif $params.type == 'date' || $params.type == 'datetime'}
											{l s='From'} <input type="text" class="filter datepicker" id="{$name_id}_0" name="{$name}[0]" value="{if isset($value.0)}$value.0{/if}"{if isset($params.width)} style="width:{$params.width}px"{/if}/><br />
											{l s='To'} <input type="text" class="filter datepicker" id="{$name_id}_1" name="{$name}[1]" value="{if isset($value.1)}$value.1{/if}"{if isset($params.width)} style="width:{$params.width}px"{/if}/>
										{elseif $params.type == 'select'}
											{if isset($params.filter_key)}
												<select onchange="$('#submitFilterButton{$table}').focus();$('#submitFilterButton{$table}').click();" name="{$table}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
													<option value="" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>--</option>
													{if isset($params.select) && is_array($params.select)}
														{foreach $params.select AS $option_value => $option_display}
															<option value="{$option_value}" {if $option_display.selected == 'selected'} selected="selected"{/if}>{$option_display}</option>
														{/foreach}
													{/if}
												</select>
											{/if}
										{else}
											<input type="text" class="filter" name="{$table}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width)} style="width:{$params.width}px"{/if} />
										{/if}
									{/if}
								</td>
							{/foreach}

							{if $shop_link_type}
								<td>--</td>
							{/if}
							{if $has_actions}
								<td class="center">--</td>
							{/if}
							</tr>
						</thead>
