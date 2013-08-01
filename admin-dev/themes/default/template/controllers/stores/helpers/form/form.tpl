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
			value="{$fields_value[$input.name]|escape:'htmlall'}" /> /
		<input type="text"
			{if isset($input.size)}size="{$input.size}"{/if}
			{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
			name="longitude"
			id="longitude"
			value="{$fields_value['longitude']|escape:'htmlall'}" />
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="other_input"}
	{if $key == 'rightCols'}
		{foreach $field as $input}
			{if $input.type == 'file'}
				<div class="row">
					<label class="control-label col-lg-3">{$input.label} </label>
					<div class="col-lg-6">
						<input type="file" name="{$input.name}" />
						<p>{$input.desc}</p>
						{if isset($fields_value.image) && $fields_value.image}
							<div id="image">
								{$fields_value.image}
								<p>{l s='File size'} {$fields_value.size}kb</p>
								<a href="{$current}&id_store={$form_id}&token={$token}&deleteImage=1" class="btn btn-primary">
									<i class="icon-remove"></i>
									{l s='Delete'}
								</a>
							</div>
						{/if}
					</div>
				</div>
			{/if}
			<div class="row">
				<table class="table">
					<thead>
						<tr>
							<th>{l s='Hours:'}</th>
							<th>{l s='e.g. 10:00AM - 9:30PM'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $fields_value.days as $k => $value}
							<tr style="color: #7F7F7F; font-size: 0.85em;">
								<td>{$value}</td>
								<td><input type="text" size="25" name="hours_{$k}" value="{if isset($fields_value.hours[$k-1])}{$fields_value.hours[$k-1]|escape:'htmlall'}{/if}" /><br /></td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{/foreach}
	{/if}
{/block}

