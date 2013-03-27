/*
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
*/


//global variables
var combinations = [];
var selectedCombination = [];
var globalQuantity = 0;
var colors = [];

//check if a function exists
function function_exists(function_name)
{
	if (typeof function_name == 'string')
		return (typeof window[function_name] == 'function');
	return (function_name instanceof Function);
}

//execute oosHook js code
function oosHookJsCode()
{
	for (var i = 0; i < oosHookJsCodeFunctions.length; i++)
	{
		if (function_exists(oosHookJsCodeFunctions[i]))
			setTimeout(oosHookJsCodeFunctions[i] + '()', 0);
	}
}

//add a combination of attributes in the global JS sytem
function addCombination(idCombination, arrayOfIdAttributes, quantity, price, ecotax, id_image, reference, unit_price, minimal_quantity, available_date, combination_specific_price)
{
	globalQuantity += quantity;

	var combination = [];
	combination['idCombination'] = idCombination;
	combination['quantity'] = quantity;
	combination['idsAttributes'] = arrayOfIdAttributes;
	combination['price'] = price;
	combination['ecotax'] = ecotax;
	combination['image'] = id_image;
	combination['reference'] = reference;
	combination['unit_price'] = unit_price;
	combination['minimal_quantity'] = minimal_quantity;
	combination['available_date'] = [];
	combination['available_date'] = available_date;
	combination['specific_price'] = [];
	combination['specific_price'] = combination_specific_price;
	combinations.push(combination);
}

// search the combinations' case of attributes and update displaying of availability, prices, ecotax, and image
function findCombination(firstTime)
{
	$('#minimal_quantity_wanted_p').fadeOut();
	$('#quantity_wanted').val(1);
	//create a temporary 'choice' array containing the choices of the customer
	var choice = [];
	$('#attributes select, #attributes input[type=hidden], #attributes input[type=radio]:checked').each(function(){
		choice.push($(this).val());
	});

	//testing every combination to find the conbination's attributes' case of the user
	for (var combination = 0; combination < combinations.length; ++combination)
	{
		//verify if this combinaison is the same that the user's choice
		var combinationMatchForm = true;
		$.each(combinations[combination]['idsAttributes'], function(key, value)
		{
			if (!in_array(value, choice))
				combinationMatchForm = false;
		});

		if (combinationMatchForm)
		{
			if (combinations[combination]['minimal_quantity'] > 1)
			{
				$('#minimal_quantity_label').html(combinations[combination]['minimal_quantity']);
				$('#minimal_quantity_wanted_p').fadeIn();
				$('#quantity_wanted').val(combinations[combination]['minimal_quantity']);
				$('#quantity_wanted').bind('keyup', function() {checkMinimalQuantity(combinations[combination]['minimal_quantity']);});
			}
			//combination of the user has been found in our specifications of combinations (created in back office)
			selectedCombination['unavailable'] = false;
			selectedCombination['reference'] = combinations[combination]['reference'];
			$('#idCombination').val(combinations[combination]['idCombination']);

			//get the data of product with these attributes
			quantityAvailable = combinations[combination]['quantity'];
			selectedCombination['price'] = combinations[combination]['price'];
			selectedCombination['unit_price'] = combinations[combination]['unit_price'];
			selectedCombination['specific_price'] = combinations[combination]['specific_price'];
			if (combinations[combination]['ecotax'])
				selectedCombination['ecotax'] = combinations[combination]['ecotax'];
			else
				selectedCombination['ecotax'] = default_eco_tax;

			//show the large image in relation to the selected combination
			if (combinations[combination]['image'] && combinations[combination]['image'] != -1)
				displayImage( $('#thumb_' + combinations[combination]['image']).parent() );

			//show discounts values according to the selected combination
			if (combinations[combination]['idCombination'] && combinations[combination]['idCombination'] > 0)
				displayDiscounts(combinations[combination]['idCombination']);

			//get available_date for combination product
			selectedCombination['available_date'] = combinations[combination]['available_date'];
			
			//update the display
			updateDisplay();

			if(typeof(firstTime) != 'undefined' && firstTime)
				refreshProductImages(0);
			else
				refreshProductImages(combinations[combination]['idCombination']);
			//leave the function because combination has been found
			return;
		}
	}
	//this combination doesn't exist (not created in back office)
	selectedCombination['unavailable'] = true;
	if (typeof(selectedCombination['available_date']) != 'undefined')
		delete selectedCombination['available_date'];
	updateDisplay();
}

