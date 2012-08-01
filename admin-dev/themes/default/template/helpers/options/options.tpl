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
*  @version  Release: $Revision: 9548 $
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
	enctype="multipart/form-data">
	{foreach $option_list AS $category => $categoryData}
		{if isset($categoryData['top'])}{$categoryData['top']}{/if}
		<fieldset {if isset($categoryData['class'])}class="{$categoryData['class']}"{/if}>
		{* Options category title *}
		<legend>
			<img src="{$categoryData['image']}"/>
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
			<input type="checkbox" style="vertical-align: text-top" onclick="checkAllMultishopDefaultValue(this)" /> <b>{l s='Check/uncheck all'}</b> {l s='(check boxes if you want to set a custom value for this shop or group shop context)'}
			<div class="separation"></div>
		{/if}

		{foreach $categoryData['fields'] AS $key => $field}
				{if $field['type'] == 'hidden'}
					<input type="hidden" name="{$key}" value="{$field['value']}" />
				{else}
					<div style="clear: both; padding-top:15px;" id="conf_id_{$key}" {if $field['is_invisible']} class="isInvisible"{/if}>
					{if !$categoryData['hide_multishop_checkbox'] && $field['multishop_default'] && empty($field['no_multishop_checkbox'])}
						<div class="preference_default_multishop">
							<input type="checkbox" name="multishopOverrideOption[{$key}]" value="1" {if !$field['is_disabled']}checked="checked"{/if} onclick="checkMultishopDefaultValue(this, '{$key}')" />
						</div>
					{/if}
					{block name="label"}
						{if isset($field['title'])}
							<label class="conf_title">
							{$field['title']}</label>
						{/if}
					{/block}
					{block name="field"}
						<div class="margin-form">
					{block name="input"}
						{if $field['type'] == 'select'}
							{if $field['list']}
								<select name="{$key}"{if isset($field['js'])} onchange="{$field['js']}"{/if} id="{$key}" {if isset($field['size'])} size="{$field['size']}"{/if}>
									{foreach $field['list'] AS $k => $option}
										<option value="{$option[$field['identifier']]}"{if $field['value'] == $option[$field['identifier']]} selected="selected"{/if}>{$option['name']}</option>
									{/foreach}
								</select>
							{else if isset($input.empty_message)}
								{$input.empty_message}
							{/if}
						{elseif $field['type'] == 'bool'}
							<label class="t" for="{$key}_on"><img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" /></label>
							<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
							<label class="t" for="{$key}_on"> {l s='Yes'}</label>
							<label class="t" for="{$key}_off"><img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="margin-left: 10px;" /></label>
							<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
							<label class="t" for="{$key}_off"> {l s='No'}</label>
						{elseif $field['type'] == 'radio'}
							{foreach $field['choices'] AS $k => $v}
								<input type="radio" name="{$key}" id="{$key}_{$k}" value="{$k}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
								<label class="t" for="{$key}_{$k}"> {$v}</label><br />
							{/foreach}
							<br />
						{elseif $field['type'] == 'checkbox'}
							{foreach $field['choices'] AS $k => $v}
								<input type="checkbox" name="{$key}" id="{$key}{$k}_on" value="{$k|intval}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
								<label class="t" for="{$key}{$k}_on"> {$v}</label><br />
							{/foreach}
							<br />
						{elseif $field['type'] == 'text'}
							<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
							{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
						{elseif $field['type'] == 'password'}
							<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if} />
							{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
						{elseif $field['type'] == 'textarea'}
							<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|escape:'htmlall':'UTF-8'}</textarea>
						{elseif $field['type'] == 'file'}
							{if isset($field['thumb']) && $field['thumb']}
								<img src="{$field['thumb']}" alt="{$field['title']}" title="{$field['title']}" /><br />
							{/if}
							<input type="file" name="{$key}" />
						{elseif $field['type'] == 'price'}
							{$currency_left_sign}<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />{$currency_right_sign} {l s='(tax excl.)'}
						{elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
							{if $field['type'] == 'textLang'}
								{foreach $field['languages'] AS $id_lang => $value}
									<div id="{$key}_{$id_lang}" style="margin-bottom:8px; display: {if $id_lang == $current_id_lang}block{else}none{/if}; float: left; vertical-align: top;">
										<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}_{$id_lang}" value="{$value|escape:'htmlall':'UTF-8'}" />
									</div>
								{/foreach}
							{elseif $field['type'] == 'textareaLang'}
								{foreach $field['languages'] AS $id_lang => $value}
									<div id="{$key}_{$id_lang}" style="display: {if $id_lang == $current_id_lang}block{else}none{/if}; float: left;">
										<textarea rows="{$field['rows']}" cols="{$field['cols']|intval}"  name="{$key}_{$id_lang}">{$value|replace:'\r\n':"\n"}</textarea>
									</div>
								{/foreach}
							{elseif $field['type'] == 'selectLang'}
								{foreach $languages as $language}
								<div id="{$key}_{$language.id_lang}" style="margin-bottom:8px; display: {if $language.id_lang == $current_id_lang}block{else}none{/if}; float: left; vertical-align: top;">
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
								<div class="displayed_flag">
									<img src="../img/l/{$current_id_lang}.jpg"
										class="pointer"
										id="language_current_{$key}"
										onclick="toggleLanguageFlags(this);" />
								</div>
								<div id="languages_{$key}" class="language_flags">
									{l s='Choose language:'}<br /><br />
									{foreach $languages as $language}
											<img src="../img/l/{$language.id_lang}.jpg"
												class="pointer"
												alt="{$language.name}"
												title="{$language.name}"
												onclick="changeLanguage('{$key}', '{if isset($custom_key)}{$custom_key}{else}{$key}{/if}', {$language.id_lang}, '{$language.iso_code}');" />
									{/foreach}
								</div>
							{/if}
							<br style="clear:both">
						{/if}

						{if isset($field['required']) && $field['required'] && $field['type'] != 'radio'}
							<sup>*</sup>
						{/if}
						{if isset($field['hint'])}<span class="hint" name="help_box">{$field['hint']}<span class="hint-pointer">&nbsp;</span></span>{/if}
					{/block}{* end block input *}
					{if isset($field['desc'])}<p class="preference_description">{$field['desc']}</p>{/if}
					{if $field['is_invisible']}<p class="warn">{l s='You can\'t change the value of this configuration field in the context of this shop'}</p>{/if}
					</div>
					</div>
					<div class="clear"></div>
				{/block}{* end block field *}
			{/if}
		{/foreach}
		{if isset($categoryData['submit'])}
			<div class="margin-form">
				<input type="submit"
						value="{if isset($categoryData['submit']['title'])}{$categoryData['submit']['title']}{else}{l s='Save'}{/if}"
						name="{if isset($categoryData['submit']['name'])}{$categoryData['submit']['name']}{else}submitOptions{$table}{/if}"
						class="{if isset($categoryData['submit']['class'])}{$categoryData['submit']['class']}{else}button{/if}"
						id="{$table}_form_submit_btn"
				/>
			</div>
		{/if}
		{if isset($categoryData['required_fields']) && $categoryData['required_fields']}
			<div class="small"><sup>*</sup> {l s='Required field'}</div>
		{/if}
		{if isset($categoryData['bottom'])}{$categoryData['bottom']}{/if}
		</fieldset><br />
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
