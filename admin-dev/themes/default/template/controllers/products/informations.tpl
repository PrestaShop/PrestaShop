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
{if $check_product_association_ajax}
	{assign var=class_input_ajax value='check_product_name '}
{else}
	{assign var=class_input_ajax value=''}
{/if}
	<input type="hidden" name="submitted_tabs[]" value="Informations" />
	<legend class="tab">{l s='Info.'}</legend>
	<div class="alert alert-info">{l s='Product global information'}</div>
	<script type="text/javascript">
		{if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		{else}
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		{/if}
		{$combinationImagesJs}
		{if $check_product_association_ajax}
				var search_term = '';
				$('document').ready( function() {
					$(".check_product_name")
						.autocomplete(
							'{$link->getAdminLink('AdminProducts', true)|escape:'html'}', {
								minChars: 3,
								max: 10,
								width: $(".check_product_name").width(),
								selectFirst: false,
								scroll: false,
								dataType: "json",
								formatItem: function(data, i, max, value, term) {
									search_term = term;
									// adding the little
									if ($('.ac_results').find('.separation').length == 0)
										$('.ac_results').css('background-color', '#EFEFEF')
											.prepend('<div style="color:#585A69; padding:2px 5px">{l s='Use a product from the list'}<div class="separation"></div></div>');
									return value;
								},
								parse: function(data) {
									var mytab = new Array();
									for (var i = 0; i < data.length; i++)
										mytab[mytab.length] = { data: data[i], value: data[i].name };
									return mytab;
								},
								extraParams: {
									ajax: 1,
									action: 'checkProductName',
									id_lang: {$id_lang}
								}
							}
						)
						.result(function(event, data, formatted) {
							// keep the searched term in the input
							$('#name_{$id_lang}').val(search_term);
							jConfirm('{l s='Do you want to use this product?'}&nbsp;<strong>'+data.name+'</strong>', '{l s='Confirmation'}', function(confirm){
								if (confirm == true)
									document.location.href = '{$link->getAdminLink('AdminProducts', true)}&updateproduct&id_product='+data.id_product;
								else
									return false;
							});
						});
				});
		{/if}
	</script>

	{if isset($display_common_field) && $display_common_field}
		<div class="alert alert-block" style="display: block">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product' sprintf=$bullet_common_field}</div>
	{/if}

	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Informations"}

<div id="warn_virtual_combinations" class="alert alert-block" style="display:none">{l s='You cannot use combinations with a virtual product.'}</div>

<div class="row">
	<label class="control-label col-lg-3">{$bullet_common_field} {l s='Type:'}</label>
	<div class="col-lg-9">
		<div class="radio">
			<label for="simple_product">
				<input type="radio" name="type_product" id="simple_product" value="{Product::PTYPE_SIMPLE}" {if $product_type == Product::PTYPE_SIMPLE}checked="checked"{/if} />
				{l s='Product'}
			</label>
		</div>
		<div class="radio">
			<label for="pack_product">
				<input type="radio" name="type_product" {if $is_in_pack}disabled="disabled"{/if} id="pack_product" value="{Product::PTYPE_PACK}" {if $product_type == Product::PTYPE_PACK}checked="checked"{/if} />
				{l s='Pack'}
			</label>
		</div>
		<div class="radio">
			<label for="virtual_product">
				<input type="radio" name="type_product" id="virtual_product" {if $is_in_pack}disabled="disabled"{/if} value="{Product::PTYPE_VIRTUAL}" {if $product_type == Product::PTYPE_VIRTUAL}checked="checked"{/if} />
				{l s='Virtual Product (services, booking or downloadable products)'}
			</label>
		</div>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3 required">{l s='Name:'}</label>
	<div class="col-lg-5">
		<div class="row">
			{include file="controllers/products/input_text_lang.tpl"
				languages=$languages
				input_class="{$class_input_ajax}{if !$product->id}copy2friendlyUrl{/if} updateCurrentText"
				input_value=$product->name
				input_name="name"
			}
		</div>
		<span class="help-block" name="help_box">{l s='Invalid characters:'} &lt;&gt;;=#{}</span>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3">{$bullet_common_field} {l s='Reference:'}</label>
	<div class="col-lg-5">
		<input type="text" name="reference" value="{$product->reference|htmlentitiesUTF8}" />
		<span class="help-block" name="help_box">{l s='Special characters allowed:'} .-_#\</span>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3">{$bullet_common_field} {l s='EAN13 or JAN:'}</label>
	<div class="col-lg-5">
		<input maxlength="13" type="text" name="ean13" value="{$product->ean13|htmlentitiesUTF8}" />
		<span class="help-block">{l s='(Europe, Japan)'}</span>
	</div>
</div>

<div class="row">
	<label class="control-label col-lg-3">{$bullet_common_field} {l s='UPC:'}</label>
	<div class="col-lg-5">
		<input maxlength="12" type="text" name="upc" value="{$product->upc|escape:html:'UTF-8'}" />
		<span class="help-block">{l s='(US, Canada)'}</span>
	</div>
</div>

{* status informations *}
{include file="controllers/products/multishop/checkbox.tpl" field="active" type="radio" onclick=""}
<div class="row">
	<label class="control-label col-lg-3">{l s='Status:'}</label>
	<div class="input-group col-lg-3">
		<span class="switch prestashop-switch">
			<input onclick="toggleDraftWarning(false);showOptions(true);showRedirectProductOptions(false);" type="radio" name="active" id="active_on" value="1" {if $product->active || !$product->isAssociatedToShop()}checked="checked" {/if} />
			<label for="active_on" class="radioCheck">
				<i class="icon-check-sign"></i> {l s='Enabled'}
			</label>
			<input onclick="toggleDraftWarning(true);showOptions(false);showRedirectProductOptions(true);"  type="radio" name="active" id="active_off" value="0" {if !$product->active && $product->isAssociatedToShop()}checked="checked"{/if} />
			<label for="active_off" class="radioCheck">
				<i class="icon-ban-circle"></i> {l s='Disabled'}
			</label>
			<span class="slide-button btn btn-default"></span>
		</span>
	</div>
</div>

<div class="row redirect_product_options" style="display:none">
	{include file="controllers/products/multishop/checkbox.tpl" field="active" type="radio" onclick=""}
	<label class="control-label col-lg-3">{l s='Redirect:'}</label>
	<div class="col-lg-5">
		<select name="redirect_type" id="redirect_type">
			<option value="404" {if $product->redirect_type == '404'} selected="selected" {/if}>{l s='No redirect (404)'}</option>
			<option value="301" {if $product->redirect_type == '301'} selected="selected" {/if}>{l s='Redirect permanently (301)'}</option>
			<option value="302" {if $product->redirect_type == '302'} selected="selected" {/if}>{l s='Redirect temporarily (302)'}</option>
		</select>
		<div class="help-block" name="help_box">
			<ul class="list-unstyled">
				<li>{l s='404 : Not Found = Product does not exist and no redirect'}</li>
				<li>{l s='301 : Moved Permanently = Product Moved Permanently'}</li>
				<li>{l s='302 : Moved Temporarily = Product moved temporarily'}</li>
			</ul>
		</div>
	</div>
</div>

<div class="row redirect_product_options redirect_product_options_product_choise" style="display:none">
	{include file="controllers/products/multishop/checkbox.tpl" field="active" type="radio" onclick=""}
	
	<label class="control-label col-lg-3">{l s='Related product:'}</label>
	<div class="col-lg-5">
		<input type="hidden" value="" name="id_product_redirected" />
		<input type="text" id="related_product_autocomplete_input" autocomplete="off" class="ac_input" />
		<span id="related_product_remove" style="display:none">
			<a class="btn btn-default" href="#" onclick="removeRelatedProduct(); return false" id="related_product_remove_link">
				<i class="icon-trash"></i>
			</a>
		</span>
		<span id="related_product_name">{l s='No related product'}</span>
	</div>
	<script>
		var no_related_product = '{l s='No related product'}';
		var id_product_redirected = {$product->id_product_redirected|intval};
		var product_name_redirected = '{$product_name_redirected|escape:html:'UTF-8'}';
	</script>
</div>




<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="visibility" type="default"}
	<label class="control-label col-lg-3">{l s='Visibility:'}</label>
	<div class="col-lg-5">
		<select name="visibility" id="visibility">
			<option value="both" {if $product->visibility == 'both'}selected="selected"{/if} >{l s='Everywhere'}</option>
			<option value="catalog" {if $product->visibility == 'catalog'}selected="selected"{/if} >{l s='Catalog only'}</option>
			<option value="search" {if $product->visibility == 'search'}selected="selected"{/if} >{l s='Search only'}</option>
			<option value="none" {if $product->visibility == 'none'}selected="selected"{/if}>{l s='Nowhere'}</option>
		</select>
	</div>
