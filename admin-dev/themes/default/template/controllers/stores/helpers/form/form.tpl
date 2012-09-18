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
{extends file="helpers/form/form.tpl"}

{block name=script}
	$(document).ready(function() {
		$('#latitude, #longitude').keyup(function() {
			$(this).val($(this).val().replace(/,/g, '.'));
		});
	});
{/block}

{block name="input"}
	{if $input.type == 'latitude'}
		<input type="text"
			{if isset($input.size)}size="{$input.size}"{/if}
			{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
			name="latitude"
			id="latitude"
			value="{$fields_value[$input.name]|htmlentities}" /> /
		<input type="text"
			{if isset($input.size)}size="{$input.size}"{/if}
			{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
			name="longitude"
			id="longitude"
			value="{$fields_value['longitude']|htmlentities}" />
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="other_input"}
	{if $key == 'rightCols'}
		{foreach $field as $input}
			{if $input.type == 'file'}
				<label style="text-align: left; width: inherit;width:250px;text-align:right">{$input.label} </label>
				<div class="margin-form">
					<input type="file" name="{$input.name}" />
					<p class="clear">{$input.desc}</p>
					{if isset($fields_value.image) && $fields_value.image}
						<div id="image" style="width:370px;">
							{$fields_value.image}
							<p align="center">{l s='File size'} {$fields_value.size}kb</p>
							<a href="{$current}&id_store={$form_id}&token={$token}&deleteImage=1">
								<img src="../img/admin/delete.gif" alt="{l s='Delete'}" /> {l s='Delete'}
							</a>
						</div>
					{/if}
				</div>
			{/if}
			<table cellpadding="2" cellspacing="2" style="padding: 10px; margin: 15px 0 20px 260px; border: 1px solid #BBB;">
				<tr>
					<th colspan="2">{l s='Hours:'}</th>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="font-size: 0.85em;">{l s='e.g. 10:00AM - 9:30PM'}</td>
				</tr>

				{foreach $fields_value.days as $k => $value}
					<tr style="color: #7F7F7F; font-size: 0.85em;">
						<td>{$value}</td>
						<td><input type="text" size="25" name="hours_{$k}" value="{if isset($fields_value.hours[$k-1])}{$fields_value.hours[$k-1]|htmlentities}{/if}" /><br /></td>
					</tr>
				{/foreach}
			</table>
		<div class="clear"></div>
		{/foreach}
	{/if}
{/block}

