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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $firstCall}
	<script type="text/javascript">
		var vat_number = {$vat_number};
		var module_dir = '{$module_dir}';
		var id_language = {$defaultFormLanguage};
		var languages = new Array();
		var employeePage = true;

		$(document).ready(function() {ldelim}
			{foreach $languages as $k => $language}
				languages[{$k}] = {ldelim}
					id_lang: {$language.id_lang},
					iso_code: '{$language.iso_code}',
					name: '{$language.name}'
				{rdelim};
			{/foreach}
			displayFlags(languages, id_language, {$allowEmployeeFormLang});

			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{ldelim}
					ajaxStates({$fields_value.id_state});
					$('#id_country').change(function() {ldelim}
						ajaxStates();
					{rdelim});
				{rdelim}
			{/if}
		{rdelim});
	</script>
	<script type="text/javascript" src="../js/form.js"></script>
{/if}
{if isset($fields.title)}<h2>{$fields.title}</h2>{/if}
<form action="{$current}&{$submit_action}=1&token={$token}" method="post" enctype="multipart/form-data">
	{if $form_id}
		<input type="hidden" name="id_{$table}" value="{$form_id}" />
	{/if}
	<fieldset>
		{foreach $fields as $key => $field}
			{if $key == 'legend'}
				<legend>
					{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
					{$field.title}
				</legend>
			{elseif $key == 'input'}
				{foreach $field as $input}
					{if $input.name == 'id_state'}
						<div id="contains_states" {if $contains_states}style="display:none;"{/if}>
					{/if}
					{if isset($input.label)}
					<label>{$input.label} </label>
					{/if}
					{if $input.type == 'hidden'}
						<input type="hidden" name="{$input.name}" value="{$fields_value[$input.name]}" />
					{else}
					<div class="margin-form">
						{if $input.type == 'text'}
							{if isset($input.lang) && isset($input.attributeLang)}
								{foreach $languages as $language}
									<div id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
										<input type="text"
												name="{$input.name}_{$language.id_lang}"
												value="{$fields_value[$input.name][$language.id_lang]}"
												{if isset($input.size)}size="{$input.size}"{/if}
												{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
												{if isset($input.class)}class="{$input.class}"{/if}
												{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
												{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
										{if isset($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
									</div>
								{/foreach}
								{if count($languages) > 1}
									<div class="displayed_flag">
										<img src="../img/l/{$defaultFormLanguage}.jpg"
											class="pointer"
											id="language_current_{$input.name}"
											onclick="toggleLanguageFlags(this);" />
									</div>
									<div id="languages_{$input.name}" class="language_flags">
										{l s='Choose language:'}<br /><br />
										{foreach $languages as $language}
												<img src="../img/l/{$language.id_lang}.jpg"
													class="pointer"
													alt="{$language.name}"
													title="{$language.name}"
													onclick="changeLanguage('{$input.name}', '{$input.attributeLang}', {$language.id_lang}, '{$language.iso_code}');" />
										{/foreach}
									</div>
								{/if}
							{else}
								<input type="text"
										name="{$input.name}"
										id="{$input.name}"
										value="{$fields_value[$input.name]}"
										{if isset($input.size)}size="{$input.size}"{/if}
										{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
										{if isset($input.class)}class="{$input.class}"{/if}
										{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
										{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
								{if isset($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
							{/if}
						{elseif $input.type == 'select'}
							<select name="{$input.name}" id="{$input.name}" {if isset($input.multiple)}multiple="multiple" {/if}{if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
								{if isset($input.options.default)}
									<option value="{$input.options.default.value}">{$input.options.default.label}</option>
								{/if}
								{if isset($input.options.optiongroup)}
									{foreach $input.options.optiongroup.query AS $optiongroup}
										<optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
											{foreach $optiongroup[$input.options.options.query] as $option}
												<option value="{$option[$input.options.options.id]}"
													{if isset($input.multiple)}
														{foreach $fields_value[$input.name] as $field_value}
															{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
														{/foreach}
													{else}
														{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
													{/if}
												>{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</optgroup>
									{/foreach}
								{else}
									{foreach $input.options.query AS $option}
										{if $input.name == 'bo_theme'}
											<option value="{$option}"
												{if isset($input.multiple)}
													{foreach $fields_value[$input.name] as $field_value}
														{$field_value}
														{if $field_value == $option}selected="selected"{/if}
													{/foreach}
												{else}
													{if $fields_value[$input.name] == $option}selected="selected"{/if}
												{/if}
											>{$option|escape:'htmlall':'UTF-8'}</option>
										{else}
											<option value="{$option[$input.options.id]}"
												{if isset($input.multiple)}
													{foreach $fields_value[$input.name] as $field_value}
														{$field_value}
														{if $field_value == $option[$input.options.id]}selected="selected"{/if}
													{/foreach}
												{else}
													{if $fields_value[$input.name] == $option[$input.options.id]}selected="selected"{/if}
												{/if}
											>{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
										{/if}
									{/foreach}
								{/if}
							</select>
							{if isset($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
						{elseif $input.type == 'radio'}
							{foreach $input.values as $value}
								<input type="radio"
										name="{$input.name}"
										id="{$value.id}"
										value="{$value.value|escape:'htmlall':'UTF-8'}"
										{if $fields_value[$input.name] == $value.value}checked="checked"{/if} />
								<label {if isset($input.class)}class="{$input.class}"{/if} for="{$value.id}">
								 {if isset($input.is_bool) && $input.is_bool == true}
								 	{if $value.value == 1}
								 		<img src="../img/admin/enabled.gif" alt="{$value.label}" title="{$value.label}" />
								 	{else}
								 		<img src="../img/admin/disabled.gif" alt="{$value.label}" title="{$value.label}" />
								 	{/if}
								 {else}
								 	{$value.label}
								 {/if}
								</label>
								{if isset($input.br) && $input.br}<br />{/if}
							{/foreach}
						{elseif $input.type == 'textarea'}
							{if isset($input.lang) && isset($input.attributeLang)}
								{foreach $languages as $language}
									<div id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
										<textarea cols="{$input.cols}" rows="{$input.rows}" name="{$input.name}_{$language.id_lang}">{$fields_value[$input.name][$language.id_lang]}</textarea>
									</div>
								{/foreach}
								{if count($languages) > 1}
									<div class="displayed_flag">
										<img src="../img/l/{$defaultFormLanguage}.jpg"
											class="pointer"
											id="language_current_{$input.name}"
											onclick="toggleLanguageFlags(this);" />
									</div>
									<div id="languages_{$input.name}" class="language_flags">
										{l s='Choose language:'}<br /><br />
										{foreach $languages as $language}
												<img src="../img/l/{$language.id_lang}.jpg"
													class="pointer"
													alt="{$language.name}"
													title="{$language.name}"
													onclick="changeLanguage('{$input.name}', '{$input.attributeLang}', {$language.id_lang}, '{$language.iso_code}');" />
										{/foreach}
									</div>
								{/if}
							{else}
								<textarea name="{$input.name}" id="{$input.name}" cols="{$input.cols}" rows="{$input.rows}">{$fields_value[$input.name]}</textarea>
							{/if}
						{elseif $input.type == 'checkbox'}
							{foreach $input.values.query as $value}
								{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
								<input type="checkbox" name="{$id_checkbox}" id="{$id_checkbox}" {if $fields_value[$id_checkbox]}checked="checked"{/if} />
								<label for="{$id_checkbox}" class="t"><strong>{$value[$input.values.name]}</strong></label><br />
							{/foreach}
						{elseif $input.type == 'file'}
							<input type="file" name="{$input.name}" />
							<img src="{$fields_value[$input.name]}" />
						{elseif $input.type == 'password'}
							<input type="password"
									name="{$input.name}"
									size="{$input.size}"
									value="" />
						{elseif $input.type == 'group'}
							{assign var=groups value=$input.values}
							{include file='helper/form/form_group.tpl'}
						{elseif $input.type == 'shop' OR $input.type == 'group_shop'}
							<script type="text/javascript">
								$(document).ready(function(){
									$('select[name=id_profile]').change(function(){
										ifSuperAdmin($(this));
									});
		
									ifSuperAdmin($('select[name=id_profile]'));
								});
		
								function ifSuperAdmin(el)
								{
									var val = $(el).val();
									if(val == {$_PS_ADMIN_PROFILE_})
									{
										$('.assoShop input[type=checkbox]').attr('disabled', 'disabled');
										$('.assoShop input[type=checkbox]').attr('checked', 'checked');
									}
									else
										$('.assoShop input[type=checkbox]').attr('disabled', '');
								}
							</script>
							{include file='helper/form/form_shop.tpl'}
						{elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
							<label>{l s='Shop association:'}</label>
							<div class="margin-form">
								{$asso_shop}
							</div>
						{elseif $input.type == 'color'}
							<script type="text/javascript" src="../js/jquery/jquery-colorpicker.js"></script>
							<input type="color"
								size="{$input.size}"
								data-hex="true"
								class="{$input.class}"
								name="{$input.name}"
								value="{$fields_value[$input.name]}" />
						{/if}
						{if isset($input.required) && $input.required} <sup>*</sup>{/if}
						{if isset($input.p)}
							<p class="clear">
								{if is_array($input.p)}
									{foreach $input.p as $p}
										{if is_array($p)}
											<span id="{$p.id}">{$p.text}</span><br />
										{else}
											{$p}<br />
										{/if}
									{/foreach}
								{else}
									{$input.p}
								{/if}
							</p>
						{/if}
						{if isset($languages)}<div class="clear"></div>{/if}
					</div>
					{/if}
					{if $input.name == 'id_state'}
						</div>
					{/if}
				{/foreach}
			{elseif $key == 'submit'}
				<div class="margin-form">
					<input type="submit" value="{$field.title}" name="{$submit_action}" {if isset($field.class)}class="{$field.class}"{/if} />
				</div>
			{/if}
		{/foreach}
		{if $required_fields}
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		{/if}
	</fieldset>
</form>

<br /><br />
{if $firstCall && !$no_back}
	{if $back}
		<a href="{$back}"><img src="../img/admin/arrow2.gif" />{l s='Back'}</a>
	{else}
		<a href="{$current}&token={$token}"><img src="../img/admin/arrow2.gif" />{l s='Back to list'}</a>
	{/if}
	<br />
{/if}