//update display of the availability of the product AND the prices of the product
function updateDisplay()
{
	var productPriceDisplay = productPrice;
	var productPriceWithoutReductionDisplay = productPriceWithoutReduction;

	if (!selectedCombination['unavailable'] && quantityAvailable > 0 && productAvailableForOrder == 1)
	{
		//show the choice of quantities
		$('#quantity_wanted_p:hidden').show('slow');

		//show the "add to cart" button ONLY if it was hidden
		$('#add_to_cart:hidden').fadeIn(600);

		//hide the hook out of stock
		$('#oosHook').hide();
		
		$('#availability_date').fadeOut();

		//availability value management
		if (availableNowValue != '')
		{
			//update the availability statut of the product
			$('#availability_value').removeClass('warning_inline');
			$('#availability_value').text(availableNowValue);
			if(stock_management == 1)
				$('#availability_statut:hidden').show();
		}
		else
			$('#availability_statut:visible').hide();

		//'last quantities' message management
		if (!allowBuyWhenOutOfStock)
		{
			if (quantityAvailable <= maxQuantityToAllowDisplayOfLastQuantityMessage)
				$('#last_quantities').show('slow');
			else
				$('#last_quantities').hide('slow');
		}

		if (quantitiesDisplayAllowed)
		{
			$('#pQuantityAvailable:hidden').show('slow');
			$('#quantityAvailable').text(quantityAvailable);

			if (quantityAvailable < 2) // we have 1 or less product in stock and need to show "item" instead of "items"
			{
				$('#quantityAvailableTxt').show();
				$('#quantityAvailableTxtMultiple').hide();
			}
			else
			{
				$('#quantityAvailableTxt').hide();
				$('#quantityAvailableTxtMultiple').show();
			}
		}
	}
	else
	{
		//show the hook out of stock
		if (productAvailableForOrder == 1)
		{
			$('#oosHook').show();
			if ($('#oosHook').length > 0 && function_exists('oosHookJsCode'))
				oosHookJsCode();
		}

		//hide 'last quantities' message if it was previously visible
		$('#last_quantities:visible').hide('slow');

		//hide the quantity of pieces if it was previously visible
		$('#pQuantityAvailable:visible').hide('slow');

		//hide the choice of quantities
		if (!allowBuyWhenOutOfStock)
			$('#quantity_wanted_p:visible').hide('slow');

		//display that the product is unavailable with theses attributes
		if (!selectedCombination['unavailable'])
			$('#availability_value').text(doesntExistNoMore + (globalQuantity > 0 ? ' ' + doesntExistNoMoreBut : '')).addClass('warning_inline');
		else
		{
			$('#availability_value').text(doesntExist).addClass('warning_inline');
			$('#oosHook').hide();
		}
		if(stock_management == 1 && !allowBuyWhenOutOfStock)
			$('#availability_statut:hidden').show();

		if (typeof(selectedCombination['available_date']) != 'undefined' && selectedCombination['available_date']['date'].length != 0)
		{
			var available_date = selectedCombination['available_date']['date'];
			var tab_date = available_date.split('-');
			var time_available = new Date(tab_date[0], tab_date[1], tab_date[2]);
			time_available.setMonth(time_available.getMonth()-1);
			var now = new Date();
			if (now.getTime() < time_available.getTime() && $('#availability_date_value').text() != selectedCombination['available_date']['date_formatted'])
			{
				$('#availability_date').fadeOut('normal', function(){
					$('#availability_date_value').text(selectedCombination['available_date']['date_formatted']);
					$(this).fadeIn();
				});
			}
			else if(now.getTime() < time_available.getTime())
				$('#availability_date').fadeIn();
		}
		else
			$('#availability_date').fadeOut();

		//show the 'add to cart' button ONLY IF it's possible to buy when out of stock AND if it was previously invisible
		if (allowBuyWhenOutOfStock && !selectedCombination['unavailable'] && productAvailableForOrder == 1)
		{
			$('#add_to_cart:hidden').fadeIn(600);

			if (availableLaterValue != '')
			{
				$('#availability_value').text(availableLaterValue);
				if(stock_management == 1)
					$('#availability_statut:hidden').show('slow');
			}
			else
				$('#availability_statut:visible').hide('slow');
		}
		else
		{
			$('#add_to_cart:visible').fadeOut(600);
			if(stock_management == 1)
				$('#availability_statut:hidden').show('slow');
		}

		if (productAvailableForOrder == 0)
			$('#availability_statut:visible').hide();
	}

	if (selectedCombination['reference'] || productReference)
	{
		if (selectedCombination['reference'])
			$('#product_reference span').text(selectedCombination['reference']);
		else if (productReference)
			$('#product_reference span').text(productReference);
		$('#product_reference:hidden').show('slow');
	}
	else
		$('#product_reference:visible').hide('slow');

	//update display of the the prices in relation to tax, discount, ecotax, and currency criteria
	if (!selectedCombination['unavailable'] && productShowPrice == 1)
	{
		var priceTaxExclWithoutGroupReduction = '';

		// retrieve price without group_reduction in order to compute the group reduction after
		// the specific price discount (done in the JS in order to keep backward compatibility)
		if (!displayPrice && !noTaxForThisProduct)
		{
			priceTaxExclWithoutGroupReduction = ps_round(productPriceTaxExcluded, 6) * (1 / group_reduction);
		} else {
			priceTaxExclWithoutGroupReduction = ps_round(productPriceTaxExcluded, 6) * (1 / group_reduction);
		}
		var combination_add_price = selectedCombination['price'] * group_reduction;

		var tax = (taxRate / 100) + 1;

		var reduction = 0;
		if (selectedCombination['specific_price'].reduction_price || selectedCombination['specific_price'].reduction_percent)
		{
			reduction_price = (specific_currency ? selectedCombination['specific_price'].reduction_price : selectedCombination['specific_price'].reduction_price * currencyRate);
			reduction = productPriceDisplay * (parseFloat(selectedCombination['specific_price'].reduction_percent) / 100) + reduction_price;
			if (reduction_price && (displayPrice || noTaxForThisProduct))
				reduction = ps_round(reduction / tax, 6);
		}
		else if (product_specific_price.reduction_price || product_specific_price.reduction_percent)
		{
			reduction_price = (specific_currency ? product_specific_price.reduction_price : product_specific_price.reduction_price * currencyRate);
			reduction = productPriceDisplay * (parseFloat(product_specific_price.reduction_percent) / 100) + reduction_price;
			if (reduction_price && (displayPrice || noTaxForThisProduct))
				reduction = ps_round(reduction / tax, 6);
		}

		if (selectedCombination.specific_price)
		{
			if (selectedCombination['specific_price'].reduction_type == 'percentage')
			{
				$('#reduction_amount').hide();
				$('#reduction_percent_display').html('-' + parseFloat(selectedCombination['specific_price'].reduction_percent) + '%');
				$('#reduction_percent').show();
			} else if (selectedCombination['specific_price'].reduction_type == 'amount' && selectedCombination['specific_price'].reduction_price != 0) {
				$('#reduction_amount_display').html('-' + formatCurrency(reduction_price, currencyFormat, currencySign, currencyBlank));
				$('#reduction_percent').hide();
				$('#reduction_amount').show();
			} else {
				$('#reduction_percent').hide();
				$('#reduction_amount').hide();
			}
		}
		else
			if (product_specific_price['reduction_type'] == 'percentage')
				$('#reduction_percent_display').html(product_specific_price['specific_price'].reduction_percent);

		if (product_specific_price['reduction_type'] != '' || selectedCombination['specific_price'].reduction_type != '')
			$('#discount_reduced_price,#old_price').show();
		else
			$('#discount_reduced_price,#old_price').hide();
		
		if (product_specific_price['reduction_type'] == 'percentage' || selectedCombination['specific_price'].reduction_type == 'percentage')
			$('#reduction_percent').show();
		else
			$('#reduction_percent').hide();
		if (product_specific_price['price'] || selectedCombination.specific_price['price'])
			$('#not_impacted_by_discount').show();
		else
			$('#not_impacted_by_discount').hide();

		if (selectedCombination.specific_price['price'] && selectedCombination.specific_price['price'] >=0)
			var taxExclPrice = (specific_currency ? selectedCombination.specific_price['price'] : selectedCombination.specific_price['price'] * currencyRate);
		else
			var taxExclPrice = priceTaxExclWithoutGroupReduction + (selectedCombination['price'] * currencyRate);

		if (!displayPrice && !noTaxForThisProduct)
			productPriceDisplay = taxExclPrice * tax; // Need to be global => no var
		else
			productPriceDisplay = ps_round(taxExclPrice, 2); // Need to be global => no var

		productPriceWithoutReductionDisplay = productPriceDisplay * group_reduction;

		productPriceDisplay -= reduction;
		var tmp = productPriceDisplay * group_reduction;
		productPriceDisplay = ps_round(productPriceDisplay * group_reduction, 2);

		var ecotaxAmount = !displayPrice ? ps_round(selectedCombination['ecotax'] * (1 + ecotaxTax_rate / 100), 2) : selectedCombination['ecotax'];
		productPriceDisplay += ecotaxAmount;
		productPriceWithoutReductionDisplay += ecotaxAmount;

		var our_price = '';
		if (productPriceDisplay > 0) {
			our_price = formatCurrency(productPriceDisplay, currencyFormat, currencySign, currencyBlank);
		} else {
			our_price = formatCurrency(0, currencyFormat, currencySign, currencyBlank);
		}
		$('#our_price_display').text(our_price);
		$('#old_price_display').text(formatCurrency(productPriceWithoutReductionDisplay, currencyFormat, currencySign, currencyBlank));
		if (productPriceWithoutReductionDisplay > productPriceDisplay)
			$('#old_price,#old_price_display,#old_price_display_taxes').show();
		else
			$('#old_price,#old_price_display,#old_price_display_taxes').hide();
		// Special feature: "Display product price tax excluded on product page"
		var productPricePretaxed = '';
		if (!noTaxForThisProduct)
			productPricePretaxed = productPriceDisplay / tax;
		else
			productPricePretaxed = productPriceDisplay;
		$('#pretaxe_price_display').text(formatCurrency(productPricePretaxed, currencyFormat, currencySign, currencyBlank));
		// Unit price 
		productUnitPriceRatio = parseFloat(productUnitPriceRatio);
		if (productUnitPriceRatio > 0 )
		{
			newUnitPrice = (productPriceDisplay / parseFloat(productUnitPriceRatio)) + selectedCombination['unit_price'];
			$('#unit_price_display').text(formatCurrency(newUnitPrice, currencyFormat, currencySign, currencyBlank));
		}

		// Ecotax
		ecotaxAmount = !displayPrice ? ps_round(selectedCombination['ecotax'] * (1 + ecotaxTax_rate / 100), 2) : selectedCombination['ecotax'];
		$('#ecotax_price_display').text(formatCurrency(ecotaxAmount, currencyFormat, currencySign, currencyBlank));
	}
}

