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
*  @version  Release: $Revision: 16903 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<input type="hidden" name="submitted_tabs[]" value="Associations" />
<div class="Associations">
	<h4>{l s='Associations'}</h4>

	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Associations"}
	<div class="separation"></div>
		<div id="no_default_category" class="hint">
		{l s='Please select a default category.'}
	</div>
	<table>
		<tr>
			<td class="col-left">
				{include file="controllers/products/multishop/checkbox.tpl" field="category_box" type="category_box"}
				<label for="category_block">{l s='Associated categories:'}</label>
			</td>
			<td class="col-right">
				<div id="category_block">
					{$category_tree}
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td class="col-right">
					<a class="button bt-icon confirm_leave" href="{$link->getAdminLink('AdminCategories')|escape:'htmlall':'UTF-8'}&addcategory">
						<img src="../img/admin/add.gif" alt="{l s='Create new category'}" title="{l s='Create new category'}" />
						<span>{l s='Create new category'}</span>
					</a>
			</td>
		</tr>
		<tr>
			<td class="col-left">
				{include file="controllers/products/multishop/checkbox.tpl" field="id_category_default" type="default"}
				<label for="id_category_default">{l s='Default category:'}</label>
			</td>
			<td class="col-right">
				<select id="id_category_default" name="id_category_default">
					{foreach from=$selected_cat item=cat}
						<option value="{$cat.id_category}" {if $id_category_default == $cat.id_category}selected="selected"{/if} >{$cat.name}</option>
					{/foreach}
				</select>
				<div class="hint" style="display:block;">{l s='The default category is the category which is displayed by default.'}</div>
			</td>
		</tr>
	</table>
<div class="separation"></div>
	<table>
		<tr>
			<td class="col-left"><label>{l s='Accessories:'}</label></td>
			<td style="padding-bottom:5px;">
				<input type="hidden" name="inputAccessories" id="inputAccessories" value="{foreach from=$accessories item=accessory}{$accessory.id_product}-{/foreach}" />
				<input type="hidden" name="nameAccessories" id="nameAccessories" value="{foreach from=$accessories item=accessory}{$accessory.name|escape:'htmlall':'UTF-8'}¤{/foreach}" />

				<div id="ajax_choose_product">
					<p style="clear:both;margin-top:0;">
						<input type="text" value="" id="product_autocomplete_input" />
						{l s='Begin typing the first letters of the product name, then select the product from the drop-down list'}
					</p>
					<p class="preference_description">{l s='(Do not forget to save the product afterward)'}</p>
					<!--<img onclick="$(this).prev().search();" style="cursor: pointer;" src="../img/admin/add.gif" alt="{l s='Add an accessory'}" title="{l s='Add an accessory'}" />-->
				</div>
				<div id="divAccessories">
					{* @todo : donot use 3 foreach, but assign var *}
					{foreach from=$accessories item=accessory}
						{$accessory.name|escape:'htmlall':'UTF-8'}{if !empty($accessory.reference)}{$accessory.reference}{/if}
						<span class="delAccessory" name="{$accessory.id_product}" style="cursor: pointer;">
							<img src="../img/admin/delete.gif" class="middle" alt="" />
						</span><br />
					{/foreach}
				</div>
			</td>
		</tr>
		<tr>
		<br />
		<td class="col-left"><label>{l s='Manufacturer:'}</label></td>
		<td style="padding-bottom:5px;">
			<select name="id_manufacturer" id="id_manufacturer">
				<option value="0">-- {l s='Choose (optional)'} --</option>
				{if $product->id_manufacturer}
				<option value="{$product->id_manufacturer}" selected="selected">{$product->manufacturer_name}</option>
				{/if}
				<option disabled="disabled">----------</option>
			</select>&nbsp;&nbsp;&nbsp;
			<a class="button bt-icon confirm_leave" style="margin-bottom:0" href="{$link->getAdminLink('AdminManufacturers')|escape:'htmlall':'UTF-8'}&addmanufacturer">
				<img src="../img/admin/add.gif" alt="{l s='Create new manufacturer'}" title="{l s='Create new manufacturer'}" />
				<span>{l s='Create new manufacturer'}</span>
			</a>
		</td>
		</tr>
	</table>
</div>