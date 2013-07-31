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

{if isset($obj->id)}
	<input type="hidden" name="submitted_tabs[]" value="Customization" />
	<h3>{l s='Add or modify customizable properties.'}</h3>
	
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Customization"}

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="uploadable_files" type="default"}
	<label class="control-label col-lg-3" for="uploadable_files">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Number of upload file fields displayed'}">
			{l s='File fields:'}
		</span>
	</label>
	<div class="col-lg-1">
		<input type="text" name="uploadable_files" id="uploadable_files" value="{$uploadable_files|htmlentities}" />
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="text_fields" type="default"}
	<label class="control-label col-lg-3" for="text_fields">
		<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Number of text fields displayed'}">
			{l s='Text fields:'}
		</span>
	</label>
	<div class="col-lg-1">
		<input type="text" name="text_fields" id="text_fields" value="{$text_fields|htmlentities}" />
	</div>
</div>

	{if $has_file_labels}
		{l s='Define the label of the file fields:'}
		{$display_file_labels}
	{/if}

	{if $has_text_labels}
		{l s='Define the label of the text fields:'}
		{$display_text_labels}
	{/if}

{/if}