//update display of the large image
function displayImage(domAAroundImgThumb, no_animation)
{
	if (typeof(no_animation) == 'undefined')
		no_animation = false;
	
	if (domAAroundImgThumb.attr('href'))
	{
		var newSrc = domAAroundImgThumb.attr('href').replace('thickbox','large');
		if ($('#bigpic').attr('src') != newSrc)
		{
			$('#bigpic').fadeOut((no_animation ? 0 : 'fast'), function(){
				$(this).attr('src', newSrc).show();
				if (typeof(jqZoomEnabled) != 'undefined' && jqZoomEnabled)
					$(this).attr('alt', domAAroundImgThumb.attr('href'));
			});
		}
		$('#views_block li a').removeClass('shown');
		$(domAAroundImgThumb).addClass('shown');
	}
}

//update display of the discounts table
function displayDiscounts(combination)
{
	$('#quantityDiscount tbody tr').each(function() {
		if (($(this).attr('id') != 'quantityDiscount_0') &&
			($(this).attr('id') != 'quantityDiscount_' + combination) &&
			($(this).attr('id') != 'noQuantityDiscount'))
			$(this).fadeOut('slow');
	 });

	if ($('#quantityDiscount_' + combination).length != 0)
	{
		$('#quantityDiscount_' + combination).show();
		$('#noQuantityDiscount').hide();
	}
	else
		$('#noQuantityDiscount').show();
}

