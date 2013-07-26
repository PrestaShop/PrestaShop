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

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

<script type="text/javascript">
	id_language = Number({$current_id_lang});
</script>

{block name="defaultOptions"}
<form action="{$current}&token={$token}"
	id="{$table}_form"
	{if isset($categoryData['name'])} name={$categoryData['name']}{/if}
	{if isset($categoryData['id'])} id={$categoryData['id']} {/if}
	method="post"
	enctype="multipart/form-data" class="form-horizontal">
	{foreach $option_list AS $category => $categoryData}
		{if isset($categoryData['top'])}{$categoryData['top']}{/if}
		<fieldset {if isset($categoryData['class'])}class="{$categoryData['class']}"{/if} class="col-lg-12">
			{* Options category title *}
			<legend>
				<i class="icon-cogs"></i>
				{if isset($categoryData['title'])}{$categoryData['title']}{else}{l s='Options'}{/if}
			</legend>

			{* Category description *}
			{if (isset($categoryData['description']) && $categoryData['description'])}
				<div class="optionsDescription">{$categoryData['description']}</div>
			{/if}
			{* Category info *}
			{if (isset($categoryData['info']) && $categoryData['info'])}
				<p>{$categoryData['info']}</p>
			{/if}

			{if !$categoryData['hide_multishop_checkbox'] && $use_multishop}
				<input type="checkbox" style="vertical-align: text-top" onclick="checkAllMultishopDefaultValue(this)" /> <b>{l s='Check/uncheck all'}</b> {l s='(Check boxes if you want to set a custom value for this shop or group shop context)'}
				<div class="separation"></div>
			{/if}

			{foreach $categoryData['fields'] AS $key => $field}
					{if $field['type'] == 'hidden'}
						<input type="hidden" name="{$key}" value="{$field['value']}" />
					{else}
						<div class="row">
							<div id="conf_id_{$key}" {if $field['is_invisible']} class="isInvisible"{/if}>
								{if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
									<div class="preference_default_multishop col-lg-6">
										<input type="checkbox" name="multishopOverrideOption[{$key}]" value="1" {if !$field['is_disabled']}checked="checked"{/if} onclick="checkMultishopDefaultValue(this, '{$key}')" />
									</div>
								{/if}
								{block name="label"}
									{if isset($field['title']) && isset($field['desc'])}
										<label class="control-label col-lg-3">
											<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{$field['desc']}">
												{$field['title']}
											</span>
										</label>
									{else}
										<label class="control-label col-lg-3">{$field['title']}</label>
									{/if}
								{/block}
								{block name="field"}
									
								{block name="input"}
									{if $field['type'] == 'select'}
										<div class="col-lg-6">
											{if $field['list']}
												<select name="{$key}"{if isset($field['js'])} onchange="{$field['js']}"{/if} id="{$key}" {if isset($field['size'])} size="{$field['size']}"{/if}>
													{foreach $field['list'] AS $k => $option}
														<option value="{$option[$field['identifier']]}"{if $field['value'] == $option[$field['identifier']]} selected="selected"{/if}>{$option['name']}</option>
													{/foreach}
												</select>
											{else if isset($input.empty_message)}
												{$input.empty_message}
											{/if}
										</div>
									{elseif $field['type'] == 'bool'}
										<div class="col-lg-9">
											<div class="row">
												<div class="input-group col-lg-2">
													<span class="switch prestashop-switch">
														<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
														<label for="{$key}_on" class="radioCheck">
															<i class="icon-check-sign"></i> {l s='Yes'}
														</label>
														<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
														<label for="{$key}_off" class="radioCheck">
															<i class="icon-ban-circle"></i> {l s='No'}
														</label>
														<span class="slide-button btn btn-default"></span>
													</span>
												</div>
											</div>
										</div>

									{elseif $field['type'] == 'radio'}
										{foreach $field['choices'] AS $k => $v}
											<div class="col-lg-6">
												<p class="radio">
													<input type="radio" name="{$key}" id="{$key}_{$k}" value="{$k}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
													<label class="col-lg-3" for="{$key}_{$k}"> {$v}</label>
												</p>
											</div>
										{/foreach}
									{elseif $field['type'] == 'checkbox'}
										{foreach $field['choices'] AS $k => $v}
											<div class="col-lg-6">
												<p class="checkbox">
													<input type="checkbox" name="{$key}" id="{$key}{$k}_on" value="{$k|intval}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
													<label class="col-lg-3" for="{$key}{$k}_on"> {$v}</label>
												</p>
											</div>
										{/foreach}
									{elseif $field['type'] == 'text'}
										<div class="col-lg-6">
											<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
											{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
										</div>
									{elseif $field['type'] == 'password'}
										<div class="col-lg-6">
											<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if} />
											{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
										</div>
									{elseif $field['type'] == 'textarea'}
										<div class="col-lg-6">
											<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|escape:'htmlall':'UTF-8'}</textarea>
										</div>
									{elseif $field['type'] == 'file'}
										{if isset($field['thumb']) && $field['thumb']}
											<div class="col-lg-6">
												<img src="{$field['thumb']}" alt="{$field['title']}" title="{$field['title']}" />
											</div>
										{/if}
										<div class="col-lg-6">
											<input type="file" name="{$key}" />
										</div>
						            {elseif $field['type'] == 'color'}
										<div class="col-lg-6">
							              <input type="color" size="{$field['size']}" data-hex="true" {if isset($input.class)}class="{$field['class']}" {else}class="color mColorPickerInput"{/if} name="{$field['name']}" class="{if isset($field['class'])}{$field['class']}{/if}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />
							            </div>
									{elseif $field['type'] == 'price'}
										<div class="col-lg-6">
											{$currency_left_sign}<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />{$currency_right_sign} {l s='(tax excl.)'}
										</div>
									{elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
										{if $field['type'] == 'textLang'}
											{foreach $field['languages'] AS $id_lang => $value}
												<div id="{$key}_{$id_lang}" style="display: {if $id_lang == $current_id_lang}block{else}none{/if};" class="col-lg-1">
													<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}_{$id_lang}" value="{$value|escape:'htmlall':'UTF-8'}" />
												</div>
											{/foreach}
										{elseif $field['type'] == 'textareaLang'}
											{foreach $field['languages'] AS $id_lang => $value}
												<div id="{$key}_{$id_lang}" style="display: {if $id_lang == $current_id_lang}block{else}none{/if};" class="col-lg-6">
													<textarea rows="{$field['rows']}" cols="{$field['cols']|intval}"  name="{$key}_{$id_lang}">{$value|replace:'\r\n':"\n"}</textarea>
												</div>
											{/foreach}
										{elseif $field['type'] == 'selectLang'}
											{foreach $languages as $language}
											<div id="{$key}_{$language.id_lang}" style="display: {if $language.id_lang == $current_id_lang}block{else}none{/if};" class="col-lg-6">
												<select name="{$key}_{$language.iso_code|upper}">
													{foreach $field['list'] AS $k => $v}
														<option value="{if isset($v.cast)}{$v.cast[$v[$field.identifier]]}{else}{$v[$field.identifier]}{/if}"
															{if $field['value'][$language.id_lang] == $v['name']} selected="selected"{/if}>
															{$v['name']}
														</option>
													{/foreach}
												</select>
											</div>


											{/foreach}
										{/if}
										{if count($languages) > 1}
											{* TO DO - ajouter les langues*}
											<div class="displayed_flag">
												<img src="../img/l/{$current_id_lang}.jpg" class="pointer" id="language_current_{$key}" onclick="toggleLanguageFlags(this);" />
											</div>
											<div id="languages_{$key}" class="language_flags">
												{l s='Choose language:'}<br /><br />
												{foreach $languages as $language}
														<img src="../img/l/{$language.id_lang}.jpg" class="pointer" alt="{$language.name}" title="{$language.name}" onclick="changeLanguage('{$key}', '{if isset($custom_key)}{$custom_key}{else}{$key}{/if}', {$language.id_lang}, '{$language.iso_code}');" />
												{/foreach}
											</div>
										{/if}
									{/if}

									{if isset($field['required']) && $field['required'] && $field['type'] != 'radio'}
										<sup>*</sup>
									{/if}
									{if isset($field['hint'])}<span class="alert alert-info" name="help_box">{$field['hint']}<span class="hint-pointer">&nbsp;</span></span>{/if}
								{/block}{* end block input *}
								{if $field['is_invisible']}
									<p class="alert alert-block">
										{l s='You can\'t change the value of this configuration field in the context of this shop.'}
									</p>
								{/if}
							</div>
						</div>
					{/block}{* end block field *}
				{/if}
			{/foreach}
			{if isset($categoryData['submit'])}
				<div class="row">
					<div class="col-lg-9 col-offset-3">
						<button
							type="submit"
							id="{$table}_form_submit_btn"
							name="{if isset($categoryData['submit']['name'])}{$categoryData['submit']['name']}{else}submitOptions{$table}{/if}"
							class="{if isset($categoryData['submit']['class'])}{$categoryData['submit']['class']}{else}btn btn-primary{/if}"
							>
							<i class="{if isset($categoryData['submit']['icon'])}{$categoryData['submit']['icon']}{else}icon-save{/if}"></i>
							{if isset($categoryData['submit']['title'])}{$categoryData['submit']['title']}{else}{l s='Save'}{/if}
						</button>
					</div>
				</div>
			{/if}
			{if isset($categoryData['required_fields']) && $categoryData['required_fields']}
				<div class="small"><sup>*</sup> {l s='Required field'}</div>
			{/if}
			{if isset($categoryData['bottom'])}{$categoryData['bottom']}{/if}
		</fieldset>
	{/foreach}
	{hook h='displayAdminOptions'}
	{if isset($name_controller)}
		{capture name=hookName assign=hookName}display{$name_controller|ucfirst}Options{/capture}
		{hook h=$hookName}
	{elseif isset($smarty.get.controller)}
		{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}Options{/capture}
		{hook h=$hookName}
	{/if}
</form>
{/block}
{block name="after"}{/block}
