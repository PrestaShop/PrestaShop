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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	
	function validateInputDate(input, displayError)
	{
		{literal}
		dateRegex = /^\d{4}-\d{1,2}-\d{1,2}$/
  	{/literal}
			
  	if (!input.val().match(dateRegex))
  	{
			input.parent().find('span.input-error').fadeIn('fast');
			return false;
		}
		input.parent().find('span.input-error').css('display','none');
		return true;
	}
	
	function validateAccountingForm()
	{
		validation = true;
		
		$('span.input-error').css('display', 'none');
		$('.datepicker:visible').each(function() {
			if (!(validateInputDate($(this), true)))
				validation = false;
		});
    
		return validation;
	}
	
	$(document).ready(function() {
		
		$('.datepicker').each(function() {
			$(this).change(function() {
				validateInputDate($(this), true);
			});
			$(this).datepicker({
	     prevText: '',
	     nextText: '',
	     dateFormat: 'yy-mm-dd'
	    });			
		});
    
    $('.formAccountingExport form input[type="submit"]').each(function()
    {
    	$(this).click(function() {
    		return validateAccountingForm();
    	});
    });
	});
</script>

<div id="export_menu">
	<div class="toolbarBox">
		<div class="pageTitle">
			<h3>
				<span id="current_obj" style="font-weight: normal;">
					{l s='Accounting Export:'}
				</span>
			</h3>
		</div>
	</div>
</div>

{foreach from=$preventList key=name item=preventType}
	{if !empty($preventType)}
		<div class="{$name}">
			<ul>
				{foreach from=$preventType item=translationPrevent}
					<li>{$translationPrevent}</li>
				{/foreach}
				{if $already_generated}
					<li>
						{l s='Generate it:'}
						<a href="{$smarty.server.REQUEST_URI}&begin_date={$begin_date}&end_date={$end_date}&type={$export_type}&format={$file_format}&regenerate={$already_generated}&accounting_export=true">
							{l s='Start export'}
						</a>
					<li>
				{/if}
			</ul>
		</div>
	{/if}
{/foreach}

<div id="account_list">
	<form id="{$table}_form" method="POST" action="{$smarty.server.REQUEST_URI}">
		<label for="beginDate">{l s='Begin to:'}</label>
		<div class="margin-form">
			<input class="datepicker" id="begin_date" type="text" name="begin_date" value="{$begin_date}" />
			<span class="input-error">{l s='The date has not the right format'}</span>
		</div>

		<label for="endDate">{l s='End to:'}</label>
		<div class="margin-form">
			<input class="datepicker" id="end_date" type="text" name="end_date" value="{$end_date}" />
			<span class="input-error">{l s='The date has not the right format'}</span>
		</div>

		<label for="format">{l s='File format:'}</label>
		<div class="margin-form">
			<select id="format" name="format">
				<option value="">{l s='Choose a format'}</option>
				<option value="csv" {if $file_format == 'csv'} selected="selected"{/if}>
					{l s='Excel (CSV)'}
				</option>
				<option value="txt" {if $file_format == 'txt'} selected="selected"{/if}>
					{l s='Text (TXT)'}
				</option>
			</select>
			<span class="input-error">{l s='The date has not the right format'}</span>
		</div>

		<label for="type">{l s='Export Type:'}</label>
		<div class="margin-form">
			<select id="type" name="type">
				<option value="">{l s='Choose export type'}</option>
				<option value="accounting_export" {if $export_type == 'accounting_export'} selected="selected"{/if}>
					{l s='Accounting export'}
				</option>
				<option value="reconciliation_export" {if $export_type == 'reconciliation_export'} selected="selected"{/if}>
					{l s='Reconciliation'}
				</option>
			</select>
			<span class="input-error">{l s='The date has not the right format'}</span>
		</div>

		<div class="margin-form">
			<input type="submit" class="button" id="{$table}_form_submit_btn" name="accounting_export" value="{l s='Launch export'}"/>
		</div>

	</form>
</div>

<div id="account_list" style="margin-top:10px;">
	<h3>{l s='History'}</h3>
	{if $exported_list|count}
		<table class="table" style="width:100%;">
			<thead>
				<th>{l s='Export type'}</th>
				<th>{l s='Date start to'}</th>
				<th>{l s='Date end to'}</th>
				<th>{l s='File'}</th>
			</thead>
			<tbody>
				{foreach from=$exported_list item=export_detail}
					<tr>
						<td>{$export_detail['title']}</td>
						<td>{$export_detail['begin_to']}</td>
						<td>{$export_detail['end_to']}</td>
						<td>
							<a href="{$smarty.server.REQUEST_URI}&download={$export_detail['file']}">
								{l s='Download'}
								<img src="../img/admin/{if $export_detail['file']|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'csv'}excel_file.png{else}page_white_text.png{/if}" />
							<a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{l s='No exported data found'}
	{/if}
</div>