</div>


<div id="product_options" class="row" {if !$product->active}style="display:none"{/if} >
	<div class="col-lg-12">
		{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
		<div class="row multishop_product_checkbox">
			{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="available_for_order" type="default"}
			{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="show_price" type="show_price"}
			{include file="controllers/products/multishop/checkbox.tpl" only_checkbox="true" field="online_only" type="default"}
		</div>
		{/if}
		<div class="row">
			<label class="control-label col-lg-3">{l s='Options:'}</label>
			<div class="col-lg-5">
				<p class="checkbox">
					<input type="checkbox" name="available_for_order" id="available_for_order" value="1" {if $product->available_for_order}checked="checked"{/if}  />
					<label for="available_for_order" class="t">{l s='Available for order'}</label>
				</p>
				<p class="checkbox">	
					<input type="checkbox" name="show_price" id="show_price" value="1" {if $product->show_price}checked="checked"{/if} {if $product->available_for_order}disabled="disabled"{/if}/>
					<label for="show_price" class="t">{l s='show price'}</label>
				</p>
				<p class="checkbox">
					<input type="checkbox" name="online_only" id="online_only" value="1" {if $product->online_only}checked="checked"{/if} />
					<label for="online_only" class="t">{l s='Online only (not sold in store)'}</label>
				</p>
			</div>
		</div>
		<div class="row">
			{include file="controllers/products/multishop/checkbox.tpl" field="condition" type="default"}
			<label class="control-label col-lg-3">{l s='Condition:'}</label>
			<div class="col-lg-5">
				<select name="condition" id="condition">
					<option value="new" {if $product->condition == 'new'}selected="selected"{/if} >{l s='New'}</option>
					<option value="used" {if $product->condition == 'used'}selected="selected"{/if} >{l s='Used'}</option>
					<option value="refurbished" {if $product->condition == 'refurbished'}selected="selected"{/if}>{l s='Refurbished'}</option>
				</select>
			</div>	
		</div>
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="description_short" type="tinymce" multilang="true"}
	<label class="control-label col-lg-3">{l s='Short description:'}</label>
	<div class="col-lg-5">
		{include file="controllers/products/textarea_lang.tpl" languages=$languages input_name='description_short' input_value=$product->description_short max=$PS_PRODUCT_SHORT_DESC_LIMIT}
		<p class="help-block">({l s='Appears in the product list(s), and on the top of the product page.'})</p>
	</div>
