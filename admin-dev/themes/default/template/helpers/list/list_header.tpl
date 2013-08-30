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
			$('table.{$list_id} .filter').keypress(function(event){
				formSubmit(event, 'submitFilterButton{$list_id}')
			})
		});
	</script>

	{* Display column names and arrows for ordering (ASC, DESC) *}
	{if $is_order_position}
		<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
		<script type="text/javascript">
			var token = '{$token}';
			var come_from = '{$list_id}';
			var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
	{/if}
	<script type="text/javascript">
		$(function() {
			if ($("table.{$list_id} .datepicker").length > 0)
				$("table.{$list_id} .datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
		});
	</script>
{/if}

{if !$simple_header}
<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

{block name="override_header"}{/block}

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
	{block name="override_form_extra"}{/block}
	<fieldset class="col-lg-12">
		<div class="panel-heading">
			{if is_array($title)}{$title|end}{else}{$title}{/if}
			{if isset($toolbar_btn) && count($toolbar_btn) >0}
			<span class="panel-heading-action">
			{foreach from=$toolbar_btn item=btn key=k}
				<a id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="list-tooolbar-btn" {if isset($btn.href)}href="{$btn.href}"{/if} title="{$btn.desc}" {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
					<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i>
				</a>
				{if $k == 'modules-list'}
				<div class="modal fade" id="modules_list_container">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title">{l s='Modules'}</h3>
							</div>
							<div class="modal-body">
								<div id="modules_list_container_tab" style="display:none;"></div>
								<div id="modules_list_loader"><img src="../img/loader.gif" alt=""/></div>
							</div>
						</div>
					</div>
				</div>
				{/if}
			{/foreach}
				<a id="desc-{$table}-refresh" class="list-tooolbar-btn" href="javascript:location.reload();" title="{l s='refresh'}">
					<i class="process-icon-refresh" ></i>
				</a>
			</span>
			{/if}
			{if $show_toolbar}
			<script language="javascript" type="text/javascript">
			//<![CDATA[
				var submited = false
				var modules_list_loaded = false;
				$(function() {
					//get reference on save link
					btn_save = $('i[class~="process-icon-save"]').parent();

					//get reference on form submit button
					btn_submit = $('#{$table}_form_submit_btn');

					if (btn_save.length > 0 && btn_submit.length > 0)
					{
						//get reference on save and stay link
						btn_save_and_stay = $('i[class~="process-icon-save-and-stay"]').parent();

						//get reference on current save link label
						lbl_save = $('#desc-{$table}-save div');

						//override save link label with submit button value
						if (btn_submit.val().length > 0)
							lbl_save.html(btn_submit.attr("value"));

						if (btn_save_and_stay.length > 0)
						{

							//get reference on current save link label
							lbl_save_and_stay = $('#desc-{$table}-save-and-stay div');

							//override save and stay link label with submit button value
							if (btn_submit.val().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
							{
								lbl_save_and_stay.html(btn_submit.val() + " {l s='and stay'} ");
							}

						}

						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#{$table}_form').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea')
								$('#desc-{$table}-save').click();
						});
						//submit the form
						{block name=formSubmit}
							btn_save.click(function() {
								// Avoid double click
								if (submited)
									return false;
								submited = true;
								
								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

								$('#{$table}_form').submit();
								return false;
							});

							if (btn_save_and_stay)
							{
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');

									$('#{$table}_form').submit();
									return false;
								});
							}
						{/block}
					}
					{if isset($tab_modules_open)}
						if ({$tab_modules_open})
							openModulesList();
					{/if}
				});
				{if isset($tab_modules_list)}
				$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
					console.log('ok');
					openModulesList();
				});
				
				function openModulesList()
				{
					$('#modules_list_container').modal('show');
					if (!modules_list_loaded)
					{
						$.ajax({
							type: "POST",
							url : '{$admin_module_ajax_url}',
							async: true,
							data : {
								ajax : "1",
								controller : "AdminModules",
								action : "getTabModulesList",
								tab_modules_list : '{$tab_modules_list}',
								back_tab_modules_list : '{$back_tab_modules_list}'
							},
							success : function(data)
							{
								$('#modules_list_container_tab').html(data).slideDown();
								$('#modules_list_loader').hide();
								modules_list_loaded = true;
							}
						});
					}
					else
					{
						$('#modules_list_container_tab').slideDown();
						$('#modules_list_loader').hide();
					}
					return false;
				}
				{/if}
			//]]>
		</script>
		{/if}
		</div>
		{if $show_filters}
		<input type="hidden" id="submitFilter{$list_id}" name="submitFilter{$list_id}" value="0"/>
		<span class="pull-right">
			<button type="submit" name="submitReset{$list_id}" class="btn {if !$filters_has_value}btn-default{else}btn-primary{/if} btn-small"{if !$filters_has_value} disabled="disabled"{/if}>
				<i class="icon-eraser"></i> {l s='Reset'}
			</button>
			<button type="submit" id="submitFilterButton{$list_id}" name="submitFilter" class="btn btn-default btn-small" />
				<i class="icon-filter"></i> {l s='Filter'}
			</button>
		</span>
		{/if}
{/if}
	<div class="table-responsive">
		<table 
			class="table"
			name="list_table"
			{if $table_id} id={$table_id}{/if}
			class="table {if $table_dnd}tableDnD{/if} {$table}"
			>
			<thead>
				<tr class="nodrag nodrop">
					<th class="center">
					</th>
					{foreach $fields_display AS $key => $params}
					<th {if isset($params.align)} class="{$params.align}"{/if}>

						<span class="title_box {if isset($order_by) && ($key == $order_by)} active{/if}">

							{if isset($params.hint)}
							<span class="label-tooltip" data-toggle="tooltip"
								title="
									{if is_array($params.hint)}
										{foreach $params.hint as $hint}
											{if is_array($hint)}
												{$hint.text}
											{else}
												{$hint}
											{/if}
										{/foreach}
									{else}
										{$params.hint}
									{/if}
								">
								{$params.title}
							</span>
							{else}
								{$params.title}
							{/if}
							
							{if (!isset($params.orderby) || $params.orderby) && !$simple_header}
							<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}class="active"{/if}  href="{$currentIndex}&{$list_id}Orderby={$key|urlencode}&{$list_id}Orderway=desc&token={$token}{if isset($smarty.get.$identifier)}&{$identifier}={$smarty.get.$identifier|intval}{/if}">
								<i class="icon-caret-down"></i>
							</a>
							<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}class="active"{/if} href="{$currentIndex}&{$list_id}Orderby={$key|urlencode}&{$list_id}Orderway=asc&token={$token}{if isset($smarty.get.$identifier)}&{$identifier}={$smarty.get.$identifier|intval}{/if}">
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
			{if !$simple_header && $show_filters}
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
								<select class="filter" onchange="$('#submitFilterButton{$list_id}').focus();$('#submitFilterButton{$list_id}').click();" name="{$list_id}Filter_{$key}">
									<option value="">--</option>
									<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes'}</option>
									<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No'}</option>
								</select>
							{elseif $params.type == 'date' || $params.type == 'datetime'}
								{l s='From'} <input type="text" class="filter datepicker" id="{$params.id_date}_0" name="{$params.name_date}[0]" value="{if isset($params.value.0)}{$params.value.0}{/if}"{if isset($params.width)} style="width:70px"{/if}/>
								{l s='To'} <input type="text" class="filter datepicker" id="{$params.id_date}_1" name="{$params.name_date}[1]" value="{if isset($params.value.1)}{$params.value.1}{/if}"{if isset($params.width)} style="width:70px"{/if}/>
							{elseif $params.type == 'select'}
								{if isset($params.filter_key)}
									<select class="filter" onchange="$('#submitFilterButton{$list_id}').focus();$('#submitFilterButton{$list_id}').click();" name="{$list_id}Filter_{$params.filter_key}" {if isset($params.width)} style="width:{$params.width}px"{/if}>
										<option value="" {if $params.value == ''} selected="selected" {/if}>--</option>
										{if isset($params.list) && is_array($params.list)}
											{foreach $params.list AS $option_value => $option_display}
												<option value="{$option_value}" {if $option_display == $params.value ||  $option_value == $params.value} selected="selected"{/if}>{$option_display}</option>
											{/foreach}
										{/if}
									</select>
								{/if}
							{else}
								<input type="text" class="filter" name="{$list_id}Filter_{if isset($params.filter_key)}{$params.filter_key}{else}{$key}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width}px"{/if} />
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
