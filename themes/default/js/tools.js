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

function ps_round(value, precision)
{
	if (typeof(roundMode) === 'undefined')
		roundMode = 2;
	if (typeof(precision) === 'undefined')
		precision = 2;
	
	method = roundMode;
	if (method === 0)
		return ceilf(value, precision);
	else if (method === 1)
		return floorf(value, precision);
	precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
	return Math.round(value * precisionFactor) / precisionFactor;
}

function ceilf(value, precision)
{
	if (typeof(precision) === 'undefined')
		precision = 0;
	precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
	tmp = value * precisionFactor;
	tmp2 = tmp.toString();
	if (tmp2[tmp2.length - 1] === 0)
		return value;
	return Math.ceil(value * precisionFactor) / precisionFactor;
}

function floorf(value, precision)
{
	if (typeof(precision) === 'undefined')
		precision = 0;
	precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
	tmp = value * precisionFactor;
	tmp2 = tmp.toString();
	if (tmp2[tmp2.length - 1] === 0)
		return value;
	return Math.floor(value * precisionFactor) / precisionFactor;
}

function formatedNumberToFloat(price, currencyFormat, currencySign)
{
	price = price.replace(currencySign, '');
	if (currencyFormat === 1)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	else if (currencyFormat === 2)
		return parseFloat(price.replace(' ', '').replace(',', '.'));
	else if (currencyFormat === 3)
		return parseFloat(price.replace('.', '').replace(' ', '').replace(',', '.'));
	else if (currencyFormat === 4)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	return price;
}

//return a formatted price
function formatCurrency(price, currencyFormat, currencySign, currencyBlank)
{
	// if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
	blank = '';
	price = parseFloat(price.toFixed(6));
	price = ps_round(price, priceDisplayPrecision);
	if (currencyBlank > 0)
		blank = ' ';
	if (currencyFormat == 1)
		return currencySign + blank + formatNumber(price, priceDisplayPrecision, ',', '.');
	if (currencyFormat == 2)
		return (formatNumber(price, priceDisplayPrecision, ' ', ',') + blank + currencySign);
	if (currencyFormat == 3)
		return (currencySign + blank + formatNumber(price, priceDisplayPrecision, '.', ','));
	if (currencyFormat == 4)
		return (formatNumber(price, priceDisplayPrecision, ',', '.') + blank + currencySign);
	if (currencyFormat == 5)
		return (formatNumber(price, priceDisplayPrecision, ' ', '.') + blank + currencySign);
	return price;
}

//return a formatted number
function formatNumber(value, numberOfDecimal, thousenSeparator, virgule)
{
	value = value.toFixed(numberOfDecimal);
	var val_string = value+'';
	var tmp = val_string.split('.');
	var abs_val_string = (tmp.length === 2) ? tmp[0] : val_string;
	var deci_string = ('0.' + (tmp.length === 2 ? tmp[1] : 0)).substr(2);
	var nb = abs_val_string.length;

	for (var i = 1 ; i < 4; i++)
		if (value >= Math.pow(10, (3 * i)))
			abs_val_string = abs_val_string.substring(0, nb - (3 * i)) + thousenSeparator + abs_val_string.substring(nb - (3 * i));

	if (parseInt(numberOfDecimal) === 0)
		return abs_val_string;
	return abs_val_string + virgule + (deci_string > 0 ? deci_string : '00');
}

//change the text of a jQuery element with a sliding effect (velocity could be a number in ms, 'slow' or 'fast', effect1 and effect2 could be slide, fade, hide, show)
function updateTextWithEffect(jQueryElement, text, velocity, effect1, effect2, newClass)
{
	if(jQueryElement.text() !== text)
		if(effect1 === 'fade')
			jQueryElement.fadeOut(velocity, function(){
				$(this).addClass(newClass);
				if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
				else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
					else if(effect2 === 'show')	$(this).text(text).show(velocity, function(){});
			});
		else if(effect1 === 'slide')
			jQueryElement.slideUp(velocity, function(){
				$(this).addClass(newClass);
				if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
				else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
					else if(effect2 === 'show')	$(this).text(text).show(velocity);
			});
			else if(effect1 === 'hide')
				jQueryElement.hide(velocity, function(){
					$(this).addClass(newClass);
					if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
					else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
						else if(effect2 === 'show')	$(this).text(text).show(velocity);
				});
}

//show a JS debug
function dbg(value)
{
	var active = false;//true for active
	var firefox = true;//true if debug under firefox

	if (active)
		if (firefox)
			console.log(value);
		else
			alert(value);
}

/**
* Function : print_r()
* Arguments: The data  - array,hash(associative array),object
*            The level - OPTIONAL
* Returns  : The textual representation of the array.
* This function was inspired by the print_r function of PHP.
* This will accept some data as the argument and return a
* text that will be a more readable version of the
* array/hash/object that is given.
*/
function print_r(arr, level)
{
	var dumped_text = "";
	if (!level)
		level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for (var j = 0 ; j < level + 1; j++)
		level_padding += "    ";

	if (typeof(arr) === 'object')
	{ //Array/Hashes/Objects 
		for (var item in arr)
		{
			var value = arr[item];
			if (typeof(value) === 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			}
			else
			{
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	}
	else
	{ //Stings/Chars/Numbers etc.
		dumped_text = "===>" + arr + "<===("+typeof(arr)+")";
	}
	return dumped_text;
}

//verify if value is in the array
function in_array(value, array)
{
	for (var i in array)
		if (array[i] === value)
			return true;
	return false;
}

function resizeAddressesBox(nameBox)
{
	maxHeight = 0;

	if (typeof(nameBox) === 'undefined')
		nameBox = '.address';
	$(nameBox).each(function()
	{
		$(this).css('height', 'auto');
		currentHeight = $(this).height();
		if (maxHeight < currentHeight)
			maxHeight = currentHeight;
	});
	$(nameBox).height(maxHeight);
}

$(document).ready(function() {
	$.fn.checkboxChange = function(fnChecked, fnUnchecked) {
		if ($(this).prop('checked') && fnChecked)
			fnChecked.call(this);
		else if(fnUnchecked)
			fnUnchecked.call(this);
		
		if (!$(this).attr('eventCheckboxChange'))
		{
			$(this).live('change', function() { $(this).checkboxChange(fnChecked, fnUnchecked); });
			$(this).attr('eventCheckboxChange', true);
		}
	};
});


// Use it to simulate target blank link
$(function(){
    $('a.js-new-window').click(function(){
        window.open(this.href);
        return false;
    });
});
