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

<script type="text/javascript">
// <![CDATA[
function initProductPage()
{
	// PrestaShop internal settings
	ProductFn.currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	ProductFn.currencyRate = '{$currencyRate|floatval}';
	ProductFn.currencyFormat = '{$currencyFormat|intval}';
	ProductFn.currencyBlank = '{$currencyBlank|intval}';
	ProductFn.taxRate = {$tax_rate|floatval};
	
	// Parameters
	ProductFn.id_product = '{$product->id|intval}';
	{if isset($groups)}ProductFn.productHasAttributes = true;{/if}
	{if $display_qties == 1}ProductFn.quantitiesDisplayAllowed = true;{/if}
	{if $display_qties == 1 && $product->quantity}ProductFn.quantityAvailable = {$product->quantity};{/if}
	{if $allow_oosp == 1}ProductFn.allowBuyWhenOutOfStock = true{/if};
		ProductFn.availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
		ProductFn.availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
		ProductFn.productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
	{if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}
		ProductFn.reduction_percent = {$product->specificPrice.reduction*100};
	{/if}
	{if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}
		ProductFn.reduction_price = {$product->specificPrice.reduction|floatval};
	{/if}
	{if $product->specificPrice AND $product->specificPrice.price}
		ProductFn.specific_price = {$product->specificPrice.price};
	{/if}
	{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
		ProductFn.product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
	{/foreach}
	
	{if $product->specificPrice AND $product->specificPrice.id_currency}
		ProductFn.specific_currency = true;
	{/if}
	ProductFn.group_reduction = '{$group_reduction}';
	ProductFn.default_eco_tax = {$product->ecotax};
	ProductFn.ecotaxTax_rate = {$ecotaxTax_rate};
	ProductFn.currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
	ProductFn.maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
	{if $no_tax == 1}ProductFn.noTaxForThisProduct = true;{/if}
	ProductFn.displayPrice = {$priceDisplay};
	ProductFn.productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
	ProductFn.productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
	{if !$PS_CATALOG_MODE}ProductFn.productShowPrice = '{$product->show_price}';{/if}
	ProductFn.productUnitPriceRatio = '{$product->unit_price_ratio}';
	{if isset($cover.id_image_only)}ProductDisplay.idDefaultImage = {$cover.id_image_only};{/if}
	
	{if !$priceDisplay || $priceDisplay == 2}
		{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 2)}
		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
	{elseif $priceDisplay == 1}
		{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 2)}
		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
	{/if}
	
	ProductFn.productPriceWithoutReduction = '{$productPriceWithoutReduction}';
	ProductFn.productPrice = '{$productPrice}';
	
	// Customizable field
	ProductFn.img_ps_dir = '{$img_ps_dir}';
	{assign var='imgIndex' value=0}
	{assign var='textFieldIndex' value=0}
	{foreach from=$customizationFields item='field' name='customizationFields'}
		{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
		ProductFn.customizationFields[{$smarty.foreach.customizationFields.index|intval}] = [];
		ProductFn.customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
		ProductFn.customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
	{/foreach}
	
	// Images
	ProductFn.img_prod_dir = '{$img_prod_dir}';
	
	{if isset($combinationImages)}
		{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
			ProductFn.combinationImages[{$combinationId}] = [];
			{foreach from=$combination item='image' name='f_combinationImage'}
				ProductFn.combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
			{/foreach}
		{/foreach}
	{/if}
	
	ProductFn.combinationImages[0] = [];
	{if isset($images)}
		{foreach from=$images item='image' name='f_defaultImages'}
			ProductFn.combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
		{/foreach}
	{/if}
	
	// Translations
	ProductFn.doesntExist = '{l s='The combination does not exist for this product. Please choose another.' js=1}';
	ProductFn.doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
	ProductFn.doesntExistNoMoreBut = '{l s='with those attributes but is available with others' js=1}';
	ProductFn.uploading_in_progress = '{l s='Uploading in progress, please wait...' js=1}';
	ProductFn.fieldRequired = '{l s='Please fill in all required fields, then save your customization.' js=1}';
	
	{if isset($groups)}
		// Combinations
		{foreach from=$combinations key=idCombination item=combination}
			var oSpecificPriceCombination = new SpecificPriceCombination();
			{if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}
				oSpecificPriceCombination.reduction_percent = {$combination.specific_price.reduction*100};
			{/if}
			{if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}
				oSpecificPriceCombination.reduction_price = {$combination.specific_price.reduction};
			{/if}
			{if $combination.specific_price AND $combination.specific_price.price}
				oSpecificPriceCombination.price = {$combination.specific_price.price};
			{/if}
			{if $combination.specific_price}
				oSpecificPriceCombination.reduction_type = '{$combination.specific_price.reduction_type}';
			{/if}
			var oCombination = new ProductCombination({$idCombination|intval});
			oCombination.idsAttributes = new Array({$combination.list});
			oCombination.quantity = {$combination.quantity};
			oCombination.price = {$combination.price};
			oCombination.ecotax = {$combination.ecotax};
			oCombination.idImage = {$combination.id_image};
			oCombination.reference = '{$combination.reference}';
			oCombination.unitPrice = {$combination.unit_impact};
			oCombination.minimalQuantity = {$combination.minimal_quantity};
			oCombination.availableDate = '{$combination.available_date}';
			oCombination.specific_price = oSpecificPriceCombination;
			ProductFn.combinations.push(oCombination);
			ProductFn.globalQuantity += oCombination.quantity;
		{/foreach}
	{/if}
	
	{if isset($attributesCombinations)}
		// Combinations attributes informations
		{foreach from=$attributesCombinations key=id item=aC}
			var oAttributeInfos = new AttributeCombination('{$aC.id_attribute|intval}');
			oAttributeInfos.attribute = '{$aC.attribute}';
			oAttributeInfos.group = '{$aC.group}';
			oAttributeInfos.id_attribute_group = '{$aC.id_attribute_group|intval}';
			ProductFn.attributesCombinations.push(oAttributeInfos);
		{/foreach}
	{/if}
}


//]]>
</script>