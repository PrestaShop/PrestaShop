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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name=script}

	function toggleShareOrders() {

		var disabled_customer = ($('#share_customer_on').attr('checked')) ? false : true;
		var disabled_stock = ($('#share_stock_on').attr('checked')) ? false : true;

		if (disabled_customer || disabled_stock)
		{
			$("input[name=share_order]").each(function(i) {
	            $(this).attr('disabled', 'disabled');
	        });

			$('#share_order_off').attr('checked', true);
		}
		else
		{
			$('input[name=share_order]').attr('disabled', '');
		}
	}

	$(document).ready(function() {
		toggleShareOrders();
		$('input[name=share_customer]').click(function()
		{
			toggleShareOrders();
		});
		$('input[name=share_stock]').click(function()
		{
			toggleShareOrders();
		});

		$('#useImportData').click(function() {
			$('#importList').slideToggle('slow');
		});
	});

{/block}

{block name="label"}

	{if $input.type == 'text' && $input.name == 'name'}
		<div class="hint" name="help_box" style="display:block;">{l s='You can\'t edit GroupShop when you have more than one Shop'}</div><br />
	{/if}

	{if isset($input.label)}
		<label>{$input.label} </label>
	{/if}

{/block}

{block name="other_fieldsets"}
	{if isset($form_import)}
		<br /><br />
		<fieldset>
			{foreach $form_import as $key => $field}
				{if $key == 'legend'}
					<legend>
						{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
						{$field.title}
					</legend>
				{elseif $key == 'label'}
					<label>{$field}</label>
				{/if}
				<div class="clear"></div>
				{if $key == 'checkbox'}
					<div class="margin-form">
						<label><input type="{$field.type}" value="{$field.value}" name="{$field.name}" id="{$field.name}" {if $checked} checked="checked"{/if}/> {$field.label}</label>
				{elseif $key == 'select'}
						<select name="{$field.name}" id="{$field.name}">
							{foreach $field.options.query AS $key => $option}
								<option value="{$key}" {if $key == $defaultGroup}selected="selected"{/if}>
									{$option.name}
								</option>
							{/foreach}
						</select>
				{elseif $key == 'allcheckbox'}
						<div id="importList" {if !$checked}style="display:none"{/if}>
							<ul>
								{foreach $field.values as $key => $label}
									<li><label><input type="checkbox" name="importData[{$key}]" checked="checked" /> {$label}</label></li>
								{/foreach}
							</ul>
						</div>
				{elseif $key == 'p'}
						<p>{$field}</p>
					</div>
				{elseif $key == 'submit'}
					<div class="margin-form">
						<input type="submit" value="{$field.title}" name="submitAdd{$table}" {if isset($field.class)}class="{$field.class}"{/if} />
					</div>
				{/if}
			{/foreach}
		</fieldset>
	{/if}
{/block}
