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
*  @version  Release: $Revision: 7096 $
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
		if (!$('\'input[name='+$(this).attr('name')+']:hidden\'').length)
			$('<input />').attr('type', 'hidden').attr('name', $(this).attr('name')).val($(this).attr('rel')).appendTo('#layered_form');
		else
			$('\'input[name='+$(this).attr('name')+']:hidden\'').remove();
		reloadContent();
	});
	
	$('#layered_form input[type=checkbox]').live('click', function()
	{
		reloadContent();
	});
	
	paginationButton();
});

function paginationButton() {
	$('#pagination li').not('.current, .disabled').each( function () {
		var nbPage = 0;
		if ($(this).attr('id') == 'pagination_next')
			nbPage = parseInt($('#pagination li.current').children().html())+ 1;
		else if ($(this).attr('id') == 'pagination_previous')
			nbPage = parseInt($('#pagination li.current').children().html())- 1;
	
		$(this).children().click(function () {
			if (nbPage == 0)
				p = parseInt($(this).html()) + parseInt(nbPage);
			else
				p = nbPage;
			p = '&p='+ p;
			reloadContent(p);
			nbPage = 0;
			return false;
		});
	});
}

function cancelFilter()
{
	$('#enabled_filters a').live('click', function(e)
	{
		$('#'+$(this).attr('rel')).attr('checked', false);
		$('#layered_form input[name='+$(this).attr('rel')+']:hidden').remove();
		reloadContent();
		e.preventDefault();
	});
}

function openCloseFilter()
{
	$('#layered_form span.layered_close a').live('click', function(e)
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
		
		e.preventDefault();
	});
}

function reloadContent(params_plus)
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
	
	if ($('#selectPrductSort').length)
	{
		var splitData = $('#selectPrductSort').val().split(':');
		data += '&orderby='+splitData[0]+'&orderway='+splitData[1];
	}
	
	if(params_plus == undefined)
		params_plus = '';
	
	// Get nb items per page
	var n = '';
	$('#pagination #nb_item').children().each(function(it, option) {
		if(option.selected) {
			n = '&n='+option.value;
		}
	});
	
	ajaxQuery = $.ajax(
	{
		type: 'GET',
		url: baseDir + 'modules/blocklayered/blocklayered-ajax.php',
		data: data+params_plus+n,
		dataType: 'json',
		success: function(result)
		{
			$('#layered_block_left').after('<div id="tmp_layered_block_left"></div>').remove();
			$('#tmp_layered_block_left').html(result.filtersBlock).attr('id', 'layered_block_left');

			$('#product_list').html(result.productList).html();
			$('#product_list').css('opacity', '1');
			$('div#pagination').html(result.pagination);
			paginationButton();
			ajaxLoaderOn = 0;
			
			// On submiting nb items form, relaod with the good nb of items
			$("#pagination form").submit(function() {
				val = $('#pagination #nb_item').val();
				$('#pagination #nb_item').children().each(function(it, option) {
					if(option.value == val) {
						$(option).attr('selected', 'selected');
					} else {
						$(option).removeAttr('selected');
					}
				});
				// Reload products and pagination
				reloadContent();
				return false;
			});
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
