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
var productBasePriceTaxExcluded = {$product->base_price} - {$product->ecotax};
var attribute_anchor_separator = '{$attribute_anchor_separator|addslashes}';

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
	{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
	{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}


var productPriceWithoutReduction = '{$productPriceWithoutReduction}';
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
var doesntExist = '{l s='This combination does not exist for this product. Please select another combination.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others.' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please be patient.' js=1}';
var fieldRequired = '{l s='Please fill in all the required fields before saving your customization.' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		var specific_price_combination = new Array();
		var available_date = new Array();
		specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
		specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
		specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
		specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
		specific_price_combination['id_product_attribute'] = {if $combination.specific_price}{$combination.specific_price.id_product_attribute|intval}{else}0{/if};
		available_date['date'] = '{$combination.available_date}';
		available_date['date_formatted'] = '{dateFormat date=$combination.available_date full=false}';
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, available_date, specific_price_combination);
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
var isLoggedWishlist = {if $logged}true{else}false{/if};
var contentOnly = {if $content_only}true{else}false{/if}
//]]>
</script>
<div id="primary_block" class="row" itemscope itemtype="http://schema.org/Product">


{if !$content_only}<div class="container"><div class="top-hr"></div></div>{/if}

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
    
   <div id="pb-right-column" class="col-xs-12 col-sm-4 col-md-5">
		<!-- product img-->        
        
		<div id="image-block">

                       
      	{if $product->on_sale}
		<span class="sale-box"><span class="sale">{l s='Sale!'}</span></span>
		{elseif $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutReduction > $productPrice}
		<span class="discount">{l s='Reduced price!'}</span>
		{/if} 
        
		{if $have_image}
			<span id="view_full_size">
				<img itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html'}"{if $jqZoomEnabled && $have_image} class="jqzoom"{/if} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}"/>
				{if !$content_only}<span class="span_link">{l s='View larger'}</span>{/if}
			</span>
		{else}
			<span id="view_full_size">
				<img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
				{if !$content_only}<span class="span_link">{l s='View larger'}</span>{/if}
			</span>
		{/if}
		</div>
		{if isset($images) && count($images) > 0}
		<!-- thumbnails -->
		<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
		{if isset($images) && count($images) > 3}<span class="view_scroll_spacer"><a id="view_scroll_left" class="" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Previous'}</a></span>{/if}
		<div id="thumbs_list">
			<ul id="thumbs_list_frame">
				{if isset($images)}
					{foreach from=$images item=image name=thumbnails}
					{assign var=imageIds value="`$product->id`-`$image.id_image`"}
					<li id="thumbnail_{$image.id_image}" {if $smarty.foreach.thumbnails.last} class="last"{/if} >
						<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html'}" rel="other-views" class="thickbox{if $smarty.foreach.thumbnails.first} shown{/if}" title="{$image.legend|htmlspecialchars}">
							<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')|escape:'html'}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
						</a>
					</li>
					{/foreach}
				{/if}
			</ul>
		</div>
		{if isset($images) && count($images) > 3}<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Next'}</a>{/if}
		</div>
		{/if}
		{if isset($images) && count($images) > 1}<p class="resetimg clear"><span id="wrapResetImages" style="display: none;">
         <a id="resetImages" href="{$link->getProductLink($product)|escape:'html'}" onclick="$('span#wrapResetImages').hide('slow');return (false);"><i class="icon-repeat"></i>{l s='Display all pictures'}</a></span></p>{/if}

	</div> 
   	<!-- end right infos--> 

        <!---- left infos---->
	<div id="pb-left-column" class="col-xs-12 col-sm-4">
    
    	{if $product->online_only}
			<p class="online_only">{l s='Online only'}</p>
			{/if}
    
		<h1 itemprop="name">{$product->name|escape:'htmlall':'UTF-8'}</h1>
			<p id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>
				<label>{l s='Model'} </label>
				<span class="editable" itemprop="sku">{$product->reference|escape:'htmlall':'UTF-8'}</span>
			</p>
		{if $product->description_short OR $packItems|@count > 0}
		<div id="short_description_block">
			{if $product->description_short}
				<div id="short_description_content" class="rte align_justify" itemprop="description">{$product->description_short}</div>
			{/if}
			{if $product->description}
			<p class="buttons_bottom_block"><a href="javascript:{ldelim}{rdelim}" class="button">{l s='More details'}</a></p>
			{/if}
		<!--	{if $packItems|@count > 0}
            
			<div class="short_description_pack">
	        <h3>{l s='Pack content'}</h3>
				{foreach from=$packItems item=packItem}
                
				<div class="pack_content">
					{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)|escape:'html'}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
					<p>{$packItem.description_short}</p>
				</div>
				{/foreach}
			</div>
            
			{/if}-->
		</div>
		{/if}

			<!-- number of item in stock -->
			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
			<p id="pQuantityAvailable"{if $product->quantity <= 0} style="display: none;"{/if}>
				<span id="quantityAvailable">{$product->quantity|intval}</span>
				<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='Item'}</span>
				<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='Items'}</span>
			</p>
			{/if}
            

			<!-- availability -->
			<p id="availability_statut"{if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				{*<span id="availability_label">{l s='Availability:'}</span>*}
				<span id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}</span>				
			</p>
            
			<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
            
			<p id="availability_date"{if ($product->quantity > 0) OR !$product->available_for_order OR $PS_CATALOG_MODE OR !isset($product->available_date) OR $product->available_date < $smarty.now|date_format:'%Y-%m-%d'} style="display: none;"{/if}>
				<span id="availability_date_label">{l s='Availability date:'}</span>
				<span id="availability_date_value">{dateFormat date=$product->available_date full=false}</span>
			</p>
            
            

			<!-- Out of stock hook -->
			<div id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
				{$HOOK_PRODUCT_OOS}
			</div>

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
		<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')|escape:'html'}" method="post">
			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" value="{$static_token}" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>
		</form>
		{/if}
		{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}        
        {if !$content_only}
        <!-- usefull links-->
		<ul id="usefull_link_block" class="clearfix">
			{if $HOOK_EXTRA_LEFT}{$HOOK_EXTRA_LEFT}{/if}
			<li class="print"><a href="javascript:print();">{l s='Print'}</a></li>
			{if $have_image && !$jqZoomEnabled}{/if}
			
		</ul>         
        {/if}
	</div>    
    
    <!-- end left infos-->  
    
    
   <!-- pb-right-column1-->      
