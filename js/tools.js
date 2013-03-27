/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function formatedNumberToFloat(price, currencyFormat, currencySign)
{
	price = price.replace(currencySign, '');
	if (currencyFormat == 1)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	else if (currencyFormat == 2)
		return parseFloat(price.replace(' ', '').replace(',', '.'));
	else if (currencyFormat == 3)
		return parseFloat(price.replace('.', '').replace(' ', '').replace(',', '.'));
	else if (currencyFormat == 4)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	return price;
}

//return a formatted number
function formatNumber(value, numberOfDecimal, thousenSeparator, virgule)
{
	value = value.toFixed(numberOfDecimal);
	var val_string = value+'';
	var tmp = val_string.split('.');
	var abs_val_string = (tmp.length == 2) ? tmp[0] : val_string;
	var deci_string = ('0.' + (tmp.length == 2 ? tmp[1] : 0)).substr(2);
	var nb = abs_val_string.length;

	for (var i = 1 ; i < 4; i++)
		if (value >= Math.pow(10, (3 * i)))
			abs_val_string = abs_val_string.substring(0, nb - (3 * i)) + thousenSeparator + abs_val_string.substring(nb - (3 * i));

	if (parseInt(numberOfDecimal) == 0)
		return abs_val_string;
	return abs_val_string + virgule + (deci_string > 0 ? deci_string : '00');
}

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

function ps_round(value, precision)
{
	if (typeof(roundMode) == 'undefined')
		roundMode = 2;
	if (typeof(precision) == 'undefined')
		precision = 2;
	
	method = roundMode;
	if (method == 0)
		return ceilf(value, precision);
	else if (method == 1)
		return floorf(value, precision);
	precisionFactor = precision == 0 ? 1 : Math.pow(10, precision);
	return Math.round(value * precisionFactor) / precisionFactor;
}

function autoUrl(name, dest)
{
	var loc;
	var id_list;

	id_list = document.getElementById(name);
	loc = id_list.options[id_list.selectedIndex].value;
	if (loc != 0)
		location.href = dest+loc;
	return ;
}

function autoUrlNoList(name, dest)
{
	var loc;

	loc = document.getElementById(name).checked;
	location.href = dest + (loc == true ? 1 : 0);
	return ;
}

/*
** show or hide element e depending on condition show
*/
function toggle(e, show)
{
	e.style.display = show ? '' : 'none';
}

function toggleMultiple(tab)
{
    var len = tab.length;

    for (var i = 0; i < len; i++)
        if (tab[i].style)
            toggle(tab[i], tab[i].style.display == 'none');
}

/**
* Show dynamicaly an element by changing the sytle "display" property
* depending on the option selected in a select.
*
* @param string $select_id id of the select who controls the display
* @param string $elem_id prefix id of the elements controlled by the select
*   the real id must be : 'elem_id'+nb with nb the corresponding number in the
*   select (starting with 0).
*/
function showElemFromSelect(select_id, elem_id)
{
	var select = document.getElementById(select_id);
	for (var i = 0; i < select.length; ++i)
	{
	    var elem = document.getElementById(elem_id + select.options[i].value);
		if (elem != null)
			toggle(elem, i == select.selectedIndex);
	}
}

/**
* Get all div with specified name and for each one (by id), toggle their visibility
*/
function openCloseAllDiv(name, option)
{
	var tab = $('*[name='+name+']');
	for (var i = 0; i < tab.length; ++i)
		toggle(tab[i], option);
}

/**
* Toggle the value of the element id_button between text1 and text2
*/
function toggleElemValue(id_button, text1, text2)
{
	var obj = document.getElementById(id_button);
	if (obj)
		obj.value = ((!obj.value || obj.value == text2) ? text1 : text2);
}

function addBookmark(url, title)
{
	if (window.sidebar)
		return window.sidebar.addPanel(title, url, "");
	else if ( window.external && ('AddFavorite' in window.external))
		return window.external.AddFavorite( url, title);
	else if (window.opera && window.print)
		return true;
	return true;
}

function writeBookmarkLink(url, title, text, img)
{
	var insert = '';
	if (img)
		insert = writeBookmarkLinkObject(url, title, '<img src="' + img + '" alt="' + escape(text) + '" title="' + escape(text) + '" />') + '&nbsp';
	insert += writeBookmarkLinkObject(url, title, text);
	if (window.sidebar || window.opera && window.print || (window.external && ('AddFavorite' in window.external)))	
		document.write(insert);
}

function writeBookmarkLinkObject(url, title, insert)
{
	if (window.sidebar || window.external)
		return ('<a href="javascript:addBookmark(\'' + escape(url) + '\', \'' + escape(title) + '\')">' + insert + '</a>');
	else if (window.opera && window.print)
		return ('<a rel="sidebar" href="' + escape(url) + '" title="' + escape(title) + '">' + insert + '</a>');
	return ('');
}

function checkCustomizations()
{
	var pattern = new RegExp(' ?filled ?');

	if (typeof customizationFields != 'undefined')
		for (var i = 0; i < customizationFields.length; i++)
		{								
			/* If the field is required and empty then we abort */
			if (parseInt(customizationFields[i][1]) == 1 && ($('#' + customizationFields[i][0]).html() == '' ||  $('#' + customizationFields[i][0]).text() != $('#' + customizationFields[i][0]).val()) && !pattern.test($('#' + customizationFields[i][0]).attr('class')))
				return false;
		}
	return true;
}

function emptyCustomizations()
{
	if(typeof(customizationFields) == 'undefined') return;

	$('.customization_block .success').fadeOut(function(){
		$(this).remove();
	});
	$('.customization_block .error').fadeOut(function(){
		$(this).remove();
	});
	for (var i = 0; i < customizationFields.length; i++)
	{
		$('#' + customizationFields[i][0]).html('');
		$('#' + customizationFields[i][0]).val('');
	}
}

function ceilf(value, precision)
{
	if (typeof(precision) == 'undefined')
		precision = 0;
	var precisionFactor = precision == 0 ? 1 : Math.pow(10, precision);
	var tmp = value * precisionFactor;
	var tmp2 = tmp.toString();
	// If the current value has already the desired precision
	if (tmp2.indexOf('.') === false)
		return (value);
	if (tmp2.charAt(tmp2.length - 1) == 0)
		return value;
	return Math.ceil(tmp) / precisionFactor;
}

function floorf(value, precision)
{
	if (typeof(precision) == 'undefined')
		precision = 0;
	var precisionFactor = precision == 0 ? 1 : Math.pow(10, precision);
	var tmp = value * precisionFactor;
	var tmp2 = tmp.toString();
	// If the current value has already the desired precision
	if (tmp2.indexOf('.') === false)
		return (value);
	if (tmp2.charAt(tmp2.length - 1) == 0)
		return value;
	return Math.floor(tmp) / precisionFactor;
}

function setCurrency(id_currency)
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseDir + 'index.php' + '?rand=' + new Date().getTime(),
		data: 'controller=change-currency&id_currency='+ parseInt(id_currency),
		success: function(msg)
		{
			location.reload(true);
		}
	});
}

function isArrowKey(k_ev)
{
	var unicode=k_ev.keyCode? k_ev.keyCode : k_ev.charCode;
	if (unicode >= 37 && unicode <= 40)
		return true;
	return false;
}

//On dom ready
$().ready(function()
{
	// Hide all elements with .hideOnSubmit class when parent form is submit
	$('form').submit(function()
	{
		$(this).find('.hideOnSubmit').hide();
	});
	// attribute target="_blank" is not W3C compliant
	$('a._blank').attr('target', '_blank');
});
