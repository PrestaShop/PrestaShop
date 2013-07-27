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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	<script type="text/javascript">
		var errorEmpty = "{l s='Please name your matching configuration to save.'}"
		var token = '{$token}';
	</script>
	<div id="container-customer">
	<h2>{l s='View your data'}</h2>
	<div>
		<b>{l s='Save and load your configuration for importing files'} : </b><br><br>
		<input type="text" name="newImportMatchs" id="newImportMatchs">
		<a id="saveImportMatchs" class="button" href="#">{l s='Save'}</a><br><br>
		<div id="selectDivImportMatchs" {if !$import_matchs}style="display:none"{/if}>
			<select id="valueImportMatchs">
				{foreach $import_matchs as $match}
					<option id="{$match.id_import_match}" value="{$match.match}">{$match.name}</option>
				{/foreach}
			</select>
			<a class="button" id="loadImportMatchs" href="#">{l s='Load'}</a>
			<a class="button" id="deleteImportMatchs" href="#">{l s='Delete'}</a>
		</div>
	</div>

	<h3>{l s='Please set the value type of each column'}</h3>

	<div id="error_duplicate_type" class="warning warn" style="display:none;">
		<h3>{l s='Columns cannot have the same value type'}</h3>
	</div>

	<div id="required_column" class="warning warn" style="display:none;">
		<h3>{l s='Column'} <span id="missing_column">&nbsp;</span> {l s='must be set'}</h3>
	</div>

	<form action="{$current}&token={$token}" method="post" id="import_form" name="import_form">
		{l s='Skip'} <input type="text" size="2" name="skip" value="0" /> {l s='lines'}
		<input type="hidden" name="csv" value="{$fields_value.csv}" />
		<input type="hidden" name="convert" value="{$fields_value.convert}" />
		<input type="hidden" name="entity" value="{$fields_value.entity}" />
		<input type="hidden" name="iso_lang" value="{$fields_value.iso_lang}" />
		{if $fields_value.truncate}
			<input type="hidden" name="truncate" value="1" />
		{/if}
		{if $fields_value.forceIDs}
			<input type="hidden" name="forceIDs" value="1" />
		{/if}
		{if $fields_value.match_ref}
			<input type="hidden" name="match_ref" value="1" />
		{/if}
		<input type="hidden" name="separator" value="{$fields_value.separator}">
		<input type="hidden" name="multiple_value_separator" value="{$fields_value.multiple_value_separator}">
		<script type="text/javascript">
			var current = 0;

			function showTable(nb)
			{
				getE('btn_left').disabled = null;
				getE('btn_right').disabled = null;
				if (nb <= 0)
				{
					nb = 0;
					getE('btn_left').disabled = 'true';
				}
				if (nb >= {$nb_table} - 1)
				{
					nb = {$nb_table} - 1;
					getE('btn_right').disabled = 'true';
				}
				toggle(getE('table'+current), false);
				current = nb;
				toggle(getE('table'+current), true);
			}

			$(function() {
				var btn_save_import = $('span[class~="process-icon-save-import"]').parent();
				var btn_submit_import = $('#import');

				if (btn_save_import.length > 0 && btn_submit_import.length > 0)
				{
					btn_submit_import.hide();
					btn_save_import.find('span').removeClass('process-icon-save-import');
					btn_save_import.find('span').addClass('process-icon-save');
					btn_save_import.click(function() {
						btn_submit_import.before('<input type="hidden" name="'+btn_submit_import.attr("name")+'" value="1" />');

						$('#import_form').submit();
					});
				}
			});

		</script>

		<table>
			<tr>
				<td colspan="3" align="center">
					<input name="import" type="submit" onclick="return (validateImportation(new Array({$res})));" id="import" value="{l s='Import .CSV data'}" class="button" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<input id="btn_left" value="{l s='<<'}" type="button" class="button" onclick="showTable(current - 1)" />
				</td>
				<td align="left">
					{section name=nb_i start=0 loop=$nb_table step=1}
						{assign var=i value=$smarty.section.nb_i.index}
						{$data.$i}
					{/section}
				</td>
				<td valign="top" align="center">
					<input id="btn_right" value="{l s='>>'}" type="button" class="button" onclick="showTable(current + 1)" />
				</td>
			</tr>
		</table>
		<script type="text/javascript">showTable(current);</script>
	</form>
	</div>
{/block}