<div id="pb-right-column1" class="col-xs-12 col-sm-4  col-md-3">       
<div class="box-info-product">  
     <div class="content_prices clearfix">
			<!-- prices -->
            {if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}		

			<div class="price">
				<p class="our_price_display" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <link itemprop="availability" href="http://schema.org/InStock">
				{if $priceDisplay >= 0 && $priceDisplay <= 2}
					<span id="our_price_display" itemprop="price">{convertPrice price=$productPrice}</span>
					<!--{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
						{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
					{/if}-->
				{/if}
				</p>

<p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}><span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>


		<p id="old_price"{if !$product->specificPrice || !$product->specificPrice.reduction} class="hidden"{/if}>
			{if $priceDisplay >= 0 && $priceDisplay <= 2}
					<span id="old_price_display">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction}{/if}</span>
					<!-- {if $tax_enabled && $display_tax_label == 1}{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}{/if} -->
			{/if}
			</p>
				{if $priceDisplay == 2}
					<br />
					<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
				{/if}
			</div>            
            
			<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|intval ==0} style="display:none"{/if}>
				<span id="reduction_amount_display">
				{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' AND $product->specificPrice.reduction|intval !=0}
					-{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
				{/if}
				</span>
			</p>
	
			{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
				<p class="pack_price">{l s='Instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
			
			{/if}
			{if $product->ecotax != 0}
				<p class="price-ecotax">{l s='Include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='For green tax'}
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

			<div class="clear"></div>
		</div>       
             
       <div class="product_attributes">
       
  <!-- quantity wanted -->
			<p id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) OR $virtual OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<label>{l s='Quantity:'}</label>
                
               
               <input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" size="2" maxlength="3" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />
             <a href="#"  rel="qty" class="btn btn-default button-minus product_quantity_down"><span><i class="icon-minus"></i></span></a>
             <a href="#"  rel="qty" class="btn btn-default button-plus product_quantity_up "><span><i class="icon-plus"></i></span></a>
		<span class="clearfix"></span>

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
       
				{if isset($groups)}
				<!-- attributes -->
				<div id="attributes">
				<div class="clear"></div>
				{foreach from=$groups key=id_attribute_group item=group}
					{if $group.attributes|@count}
						<fieldset class="attribute_fieldset">
							<label class="attribute_label" for="group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'} :&nbsp;</label>
							{assign var="groupName" value="group_$id_attribute_group"}
							<div class="attribute_list">
							{if ($group.group_type == 'select')}
								<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="form-control attribute_select" onchange="findCombination();getProductAttribute();">
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							{elseif ($group.group_type == 'color')}
								<ul id="color_to_pick_list" class="clearfix">
									{assign var="default_colorpicker" value=""}
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
									<li{if $group.default == $id_attribute} class="selected"{/if}>
										<a id="color_{$id_attribute|intval}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}" style="background: {$colors.$id_attribute.value};" title="{$colors.$id_attribute.name}" onclick="colorPickerClick(this);getProductAttribute();">
											{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
												<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$colors.$id_attribute.name}" width="20" height="20" /><br />
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
								<ul>
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										<li>
											<input type="radio" class="attribute_radio" name="{$groupName}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} onclick="findCombination();getProductAttribute();" />
											<span>{$group_attribute|escape:'htmlall':'UTF-8'}</span>
										</li>
									{/foreach}
								</ul>
							{/if}
							</div>
						</fieldset>
					{/if}
				{/foreach}
				</div>
			{/if}
           
            
		</div>
        
        <div class="box-cart-bottom">        
        
     <p id="add_to_cart" {if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}style="display:none"{/if} class="buttons_bottom_block">	
    	<button type="submit" name="Submit" class="exclusive"><span>{l s='Add to cart'}</span> </button>
	</p>	

	{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}<strong></strong>

        
        </div>
        
        {if !$content_only}
        <div class="box-security">
        <h5 class="product-heading-h5">{l s='Your Security Guaranteed'}</h5> 
        		<img class="logo-payment" src="{$img_dir}payment-logo.png" alt="" />     
                  
        </div>
        
        {/if}
           
       </div>       
       
       </div>       
    <!-- end pb-right-column1-->     
    
</div>

{if !$content_only}
<!-- More info -->
{if $product->description}
    <section class="page-product-box">
    	<h3 class="page-product-heading">{l s='More info'}</h3>{/if}
        {if isset($product) && $product->description}
            <!-- full description -->
            <div  class="rte">{$product->description}</div>
    </section>  
{/if}
<!--end  More info -->


<!-- Data sheet -->
{if $features}
<section class="page-product-box">
<h3 class="page-product-heading">{l s='Data sheet'}</h3>{/if}
{if isset($features) && $features}
<table class="table-data-sheet">			
		{foreach from=$features item=feature}
        <tr class="{cycle values="odd,even"}">
            {if isset($feature.value)}			    
                <td>{$feature.name|escape:'htmlall':'UTF-8'}</td><td> {$feature.value|escape:'htmlall':'UTF-8'}</td>
            {/if}
		</tr>
        {/foreach}
   </table>
</section>
{/if}
<!--end Data sheet -->


<!--HOOK_PRODUCT_TAB -->
<section class="page-product-box">
	{$HOOK_PRODUCT_TAB}
	{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
 </section>
<!--end HOOK_PRODUCT_TAB -->

<!--Accessories -->

{if isset($accessories) AND $accessories}
<section class="page-product-box">
	<h3 class="page-product-heading">{l s='Accessories'}</h3>
	<!-- accessories -->		
		<div class="block products_block accessories-block clearfix">
				<div class="block_content">
					<ul id="bxslider" class="bxslider clearfix">
					{foreach from=$accessories item=accessory name=accessories_list}
						{if ($accessory.allow_oosp || $accessory.quantity_all_versions > 0 || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode)}
							{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
							<li class="item product-box ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if} product_accessories_description">
						
								<div class="product_desc">
								<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.legend|escape:'htmlall':'UTF-8'}" class="product-image product_image"><img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html'}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}" width="{$homeSize.width}" height="{$homeSize.height}" class="lazyOwl" /></a>
                                
                                		<div class="block_description">                                    
										<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='More'}" class="product_description">{$accessory.description_short|strip_tags|truncate:25:'...'}</a>
									</div>								
								</div>
								
                             		<div class="s_title_block">
									<h5 class="product-name"><a href="{$accessoryLink|escape:'htmlall':'UTF-8'}">      {$accessory.name|truncate:20:'...':true|escape:'htmlall':'UTF-8'}</a></h5>
                                    
									{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                                    <span class="price">{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}</span>{/if}
								</div>  
                                
								<div class="clearfix" style="margin-top:5px">									
									{if !$PS_CATALOG_MODE && ($accessory.allow_oosp || $accessory.quantity > 0)}
<div>
<div>
<div>
	<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html'}" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a> </div> </div> </div>
                               
                                    
									{/if}
								</div>
								
							</li>
						{/if}
					{/foreach}
					</ul>
				</div>
			</div>	
</section>    	
{/if}
<!--end Accessories -->

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}

<!-- quantity discount -->
<section class="page-product-box">
<h3 class="page-product-heading">{l s='Volume discounts'}</h3>
<div id="quantityDiscount">
	<table class="std table-produst-discounts">
        <thead>
            <tr>                
                <th>{l s='Quantity'}</th>
                <th>{l s='Discount'}</th>
                <th>{l s='You Save'}</th>
            </tr>
        </thead>
		<tbody>
            {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
            <tr id="quantityDiscount_{$quantity_discount.id_product_attribute}" class="quantityDiscount_{$quantity_discount.id_product_attribute}">
            <td>{$quantity_discount.quantity|intval}</td>             
                
                <td>
                    {if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
                       {convertPrice price=$quantity_discount.real_value|floatval}
                   {else}
                       {$quantity_discount.real_value|floatval}%
                   {/if}
                </td>
                <td>
					<span>{l s='Up to'}</span>
					{if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
						{$discountPrice=$productPrice-$quantity_discount.real_value|floatval}
					{else}
						{$discountPrice=$productPrice-($productPrice*$quantity_discount.reduction)|floatval}
					{/if}
					{$discountPrice=$discountPrice*$quantity_discount.quantity}
					{$qtyProductPrice = $productPrice*$quantity_discount.quantity}
					{convertPrice price=$qtyProductPrice-$discountPrice}
                </td>
            </tr>
            {/foreach}
        </tbody>
	</table>
</div>
{/if}

</section>
{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}


<!-- description and features -->
{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}


<!--Download -->
	{if isset($attachments) && $attachments}
    <section class="page-product-box">
    	<h3 class="page-product-heading">{l s='Download'}</h3>
		{foreach from=$attachments item=attachment}
		<a  href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html'}"><span>{$attachment.name|escape:'htmlall':'UTF-8'}</span></a><br />{$attachment.description|escape:'htmlall':'UTF-8'}
		{/foreach}	
        
</section>    
	{/if}    
<!--end Download -->



<!--Customization -->
{if isset($product) && $product->customizable}	
<section class="page-product-box">

	<h3 class="page-product-heading">{l s='Product customization'}</h3>

<!-- Customizable products -->

	   <form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
				<p class="infoCustomizable">
					{l s='After saving your customized product, remember to add it to your cart.'}
					{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
				</p>
				{if $product->uploadable_files|intval}
				<div class="customizableProductsFile">
					<h5 class="product-heading-h5">{l s='Pictures'}</h5>
					<ul id="uploadable_files" class="clearfix">
						{counter start=0 assign='customizationField'}
						{foreach from=$customizationFields item='field' name='customizationFields'}
							{if $field.type == 0}
								<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
									{if isset($pictures.$key)}
									<div class="customizationUploadBrowse">
										<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
										<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html'}" title="{l s='Delete'}" >
											<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
										</a>
									</div>
									{/if}
									<div class="customizationUploadBrowse form-group">
										<label class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your computer'}{/if}{if $field.required}<sup>*</sup>{/if}</label>
										<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="form-control customization_block_input {if isset($pictures.$key)}filled{/if}" />
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
					<h5 class="product-heading-h5">{l s='Text'}</h5>
					<ul id="text_fields">
					{counter start=0 assign='customizationField'}
					{foreach from=$customizationFields item='field' name='customizationFields'}
						{if $field.type == 1}
						<li class="customizationUploadLine{if $field.required} required{/if}">
							<label for ="textField{$customizationField}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}</label>
							<textarea name="textField{$field.id_customization_field}" class="form-control" id="textField{$customizationField}" rows="3" cols="20" class="customization_block_input">{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if} </textarea>
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
					<!--<input type="button" class="button btn btn-default button button-small" value="{l s='Save'}" onclick="javascript:saveCustomization()" />-->
                    <button  class="button btn btn-default button button-small"  onclick="javascript:saveCustomization()" /><span>{l s='Save'}</span></button>
					<span id="ajax-loader" style="display:none"><img src="{$img_ps_dir}loader.gif" alt="loader" /></span>
				</p>
			</form>
			<p class="clear required"><sup>*</sup> {l s='required fields'}</p>	
</section>
	{/if}
<!--end Customization -->




{/if}
{if isset($packItems) && $packItems|@count > 0}
	<section id="blockpack">
		<h3 class="page-product-heading">{l s='Pack content'}</h3>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}
	</section>
{/if}
{/if}


<script type="text/javascript">
$(document).ready(function() {	
 
$('#bxslider').bxSlider({
  minSlides: 1,
  maxSlides: 6,
  slideWidth: 178,
  slideMargin: 20,
  pager: false,
  nextText: '',
  prevText: '',
  moveSlides:1,
  infiniteLoop:false,
  hideControlOnEnd: true
	 });

	});
	</script>

{/if}
