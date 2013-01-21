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
*  @version  Release: $Revision: 17410 $
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

		function addThemeSelect(el)
		{
			var list_type_for_theme = [{foreach $translations_type_for_theme as $type}'{$type}', {/foreach}];
			var type = el.value;

			$('select[name=theme]').hide();
			for (i=0; i < list_type_for_theme.length; i++)
				if (list_type_for_theme[i] == type)
					$('select[name=theme]').show();
				else
					$('select[name=theme]').val('{$theme_default}');
		}

		$(document).ready(function(){
			$('select[name=type]').change(function() {
				addThemeSelect(this);
			});
		});
	</script>
	
	<fieldset>
		<legend><img src="../img/admin/translation.gif" />{l s='Modify translations'}</legend>
		{l s='Here you can modify translations for all text input in PrestaShop.'}<br />
		{l s='First, select a section (such as Back Office or Modules), then click the flag representing the language you want to edit.'}<br /><br />
		<form method="get" action="index.php" id="typeTranslationForm">
			<input type="hidden" name="controller" value="AdminTranslations" />
			<input type="hidden" name="lang" id="translation_lang" value="0" />
			<select name="type" style="float:left; margin-right:10px;">
				{foreach $translations_type as $type => $array}
					<option value="{$type}">{$array.name} &nbsp;</option>
				{/foreach}
			</select>
			<select name="theme" style="float:left; margin-right:10px;">
				{foreach $themes as $theme}

					<option value="{$theme->directory}" {if $id_theme_current == $theme->id}selected=selected{/if}>{$theme->name} &nbsp;</option>
				{/foreach}
			</select>
			{foreach $languages as $language}
				<a href="javascript:chooseTypeTranslation('{$language['iso_code']}');">
					<img src="{$theme_lang_dir}{$language['id_lang']}.jpg" alt="{$language['iso_code']}" title="{$language['iso_code']}" />
				</a>
			{/foreach}
			<input type="hidden" name="token" value="{$token}" />
		</form>
	</fieldset>
	
	<br /><h2>{l s='Translation exchange'}</h2>
	<form action="{$url_submit}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>
				<img src="../img/admin/import.gif" />{l s='Add / Update a language'}
			</legend>
			<div id="submitAddLangContent" style="float:left;">
				<p>{l s='You can add or update a language directly from prestashop.com here'}</p>
				<div class="warn">
					{l s='If you choose to update an existing language pack, all your previous customization in the theme named "Default" will be lost. This includes Front Office expressions and default e-mail templates.'}
				</div>
				{if $packs_to_update || $packs_to_install}
					<div style="font-weight:bold; float:left;">{l s='Language you want to add or update:'}
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
					<br /><br /><p class="error">{l s='Cannot connect to prestashop.com to get language list.'}</p></div>
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
					{l s='If the name format is: isocode.gzip (e.g. us.gzip) and the language corresponding to this package does not exist, it will automatically be created.'}
					{l s='Be careful, as this will replace all existing data for the destination language!'}
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
				<p class="margin-form"><input type="submit" value="{l s='Import'}" name="submitImport" class="button" /></p>
			</div>
		</fieldset>
	</form>
	<br /><br />
	
	<form action="{$url_submit}" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend><img src="../img/admin/export.gif" />{l s='Export a language'}</legend>
			<p>{l s='Export data from one language to a file (language pack).'}<br />
			{l s='Choose the theme from which you want to export translations.'}<br />
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
			{l s='Be careful, as this will replace all existing data for the destination language!'}<br />
			{l s='If necessary'}, <b><a href="{$url_create_language}">{l s='first create a new language'}</a></b>.</p>
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
			<p style="clear: left; padding: 16px 0px 0px 0px;"><span style="font-style: bold; color: red;">*</span> {l s='Language files must be complete to allow copying of translations'}</p>
		</fieldset>
	</form>
{/block}