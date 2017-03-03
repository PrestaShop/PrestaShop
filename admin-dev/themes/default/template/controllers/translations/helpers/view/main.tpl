{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
	<script type="text/javascript">
		function chooseTypeTranslation(id_lang)
		{
			getE('translation_lang').value = id_lang;

      var formTranslation = $('form#typeTranslationForm');
      var typeOption = $('#type option:selected');

      if ('mails' == $('#type option:selected').val()) {
        typeOption = $('#ps_email_selector select[name="selected-emails"] option:selected');
      }

      if ('modules' == $('#type option:selected').val()) {
        urlToTranslate = $('#ps_module_selector select[name="selected-modules"] option:selected').data('url-to-translate');
        if ('' !== urlToTranslate) {
          formTranslation.attr(
            'action',
            urlToTranslate + '&lang=' + id_lang
          );
        } else {
          formTranslation.attr('action', formTranslation.data('moduleaction'));
        }
      } else {
        if ('legacy' === typeOption.data('controller')) {
          formTranslation.attr('action', formTranslation.data('legacyaction'));
        } else {
          formTranslation.attr('action', formTranslation.data('sfaction'));
        }
      }

      formTranslation.submit();
		}

		$(document).ready(function() {
      var themeSelector = $('#ps_theme_selector');
      var themeCoreOption = themeSelector.find('select[name="selected-theme"] option#core-option');
      var emailSelector = $('#ps_email_selector');
      var moduleSelector = $('#ps_module_selector');
      var allSelectors = $('select[name="selected-modules"], select[name="selected-emails"], select[name="selected-theme"], select[name="locale"]');

      themeSelector.hide();
      emailSelector.hide();
      moduleSelector.hide();

      $('#type').on('change', function () {

        // reset all select
        allSelectors.each(function () {
          $(this).prop('selectedIndex',0);
        });

        if ('mails' === $(this).val()) {
          emailSelector.show();
        } else {
          emailSelector.hide();
        }

        if ('modules' === $(this).val()) {
          moduleSelector.show();
        } else {
          moduleSelector.hide();
        }

        if ('themes' === $(this).val()) {
          themeSelector.find('select[name="selected-theme"]').prop('selectedIndex',1);
          themeCoreOption.hide().attr('disabled', true);
        } else {
          themeCoreOption.show().attr('disabled', false);
        }

        if (1 === $('#type option:selected').data('choicetheme')) {
          themeSelector.show();
        } else {
          themeSelector.hide();
        }
      });

      $('select[name="selected-emails"]').on('change', function() {
        if ('subject' === $(this).val()) {
          themeSelector.hide();
        } else {
          themeSelector.show();
        }
      });

			$('#modify-translations').click(function() {
				var languages = $('#translations-languages option');
				var i;
				var selectedLanguage;

				for (i = 0; i < languages.length; i++) {
					if (languages[i].selected) {
						selectedLanguage = languages[i].value;

						break;
					}
				}

        if ('modules' === $('#type option:selected').val() && '' === $('[name="selected-modules"]').val()) {
          alert('{l s='Please select a module!'}');

          return;
        }

				if (0 === selectedLanguage.length) {
					alert('{l s='Please select your language!'}');

					return;
				}

				chooseTypeTranslation(selectedLanguage);
			});
		});
	</script>
  <form method="post" action="{url entity=sf route=admin_international_translations_list }"
        data-sfaction="{url entity=sf route=admin_international_translations_list }"
        data-moduleaction="{url entity=sf route=admin_international_translations_module }"
        data-legacyaction="{$link->getAdminLink('AdminTranslations', true)}"
        id="typeTranslationForm" class="form-horizontal">
    <div class="panel">
      <h3>
        <i class="icon-file-text"></i>
        {l s='Modify translations'}
      </h3>
      <p class="alert alert-info">
        {l s='Here you can modify translations for every line of text inside PrestaShop.'}<br />
        {l s='First, select a type of translation (such as "Back office" or "Installed modules"), and then select the language you want to translate strings in.' html=true}
      </p>
      <div class="form-group">
        <input type="hidden" name="controller" value="AdminTranslations" />
        <input type="hidden" name="lang" id="translation_lang" value="0" />
        <label class="control-label col-lg-3" for="type">{l s='Type of translation'}</label>
        <div class="col-lg-4">
          <select name="type" id="type">
            {foreach $translations_type as $type => $array}
              {if isset($array.name)}<option value="{$type}" data-controller="{if $array.sf_controller}sf{else}legacy{/if}" data-choicetheme="{$array.choice_theme}">{$array.name}</option>{/if}
            {/foreach}
          </select>
        </div>
      </div>
      <div class="form-group" id="ps_email_selector">
        <label class="control-label col-lg-3" for="selected-emails">{l s='Select the type of email content'}</label>
        <div class="col-lg-4">
          <select name="selected-emails">
            <option value="subject" data-controller="sf">{l s='Subject'}</option>
            <option value="body" data-controller="legacy">{l s='Body'}</option>
          </select>
        </div>
      </div>
      <div class="form-group" id="ps_module_selector">
        <label class="control-label col-lg-3" for="selected-modules">{l s='Select your module'}</label>
        <div class="col-lg-4">
          <select name="selected-modules">
            <option id="no-module" value="">---</option>
            {foreach $modules as $module}
              <option value="{$module.name}" data-url-to-translate="{$module.urlToTranslate}">{$module.name}</option>
            {/foreach}
          </select>
        </div>
      </div>
      <div class="form-group" id="ps_theme_selector">
        <label class="control-label col-lg-3" for="selected-theme">{l s='Select your theme'}</label>
        <div class="col-lg-4">
          <select name="selected-theme">
              <option id="core-option" value="">{l s='Core (no theme selected)'}</option>
            {foreach $themes as $theme}
              <option value="{$theme->getName()}" {if $current_theme_name == $theme->getName()}selected=selected{/if}>{$theme->getName()}</option>
            {/foreach}
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3" for="translations-languages">{l s='Select your language'}</label>
        <div class="col-lg-4">
          <select name="locale" id="translations-languages">
            <option value="">{l s='Language'}</option>
            {foreach $languages as $language}
              <option value="{$language['iso_code']}">{$language['name']}</option>
            {/foreach}
          </select>
        </div>
      </div>
      <div class="panel-footer">
        <button type="button" class="btn btn-default pull-right" id="modify-translations">
          <i class="process-icon-edit"></i> {l s='Modify'}
        </button>
      </div>
    </div>
  </form>

	<form action="{$url_submit|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div class="panel">
			<h3>
				<i class="icon-download"></i>
				{l s='Add / Update a language'}
			</h3>
			<div id="submitAddLangContent" class="form-group">
				<p class="alert alert-info">
					{l s='You can add or update a language directly from the PrestaShop website here.'}
				</p>
				{if $packs_to_update || $packs_to_install}
					<label class="control-label col-lg-3" for="params_import_language">{l s='Please select the language you want to add or update'}</label>
					<div class="col-lg-9">
						<div class="row">
							<div class="col-lg-6">
								<select id="params_import_language" name="params_import_language" class="chosen" {if $level == 1} disabled="disabled" {/if}>
								<optgroup label="{l s='Update a language'}">
									{foreach from=$packs_to_update key=locale item=name}
										<option value="{$locale}">{$name}</option>
									{/foreach}
								</optgroup>
								<optgroup label="{l s='Add a language'}">
									{foreach from=$packs_to_install key=locale item=name}
										<option value="{$locale}">{$name}</option>
									{/foreach}
								</optgroup>
							</select>
							</div>
						</div>
					</div>

				{else}
					<p class="text-danger">{l s='Cannot connect to the PrestaShop website to get the language list.'}</p>
				{/if}
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitAddLanguage" class="btn btn-default pull-right" {if $level == 1} disabled="disabled" {/if}>
					<i class="process-icon-cogs"></i> {l s='Add or update a language'}
				</button>
			</div>
		</div>
	</form>
	<form action="{$url_submit|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal hide">
		<div class="panel">
			<h3>
				<i class="icon-download"></i>
				{l s='Import a language pack manually'}
			</h3>
			<p class="alert alert-info">
				{l s='If the language file format is ISO_code.gzip (e.g. "us.gzip"), and the language corresponding to this package does not exist, it will automatically be created.' html=true}
				{l s='Warning: This will replace all of the existing data inside the destination language.'}
			</p>
			<div class="form-group">
				<label for="importLanguage" class="control-label col-lg-3">{l s='Language pack to import'}</label>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="col-lg-12">
							<input id="importLanguage" type="file" name="file" class="hide" {if $level == 1} disabled="disabled" {/if} />
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input id="file-name" type="text" class="disabled" name="filename" readonly />
								<span class="input-group-btn">
									<button id="file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default" {if $level == 1} disabled="disabled" {/if}>
										<i class="icon-folder-open"></i> {l s='Add file'}
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="selectThemeForImport" class="control-label col-lg-3">{l s='Select your theme'}</label>
				<div class="col-lg-4">
					<select name="theme[]" id="selectThemeForImport" {if $level == 1} disabled="disabled" {/if} {if count($themes) > 1}multiple="multiple"{/if} >
						{foreach $themes as $theme}
							<option value="{$theme->getDirectory()}" selected="selected">{$theme->getName()} &nbsp;</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitImport" class="btn btn-default pull-right" {if $level == 1} disabled="disabled" {/if} ><i class="process-icon-upload"></i> {l s='Import' d='Admin.Actions'}</button>
			</div>
		</div>
	</form>
	<form action="{url entity=sf route=admin_international_translations_export_theme }" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div class="panel">
			<h3>
				<i class="icon-upload"></i>
				{l s='Export a language'}
			</h3>
			<p class="alert alert-info">
				{l s='Export data from one language to a file (language pack).'}<br />
				{l s='Select which theme you would like to export your translations to.'}
			</p>
			<div class="form-group">
				<label class="control-label col-lg-3" for="iso_code">{l s='Language'}</label>
				<div class="col-lg-4">
					<select name="iso_code" id="iso_code" {if $level == 1} disabled="disabled" {/if}>
						{foreach $languages as $language}
							<option value="{$language['iso_code']}">{$language['name']}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="export-theme">{l s='Select your theme'}</label>
				<div class="col-lg-4">
					<select name="theme-name" id="export-theme" {if $level == 1} disabled="disabled" {/if}>
						{foreach $themes as $theme}
							<option value="{$theme->getName()}" {if $current_theme_name ==$theme->getName()}selected=selected{/if}>{$theme->getName()}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitExport" class="btn btn-default pull-right" {if $level == 1} disabled="disabled" {/if}><i class="process-icon-download"></i> {l s='Export' d='Admin.Actions'}</button>
			</div>
		</div>
	</form>
	<form action="{$url_submit|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
		<div class="panel">
			<h3>
				<i class="icon-copy"></i>
				{l s='Copy'}
			</h3>
			<p class="alert alert-info">
				{l s='Copies data from one language to another.'}<br />
				{l s='Warning: This will replace all of the existing data inside the destination language.'}<br />
				{l s='If necessary'}, <b><a href="{$url_create_language|escape:'html':'UTF-8'}" class="btn btn-link"><i class="icon-external-link-sign"></i> {l s='you must first create a new language.'}</a></b>.
			</p>
			<div class="form-group">
				<label class="control-label col-lg-3 required" for="fromLang"> {l s='From'}</label>
				<div class="col-lg-4">
					<select name="fromLang" id="fromLang">
						{foreach $languages as $language}
							<option value="{$language['iso_code']}">{$language['name']}</option>
						{/foreach}
					</select>
				</div>
				<div class="col-lg-4">
					<select name="fromTheme">
						{foreach $themes as $theme}
							<option value="{$theme->getName()}" {if $current_theme_name ==$theme->getName()}selected=selected{/if}>{$theme->getName()}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="toLang">{l s='To'}</label>
				<div class="col-lg-4">
					<select name="toLang" id="toLang">
						{foreach $languages as $language}
							<option value="{$language['iso_code']}">{$language['name']}</option>
						{/foreach}
					</select>
				</div>
				<div class="col-lg-4">
					<select name="toTheme">
						{foreach $themes as $theme}
							<option value="{$theme->getName()}" {if $current_theme_name ==$theme->getName()}selected=selected{/if}>{$theme->getName()}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<p class="col-lg-12 text-muted required">
					<span class="text-danger">*</span>
					{l s='Language files must be complete to allow copying of translations.'}
				</p>
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitCopyLang" class="btn btn-default pull-right"><i class="process-icon-duplicate"></i> {l s='Copy'}</button>
			</div>
		</div>
	</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('#file-selectbutton').click(function(e) {
			$('#importLanguage').trigger('click');
		});

		$('#file-name').click(function(e) {
			$('#importLanguage').trigger('click');
		});

		$('#importLanguage').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';

				$.each(files, function(index, value) {
					name += value.name+', ';
				});

				$('#file-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#file-name').val(name[name.length-1]);
			}
		});
	});
</script>
{/block}
