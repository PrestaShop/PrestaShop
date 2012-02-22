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
{debug}
{block name="label"}

	{if $input.type == 'text' && $input.name == 'name'}
		<div class="hint" name="help_box" style="display:block;">{l s='You can\'t change the GroupShop when you have more than one Shop'}</div><br />
	{/if}

	{if isset($input.label)}
		<label>{$input.label} </label>
	{/if}

{/block}

{block name="start_field_block"}
	<div class="margin-form">
	{if $input.type == 'select' && $input.name == 'id_category'}
		<script type="text/javascript">
			$(document).ready(function(){
				$("#id_category").change(function(){
					doAdminAjax(
						{
						ajax:"1",
						id_category : $(this).val(),
						use_shop_context : 0,
						action : "getCategoriesFromRootCategory",
						controller: "AdminShop",
						token : "{$token}",
						},
						function(res)
						{
							$('#categories-treeview').parent().html(res);
						}
					);
				});
			});
		</script>
	{else if $input.type == 'theme'}
		{foreach $input.values as $theme}
			<div class="select_theme {if $theme->id == $fields_value.id_theme_checked}select_theme_choice{/if}" onclick="$(this).find('input').attr('checked', true); $('.select_theme').removeClass('select_theme_choice'); $(this).toggleClass('select_theme_choice');">
				{$theme->name}<br />
				<img src="../themes/{$theme->directory}/preview.jpg" alt="{$theme->directory}" /><br />
				<input type="radio" name="id_theme" value="{$theme->id}" {if $theme->id == $fields_value.id_theme_checked}checked="checked"{/if} />
			</div>
		{/foreach}
		<div class="clear">&nbsp;</div>
	{/if}
	{if $input.type == 'textGroupShop'}
		{$input.value}
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
				{if $key == 'checkbox'}
					<div class="margin-form">
						<label><input type="{$field.type}" value="{$field.value}" name="{$field.name}" id="{$field.name}" {if $checked} checked="checked"{/if}/> {$field.label}</label>
				{elseif $key == 'select'}
						<select name="{$field.name}" id="{$field.name}">
							{foreach $field.options.query AS $key => $option}
								<option value="{$key}" {if $key == $defaultShop}selected="selected"{/if}>
									{$option.name}
								</option>
							{/foreach}
						</select>
				{elseif $key == 'allcheckbox'}
						<div id="importList" style="clear:both;{if !$checked}display:none{/if}">
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

{block name=script}

	$(document).ready(function() {
		$('#useImportData').click(function() {
			$('#importList').slideToggle('slow');
		});
	});

{/block}