// Serialscroll exclude option bug ?
function serialScrollFixLock(event, targeted, scrolled, items, position)
{
	serialScrollNbImages = $('#thumbs_list li:visible').length;
	serialScrollNbImagesDisplayed = 3;

	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + serialScrollNbImagesDisplayed >= serialScrollNbImages ? true : false;

	$('#view_scroll_left').css('cursor', leftArrow ? 'default' : 'pointer').css('display', leftArrow ? 'none' : 'block').fadeTo(0, leftArrow ? 0 : 1);
	$('#view_scroll_right').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'none' : 'block');
	return true;
}

// Change the current product images regarding the combination selected
function refreshProductImages(id_product_attribute)
{
	$('#thumbs_list_frame').scrollTo('li:eq(0)', 700, {axis:'x'});
	$('#thumbs_list li').hide();
	id_product_attribute = parseInt(id_product_attribute);

	if (typeof(combinationImages) != 'undefined' && typeof(combinationImages[id_product_attribute]) != 'undefined')
	{
		for (var i = 0; i < combinationImages[id_product_attribute].length; i++)
			$('#thumbnail_' + parseInt(combinationImages[id_product_attribute][i])).show();
	}
	if (i > 0)
	{
		var thumb_width = $('#thumbs_list_frame >li').width() + parseInt($('#thumbs_list_frame >li').css('marginRight'));
		$('#thumbs_list_frame').width((parseInt((thumb_width)* i) + 3) + 'px'); //  Bug IE6, needs 3 pixels more ?
	}
	else
	{
		$('#thumbnail_' + idDefaultImage).show();
		displayImage($('#thumbnail_' + idDefaultImage + ' a'));
	}
	$('#thumbs_list').trigger('goto', 0);
	serialScrollFixLock('', '', '', '', 0);// SerialScroll Bug on goto 0 ?
}

