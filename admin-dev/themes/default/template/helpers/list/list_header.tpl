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

{if !$simple_header}
<script type="text/javascript">
	$(document).ready(function() {
		$('table.{$table} .filter').keypress(function(event){
			formSubmit(event, 'submitFilterButton{$table}')
		})
	});
</script>

{* Display column names and arrows for ordering (ASC, DESC) *}
{if $is_order_position}
	<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
	<script type="text/javascript">
		var token = '{$token}';
		var come_from = '{$table}';
		var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
	</script>
	<script type="text/javascript" src="../js/admin-dnd.js"></script>
{/if}
<script type="text/javascript">
	$(function() {
		if ($("table.{$table} .datepicker").length > 0)
			$("table.{$table} .datepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
	});
</script>
{/if}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}

{if !$simple_header}
<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

{hook h='displayAdminListBefore'}

{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}

{if !$simple_header}
<form method="post" action="{$action}" class="form-horizontal">
	<fieldset class="col-lg-12">
		{block name="override_header"}{/block}

		<input type="hidden" id="submitFilter{$table}" name="submitFilter{$table}" value="0"/>
		<span class="pull-right">
			<button type="submit" name="submitReset{$table}" class="btn btn-default btn-small">
				<i class="icon-eraser"></i> {l s='Reset'}
			</button>
			<button type="submit" id="submitFilterButton{$table}" name="submitFilter" class="btn btn-default btn-small" />
				<i class="icon-filter"></i> {l s='Filter'}
			</button>
		</span>
{/if}
		<table 
			class="table table-hover"
			name="list_table"
			{if $table_id} id={$table_id}{/if}
			class="table {if $table_dnd}tableDnD{/if} {$table}"
			>
			<thead>
				<tr class="nodrag nodrop">
					<th class="center">
					{if $has_bulk_actions}
						<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$table}Box[]', this.checked)" />
					{/if}
					</th>
					{foreach $fields_display AS $key => $params}
					<th {if isset($params.align)} class="{$params.align}"{/if}>
						{if isset($params.hint)}
						<span class="alert alert-info" name="help_box">{$params.hint}<span class="hint-pointer">&nbsp;</span></span>
						{/if}
						<span class="title_box {if isset($order_by) && ($key == $order_by)} active{/if}">
							{$params.title}
							{if (!isset($params.orderby) || $params.orderby) && !$simple_header}
							<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}class="active"{/if}  href="{$currentIndex}&{$table}Orderby={$key|urlencode}&{$table}Orderway=desc&token={$token}{if isset($smarty.get.$identifier)}&{$identifier}={$smarty.get.$identifier|intval}{/if}">
								<i class="icon-caret-down"></i>
							</a>
							<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}class="active"{/if} href="{$currentIndex}&{$table}Orderby={$key|urlencode}&{$table}Orderway=asc&token={$token}{if isset($smarty.get.$identifier)}&{$identifier}={$smarty.get.$identifier|intval}{/if}">
								<i class="icon-caret-up"></i>
							</a>
						</span>
					{/if}
					</th>
					{/foreach}
					{if $shop_link_type}
					<th>
						{if $shop_link_type == 'shop'}
						{l s='Shop'}
						{else}
						{l s='Group shop'}
						{/if}
					</th>
					{/if}
					{if $has_actions}
					<th>{l s='Actions'}{if !$simple_header}{/if}</th>
					{/if}
				</tr>
					{if !$simple_header}
				<tr class="nodrag nodrop filter {if $row_hover}row_hover{/if}">
					<td class="center">
						{if $has_bulk_actions}
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
								<select class="filter" onchange="$('#submitFilterButton{$table}').focus();$('#submitFilterButton{$table}').click();" name="{$table}Filter_{$key}">
									<option value="">--</option>
									<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'}</option>
									<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No'}</option>
								</select>
							{elseif $params.type == 'date' || $params.type == 'datetime'}
								{l s='From'} <input type="text" class="filter datepicker" id="{$params.id_date}_0" name="{$params.name_date}[0]" value="{if isset($params.value.0)}{$params.value.0}{/if}"{if isset($params.width)} style="width:70px"{/if}/>
								{l s='To'} <input type="text" class="filter datepicker" id="{$params.id_date}_1" name="{$params.name_date}[1]" value="{if isset($params.value.1)}{$params.value.1}{/if}"{if isset($params.width)} style="width:70px"{/if}/>
							{elseif $params.type == 'select'}
								{if isset($params.filter_key)}
									<select class="filter" onchange="$('#submitFilterButton{$table}').focus();$('#submitFilterButton{$table}').click();" name="{$table}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
										<option value="" {if $params.value == ''} selected="selected" {/if}>--</option>
										{if isset($params.list) && is_array($params.list)}
											{foreach $params.list AS $option_value => $option_display}
												<option value="{$option_value}" {if $option_display == $params.value ||  $option_value == $params.value} selected="selected"{/if}>{$option_display}</option>
											{/foreach}
										{/if}
									</select>
								{/if}
							{else}
								<input type="text" class="filter" name="{$table}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{/if} />
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
			{/if}
			</thead>