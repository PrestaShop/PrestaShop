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
*  @version  Release: $Revision: 6625 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction|floatval}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var product_specific_price = new Array();
{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
	product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
{/foreach}
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var stock_management = {$stock_management|intval};
{if !isset($priceDisplayPrecision)}
	{assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
	{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
	{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
	{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}

var productPriceWithoutRedution = '{$productPriceWithoutRedution}';
var productPrice = '{$productPrice}';

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='This combination does not exist for this product. Please choose another.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please wait...' js=1}';
var fieldRequired = '{l s='Please fill in all required fields, then save the customization.' js=1}';
{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		var specific_price_combination = new Array();
		specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
		specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
		specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
		specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, '{$combination.available_date}', specific_price_combination);
	{/foreach}
{/if}

{if isset($attributesCombinations)}
	// Combinations attributes informations
	var attributesCombinations = new Array();
	{foreach from=$attributesCombinations key=id item=aC}
		tabInfos = new Array();
		tabInfos['id_attribute'] = '{$aC.id_attribute|intval}';
		tabInfos['attribute'] = '{$aC.attribute}';
		tabInfos['group'] = '{$aC.group}';
		tabInfos['id_attribute_group'] = '{$aC.id_attribute_group|intval}';
		attributesCombinations.push(tabInfos);
	{/foreach}
{/if}
//]]>
</script>

{include file="$tpl_dir./breadcrumb.tpl"}
<div id="primary_block" class="clearfix">

	{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.'}
		<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
		<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		</p>
		<p id="admin-action-result"></p>
		</p>
	</div>
	{/if}

	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}

	<!-- right infos-->
	<div id="pb-right-column">
		<!-- product img-->
		<div id="image-block">
		{if $have_image}
			<span id="view_full_size">
				<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')}" {if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}" />
				<span class="span_link">{l s='View full size'}</span>
			</span>
		{else}
			<span id="view_full_size">
				<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
				<span class="span_link">{l s='View full size'}</span>
			</span>
		{/if}
		</div>
		{if isset($images) && count($images) > 0}
		<!-- thumbnails -->
		<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
		{if isset($images) && count($images) > 3}<span class="view_scroll_spacer"><a id="view_scroll_left" class="hidden" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Previous'}</a></span>{/if}
		<div id="thumbs_list">
			<ul id="thumbs_list_frame">
				{if isset($images)}
					{foreach from=$images item=image name=thumbnails}
					{assign var=imageIds value="`$product->id`-`$image.id_image`"}
					<li id="thumbnail_{$image.id_image}">
						<a href="{$link->getImageLink($product->link_rewrite, $imageIds, thickbox_default)}" rel="other-views" class="thickbox {if $smarty.foreach.thumbnails.first}shown{/if}" title="{$image.legend|htmlspecialchars}">
							<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
						</a>
					</li>
					{/foreach}
				{/if}
			</ul>
		</div>
		{if isset($images) && count($images) > 3}<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Next'}</a>{/if}
		</div>
		{/if}
		{if isset($images) && count($images) > 1}<p class="resetimg clear"><span id="wrapResetImages" style="display: none;"><img src="{$img_dir}icon/cancel_11x13.gif" alt="{l s='Cancel'}" width="11" height="13"/> <a id="resetImages" href="{$link->getProductLink($product)}" onclick="$('span#wrapResetImages').hide('slow');return (false);">{l s='Display all pictures'}</a></span></p>{/if}
		<!-- usefull links-->
		<ul id="usefull_link_block">
			{if $HOOK_EXTRA_LEFT}{$HOOK_EXTRA_LEFT}{/if}
			<li class="print"><a href="javascript:print();">{l s='Print'}</a></li>
			{if $have_image && !$jqZoomEnabled}
			{/if}
		</ul>
	</div>

	<!-- left infos-->
	<div id="pb-left-column">
		<h1>{$product->name|escape:'htmlall':'UTF-8'}</h1>

		{if $product->description_short OR $packItems|@count > 0}
		<div id="short_description_block">
			{if $product->description_short}
				<div id="short_description_content" class="rte align_justify">{$product->description_short}</div>
			{/if}
			{if $product->description}
			<p class="buttons_bottom_block"><a href="javascript:{ldelim}{rdelim}" class="button">{l s='More details'}</a></p>
			{/if}
			{if $packItems|@count > 0}
			<div class="short_description_pack">
				<h3>{l s='Pack content'}</h3>
				{foreach from=$packItems item=packItem}
				<div class="pack_content">
					{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
					<p>{$packItem.description_short}</p>
				</div>
				{/foreach}
			</div>
			{/if}
		</div>
		{/if}

		{*{if isset($colors) && $colors}
		<!-- colors -->
		<div id="color_picker">
			<p>{l s='Pick a color:' js=1}</p>
			<div class="clear"></div>
			<ul id="color_to_pick_list" class="clearfix">
			{foreach from=$colors key='id_attribute' item='color'}
				<li><a id="color_{$id_attribute|intval}" class="color_pick" style="background: {$color.value};" onclick="updateColorSelect({$id_attribute|intval});$('#wrapResetImages').show('slow');" title="{$color.name}">{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$color.name}" width="20" height="20" />{/if}</a></li>
			{/foreach}
			</ul>
			<div class="clear"></div>
		</div>
		{/if}*}

		{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
		<!-- add to cart form-->
		<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')}" method="post">

			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" value="{$static_token}" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>

			<div class="product_attributes">
				{if isset($groups)}
				<!-- attributes -->
				<div id="attributes">
				{foreach from=$groups key=id_attribute_group item=group}
					{if $group.attributes|@count}
						<fieldset class="attribute_fieldset">
							<label class="attribute_label" for="group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
							{assign var="groupName" value="group_$id_attribute_group"}
							<div class="attribute_list">
							{if ($group.group_type == 'select')}
								<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="attribute_select" onchange="findCombination();getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if};">
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							{elseif ($group.group_type == 'color')}
								<ul id="color_to_pick_list" class="clearfix">
									{assign var="default_colorpicker" value=""}
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
									<li{if $group.default == $id_attribute} class="selected"{/if}>
										<a id="color_{$id_attribute|intval}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}" style="background: {$colors.$id_attribute.value};" title="{$colors.$id_attribute.name}" onclick="colorPickerClick(this);getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if}">
											{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
												<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$colors.$id_attribute.name}" width="20" height="20" /><br>
											{/if}
										</a>
									</li>
									{if ($group.default == $id_attribute)}
										{$default_colorpicker = $id_attribute}
									{/if}
									{/foreach}
								</ul>
								<input type="hidden" class="color_pick_hidden" name="{$groupName}" value="{$default_colorpicker}" />
							{elseif ($group.group_type == 'radio')}
								{foreach from=$group.attributes key=id_attribute item=group_attribute}
									<input type="radio" class="attribute_radio" name="{$groupName}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} onclick="findCombination();getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if}">
									{$group_attribute|escape:'htmlall':'UTF-8'}<br/>
								{/foreach}
							{/if}
							</div>
						</fieldset>
					{/if}
				{/foreach}
				</div>
			{/if}
			<p id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>
				<label for="product_reference">{l s='Reference:'} </label>
				<span class="editable">{$product->reference|escape:'htmlall':'UTF-8'}</span>
			</p>

			<!-- quantity wanted -->
			<p id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) OR $virtual OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<label>{l s='Quantity:'}</label>
				<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" size="2" maxlength="3" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />
			</p>

			<!-- minimal quantity wanted -->
			<p id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				{l s='This product is not sold individually. You must select at least'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b> {l s='quantity for this product.'}
			</p>
			{if $product->minimal_quantity > 1}
			<script type="text/javascript">
				checkMinimalQuantity();
			</script>
			{/if}

			<!-- availability -->
			<p id="availability_statut"{if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<span id="availability_label">{l s='Availability:'}</span>
				<span id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>
				{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}
				</span>
			</p>

			<!-- number of item in stock -->
			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
			<p id="pQuantityAvailable"{if $product->quantity <= 0} style="display: none;"{/if}>
				<span id="quantityAvailable">{$product->quantity|intval}</span>
				<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='item in stock'}</span>
				<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='items in stock'}</span>
			</p>
			{/if}

			<!-- Out of stock hook -->
			<p id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
				{$HOOK_PRODUCT_OOS}
			</p>

			<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
		</div>

		<div class="content_prices clearfix">
			<!-- prices -->
			{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

			{if $product->online_only}
			<p class="online_only">{l s='Online only'}</p>
			{/if}

			<div class="price">
				{if !$priceDisplay || $priceDisplay == 2}
					{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
				{elseif $priceDisplay == 1}
					{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
				{/if}

				<p class="our_price_display">
				{if $priceDisplay >= 0 && $priceDisplay <= 2}
					<span id="our_price_display">{convertPrice price=$productPrice}</span>
					<!--{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
						{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
					{/if}-->
				{/if}
				</p>

				{if $product->on_sale}
					<img src="{$img_dir}onsale_{$lang_iso}.gif" alt="{l s='On sale'}" class="on_sale_img"/>
					<span class="on_sale">{l s='On sale!'}</span>
				{elseif $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutRedution > $productPrice}
					<span class="discount">{l s='Reduced price!'}</span>
				{/if}
				{if $priceDisplay == 2}
					<br />
					<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
				{/if}
			</div>
			<p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}><span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>
			<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' && $product->specificPrice.reduction|intval ==0} style="display:none"{/if}><span id="reduction_amount_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|intval !=0}-{convertPrice price=$product->specificPrice.reduction|floatval}{/if}</span></p>
			{if $product->specificPrice AND $product->specificPrice.reduction}
				<p id="old_price"><span class="bold">
				{if $priceDisplay >= 0 && $priceDisplay <= 2}
					{if $productPriceWithoutRedution > $productPrice}
						<span id="old_price_display">{convertPrice price=$productPriceWithoutRedution}</span>
						<!-- {if $tax_enabled && $display_tax_label == 1}
							{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
						{/if} -->
					{/if}
				{/if}
				</span>
				</p>
			{/if}
			{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
				<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
				<br class="clear" />
			{/if}
			{if $product->ecotax != 0}
				<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
					{if $product->specificPrice AND $product->specificPrice.reduction}
					<br />{l s='(not impacted by the discount)'}
					{/if}
				</p>
			{/if}
			{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
				 {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
				<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
			{/if}
			{*close if for show price*}
			{/if}
			{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}
				<span class="exclusive">
					<span></span>
					{l s='Add to cart'}
				</span>
			{else}
				<p id="add_to_cart" class="buttons_bottom_block">
					<span></span>
					<input type="submit" name="Submit" value="{l s='Add to cart'}" class="exclusive" />
				</p>
			{/if}
			{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}

			<div class="clear"></div>
		</div>
		</form>
		{/if}
		{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
	</div>
</div>

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<!-- quantity discount -->
<ul class="idTabs clearfix">
	<li><a href="#discount" style="cursor: pointer" class="selected">{l s='Quantity discount'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
        <thead>
            <tr>
                <th>{l s='product'}</th>
                <th>{l s='from (qty)'}</th>
                <th>{l s='discount'}</th>
            </tr>
        </thead>
		<tbody>
            {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
            <tr id="quantityDiscount_{$quantity_discount.id_product_attribute}">
                <td>
                    {if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}
                        {$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
                    {else}
                        {$product->getProductName($quantity_discount.id_product)}
                    {/if}
                </td>
                <td>{$quantity_discount.quantity|intval}</td>
                <td>
                    {if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
                       -{convertPrice price=$quantity_discount.real_value|floatval}
                   {else}
                       -{$quantity_discount.real_value|floatval}%
                   {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
	</table>
</div>
{/if}
{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}

<!-- description and features -->
{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
<div id="more_info_block" class="clear">
	<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">
		{if $product->description}<li><a id="more_info_tab_more_info" href="#idTab1">{l s='More info'}</a></li>{/if}
		{if $features}<li><a id="more_info_tab_data_sheet" href="#idTab2">{l s='Data sheet'}</a></li>{/if}
		{if $attachments}<li><a id="more_info_tab_attachments" href="#idTab9">{l s='Download'}</a></li>{/if}
		{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}
		{if isset($product) && $product->customizable}<li><a href="#idTab10">{l s='Product customization'}</a></li>{/if}
		{$HOOK_PRODUCT_TAB}
	</ul>
	<div id="more_info_sheets" class="sheets align_justify">
	{if isset($product) && $product->description}
		<!-- full description -->
		<div id="idTab1" class="rte">{$product->description}</div>
	{/if}
	{if isset($features) && $features}
		<!-- product's features -->
		<ul id="idTab2" class="bullet">
		{foreach from=$features item=feature}
            {if isset($feature.value)}
			    <li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> {$feature.value|escape:'htmlall':'UTF-8'}</li>
            {/if}
		{/foreach}
		</ul>
	{/if}
	{if isset($attachments) && $attachments}
		<ul id="idTab9" class="bullet">
		{foreach from=$attachments item=attachment}
			<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
		{/foreach}
		</ul>
	{/if}
	{if isset($accessories) AND $accessories}
		<!-- accessories -->
		<ul id="idTab4" class="bullet">
			<div class="block products_block accessories_block clearfix">
				<div class="block_content">
					<ul>
					{foreach from=$accessories item=accessory name=accessories_list}
						{if ($accessory.allow_oosp || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
							{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
							<li class="ajax_block_product {if $smarty.foreach.accessories_list.first}first_item{elseif $smarty.foreach.accessories_list.last}last_item{else}item{/if} product_accessories_description">
								<h5>
									<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}">{$accessory.name|escape:'htmlall':'UTF-8'}</a>
									{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE} - <span class="price">{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}</span>{/if}
								</h5>
								<div class="product_desc">
									<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.legend|escape:'htmlall':'UTF-8'}" class="product_image"><img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'medium_default')}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" /></a>
									<div class="block_description">
										<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='More'}" class="product_description">{$accessory.description_short|strip_tags|truncate:400:'...'}</a>
									</div>
									<div class="clear_product_desc">&nbsp;</div>
								</div>
								<p class="clearfix" style="margin-top:5px">
									<a class="button" href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='View'}">{l s='View'}</a>
									<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")}" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Add to cart'}">{l s='Add to cart'}</a>
								</p>
							</li>
						{/if}
					{/foreach}
					</ul>
				</div>
			</div>
		</ul>
	{/if}

	<!-- Customizable products -->
	{if isset($product) && $product->customizable}
		<div id="idTab10" class="bullet customization_block">
			<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
				<p class="infoCustomizable">
					{l s='After saving your customized product, remember to add it to your cart.'}
					{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
				</p>
				{if $product->uploadable_files|intval}
				<div class="customizableProductsFile">
					<h3>{l s='Pictures'}</h3>
					<ul id="uploadable_files" class="clearfix">
						{counter start=0 assign='customizationField'}
						{foreach from=$customizationFields item='field' name='customizationFields'}
							{if $field.type == 0}
								<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
									{if isset($pictures.$key)}
									<div class="customizationUploadBrowse">
										<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
										<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)}" title="{l s='Delete'}" >
											<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
										</a>
									</div>
									{/if}
									<div class="customizationUploadBrowse">
										<label class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}{if $field.required}<sup>*</sup>{/if}</label>
										<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="customization_block_input {if isset($pictures.$key)}filled{/if}" />
									</div>
								</li>
								{counter}
							{/if}
						{/foreach}
					</ul>
				</div>
				{/if}
				{if $product->text_fields|intval}
				<div class="customizableProductsText">
					<h3>{l s='Text'}</h3>
					<ul id="text_fields">
					{counter start=0 assign='customizationField'}
					{foreach from=$customizationFields item='field' name='customizationFields'}
						{if $field.type == 1}
						<li class="customizationUploadLine{if $field.required} required{/if}">
							<label for ="textField{$customizationField}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}</label>
							<textarea type="text" name="textField{$field.id_customization_field}" id="textField{$customizationField}" rows="1" cols="40" class="customization_block_input" />{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}</textarea>
						</li>
						{counter}
						{/if}
					{/foreach}
					</ul>
				</div>
				{/if}
				<p id="customizedDatas">
					<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
					<input type="hidden" name="submitCustomizedDatas" value="1" />
					<input type="button" class="button" value="{l s='Save'}" onclick="javascript:saveCustomization()" />
					<span id="ajax-loader" style="display:none"><img src="{$img_ps_dir}loader.gif" alt="loader" /></span>
				</p>
			</form>
			<p class="clear required"><sup>*</sup> {l s='required fields'}</p>
		</div>
	{/if}

	{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
	</div>
</div>
{/if}

{if isset($packItems) && $packItems|@count > 0}
	<div id="blockpack">
		<h2>{l s='Pack content'}</h2>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}
	</div>
{/if}

{/if}

