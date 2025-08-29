{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
{extends file="helpers/form/form.tpl"}

{block name=script}
	$(function() {
		$('#latitude, #longitude').on('keyup', function() {
			$(this).val($(this).val().replace(/,/g, '.'));
		});
	});
{/block}

{block name="input"}
	{if $input.type == 'latitude'}
	<div class="row">
		<div class="col-lg-3">
			<input type="text"
				{if isset($input.size)}size="{$input.size}"{/if}
				{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
				name="latitude"
				id="latitude"
				value="{$fields_value[$input.name]|default|escape:'html':'UTF-8'}" />
		</div>
		<div class="col-lg-1">
			<div class="form-control-static text-center"> / </div>
		</div>
		<div class="col-lg-3">
			<input type="text"
				{if isset($input.size)}size="{$input.size}"{/if}
				{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
				name="longitude"
				id="longitude"
				value="{$fields_value['longitude']|default|escape:'html':'UTF-8'}" />
		</div>
	</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="other_input"}
	{if $key == 'hours'}
    <div class="form-group">
      <label class="control-label col-lg-3">{l s='Hours:' d='Admin.Shopparameters.Feature'}</label>
      <div class="col-lg-9"><p class="form-control-static">{l s='e.g. 10:00AM - 9:30PM' d='Admin.Shopparameters.Help'}</p></div>
    </div>
    {foreach $fields_value.days|default:[] as $k => $value}
    <div class="form-group">
      <label class="control-label col-lg-3">{$value}</label>
      {if $languages|count > 1}
        {foreach $languages as $language}
          <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
            <div class="col-lg-7">
              <input type="text" size="25"
                     name="hours[{$k}][{$language.id_lang}]"
                     value="{if isset($fields_value.hours[$language.id_lang][$k-1])}{$fields_value.hours[$language.id_lang][$k-1]|escape:'html':'UTF-8'}{/if}"/>
            </div>
            <div class="col-lg-2">
              <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                {$language.iso_code}
                <i class="icon-caret-down"></i>
              </button>
              <ul class="dropdown-menu">
                {foreach from=$languages item=language}
                  <li><a href="javascript:hideOtherLanguage({$language.id_lang});"
                         tabindex="-1">{$language.name}</a></li>
                {/foreach}
              </ul>
            </div>
          </div>
        {/foreach}
      {else}
        <div class="col-lg-9"><input type="text" size="25" name="hours[{$k}]" value="{if isset($fields_value.hours[$k-1])}{$fields_value.hours[$k-1]|escape:'html':'UTF-8'}{/if}"/>
        </div>
      {/if}
    </div>
    {/foreach}
  {/if}
{/block}