//To do after loading HTML
$(document).ready(function()
{
	//init the serialScroll for thumbs
	$('#thumbs_list').serialScroll({
		items:'li:visible',
		prev:'#view_scroll_left',
		next:'#view_scroll_right',
		axis:'x',
		offset:0,
		start:0,
		stop:true,
		onBefore:serialScrollFixLock,
		duration:700,
		step: 2,
		lazy: true,
		lock: false,
		force:false,
		cycle:false
	});

	$('#thumbs_list').trigger('goto', 1);// SerialScroll Bug on goto 0 ?
	$('#thumbs_list').trigger('goto', 0);

	//hover 'other views' images management
	$('#views_block li a').hover(
		function(){displayImage($(this));},
		function(){}
	);

	//set jqZoom parameters if needed
	if (typeof(jqZoomEnabled) != 'undefined' && jqZoomEnabled)
	{
		$('img.jqzoom').jqueryzoom({
			xzoom: 200, //zooming div default width(default width value is 200)
			yzoom: 200, //zooming div default width(default height value is 200)
			offset: 21 //zooming div default offset(default offset value is 10)
			//position: "right" //zooming div position(default position value is "right")
		});
	}
	//add a link on the span 'view full size' and on the big image
	$('#view_full_size, #image-block img').click(function(){
		$('#views_block .shown').click();
	});

	//catch the click on the "more infos" button at the top of the page
	$('#short_description_block .button').click(function(){
		$('#more_info_tab_more_info').click();
		$.scrollTo( '#more_info_tabs', 1200 );
	});

	// Hide the customization submit button and display some message
	$('#customizedDatas input').click(function() {
		$('#customizedDatas input').hide();
		$('#ajax-loader').fadeIn();
		$('#customizedDatas').append(uploading_in_progress);
	});

	//init the price in relation of the selected attributes
	if (typeof productHasAttributes != 'undefined' && productHasAttributes)
		findCombination(true);
	else if (typeof productHasAttributes != 'undefined' && !productHasAttributes)
		refreshProductImages(0);

	$('#resetImages').click(function() {
		refreshProductImages(0);
	});

	$('.thickbox').fancybox({
		'hideOnContentClick': true,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
	original_url = window.location + '';
	first_url_check = true;
	checkUrl();
	initLocationChange();
	
});

function saveCustomization()
{
	$('#quantityBackup').val($('#quantity_wanted').val());
	customAction = $('#customizationForm').attr('action');
	$('body select[id^="group_"]').each(function() {
		customAction = customAction.replace(new RegExp(this.id + '=\\d+'), this.id +'=' + this.value);
	});
	$('#customizationForm').attr('action', customAction);
	$('#customizationForm').submit();
}

function submitPublishProduct(url, redirect, token)
{
	var id_product = $('#admin-action-product-id').val();

	$.ajaxSetup({async: false});
	$.post(url + '/index.php', {
		action:'publishProduct',
		id_product: id_product, 
		status: 1, 
		redirect: redirect,
		ajax: 1,
		tab: 'AdminProducts',
		token: token
		},
		function(data)
		{
			if (data.indexOf('error') === -1)
			document.location.href = data;
		}
	);
	return true;
}

function checkMinimalQuantity(minimal_quantity)
{
	if ($('#quantity_wanted').val() < minimal_quantity)
	{
		$('#quantity_wanted').css('border', '1px solid red');
		$('#minimal_quantity_wanted_p').css('color', 'red');
	}
	else
	{
		$('#quantity_wanted').css('border', '1px solid #BDC2C9');
		$('#minimal_quantity_wanted_p').css('color', '#374853');
	}
}

function colorPickerClick(elt)
{
	id_attribute = $(elt).attr('id').replace('color_', '');
	$(elt).parent().parent().children().removeClass('selected');
	$(elt).fadeTo('fast', 1, function(){
								$(this).fadeTo('fast', 0, function(){
									$(this).fadeTo('fast', 1, function(){
										$(this).parent().addClass('selected');
										});
									});
								});
	$(elt).parent().parent().parent().children('.color_pick_hidden,#color_pick_hidden').val(id_attribute);
	findCombination(false);
}


function getProductAttribute()
{
	// get product attribute id
	product_attribute_id = $('#idCombination').val();
	product_id = $('#product_page_product_id').val();

	// get every attributes values
	request = '';
	//create a temporary 'tab_attributes' array containing the choices of the customer
	var tab_attributes = [];
	$('#attributes select, #attributes input[type=hidden], #attributes input[type=radio]:checked').each(function(){
		tab_attributes.push($(this).val());
	});

	// build new request
	for (var i in attributesCombinations)
		for (var a in tab_attributes)
			if (attributesCombinations[i]['id_attribute'] === tab_attributes[a])
				request += '/'+attributesCombinations[i]['group'] + '-' + attributesCombinations[i]['attribute'];
	request = request.replace(request.substring(0, 1), '#/');
	url = window.location + '';

	// redirection
	if (url.indexOf('#') != -1)
		url = url.substring(0, url.indexOf('#'));

	// set ipa to the customization form
	$('#customizationForm').attr('action', $('#customizationForm').attr('action') + request)
	window.location = url + request;
}

function initLocationChange(time)
{
	if(!time) time = 500;
	setInterval(checkUrl, time);
}

function checkUrl()
{
	if (original_url != window.url || first_url_check)
	{
		first_url_check = false;
		url = window.location + '';
		// if we need to load a specific combination
		if (url.indexOf('#/') != -1)
		{
			// get the params to fill from a "normal" url
			params = url.substring(url.indexOf('#') + 1, url.length);
			tabParams = params.split('/');
			tabValues = [];
			if (tabParams[0] == '')
				tabParams.shift();
			for (var i in tabParams)
				tabValues.push(tabParams[i].split('-'));
			product_id = $('#product_page_product_id').val();
			// fill html with values
			$('.color_pick').removeClass('selected');
			$('.color_pick').parent().parent().children().removeClass('selected');
			count = 0;
			for (var z in tabValues)
				for (var a in attributesCombinations)
					if (attributesCombinations[a]['group'] === decodeURIComponent(tabValues[z][0])
						&& attributesCombinations[a]['attribute'] === tabValues[z][1])
					{
						count++;
						// add class 'selected' to the selected color
						$('#color_' + attributesCombinations[a]['id_attribute']).addClass('selected');
						$('#color_' + attributesCombinations[a]['id_attribute']).parent().addClass('selected');
						$('input:radio[value=' + attributesCombinations[a]['id_attribute'] + ']').attr('checked', true);
						$('input:hidden[name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
						$('select[name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
					}
			// find combination
			if (count >= 0)
			{
				findCombination(false);
				original_url = window.location + '';
			}
			// no combination found = removing attributes from url
			else
				window.location = url.substring(0, url.indexOf('#'));
		}
	}
}