</div>

<div class="row">
	{include file="controllers/products/multishop/checkbox.tpl" field="description" type="tinymce" multilang="true"}
	<label class="control-label col-lg-3">{l s='Description:'}</label>
	<div class="col-lg-5">
		{include file="controllers/products/textarea_lang.tpl" languages=$languages input_name='description' input_value=$product->description}
		<p class="help-block">({l s='Appears in the body of the product page'})</p>
	</div>
</div>



{if $images}
<div class="alert">
	{l s='Do you want an image associated with the product in your description?'}
	<a class="addImageDescription" href="javascript:void(0);">{l s='Click here'}</a>.
</div>

<div id="createImageDescription" class="well" style="display:none">

	<div class="row">
		<label class="control-label col-lg-3">{l s='Select your image:'}</label>
		<div class="col-lg-5">
			{foreach from=$images item=image key=key}
			<p class="checkbox-inline">
				<input type="radio" name="smallImage" id="smallImage_{$key}" value="{$image.id_image}" {if $key == 0}checked="checked"{/if} >
				<label for="smallImage_{$key}" class="t">
					<img src="{$image.src}" alt="{$image.legend}" />
				</label>
			</p>
			{/foreach}
		</div>
	</div>

	<div class="row">
		<label class="control-label col-lg-3">{l s='Position:'}</label>
		<div class="col-lg-5">
			<p class="checkbox">
				<input type="radio" name="leftRight" id="leftRight_1" value="left" checked>							
				<label for="leftRight_1" class="t">{l s='left'}</label>
			</p>
			<p class="checkbox">
				<input type="radio" name="leftRight" id="leftRight_2" value="right">
				<label for="leftRight_2" class="t">{l s='right'}</label>
			</p>
		</div>
	</div>

	<div class="row">
		<label class="control-label col-lg-3">{l s='Select the type of picture:'}</label>
		<div class="col-lg-5">
			{foreach from=$imagesTypes key=key item=type}
			<p class="checkbox">
				<input type="radio" name="imageTypes" id="imageTypes_{$key}" value="{$type.name}" {if $key == 0}checked="checked"{/if}>
				<label for="imageTypes_{$key}" class="t">
					{$type.name} <span>({$type.width}px {l s='by'} {$type.height}px)</span>
				</label>
			</p>
			{/foreach}
		</div>
	</div>

	<div class="row">
		<label class="control-label col-lg-3">{l s='Image tag to insert:'}</label>
		<div class="col-lg-4">
			<input type="text" id="resultImage" name="resultImage" />
		</div>
		<p class="help-block">{l s='The tag to copy/paste into the description.'}</p>
	</div>

</div>
{/if}

<div class="row">
	<label class="control-label col-lg-3">{l s='Tags:'}</label>
	<div class="col-lg-5 translatable">
		{foreach from=$languages item=language}
		<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display: none;{/if}">
			<input type="text" id="tags_{$language.id_lang}" name="tags_{$language.id_lang}"
				value="{$product->getTags($language.id_lang, true)|htmlentitiesUTF8}" />
			<p class="help-block" name="help_box">{l s='Forbidden characters:'} !&lt;;&gt;;?=+#&quot;&deg;{}_$%</p>
		</div>
		{/foreach}
		<p class="help-block">{l s='Tags separated by commas (e.g. dvd, dvd player, hifi)'}</p>
	</div>
</div>