/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//global variables
var selectedCombination = [];
var globalQuantity = 0;
var colors = [];

$(document).ready(function(){
	if (typeof customizationFields !== 'undefined' && customizationFields)
	{
		var customizationFieldsBk = customizationFields;
        customizationFields = [];
		var j = 0;
		for (var i = 0; i < customizationFieldsBk.length; ++i)
		{
			var key = 'pictures_' + parseInt(id_product) + '_' + parseInt(customizationFieldsBk[i]['id_customization_field']);
            customizationFields[i] = [];
            customizationFields[i][0] = (parseInt(customizationFieldsBk[i]['type']) == 0) ? 'img' + i : 'textField' + j++;
            customizationFields[i][1] = (parseInt(customizationFieldsBk[i]['type']) == 0 && customizationFieldsBk[i][key]) ? 2 : parseInt(customizationFieldsBk[i]['required']);
        }
	}

	if (typeof combinationImages !== 'undefined' && combinationImages)
    {
		combinationImagesJS = [];
		combinationImagesJS[0] = [];
		var k = 0;
        for (var i in combinationImages)
		{
			combinationImagesJS[i] = [];
            for (var j in combinationImages[i])
            {
                var id_image = parseInt(combinationImages[i][j]['id_image']);
             	if (id_image)
                {
					combinationImagesJS[0][k++] = id_image;
					combinationImagesJS[i][j] = [];
					combinationImagesJS[i][j] = id_image;
                }
            }
		}

	    if (typeof combinationImagesJS[0] !== 'undefined' && combinationImagesJS[0])
	    {
	       var array_values = [];
	       for (var key in arrayUnique(combinationImagesJS[0]))
	           array_values.push(combinationImagesJS[0][key]);
	       combinationImagesJS[0] = array_values;
	    }
		combinationImages = combinationImagesJS;
    }

	if (typeof combinations !== 'undefined' && combinations)
	{
		combinationsJS = [];
		var k = 0;
		for (var i in combinations)
		{
			globalQuantity += combinations[i]['quantity'];
			combinationsJS[k] = [];
			combinationsJS[k]['idCombination'] = parseInt(i);
			combinationsJS[k]['idsAttributes'] = combinations[i]['attributes'];
			combinationsJS[k]['quantity'] = combinations[i]['quantity'];
			combinationsJS[k]['price'] = combinations[i]['price'];
			combinationsJS[k]['ecotax'] = combinations[i]['ecotax'];
			combinationsJS[k]['image'] = parseInt(combinations[i]['id_image']);
			combinationsJS[k]['reference'] = combinations[i]['reference'];
			combinationsJS[k]['unit_price'] = combinations[i]['unit_impact'];
			combinationsJS[k]['minimal_quantity'] = parseInt(combinations[i]['minimal_quantity']);

			combinationsJS[k]['available_date'] = [];
				combinationsJS[k]['available_date']['date'] = combinations[i]['available_date'];
				combinationsJS[k]['available_date']['date_formatted'] = combinations[i]['date_formatted'];

			combinationsJS[k]['specific_price'] = [];
				combinationsJS[k]['specific_price']['reduction_percent'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction'] && combinations[i]['specific_price']['reduction_type'] == 'percentage') ? combinations[i]['specific_price']['reduction'] * 100 : 0;
				combinationsJS[k]['specific_price']['reduction_price'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction'] && combinations[i]['specific_price']['reduction_type'] == 'amount') ? combinations[i]['specific_price']['reduction'] : 0;
				combinationsJS[k]['price'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['price'] && parseInt(combinations[i]['specific_price']['price']) != -1) ? combinations[i]['specific_price']['price'] :  combinations[i]['price'];

			combinationsJS[k]['reduction_type'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction_type']) ? combinations[i]['specific_price']['reduction_type'] : '';
			combinationsJS[k]['id_product_attribute'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['id_product_attribute']) ? combinations[i]['specific_price']['id_product_attribute'] : 0;
			k++;
		}
		combinations = combinationsJS;
	}

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
		$('.jqzoom').jqzoom({
			zoomType: 'innerzoom', //innerzoom/standard/reverse/drag
			zoomWidth: 458, //zooming div default width(default width value is 200)
			zoomHeight: 458, //zooming div default width(default height value is 200)
			xOffset: 21, //zooming div default offset(default offset value is 10)
			yOffset: 0,
			title: false
		});
	}
	//add a link on the span 'view full size' and on the big image
	$(document).on('click', '#view_full_size, #image-block', function(e){
		$('#views_block .shown').click();
	});

	//catch the click on the "more infos" button at the top of the page
	$(document).on('click', '#short_description_block .button', function(e){
		$('#more_info_tab_more_info').click();
		$.scrollTo( '#more_info_tabs', 1200 );
	});

	// Hide the customization submit button and display some message
	$(document).on('click', '#customizedDatas input', function(e){
		$('#customizedDatas input').hide();
		$('#ajax-loader').fadeIn();
		$('#customizedDatas').append(uploading_in_progress);
	});

	original_url = window.location + '';
	first_url_check = true;
	var url_found = checkUrl();
	initLocationChange();

	//init the price in relation of the selected attributes
	if (typeof productHasAttributes != 'undefined' && productHasAttributes && !url_found)
		findCombination(true);
	else if (typeof productHasAttributes != 'undefined' && !productHasAttributes && !url_found)
		refreshProductImages(0);

	$(document).on('click', 'a[name=resetImages]', function(e){
		e.preventDefault();
		refreshProductImages(0);
	});

	$(document).on('click', '.color_pick', function(e){
		e.preventDefault();
		colorPickerClick($(this));
		getProductAttribute();
	});

	$(document).on('change', '.attribute_select', function(e){
		e.preventDefault();
		findCombination();
		getProductAttribute();
	});

	$(document).on('click', '.attribute_radio', function(e){
		e.preventDefault();
		findCombination();
		getProductAttribute();
	});

	$(document).on('click', 'button[name=saveCustomization]', function(e){
		saveCustomization();
	});

	if (contentOnly == false)
	{
		if(!!$.prototype.fancybox)
			$('.fancybox').fancybox({
				'hideOnContentClick': true,
				'openEffect'	: 'elastic',
				'closeEffect'	: 'elastic'
			});
	}
	else
	{
		$(document).on('click', '.fancybox', function(e){
			e.preventDefault();
		});

		$(document).on('click', '#image-block', function(e){
			e.preventDefault();
			var productUrl= window.document.location.href + '';
			var data = productUrl.replace('content_only=1', '');
			window.parent.document.location.href = data;
		});

		if (typeof ajax_allowed != 'undefined' && !ajax_allowed)
			$('#buy_block').attr('target', '_top');
	}

	if (!!$.prototype.bxSlider)
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

    // The button to increment the product value
    $(document).on('click', '.product_quantity_up', function(e){
        e.preventDefault();
        fieldName = $(this).data('field-qty');
        var currentVal = parseInt($('input[name='+fieldName+']').val());
		if (quantityAvailable > 0) {
				quantityAvailableT = quantityAvailable;
		} else {
				quantityAvailableT = 100000000;
		}
        if (!isNaN(currentVal) && currentVal < quantityAvailableT) {
            $('input[name='+fieldName+']').val(currentVal + 1).trigger('keyup');
        } else {
            $('input[name='+fieldName+']').val(quantityAvailableT);
        }
    });
	 // The button to decrement the product value
    $(document).on('click', '.product_quantity_down', function(e){
        e.preventDefault();
        fieldName = $(this).data('field-qty');
        var currentVal = parseInt($('input[name='+fieldName+']').val());
        if (!isNaN(currentVal) && currentVal > 1) {
            $('input[name='+fieldName+']').val(currentVal - 1).trigger('keyup');
        } else {
            $('input[name='+fieldName+']').val(1);
        }
    });

	if (typeof minimalQuantity != 'undefined' && minimalQuantity)
	{
		checkMinimalQuantity();
		$(document).on('keyup', 'input[name=qty]', function(e){
			checkMinimalQuantity(minimalQuantity);
		});
	}

	if (typeof ad !== 'undefined' && ad && typeof adtoken !== 'undefined' && adtoken)
	{
		$(document).on('click', 'input[name=publish_button]', function(e){
			e.preventDefault();
			submitPublishProduct(ad, 0, adtoken);
		});
		$(document).on('click', 'input[name=lnk_view]', function(e){
			e.preventDefault();
			submitPublishProduct(ad, 1, adtoken);
		});
	}
});

function arrayUnique(a)
{
    return a.reduce(function(p, c){
        if (p.indexOf(c) < 0)
			p.push(c);
        return p;
    }, []);
};

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
	if (typeof $('#minimal_quantity_label').text() === 'undefined' || $('#minimal_quantity_label').html() > 1)
		$('#quantity_wanted').val(1);

	//create a temporary 'choice' array containing the choices of the customer
	var choice = [];
	$('#attributes select, #attributes input[type=hidden], #attributes input[type=radio]:checked').each(function(){
		choice.push(parseInt($(this).val()));
	});

	if (typeof combinations == 'undefined' || !combinations)
		combinations = [];
	//testing every combination to find the conbination's attributes' case of the user
	for (var combination = 0; combination < combinations.length; ++combination)
	{
		//verify if this combinaison is the same that the user's choice
		var combinationMatchForm = true;
		$.each(combinations[combination]['idsAttributes'], function(key, value)
		{
			if (!in_array(parseInt(value), choice))
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
				displayImage($('#thumb_' + combinations[combination]['image']).parent());

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
	/*if (!selectedCombination['unavailable'] && productShowPrice == 1)
	{
		// retrieve price without group_reduction in order to compute the group reduction after
		// the specific price discount (done in the JS in order to keep backward compatibility)

		var tax = (taxRate / 100) + 1;

		combID = $('#idCombination').val();
		comb2 = combinationsFromController[combID];
		if (comb2.price != 0)
		{
			var taxExclPrice = productPriceTaxExcluded + comb2.price;
			productPriceWithoutReductionDisplay = productPriceWithoutReductionDisplay + (comb2.price * tax);
		}
		else
			var taxExclPrice = productPriceTaxExcluded;


		if (!displayPrice && !noTaxForThisProduct)
			productPriceDisplay = ps_round(taxExclPrice * tax, 2); // Need to be global => no var
		else
			productPriceDisplay = ps_round(taxExclPrice, 2); // Need to be global => no var

		var reduction = 0;
		if (selectedCombination['specific_price'].reduction_price || selectedCombination['specific_price'].reduction_percent)
		{
			reduction_price = (specific_currency ? selectedCombination['specific_price'].reduction_price : selectedCombination['specific_price'].reduction_price * currencyRate);
			reduction = productPriceDisplay * (parseFloat(selectedCombination['specific_price'].reduction_percent) / 100) + reduction_price;
			if (reduction_price && (displayPrice || noTaxForThisProduct))
				reduction = ps_round(reduction / tax, 6);

		}
		else if (product_specific_price && product_specific_price.reduction && !selectedCombination.specific_price)
		{
			if (product_specific_price.reduction_type == 'amount')
				reduction_price = (specific_currency ? product_specific_price.reduction : product_specific_price.reduction * currencyRate);
			else
				reduction_price = 0;

			if (product_specific_price.reduction_type == 'percentage')
				reduction_percent = productPriceDisplay * parseFloat(product_specific_price.reduction);

			reduction = reduction_price + reduction_percent;
			if (reduction_price && (displayPrice || noTaxForThisProduct))
				reduction = ps_round(reduction / tax, 6);
		}


		if (product_specific_price['reduction_type'] != '' || selectedCombination.specific_price.reduction_percent > 0 || selectedCombination.specific_price.reduction_price > 0)
			$('#discount_reduced_price,#old_price').show();
		else
			$('#discount_reduced_price,#old_price').hide();
		if ((product_specific_price['reduction_type'] == 'percentage' && selectedCombination.specific_price.reduction_percent > 0) || selectedCombination['specific_price'].reduction_type == 'percentage')
			$('#reduction_percent').show();
		else
			$('#reduction_percent').hide();
		if (product_specific_price['price'] || (selectedCombination.specific_price && selectedCombination.specific_price['price']))
			$('#not_impacted_by_discount').show();
		else
			$('#not_impacted_by_discount').hide();


		productPriceDisplay -= reduction;
		productPriceDisplay = ps_round(productPriceDisplay * (1 - group_reduction), 2);

		var ecotaxAmount = !displayPrice ? ps_round(selectedCombination['ecotax'] * (1 + ecotaxTax_rate / 100), 2) : selectedCombination['ecotax'];

		if (ecotaxAmount != default_eco_tax)
			productPriceDisplay += ecotaxAmount - default_eco_tax;
		else
			productPriceDisplay += ecotaxAmount;

		if (ecotaxAmount != default_eco_tax)
			productPriceWithoutReductionDisplay += ecotaxAmount - default_eco_tax;
		else
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
			newUnitPrice = (productPriceDisplay / parseFloat(productUnitPriceRatio)) + parseFloat(selectedCombination['unit_price']);
			$('#unit_price_display').text(formatCurrency(newUnitPrice, currencyFormat, currencySign, currencyBlank));
		}

		// Ecotax
		ecotaxAmount = !displayPrice ? ps_round(selectedCombination['ecotax'] * (1 + ecotaxTax_rate / 100), 2) : selectedCombination['ecotax'];
		$('#ecotax_price_display').text(formatCurrency(ecotaxAmount, currencyFormat, currencySign, currencyBlank));



		updateDiscountTable(productPriceDisplay);

	}*/

	updatePrice()
}

function updatePrice()
{
	// Get combination prices
	combID = $('#idCombination').val();
	combination = combinationsFromController[combID];

	// Set product (not the combination) base price
	var basePriceWithoutTax = productBasePriceTaxExcluded;
	var priceWithGroupReductionWithoutTax = 0;

	// Apply combination price impact
	// 0 by default, +x if price is inscreased, -x if price is decreased
	basePriceWithoutTax = basePriceWithoutTax + combination.price;

	// If a specific price redefine the combination base price
	if (combination.specific_price && combination.specific_price.price > 0)
		basePriceWithoutTax = combination.specific_price.price;

	// Apply group reduction
	priceWithGroupReductionWithoutTax = ps_round(basePriceWithoutTax * (1 - group_reduction), 2);
	var priceWithDiscountsWithoutTax = priceWithGroupReductionWithoutTax;

	// Apply specific price (discount)
	if (combination.specific_price && combination.specific_price.reduction > 0)
		if (combination.specific_price.reduction_type == 'amount')
			priceWithDiscountsWithoutTax = priceWithGroupReductionWithoutTax - combination.specific_price.reduction;
		else if (combination.specific_price.reduction_type == 'percentage')
			priceWithDiscountsWithoutTax = priceWithGroupReductionWithoutTax * (1 - combination.specific_price.reduction);

	// Apply Tax if necessary
	if (!noTaxForThisProduct)
	{
		basePriceDisplay = basePriceWithoutTax * (taxRate/100 + 1);
		priceWithDiscountsDisplay = priceWithDiscountsWithoutTax * (taxRate/100 + 1);
	}
	else
	{
		basePriceDisplay = basePriceWithoutTax;
		priceWithDiscountsDisplay = priceWithDiscountsWithoutTax;
	}

	// TODO: ECOTAX
	// TODO: Unit price

	// Compute discount value and percentage
	// Done just before display update so we have final prices
	if (basePriceDisplay != priceWithDiscountsDisplay)
	{
		var discountValue = basePriceDisplay - priceWithDiscountsDisplay;
		var discountPercentage = (1-(priceWithDiscountsDisplay/basePriceDisplay))*100;
	}


	/*  *****************************************************************
			Update the page content, no price calculation happens after
		***************************************************************** */

	// Hide everything. It will be shown if needed
	$('#reduction_percent').hide();
	$('#reduction_amount').hide();
	$('#old_price,#old_price_display,#old_price_display_taxes').hide();


	$('#our_price_display').text(formatCurrency(priceWithDiscountsDisplay * currencyRate, currencyFormat, currencySign, currencyBlank));

	// If the calculated price (after all discounts) is different than the base price
	// we show the old price striked through
	if (priceWithDiscountsDisplay.toFixed(2) != basePriceDisplay.toFixed(2))
	{
		$('#old_price_display').text(formatCurrency(basePriceDisplay * currencyRate, currencyFormat, currencySign, currencyBlank));
		$('#old_price,#old_price_display,#old_price_display_taxes').show();

		// Then if it's not only a group reduction we display the discount in red box
		if (priceWithDiscountsWithoutTax != priceWithGroupReductionWithoutTax)
		{
			if (combination.specific_price.reduction_type == 'amount')
			{
				$('#reduction_amount_display').html('-' + formatCurrency(parseFloat(discountValue), currencyFormat, currencySign, currencyBlank));
				$('#reduction_amount').show();
			}
			else
			{
				$('#reduction_percent_display').html('-' + parseFloat(discountPercentage).toFixed(0) + '%');
				$('#reduction_percent').show();
			}
		}
	}

	// If there is a quantity discount table,
	// we update it according to the new price
	updateDiscountTable(priceWithDiscountsDisplay);


	// 
}

//update display of the large image
function displayImage(domAAroundImgThumb, no_animation)
{
	if (typeof(no_animation) == 'undefined')
		no_animation = false;
	if (domAAroundImgThumb.prop('href'))
	{
		var new_src = domAAroundImgThumb.attr('href').replace('thickbox', 'large');
		var new_title = domAAroundImgThumb.attr('title');
		var new_href = domAAroundImgThumb.attr('href');
		if ($('#bigpic').prop('src') != new_src)
		{
			$('#bigpic').attr({
				'src' : new_src,
				'alt' : new_title,
				'title' : new_title
			}).load(function(){
				if (typeof(jqZoomEnabled) != 'undefined' && jqZoomEnabled)
					$(this).attr('rel', new_href);
			});
		}
		$('#views_block li a').removeClass('shown');
		$(domAAroundImgThumb).addClass('shown');
	}
}

//update display of the discounts table
function displayDiscounts(combination)
{
	$('#quantityDiscount tbody tr').each(function(){
		if (($(this).attr('id') != 'quantityDiscount_0') &&
			($(this).attr('id') != 'quantityDiscount_' + combination) &&
			($(this).attr('id') != 'noQuantityDiscount'))
			$(this).fadeOut('slow');
	 });

	if ($('#quantityDiscount_' + combination+',.quantityDiscount_' + combination).length != 0
		|| $('#quantityDiscount_0,.quantityDiscount_0').length != 0)
	{
		$('#quantityDiscount').parent().show();
		$('#quantityDiscount_' + combination+',.quantityDiscount_' + combination).show();
		$('#noQuantityDiscount').hide();
	}
	else
	{
		$('#quantityDiscount').parent().hide();
		$('#noQuantityDiscount').show();
	}
}

function updateDiscountTable(newPrice)
{
	$('#quantityDiscount tbody tr').each(function(){
		var type = $(this).data("discount-type");
		var discount = $(this).data("discount");
		var quantity = $(this).data("discount-quantity");

		if (type == 'percentage')
		{
			var discountedPrice = newPrice * (1 - discount/100);
			var discountUpTo = newPrice * (discount/100) * quantity;
		}
		else if (type == 'amount')
		{
			var discountedPrice = newPrice - discount;
			var discountUpTo = discount * quantity;
		}

		if (displayDiscountPrice != 0)
			$(this).children('td').eq(1).text( formatCurrency(discountedPrice, currencyFormat, currencySign, currencyBlank) );
		$(this).children('td').eq(2).text(upToTxt + ' ' + formatCurrency(discountUpTo, currencyFormat, currencySign, currencyBlank));
	});
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

	id_product_attribute = parseInt(id_product_attribute);

	if (id_product_attribute > 0 && typeof(combinationImages) != 'undefined' && typeof(combinationImages[id_product_attribute]) != 'undefined')
	{
		$('#thumbs_list li').hide();
		$('#thumbs_list').trigger('goto', 0);
		for (var i = 0; i < combinationImages[id_product_attribute].length; i++)
			if (typeof(jqZoomEnabled) != 'undefined' && jqZoomEnabled)
				$('#thumbnail_' + parseInt(combinationImages[id_product_attribute][i])).show().children('a.shown').trigger('click');
			else
				$('#thumbnail_' + parseInt(combinationImages[id_product_attribute][i])).show();
	}
	else
		$('#thumbs_list li').show();

	if (parseInt($('#thumbs_list_frame >li:visible').length) != parseInt($('#thumbs_list_frame >li').length))
		$('#wrapResetImages').stop(true, true).show();
	else
		$('#wrapResetImages').stop(true, true).hide();

	var thumb_width = $('#thumbs_list_frame >li').width() + parseInt($('#thumbs_list_frame >li').css('marginRight'));
	$('#thumbs_list_frame').width((parseInt((thumb_width) * $('#thumbs_list_frame >li').length)) + 'px');
	$('#thumbs_list').trigger('goto', 0);
	serialScrollFixLock('', '', '', '', 0);// SerialScroll Bug on goto 0 ?
}

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
	$(elt).parent().parent().parent().children('.color_pick_hidden').val(id_attribute);
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
				request += '/'+attributesCombinations[i]['group'] + attribute_anchor_separator + attributesCombinations[i]['attribute'];
	request = request.replace(request.substring(0, 1), '#/');
	url = window.location + '';

	// redirection
	if (url.indexOf('#') != -1)
		url = url.substring(0, url.indexOf('#'));

	// set ipa to the customization form
	$('#customizationForm').attr('action', $('#customizationForm').attr('action') + request);
	window.location = url + request;
}

function initLocationChange(time)
{
	if(!time) time = 500;
		setInterval(checkUrl, time);
}

function checkUrl()
{
	if (original_url != window.location || first_url_check)
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
				tabValues.push(tabParams[i].split(attribute_anchor_separator));
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
						$('input[type=hidden][name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
						$('select[name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
					}
			// find combination
			if (count >= 0)
			{
				findCombination(false);
				original_url = url;
				return true;
			}
			// no combination found = removing attributes from url
			else
				window.location = url.substring(0, url.indexOf('#'));
		}
	}
	return false;
}