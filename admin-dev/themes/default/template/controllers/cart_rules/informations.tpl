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
<div class="form-group">
	<label class="control-label col-lg-3 required">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l|escape s='This will be displayed in the cart summary, as well as on the invoice.' d='Admin.Catalog.Help'}">
			{l s='Name' d='Admin.Global'}
		</span>
	</label>
	<div class="col-lg-8">
		{foreach from=$languages item=language}
		{if $languages|count > 1}
		<div class="row">
			<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $id_lang_default}style="display:none"{/if}>
				<div class="col-lg-9">
		{/if}
					<input id="name_{$language.id_lang|intval}" type="text"  name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:'html':'UTF-8'}">
		{if $languages|count > 1}
				</div>
				<div class="col-lg-2">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
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
		</div>
		{/if}
		{/foreach}
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l|escape s='For your eyes only. This will never be displayed to the customer.' d='Admin.Catalog.Help'}">
			{l s='Description' d='Admin.Global'}
		</span>
	</label>
	<div class="col-lg-8">
		<textarea name="description" rows="2" class="textarea-autosize">{$currentTab->getFieldValue($currentObject, 'description')|escape}</textarea>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{{l|escape s='This is the code users should enter to apply the voucher to a cart. Either create your own code or generate one by clicking on "%generate_label%".' d='Admin.Catalog.Help'  sprintf=['%generate_label%' => {l s='Generate' d='Admin.Actions'}]}|escape:"html"}">
			{l s='Code' d='Admin.Global'}
		</span>
	</label>
	<div class="col-lg-9">
		<div class="input-group col-lg-4">
			<input type="text" id="code" name="code" value="{$currentTab->getFieldValue($currentObject, 'code')|escape}" />
			<span class="input-group-btn">
				<a href="javascript:gencode(8);" class="btn btn-default"><i class="icon-random"></i> {l s='Generate' d='Admin.Actions'}</a>
			</span>
		</div>
	<span class="help-block">{l s='Caution! If you leave this field blank, the rule will automatically be applied to benefiting customers.' d='Admin.Catalog.Help'}</span>
	</div>
</div>

<div class="form-group" id="cart-rules-highlight"{if !$currentTab->getFieldValue($currentObject, 'code')} style="display: none;"{/if}>
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l|escape s='If the voucher is not yet in the cart, it will be displayed in the cart summary.' d='Admin.Catalog.Help'}">
			{l s='Highlight' d='Admin.Catalog.Feature'}
		</span>
	</label>
	<div class="col-lg-9">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" name="highlight" id="highlight_on" value="1" {if $currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if}/>
			<label for="highlight_on">{l s='Yes' d='Admin.Global'}</label>
			<input type="radio" name="highlight" id="highlight_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if} />
			<label for="highlight_off">{l s='No' d='Admin.Global'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l|escape s='Only applicable if the voucher value is greater than the cart total.' d='Admin.Catalog.Help'}
		{l s='If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.' d='Admin.Catalog.Help'}">
			{l s='Partial use' d='Admin.Catalog.Feature'}
		</span>
	</label>
	<div class="col-lg-9">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" name="partial_use" id="partial_use_on" value="1" {if $currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
			<label class="t" for="partial_use_on">{l s='Yes' d='Admin.Global'}</label>
			<input type="radio" name="partial_use" id="partial_use_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
			<label class="t" for="partial_use_off">{l s='No' d='Admin.Global'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip"
		title="{l|escape s='Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".' d='Admin.Catalog.Help'}">
			{l s='Priority' d='Admin.Global'}
		</span>
	</label>
	<div class="col-lg-1">
		<input type="text" class="input-mini" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
	</div>
</div>

<div class="form-group">
	<label class="control-label col-lg-3">{l s='Status' d='Admin.Global'}</label>
	<div class="col-lg-9">
		<span class="switch prestashop-switch fixed-width-lg">
			<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_on">{l s='Yes' d='Admin.Global'}</label>
			<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
			<label class="t" for="active_off">{l s='No' d='Admin.Global'}</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>
<script type="text/javascript">
	$(".textarea-autosize").autosize();
	$(document).ready(function() {
    $('#code').bind('keyup change', function() {
      $('#cart-rules-highlight').toggle($(this).val() !== "");
    });
  });
</script>
