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
<div class="panel">
	<input type="hidden" name="submitted_tabs[]" value="Associations" />
	<h3>{l s='Associations'}</h3>
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Associations"}
	<div id="no_default_category" class="alert alert-info">
		{l s='Please select a default category.'}
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="category_block">
			{include file="controllers/products/multishop/checkbox.tpl" field="associated-categories-tree" type="category_box"}
			{l s='Associated categories:'}
		</label>
		<div class="col-lg-9">
			<div id="category_block">
				{$category_tree}
			</div>
			<a class="btn btn-link bt-icon confirm_leave" href="{$link->getAdminLink('AdminCategories')|escape:'htmlall':'UTF-8'}&addcategory">
				<i class="icon-plus-sign"></i> {l s='Create new category'} <i class="icon-external-link-sign"></i>
			</a>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="id_category_default">
			{include file="controllers/products/multishop/checkbox.tpl" field="id_category_default" type="default"}
			<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='The default category is the category displayed by default.'}">
				{l s='Default category:'}
			</span>
		</label>
		<div class="col-lg-5">
			<select id="id_category_default" name="id_category_default">
				{foreach from=$selected_cat item=cat}
					<option value="{$cat.id_category}" {if $id_category_default == $cat.id_category}selected="selected"{/if} >{$cat.name}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="product_autocomplete_input">
			<span class="label-tooltip" data-toggle="tooltip"
			title="{l s='Begin typing the first letters of the product name, then select the product from the drop-down list.'}{l s='(Do not forget to save the product afterward)'}">
			{l s='Accessories:'}
			</span>
		</label>
		<div class="col-lg-5">
			<input type="hidden" name="inputAccessories" id="inputAccessories" value="{foreach from=$accessories item=accessory}{$accessory.id_product}-{/foreach}" />
			<input type="hidden" name="nameAccessories" id="nameAccessories" value="{foreach from=$accessories item=accessory}{$accessory.name|escape:'htmlall':'UTF-8'}¤{/foreach}" />
			<div id="ajax_choose_product">
				<div class="input-group">
					<input type="text" value="" id="product_autocomplete_input" />
					<span class="input-group-addon"><i class="icon-search"></i></span>
				</div>
			</div>

			<div id="divAccessories">
			{foreach from=$accessories item=accessory}
			<div class="form-control-static">
				<button type="button" class="btn btn-default delAccessory" name="{$accessory.id_product}">
					<i class="icon-remove text-danger"></i>
				</button>
				{$accessory.name|escape:'htmlall':'UTF-8'}{if !empty($accessory.reference)}{$accessory.reference}{/if}
			</div>
			{/foreach}
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" for="id_manufacturer">{l s='Manufacturer:'}</label>
		<div class="col-lg-5">
			<select name="id_manufacturer" id="id_manufacturer">
				<option value="0">-- {l s='Choose (optional)'} --</option>
				{if $product->id_manufacturer}
				<option value="{$product->id_manufacturer}" selected="selected">{$product->manufacturer_name}</option>
				{/if}
				<option disabled="disabled">----------</option>
			</select>
		</div>
		<div class="col-lg-4">
			<a class="btn btn-link bt-icon confirm_leave" style="margin-bottom:0" href="{$link->getAdminLink('AdminManufacturers')|escape:'htmlall':'UTF-8'}&addmanufacturer">
				<i class="icon-plus-sign"></i> {l s='Create new manufacturer'} <i class="icon-external-link-sign"></i> 
			</a>
		</div>
	</div>
</div>