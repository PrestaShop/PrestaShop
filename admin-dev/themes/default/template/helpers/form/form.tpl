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
{if isset($fields.title)}<h3>{$fields.title}</h3>{/if}

{block name="defaultForm"}
<form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'htmlall':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table}_form{/if}{/if}" class="defaultForm {$name_controller} form-horizontal" action="{$current}&{if !empty($submit_action)}{$submit_action}=1{/if}&token={$token}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style}"{/if}>
	{if $form_id}
		<input type="hidden" name="{$identifier}" id="{$identifier}" value="{$form_id}" />
	{/if}
	{foreach $fields as $f => $fieldset}
		{block name="fieldset"}
		<div class="panel" id="fieldset_{$f}">
			{foreach $fieldset.form as $key => $field}
				{if $key == 'legend'}
					<h3>
						{if isset($field.image)}<img src="{$field.image}" alt="{$field.title|escape:'htmlall':'UTF-8'}" />{/if}
						{if isset($field.icon)}<i class="{$field.icon}"/></i>{/if}
						{$field.title}
					</h3>
				{elseif $key == 'description' && $field}
					<div class="alert alert-info">{$field}</div>
				{elseif $key == 'input'}
					{foreach $field as $input}
						{block name="input_row"}
						<div class="form-group {if $input.type == 'hidden'}hide{/if}" {if $input.name == 'id_state'}id="contains_states"{if !$contains_states}style="display:none;"{/if}{/if}>
						{if $input.type == 'hidden'}
							<input type="hidden" name="{$input.name}" id="{$input.name}" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
						{else}
							{block name="label"}
								{if isset($input.label)}
									<label for="{if isset($input.id)}{$input.id}{if isset($input.lang) AND $input.lang}_{$current_id_lang}{/if}{else}{$input.name}{if isset($input.lang) AND $input.lang}_{$current_id_lang}{/if}{/if}" class="control-label col-lg-3 {if isset($input.required) && $input.required && $input.type != 'radio'}required{/if}">
										{if isset($input.hint)}
										<span class="label-tooltip" data-toggle="tooltip" data-html="true"
											title="
												{if is_array($input.hint)}
													{foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text}
														{else}
															{$hint}
														{/if}
													{/foreach}
												{else}
													{$input.hint}
												{/if}
											">
										{/if}
										{$input.label}
										{if isset($input.hint)}
										</span>
										{/if}
									</label>
								{/if}
							{/block}

							{block name="field"}
								<div class="col-lg-9{if !isset($input.label)} col-lg-offset-3{/if}">
								{block name="input"}
								{if $input.type == 'text' || $input.type == 'tags'}
									{if isset($input.lang) AND $input.lang}
									{if $languages|count > 1}
									<div class="form-group">
									{/if}
									{foreach $languages as $language}
										{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
										{if $languages|count > 1}
										<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
											<div class="col-lg-9">
										{/if}
												{if $input.type == 'tags'}
													{literal}
														<script type="text/javascript">
															$().ready(function () {
																var input_id = '{/literal}{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}{literal}';
																$('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
																$({/literal}'#{$table}{literal}_form').submit( function() {
																	$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
																});
															});
														</script>
													{/literal}
												{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												<div class="input-group">
												{/if}
												{if isset($input.maxchar)}
												<span id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter" class="input-group-addon">
													<span class="text-count-down">{$input.maxchar}</span>
												</span>
												{/if}
												{if isset($input.prefix)}
													<span class="input-group-addon">
													  {$input.prefix}
													</span>
													{/if}
												<input type="text"
													id="{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"
													name="{$input.name}_{$language.id_lang}"
													class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
													value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
													onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
													{if isset($input.size)} size="{$input.size}"{/if}
													{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
													{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
													{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
													{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
													{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if} />
													{if isset($input.suffix)}
													<span class="input-group-addon">
													  {$input.suffix}
													</span>
													{/if}
												{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
												</div>
												{/if}
										{if $languages|count > 1}
											</div>
											<div class="col-lg-2">
												<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
													{$language.iso_code}
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
													{foreach from=$languages item=language}
													<li><a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a></li>
													{/foreach}
												</ul>
											</div>
										</div>
										{/if}
									{/foreach}
									{if isset($input.maxchar)}
									<script type="text/javascript">
									function countDown($source, $target) {
										var max = $source.attr("data-maxchar");
										$target.html(max-$source.val().length);

										$source.keyup(function(){
											$target.html(max-$source.val().length);
										});
									}

									$(document).ready(function(){
									{foreach from=$languages item=language}
										countDown($("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}"), $("#{if isset($input.id)}{$input.id}_{$language.id_lang}{else}{$input.name}_{$language.id_lang}{/if}_counter"));
									{/foreach}
									});
									</script>
									{/if}
									{if $languages|count > 1}
									</div>
									{/if}
									{else}
										{if $input.type == 'tags'}
											{literal}
											<script type="text/javascript">
												$().ready(function () {
													var input_id = '{/literal}{if isset($input.id)}{$input.id}{else}{$input.name}{/if}{literal}';
													$('#'+input_id).tagify();
													$('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag'}{literal}'});
													$({/literal}'#{$table}{literal}_form').submit( function() {
														$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
													});
												});
											</script>
											{/literal}
										{/if}
										{assign var='value_text' value=$fields_value[$input.name]}
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										<div class="input-group">
										{/if}
										{if isset($input.maxchar)}
										<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar}</span></span>
										{/if}
										{if isset($input.prefix)}
										<span class="input-group-addon">
										  {$input.prefix}
										</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
											class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.class)} class="{$input.class}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if} />
										{if isset($input.suffix)}
										<span class="input-group-addon">
										  {$input.suffix}
										</span>
										{/if}
										
										{if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
										</div>
										{/if}
										{if isset($input.maxchar)}
										<script type="text/javascript">
										function countDown($source, $target) {
											var max = $source.attr("data-maxchar");
											$target.html(max-$source.val().length);

											$source.keyup(function(){
												$target.html(max-$source.val().length);
											});
										}

										$(document).ready(function(){
											countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
										});
										</script>
										{/if}
									{/if}
								{elseif $input.type == 'textbutton'}
									{assign var='value_text' value=$fields_value[$input.name]}
									<div class="form-group">
										<div class="col-lg-9">
										{if isset($input.maxchar)}
										<div class="input-group">
											<span id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter" class="input-group-addon">
												<span class="text-count-down">{$input.maxchar}</span>
											</span>
										{/if}
										<input type="text"
											name="{$input.name}"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
											class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
											{if isset($input.size)} size="{$input.size}"{/if}
											{if isset($input.maxchar)} data-maxchar="{$input.maxchar}"{/if}
											{if isset($input.maxlength)} maxlength="{$input.maxlength}"{/if}
											{if isset($input.class)} class="{$input.class}"{/if}
											{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
											{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
											{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if} />
										{if isset($input.suffix)}{$input.suffix}{/if}
										{if isset($input.maxchar)}
										</div>
										{/if}
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']}{/if}{if isset($input.button.class)} {$input.button.class}{/if}"
											{foreach from=$input.button.attributes key=name item=value}
												{if $name|lower != 'class'}
												 {$name}="{$value}"
												{/if}
											{/foreach}
											>{$input.button.label}</button>
										</div>
									</div>
									{if isset($input.maxchar)}
									<script type="text/javascript">
									function countDown($source, $target) {
										var max = $source.attr("data-maxchar");
										$target.html(max-$source.val().length);

										$source.keyup(function(){
											$target.html(max-$source.val().length);
										});
									}

									$(document).ready(function(){
										countDown($("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"), $("#{if isset($input.id)}{$input.id}{else}{$input.name}{/if}_counter"));
									});
									</script>
									{/if}
								{elseif $input.type == 'select'}
									{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
										{$input.empty_message}
										{$input.required = false}
										{$input.desc = null}
									{else}
										<select name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}"
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												{if isset($input.multiple)}multiple="multiple" {/if}
												{if isset($input.size)}size="{$input.size}"{/if}
												{if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
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
															>{$option[$input.options.options.name]}</option>
														{/foreach}
													</optgroup>
												{/foreach}
											{else}
												{foreach $input.options.query AS $option}
													{if is_object($option)}
														<option value="{$option->$input.options.id}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option->$input.options.id}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option->$input.options.id}
																	selected="selected"
																{/if}
															{/if}
														>{$option->$input.options.name}</option>
													{elseif $option == "-"}
														<option value="">--</option>
													{else}
														<option value="{$option[$input.options.id]}"
															{if isset($input.multiple)}
																{foreach $fields_value[$input.name] as $field_value}
																	{if $field_value == $option[$input.options.id]}
																		selected="selected"
																	{/if}
																{/foreach}
															{else}
																{if $fields_value[$input.name] == $option[$input.options.id]}
																	selected="selected"
																{/if}
															{/if}
														>{$option[$input.options.name]}</option>

													{/if}
												{/foreach}
											{/if}
										</select>
									{/if}
								{elseif $input.type == 'radio'}
									{foreach $input.values as $value}
										<div class="radio {if isset($input.class)}"{$input.class}"{/if}">
											<label>
											<input type="radio"	name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'htmlall':'UTF-8'}"
												{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
												{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
												{$value.label}
											</label>
										</div>
										{if isset($value.p) && $value.p}<p class="help-block">{$value.p}</p>{/if}
									{/foreach}
								{elseif $input.type == 'switch'}
									<div class="row">
										<div class="input-group col-lg-2">
											<span class="switch prestashop-switch">
												{foreach $input.values as $value}
												<input
													type="radio"
													name="{$input.name}"
													{if $value.value == 1}
														id="{$input.name}_on"
													{else}
														id="{$input.name}_off"
													{/if}
													value="{$value.value}"
													{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
													{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
												/>
												<label
													class="radio"
													{if $value.value == 1}
														for="{$input.name}_on"
													{else}
														for="{$input.name}_off"
													{/if}
												>
													{if $value.value == 1}
														<i class="icon-check-sign color_success"></i> {l s='Yes'}
													{else}
														<i class="icon-ban-circle color_danger"></i> {l s='No'}
													{/if}
												</label>
												{/foreach}
												<span class="slide-button btn btn-default"></span>
											</span>
										</div>
									</div>


								{elseif $input.type == 'textarea'}
									{assign var=use_textarea_autosize value=true}
									{if isset($input.lang) AND $input.lang}
									{foreach $languages as $language}
									{if $languages|count > 1}
									<div class="form-group translatable-field lang-{$language.id_lang}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>

										<div class="col-lg-9">
									{/if}
											<textarea name="{$input.name}_{$language.id_lang}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class}{/if}{else}textarea-autosize{/if}" >{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
									{if $languages|count > 1}	
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
												{$language.iso_code}
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												{foreach from=$languages item=language}
												<li>
													<a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
												</li>
												{/foreach}
											</ul>
										</div>
									</div>
									{/if}
									{/foreach}

									{else}
										<textarea name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" cols="{$input.cols}" rows="{$input.rows}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class}{/if}{else}textarea-autosize{/if}">{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}</textarea>
									{/if}

								{elseif $input.type == 'checkbox'}
									{foreach $input.values.query as $value}
										{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
										<div class="checkbox">
											<label for="{$id_checkbox}">
												<input type="checkbox"
													name="{$id_checkbox}"
													id="{$id_checkbox}"
													class="{if isset($input.class)}{$input.class}{/if}"
													{if isset($value.val)}value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}
													{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
												{$value[$input.values.name]}
											</label>
										</div>
									{/foreach}
								{elseif $input.type == 'file'}
									{if isset($input.display_image) && $input.display_image}
										{if isset($fields_value[$input.name].image) && $fields_value[$input.name].image}
											<div id="image">
												{$fields_value[$input.name].image}
												<p>{l s='File size'} {$fields_value[$input.name].size}kb</p>
												<a class="btn btn-default" href="{$current}&{$identifier}={$form_id}&token={$token}&deleteImage=1">
													<i class="icon-trash"></i> {l s='Delete'}
												</a>
											</div>
										{/if}
									{/if}
									{if isset($input['thumb']) && $input['thumb']}
										<div class="row">
											<div class="col-lg-7">
												<img src="{$input['thumb']}" alt="{$input['name']}" title="{$input['name']}" />
												<br/><br/>
											</div>
										</div>
									{/if}
									<div class="row">
										<div class="col-lg-7">
											<input id="{$input.name}" type="file" name="{$input.name}" class="hide" />
											<div class="dummyfile input-group">
												<span class="input-group-addon"><i class="icon-file"></i></span>
												<input id="{$input.name}-name" type="text" class="disabled" name="filename" readonly />
												<span class="input-group-btn">
													<button id="{$input.name}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
														<i class="icon-folder-open"></i> {l s='Choose a file'}
													</button>
												</span>
											</div>
										</div>
									</div>

									<script>
										$(document).ready(function(){
											$('#{$input.name}-selectbutton').click(function(e){
												$('#{$input.name}').trigger('click');
											});
											$('#{$input.name}-name').click(function(e){
												$('#{$input.name}').trigger('click');
											});
											$('#{$input.name}').change(function(e){
												var val = $(this).val();
												var file = val.split(/[\\/]/);
												$('#{$input.name}-name').val(file[file.length-1]);
											});
										});
									</script>
								{elseif $input.type == 'password'}
									<input type="password"
											id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
											name="{$input.name}"
											class="{if isset($input.class)}{$input.class}{/if}"
											value=""
											{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
								{elseif $input.type == 'birthday'}
								<div class="form-group">
									{foreach $input.options as $key => $select}
									<div class="col-lg-4">
										<select name="{$key}" class="{if isset($input.class)}{$input.class}{/if}">
											<option value="">-</option>
											{if $key == 'months'}
												{*
													This comment is useful to the translator tools /!\ do not remove them
													{l s='January'}
													{l s='February'}
													{l s='March'}
													{l s='April'}
													{l s='May'}
													{l s='June'}
													{l s='July'}
													{l s='August'}
													{l s='September'}
													{l s='October'}
													{l s='November'}
													{l s='December'}
												*}
												{foreach $select as $k => $v}
													<option value="{$k}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v}</option>
												{/foreach}
											{else}
												{foreach $select as $v}
													<option value="{$v}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v}</option>
												{/foreach}
											{/if}
										</select>
									</div>
									{/foreach}
								</div>
								{elseif $input.type == 'group'}
									{assign var=groups value=$input.values}
									{include file='helpers/form/form_group.tpl'}
								{elseif $input.type == 'shop'}
									{$input.html}
								{elseif $input.type == 'categories'}
									{$categories_tree}
								{elseif $input.type == 'categories_select'}
									{$input.category_tree}
								{elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
										{$asso_shop}
								{elseif $input.type == 'color'}
								<div class="col-lg-2">
									<div class="row">
										<div class="input-group">
											<input type="color"
											data-hex="true"
											{if isset($input.class)}class="{$input.class}"
											{else}class="color mColorPickerInput"{/if}
											name="{$input.name}"
											value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
										</div>
									</div>
								</div>
								{elseif $input.type == 'date'}
									<div class="row">
										<div class="input-group col-lg-4">
											<input
												id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
												type="text"
												data-hex="true"
												{if isset($input.class)}class="{$input.class}"
												{else}class="datepicker"{/if}
												name="{$input.name}"
												value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
											<span class="input-group-addon">
												<i class="icon-calendar-empty"></i>
											</span>
										</div>
									</div>

								{elseif $input.type == 'free'}
									{$fields_value[$input.name]}
								{/if}
								{/block}{* end block input *}
								{block name="description"}
									{if isset($input.desc) && !empty($input.desc)}
										<p class="help-block">
											{if is_array($input.desc)}
												{foreach $input.desc as $p}
													{if is_array($p)}
														<span id="{$p.id}">{$p.text}</span><br />
													{else}
														{$p}<br />
													{/if}
												{/foreach}
											{else}
												{$input.desc}
											{/if}
										</p>
									{/if}
								{/block}
								</div>
							{/block}{* end block field *}
						{/if}
						</div>
						{/block}
					{/foreach}
					{hook h='displayAdminForm' fieldset=$f}
					{if isset($name_controller)}
						{capture name=hookName assign=hookName}display{$name_controller|ucfirst}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{elseif isset($smarty.get.controller)}
						{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}Form{/capture}
						{hook h=$hookName fieldset=$f}
					{/if}
				{elseif $key == 'submit'}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button
								type="submit"
								id="{if isset($field.id)}{$field.id}{else}{$table}_form_submit_btn{/if}"
								name="{if isset($field.name)}{$field.name}{else}{$submit_action}{/if}{if isset($field.stay) && $field.stay}AndStay{/if}"
								{if isset($field.class)}class="{$field.class}"{/if}
								>
								{if isset($field.icon)}<i class="{$field.icon}"></i>{/if} {$field.title}
							</button>
							{if isset($field.reset)}
							<button
								type="reset"
								id="{if isset($field.id)}{$field.id}{else}{$table}_form_reset_btn{/if}"
								class="{if isset($field.reset.class)}{$field.reset.class}{else}btn btn-default{/if}"
								>
								{if isset($field.reset.icon)}<i class="{$field.reset.icon}"></i>{/if} {$field.reset.title}
							</button>
							{/if}
						</div>
					</div>
				{elseif $key == 'desc'}
					<p class="clear">
						{if is_array($field)}
							{foreach $field as $k => $p}
								{if is_array($p)}
									<span id="{$p.id}">{$p.text}</span><br />
								{else}
									{$p}
									{if isset($field[$k+1])}<br />{/if}
								{/if}
							{/foreach}
						{else}
							{$field}
						{/if}
					</p>
				{/if}
				{block name="other_input"}{/block}
			{/foreach}
<!-- {*if $required_fields}
	<div class="small"><sup>*</sup> {l s='Required field'}</div>
{/if*} -->
		{block name="footer"}
		{include file="footer_toolbar.tpl"}
		{/block}
		</div>
		{/block}
		{block name="other_fieldsets"}{/block}
	{/foreach}
</form>
{/block}
{block name="after"}{/block}

{if isset($tinymce) && $tinymce}
	<script type="text/javascript">

	var iso = '{$iso}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';

	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"autoload_rte"
			});
		{/block}
	});
	</script>
{/if}
{if $firstCall}
	<script type="text/javascript">
		var module_dir = '{$smarty.const._MODULE_DIR_}';
		var id_language = {$defaultFormLanguage};
		var languages = new Array();
		var vat_number = {if $vat_number}1{else}0{/if};
		// Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
		// precedence conflicts with other document.ready() blocks
		{foreach $languages as $k => $language}
			languages[{$k}] = {
				id_lang: {$language.id_lang},
				iso_code: '{$language.iso_code}',
				name: '{$language.name}',
				is_default: '{$language.is_default}'
			};
		{/foreach}
		// we need allowEmployeeFormLang var in ajax request
		allowEmployeeFormLang = {$allowEmployeeFormLang|intval};
		employee_token = '{getAdminToken tab='AdminEmployees'}';
		displayFlags(languages, id_language, allowEmployeeFormLang);

		$(document).ready(function() {
			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{
					ajaxStates({$fields_value.id_state});
					$('#id_country').change(function() {
						ajaxStates();
					});
				}
			{/if}

			if ($(".datepicker").length > 0)
				$(".datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
			{if isset($use_textarea_autosize)}
			$(".textarea-autosize").autosize();
			{/if}
		});
	state_token = '{getAdminToken tab='AdminStates'}';
	{block name="script"}{/block}
	</script>
{/if}
