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

{block name="field"}
	{if $input.type == 'address_layout'}
		<div class="col-lg-8">
			<div class="form-group">
				<div class="col-lg-4">
					<textarea id="ordered_fields" name="address_layout" style="height:150px;">{$input.address_layout}</textarea>
				</div>
				<div class="col-lg-8">
					{l s='Some countries require different elements than others. Click on the button below to get the valid default address format for this country.' d='Admin.International.Feature'}
					{$input.display_valid_fields}
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l|escape s='This will restore your last registered address format.' d='Admin.International.Help'}" data-html="true"><a id="useLastDefaultLayout" href="javascript:void(0)" onclick="resetLayout('{$input.encoding_address_layout}', 'lastDefault');" class="btn btn-default">
						{l s='Use the last registered format' d='Admin.International.Feature'}</a></span>
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l|escape s='This will restore the default address format for this country.' d='Admin.International.Help'}" data-html="true"><a id="useDefaultLayoutSystem" href="javascript:void(0)" onclick="resetLayout('{$input.encoding_default_layout}', 'defaultSystem');" class="btn btn-default">
						{l s='Use the default format' d='Admin.International.Feature'}</a></span>
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l|escape s='This will restore your current address format.' d='Admin.International.Help'}" data-html="true"><a id="useCurrentLastModifiedLayout" href="javascript:void(0)" onclick="resetLayout(lastLayoutModified, 'currentModified')" class="btn btn-default">
						{l s='Use my current modified format' d='Admin.International.Feature'}</a></span>
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l|escape s='This will delete the current address format' d='Admin.International.Help'}" data-html="true"><a id="eraseCurrentLayout" href="javascript:void(0)" onclick="resetLayout('', 'erase');" class="btn btn-default">
						<i class="icon-eraser"></i> {l s='Clear format' d='Admin.International.Feature'}</a></span>
				</div>
			</div>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input_row"}
	{if $input.name == 'standardization'}
		<div class="form-group" id="TAASC" style="display: none;">
			<label for="{$input.name}" class="control-label col-lg-3">{$input.label}</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="{$input.name}" id="{$input.name}_on" value="1" />
					<label for="{$input.name}_on">
						{l s='Yes' d='Admin.Global'}
					</label>
					<input type="radio" name="{$input.name}" id="{$input.name}_off" value="0" checked="checked" />
					<label for="{$input.name}_off">
						{l s='No' d='Admin.Global'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


{block name=script}

	$(function() {

		$('.addPattern').on('click', function() {
			addFieldsToCursorPosition($(this).attr("id"))
			lastLayoutModified = $("#ordered_fields").val();
		});

		$('#ordered_fields').on('keyup', function() {
			lastLayoutModified = $(this).val();
		});

		$('#need_zip_code_on, #need_zip_code_off').on('change', function() {
			disableZipFormat();
		});

		$('#iso_code').on('change', function() {
			disableTAASC();
		});
		disableTAASC();
	});

	function addFieldsToCursorPosition(pattern) {
		$("#ordered_fields").replaceSelection(pattern + " ");
	}

	function resetLayout(defaultLayout, type) {
		if (confirm("{l s='Are you sure you want to restore the default address format for this country?' js=1 d='Admin.International.Notification'}"))
		$("#ordered_fields").val(unescape(defaultLayout.replace(/\+/g, " ")));
	}

	$('#custom-address-fields a').on('click', function (e) {
  		e.preventDefault();
  		$(this).tab('show')
	})

{/block}
