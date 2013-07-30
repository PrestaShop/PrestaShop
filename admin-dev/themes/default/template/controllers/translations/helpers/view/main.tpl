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
		function chooseTypeTranslation(id_lang)
		{
			getE('translation_lang').value = id_lang;
			document.getElementById('typeTranslationForm').submit();
		}

		function addThemeSelect()
		{
			var list_type_for_theme = ['front', 'modules', 'pdf', 'mails'];
			var type = $('select[name=type]').val();

			$('select[name=theme]').hide();
			for (i=0; i < list_type_for_theme.length; i++)
				if (list_type_for_theme[i] == type)
				{
					$('select[name=theme]').show();
					if (type == 'front')
						$('select[name=theme]').children('option[value=""]').attr('disabled', true)
					else
						$('select[name=theme]').children('option[value=""]').attr('disabled', false)
				}
				else
					$('select[name=theme]').val('{$theme_default}');
		}

		$(document).ready(function(){
			addThemeSelect();
			$('select[name=type]').change(function() {
				addThemeSelect();
			});
		});
	</script>
	
	<form method="get" action="index.php" id="typeTranslationForm" class="form-horizontal">
		<fieldset>
			<h3>
				<i class="icon-file-text"></i>
				{l s='Modify translations'}
			</h3>
			<p class="alert alert-block">
				{l s='Here you can modify translations for every line of code inside PrestaShop.'}<br />
				{l s='First, select a section (such as Back Office or Installed modules), and then click the flag representing the language you want to edit.'}
			</p>
			<input type="hidden" name="controller" value="AdminTranslations" />
			<input type="hidden" name="lang" id="translation_lang" value="0" />
			<div class="row">
				<label class="control-label col-lg-3">{l s='Type of translation:'}</label>
				<div class="col-lg-6">
					<select name="type">
						{foreach $translations_type as $type => $array}
							<option value="{$type}">{$array.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="row">
				<label class="control-label col-lg-3">{l s='Choose your theme:'}</label>
				<div class="col-lg-6">
					<select name="theme">
						<option value="">{l s='Core (no theme selected)'}</option>
						{foreach $themes as $theme}
							<option value="{$theme->directory}" {if $id_theme_current == $theme->id}selected=selected{/if}>{$theme->name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			{* TO DO - ajouter la langue*}
			{*<div class="row">
				{foreach from=$languages item=language}
				<div class="input-group col-lg-12 translatable-field lang-{$language.id_lang}">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<img src="{$base_url}/img/l/{$language.id_lang|intval}.jpg" alt="">
							{$language.iso_code}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach $languages as $language}
							<li>
								<a href="javascript:chooseTypeTranslation('{$language['iso_code']}');">
									<img src="{$theme_lang_dir}{$language['id_lang']}.jpg" alt="{$language['iso_code']}" title="{$language['iso_code']}" />
									{$language['iso_code']}
								</a>
							</li>
							{/foreach}
						</ul>
					</div>
				</div>
				{/foreach}
			</div>*}
				{foreach $languages as $language}
					<a href="javascript:chooseTypeTranslation('{$language['iso_code']}');">
						<img src="{$theme_lang_dir}{$language['id_lang']}.jpg" alt="{$language['iso_code']}" title="{$language['iso_code']}" />
					</a>
				{/foreach}
				<input type="hidden" name="token" value="{$token}" />
			</div>
		</fieldset>
	</form>
	<form action="{$url_submit}" method="post" enctype="multipart/form-data">
		<fieldset>
			<h3>
				<i class="icon-download"></i>
				{l s='Add / Update a language'}
			</h3>
			<div id="submitAddLangContent" style="float:left;">
				<p>{l s='You can add or update a language directly from the PrestaShop website here:'}</p>
				<div class="alert alert-block">
					{l s='If you choose to update an existing language pack, all of your previous customization\'s in the theme named "Default" will be lost. This includes Front Office expressions and default email templates.'}
				</div>
				{if $packs_to_update || $packs_to_install}
					<div style="font-weight:bold; float:left;">{l s='Please select the language you want to add or update:'}
						<select id="params_import_language" name="params_import_language">
							<optgroup label="{l s='Update a language'}">
								{foreach $packs_to_update as $lang_pack}
									<option value="{$lang_pack['iso_code']}|{$lang_pack['version']}">{$lang_pack['name']}</option>
								{/foreach}
							</optgroup>
							<optgroup label="{l s='Add a language'}">		
								{foreach $packs_to_install as $lang_pack}
									<option value="{$lang_pack['iso_code']}|{$lang_pack['version']}">{$lang_pack['name']}</option>
								{/foreach}
							</optgroup>
						</select> &nbsp;
						<input type="submit" value="{l s='Add or update a language'}" name="submitAddLanguage" class="button" />
					</div>
				{else}
					<br /><br /><p class="error">{l s='Cannot connect to the PrestaShop website to get the language list.'}</p></div>
				{/if}
			</div>
		</fieldset>
	</form><br /><br />
	
	<form action="{$url_submit}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>
				<img src="../img/admin/import.gif" />{l s='Import a language pack manually'}
			</legend>
			<div id="submitImportContent">
				<p>
					{l s='If the language file format is: isocode.gzip (e.g. us.gzip), and the language corresponding to this package does not exist, it will automatically be created.'}
					{l s='Warning: This will replace all of the existing data inside the destination language.'}
				</p>
				<p><label for="importLanguage">{l s='Language pack to import:'}</label><input type="file" name="file" id="importLanguage"/>&nbsp;</p>
				<p>
					<label for="selectThemeForImport">{l s='Select your theme:'}</label>
					<select name="theme[]" id="selectThemeForImport" {if count($themes) > 1}multiple="multiple"{/if} >
						{foreach $themes as $theme}
							<option value="{$theme->directory}" selected="selected">{$theme->name} &nbsp;</option>
						{/foreach}
					</select>
				</p>
				<p class="margin-form"><input type="submit" value="{l s='   Import   '}" name="submitImport" class="button" /></p>
			</div>
		</fieldset>
	</form>
	<br /><br />
	
	<form action="{$url_submit}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend><img src="../img/admin/export.gif" />{l s='Export a language'}</legend>
			<p>{l s='Export data from one language to a file (language pack).'}<br />
			{l s='Choose which theme you\'d like to export your translations to. '}<br />
			<select name="iso_code" style="margin-top:10px;">
				{foreach $languages as $language}
					<option value="{$language['iso_code']}">{$language['name']}</option>
				{/foreach}
			</select>
			&nbsp;&nbsp;&nbsp;
			<select name="theme" style="margin-top:10px;">
				{foreach $themes as $theme}
					<option value="{$theme->directory}" {if $id_theme_current == $theme->id}selected=selected{/if}>{$theme->name}</option>
				{/foreach}
			</select>&nbsp;&nbsp;
			<input type="submit" class="button" name="submitExport" value="{l s='Export'}" />
		</fieldset>
	</form>
	<br /><br />
	<form action="{$url_submit}" method="post">
		<fieldset>
			<legend><img src="../img/admin/copy_files.gif" />{l s='Copy'}</legend>
			<p>{l s='Copies data from one language to another.'}<br />
			{l s='Warning: This will replace all of the existing data inside the destination language.'}<br />
			{l s='If necessary'}, <b><a href="{$url_create_language}">{l s='you must first create a new language.'}</a></b>.</p>
			<div style="float:left;">
				<p>
					<div style="width:75px; font-weight:bold; float:left;">{l s='From:'}</div>
					<select name="fromLang">
						{foreach $languages as $language}
							<option value="{$language['iso_code']}">{$language['name']}</option>
						{/foreach}
					</select>
					&nbsp;&nbsp;&nbsp;
					<select name="fromTheme">
						{foreach $themes as $theme}
							<option value="{$theme->directory}" {if $id_theme_current == $theme->id}selected=selected{/if}>{$theme->name}</option>
						{/foreach}
					</select> <span style="font-style: bold; color: red;">*</span>
				</p>
				<p>
					<div style="width:75px; font-weight:bold; float:left;">{l s='To:'}</div>
					<select name="toLang">
						{foreach $languages as $language}
							<option value="{$language['iso_code']}">{$language['name']}</option>
						{/foreach}
					</select>
					&nbsp;&nbsp;&nbsp;
					<select name="toTheme">
						{foreach $themes as $theme}
							<option value="{$theme->directory}" {if $id_theme_current == $theme->id}selected=selected{/if}>{$theme->name}</option>
						{/foreach}
					</select>
				</p>
			</div>
			<div style="float:left;">
				<input type="submit" value="{l s='   Copy   '}" name="submitCopyLang" class="button" style="margin:25px 0px 0px 25px;" />
			</div>
			<p style="clear: left; padding: 16px 0px 0px 0px;"><span style="font-style: bold; color: red;">*</span> {l s='Language files must be complete to allow copying of translations.'}</p>
		</fieldset>
	</form>
{/block}