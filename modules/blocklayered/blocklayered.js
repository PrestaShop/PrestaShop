/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

var ajaxQueries = new Array();
var ajaxLoaderOn = 0;

$(document).ready(function()
{
	cancelFilter();
	openCloseFilter();
	
	$('#layered_form input[type=button], #layered_form label.layered_color').live('click', function()
	{
		$('<input />').attr('type', 'hidden').attr('name', $(this).attr('name')).val($(this).attr('rel')).appendTo('#layered_form');
		reloadContent();
	});
	
	$('#layered_form input[type=checkbox]').live('click', function()
	{
		reloadContent();
	});
});

function cancelFilter()
{
	$('#enabled_filters a').live('click', function()
	{
		$('#'+$(this).attr('rel')).attr('checked', false);
		$('#layered_form input[name='+$(this).attr('rel')+']').remove();
		reloadContent();
	});
}

function openCloseFilter()
{
	$('#layered_form span.layered_close a').live('click', function()
	{
		if ($(this).html() == '&lt;')
		{
			$('#'+$(this).attr('rel')).show();
			$(this).html('v');
		}
		else
		{
			$('#'+$(this).attr('rel')).hide();
			$(this).html('&lt;');
		}
	});
}

function reloadContent()
{
	for(i = 0; i < ajaxQueries.length; i++)
		ajaxQueries[i].abort();
	ajaxQueries = new Array();

	if (!ajaxLoaderOn)
	{
		$('#product_list').prepend($('#layered_ajax_loader').html());	
		$('#product_list').css('opacity', '0.7');
		ajaxLoaderOn = 1;
	}
	
	data = $('#layered_form').serialize();
	$('.layered_slider').each( function () {
		data += '&'+$(this).attr('id')+'='+$(this).slider('values', 0)+'_'+$(this).slider('values', 1);
	});
	
ajaxQuery = $.ajax(
	{
		type: 'GET',
		url: baseDir + 'modules/blocklayered/blocklayered-ajax.php',
		data: data,
		dataType: 'json',
		success: function(result)
		{
			$('#layered_block_left').after('<div id="tmp_layered_block_left"></div>').remove();
			$('#tmp_layered_block_left').html(result.filtersBlock).attr('id', 'layered_block_left');

			$('#product_list').html(result.productList).html();
			$('#product_list').css('opacity', '1');
			ajaxLoaderOn = 0;
		}
	});
	
	ajaxQueries.push(ajaxQuery);
}

/*
function initSlider(type, min, max, values, unit)
{
	$('#layered_'+type+'_slider').slider({
		range: true,
		min: min,
		max: max,
		values: [ values[0], values[1]],
		slide: function( event, ui ) {
			$('#layered_'+type+'_range').html(ui.values[ 0 ] + unit + ' - ' + ui.values[ 1 ] + unit);
		},
		stop: function () {
			reloadContent();
		}
	});
	$('#layered_'+type+'_range').html($('#layered_'+type+'_slider').slider('values', 0 ) +unit+'-'+$('#layered_'+type+'_slider').slider('values', 1)+unit );
}
*/