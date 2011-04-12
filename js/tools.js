/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

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

function	autoUrl(name, dest)
{
	var loc;
	var id_list;

	id_list = document.getElementById(name);
	loc = id_list.options[id_list.selectedIndex].value;
	if (loc != 0)
		location.href = dest+loc;
	return ;
}

function	autoUrlNoList(name, dest)
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
	else if ( window.external )
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
	document.write(insert);
}

function writeBookmarkLinkObject(url, title, insert)
{
	if (window.navigator.userAgent.indexOf('Chrome') != -1)
		return ('');
	else if (window.sidebar || window.external)
		return ('<a href="javascript:addBookmark(\'' + escape(url) + '\', \'' + escape(title) + '\')">' + insert + '</a>');
	else if (window.opera && window.print)
		return ('<a rel="sidebar" href="' + escape(url) + '" title="' + escape(title) + '">' + insert + '</a>');
	return ('');
}

function checkCustomizations()
{
	var tmp;
	var pattern = new RegExp(' ?filled ?');
	for (var i = 0; i < customizationFields.length; i++)
		/* If the field is required and empty then we abort */
		if (parseInt(customizationFields[i][1]) == 1 && $('#' + customizationFields[i][0]).val() == '' && !pattern.test($('#' + customizationFields[i][0]).attr('class')))
			return false;
	return true;
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
		url: baseDir + 'changecurrency.php',
		data: 'id_currency='+parseInt(id_currency),
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

}

