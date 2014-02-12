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
*  International Registred Trademark & Property of PrestaShop SA
*/

var ajaxQueries = new Array();
var ajaxLoaderOn = 0;
var sliderList = new Array();
var slidersInit = false;

$(document).ready(function()
{
	cancelFilter();
	openCloseFilter();

	// Click on color
	$('#layered_form input[type=button], #layered_form label.layered_color').on('click', function()
	{
		if (!$('input[name='+$(this).attr('name')+'][type=hidden]').length)
			$('<input />').attr('type', 'hidden').attr('name', $(this).attr('name')).val($(this).attr('rel')).appendTo('#layered_form');
		else
			$('input[name='+$(this).attr('name')+'][type=hidden]').remove();
		reloadContent();
	});

	// Click on checkbox
	$('#layered_form input[type=checkbox], #layered_form input[type=radio], #layered_form select').on('change', function()
	{
		reloadContent();
	});

	// Changing content of an input text
	$('#layered_form input.layered_input_range').on('keyup', function()
	{
		if ($(this).attr('timeout_id'))
			window.clearTimeout($(this).attr('timeout_id'));

		// IE Hack, setTimeout do not acept the third parameter
		var reference = this;

		$(this).attr('timeout_id', window.setTimeout(function(it) {
			if (!$(it).attr('id'))
				it = reference;

			var filter = $(it).attr('id').replace(/^layered_(.+)_range_.*$/, '$1');

			var value_min = parseInt($('#layered_'+filter+'_range_min').val());
			if (isNaN(value_min))
				value_min = 0;
			$('#layered_'+filter+'_range_min').val(value_min);

			var value_max = parseInt($('#layered_'+filter+'_range_max').val());
			if (isNaN(value_max))
				value_max = 0;
			$('#layered_'+filter+'_range_max').val(value_max);

			if (value_max < value_min) {
				$('#layered_'+filter+'_range_max').val($(it).val());
				$('#layered_'+filter+'_range_min').val($(it).val());
			}
			reloadContent();
		}, 500, this));
	});

	$('#layered_block_left .radio').on('click', function() {
		var name = $(this).attr('name');
		$.each($(this).parent().parent().find('input[type=button]'), function (it, item) {
			if ($(item).hasClass('on') && $(item).attr('name') != name) {
				$(item).click();
			}
		});
		return true;
	});

	// Click on label
	$('#layered_block_left label a').on({
		click: function() {
			var disable = $(this).parent().parent().find('input').attr('disabled');
			if (disable == ''
			|| typeof(disable) == 'undefined'
			|| disable == false)
			{
				$(this).parent().parent().find('input').click();
				reloadContent();
			}
			return false;
		}
	});

	layered_hidden_list = {};
	$('.hide-action').on('click', function() {
		if (typeof(layered_hidden_list[$(this).parent().find('ul').attr('id')]) == 'undefined' || layered_hidden_list[$(this).parent().find('ul').attr('id')] == false)
		{
			layered_hidden_list[$(this).parent().find('ul').attr('id')] = true;
		}
		else
		{
			layered_hidden_list[$(this).parent().find('ul').attr('id')] = false;
		}
		hideFilterValueAction(this);
	});
	$('.hide-action').each(function() {
		hideFilterValueAction(this);
	});

	$('.selectProductSort').unbind('change').bind('change', function(event) {
		$('.selectProductSort').val($(this).val());

		if($('#layered_form').length > 0)
			reloadContent();
	});

	$('.js-nb_item').unbind('change').attr('onchange', '');

	$('.js-nb_item').on('change', function(event) {
		$('.js-nb_item').val($(this).val());
		reloadContent();
	});

	paginationButton(false);
	initLayered();
});

function initFilters()
{
	if (typeof filters !== 'undefined')
	{
		for (key in filters)
		{
			if (filters.hasOwnProperty(key))
				var filter = filters[key];

			if (typeof filter.slider !== 'undefined' && parseInt(filter.filter_type) == 0)
			{
				var filterRange = parseInt(filter.max)-parseInt(filter.min);
				var step = filterRange / 100;

				if (step > 1)
					step = parseInt(step);

				addSlider(filter.type,
				{
					range: true,
					step: step,
					min: parseInt(filter.min),
					max: parseInt(filter.max),
					values: [filter.values[0], filter.values[1]],
					slide: function(event, ui) {
						stopAjaxQuery();

						if (parseInt(filter.format) < 5)
						{
							from = formatCurrency(ui.values[0], parseInt(filter.format), filter.unit);
							to = formatCurrency(ui.values[1], parseInt(filter.format), filter.unit);
						}
						else
						{
							from = ui.values[0] + filter.unit;
							to = ui.values[1] + filter.unit;
						}

						$('#layered_' + filter.type + '_range').html(from + ' - ' + to);
					},
					stop: function () {
						reloadContent();
					}
				}, filter.unit, parseInt(filter.format));
			}
			else if(typeof filter.slider !== 'undefined' && parseInt(filter.filter_type) == 1)
			{
				$('#layered_' + filter.type + '_range_min').attr('limitValue', filter.min);
				$('#layered_' + filter.type + '_range_max').attr('limitValue', filter.max);
			}
			
			$('.layered_' + filter.type).show();
		}

		initUniform();
	}
}

function initUniform()
{
	$("#layered_form input[type='checkbox'], #layered_form input[type='radio'], select.form-control").uniform();
}

function hideFilterValueAction(it)
{
	if (typeof(layered_hidden_list[$(it).parent().find('ul').attr('id')]) == 'undefined'
		|| layered_hidden_list[$(it).parent().find('ul').attr('id')] == false)
	{
		$(it).parent().find('.hiddable').hide();
		$(it).parent().find('.hide-action.less').hide();
		$(it).parent().find('.hide-action.more').show();
	}
	else
	{
		$(it).parent().find('.hiddable').show();
		$(it).parent().find('.hide-action.less').show();
		$(it).parent().find('.hide-action.more').hide();
	}
}

function addSlider(type, data, unit, format)
{
	sliderList.push({
		type: type,
		data: data,
		unit: unit,
		format: format
	});
}

function initSliders()
{
	$(sliderList).each(function(i, slider){
		$('#layered_'+slider['type']+'_slider').slider(slider['data']);

		var from = '';
		var to = '';
		switch (slider['format'])
		{
			case 1:
			case 2:
			case 3:
			case 4:
				from = formatCurrency($('#layered_'+slider['type']+'_slider').slider('values', 0), slider['format'], slider['unit']);
				to = formatCurrency($('#layered_'+slider['type']+'_slider').slider('values', 1), slider['format'], slider['unit']);
				break;
			case 5:
				from =  $('#layered_'+slider['type']+'_slider').slider('values', 0)+slider['unit']
				to = $('#layered_'+slider['type']+'_slider').slider('values', 1)+slider['unit'];
				break;
		}
		$('#layered_'+slider['type']+'_range').html(from+' - '+to);
	});
}

function initLayered()
{
	initFilters();
	initSliders();
	initLocationChange();
	updateProductUrl();
	if (window.location.href.split('#').length == 2 && window.location.href.split('#')[1] != '')
	{
		var params = window.location.href.split('#')[1];
		reloadContent('&selected_filters='+params);
	}
}

function paginationButton(nbProductsIn) {
	if (typeof(current_friendly_url) === 'undefined')
		current_friendly_url = '#';

	$('div.pagination a').not(':hidden').each(function () {
		if ($(this).attr('href').search('&p=') == -1) {
			var page = 1;
		}
		else {
			var page = $(this).attr('href').replace(/^.*&p=(\d+).*$/, '$1');
		}
		var location = window.location.href.replace(/#.*$/, '');
		$(this).attr('href', location+current_friendly_url.replace(/\/page-(\d+)/, '')+'/page-'+page);
	});
	$('div.pagination li').not('.current, .disabled').each(function () {
		var nbPage = 0;
		if ($(this).hasClass('pagination_next'))
			nbPage = parseInt($('div.pagination li.current').children().children().html())+ 1;
		else if ($(this).hasClass('pagination_previous'))
			nbPage = parseInt($('div.pagination li.current').children().children().html())- 1;

		$(this).children().children().click(function () {
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


	//product count refresh
	if(nbProductsIn!=false){
		if(isNaN(nbProductsIn) == 0) {
			// add variables
			var productCountRow = $('.product-count').html();
			var nbPage = parseInt($('div.pagination li.current').children().children().html());
			var nb_products = nbProductsIn;

			if ($('#nb_item option:selected').length == 0)
				var nbPerPage = nb_products;
			else
				var nbPerPage = parseInt($('#nb_item option:selected').val());

			isNaN(nbPage) ? nbPage = 1 : nbPage = nbPage;
			nbPerPage*nbPage < nb_products ? productShowing = nbPerPage*nbPage :productShowing = (nbPerPage*nbPage-nb_products-nbPerPage*nbPage)*-1;
			nbPage==1 ? productShowingStart=1 : productShowingStart=nbPerPage*nbPage-nbPerPage+1;


			//insert values into a .product-count
			productCountRow = $.trim(productCountRow);
			productCountRow = productCountRow.split(' ');
			productCountRow[1] = productShowingStart;
			productCountRow[3] = productShowing;
			productCountRow[5] = nb_products;
			productCountRow = productCountRow.join(' ');
			$('.product-count').html(productCountRow);
			$('.product-count').show();
		}
		else {
			$('.product-count').hide();
		}
	}
}

function cancelFilter()
{
	$('#enabled_filters a').on('click', function(e)
	{
		if ($(this).attr('rel').search(/_slider$/) > 0)
		{
			if ($('#'+$(this).attr('rel')).length)
			{
				$('#'+$(this).attr('rel')).slider('values' , 0, $('#'+$(this).attr('rel')).slider('option' , 'min' ));
				$('#'+$(this).attr('rel')).slider('values' , 1, $('#'+$(this).attr('rel')).slider('option' , 'max' ));
				$('#'+$(this).attr('rel')).slider('option', 'slide')(0,{values:[$('#'+$(this).attr('rel')).slider( 'option' , 'min' ), $('#'+$(this).attr('rel')).slider( 'option' , 'max' )]});
			}
			else if($('#'+$(this).attr('rel').replace(/_slider$/, '_range_min')).length)
			{
				$('#'+$(this).attr('rel').replace(/_slider$/, '_range_min')).val($('#'+$(this).attr('rel').replace(/_slider$/, '_range_min')).attr('limitValue'));
				$('#'+$(this).attr('rel').replace(/_slider$/, '_range_max')).val($('#'+$(this).attr('rel').replace(/_slider$/, '_range_max')).attr('limitValue'));
			}
		}
		else
		{
			if ($('option#'+$(this).attr('rel')).length)
			{
				$('#'+$(this).attr('rel')).parent().val('');
			}
			else
			{
				$('#'+$(this).attr('rel')).attr('checked', false);
				$('.'+$(this).attr('rel')).attr('checked', false);
				$('#layered_form input[type=hidden][name='+$(this).attr('rel')+']').remove();
			}
		}
		reloadContent();
		e.preventDefault();
	});
}

function openCloseFilter()
{
	$('#layered_form span.layered_close a').on('click', function(e)
	{
		if ($(this).html() == '&lt;')
		{
			$('#'+$(this).attr('rel')).show();
			$(this).html('v');
			$(this).parent().removeClass('closed');
		}
		else
		{
			$('#'+$(this).attr('rel')).hide();
			$(this).html('&lt;');
			$(this).parent().addClass('closed');
		}

		e.preventDefault();
	});
}

function stopAjaxQuery() {
	if (typeof(ajaxQueries) == 'undefined')
		ajaxQueries = new Array();
	for(i = 0; i < ajaxQueries.length; i++)
		ajaxQueries[i].abort();
	ajaxQueries = new Array();
}

function reloadContent(params_plus)
{
	stopAjaxQuery();

	if (!ajaxLoaderOn)
	{
		$('.product_list').prepend($('#layered_ajax_loader').html());
		$('.product_list').css('opacity', '0.7');
		ajaxLoaderOn = 1;
	}

	data = $('#layered_form').serialize();
	$('.layered_slider').each( function () {
		var sliderStart = $(this).slider('values', 0);
		var sliderStop = $(this).slider('values', 1);
		if (typeof(sliderStart) == 'number' && typeof(sliderStop) == 'number')
			data += '&'+$(this).attr('id')+'='+sliderStart+'_'+sliderStop;
	});

	$(['price', 'weight']).each(function(it, sliderType)
	{
		if ($('#layered_'+sliderType+'_range_min').length)
		{
			data += '&layered_'+sliderType+'_slider='+$('#layered_'+sliderType+'_range_min').val()+'_'+$('#layered_'+sliderType+'_range_max').val();
		}
	});

	$('#layered_form .select option').each( function () {
		if($(this).attr('id') && $(this).parent().val() == $(this).val())
		{
			data += '&'+$(this).attr('id') + '=' + $(this).val();
		}
	});

	if ($('.selectProductSort').length && $('.selectProductSort').val())
	{
		if ($('.selectProductSort').val().search(/orderby=/) > 0)
		{
			// Old ordering working
			var splitData = [
				$('.selectProductSort').val().match(/orderby=(\w*)/)[1],
				$('.selectProductSort').val().match(/orderway=(\w*)/)[1]
			];
		}
		else
		{
			// New working for default theme 1.4 and theme 1.5
			var splitData = $('.selectProductSort').val().split(':');
		}
		data += '&orderby='+splitData[0]+'&orderway='+splitData[1];
	}
	if ($('.js-nb_item').length)
	{
		data += '&n='+$('.js-nb_item').val();
	}

	var slideUp = true;
	if (params_plus == undefined)
	{
		params_plus = '';
		slideUp = false;
	}

	// Get nb items per page
	var n = '';
	$('div.pagination .js-nb_item').children().each(function(it, option) {
		if (option.selected)
			n = '&n='+option.value;
	});

	ajaxQuery = $.ajax(
	{
		type: 'GET',
		url: baseDir + 'modules/blocklayered/blocklayered-ajax.php',
		data: data+params_plus+n,
		dataType: 'json',
		cache: false, // @todo see a way to use cache and to add a timestamps parameter to refresh cache each 10 minutes for example
		success: function(result)
		{
			if (result.meta_description != '')
				$('meta[name="description"]').attr('content', result.meta_description);

			if (result.meta_keywords != '')
				$('meta[name="keywords"]').attr('content', result.meta_keywords);

			if (result.meta_title != '')
				$('title').html(result.meta_title);

			if (result.heading != '')
			{
				$('h1.page-heading .cat-name').html(result.heading);
				$('span.category-name').html(result.heading);
			}

			$('#layered_block_left').replaceWith(utf8_decode(result.filtersBlock));
			$('.category-product-count, .heading-counter').html(result.categoryCount);

			if (result.productList)
				$('.product_list').replaceWith(utf8_decode(result.productList));
			else
				$('.product_list').html('');

			$('.product_list').css('opacity', '1');
			if ($.browser.msie) // Fix bug with IE8 and aliasing
				$('.product_list').css('filter', '');

			if (result.pagination.search(/[^\s]/) >= 0) {
				var data = $('<div/>').html(result.pagination);

				if (data.find('ul.pagination').length)
				{
					$('div.pagination').show();
					$('ul.pagination').each(function () {
						$(this).replaceWith(data.find('ul.pagination'));
					});
				}
				else if (!$('ul.pagination').length)
				{
					$('div.pagination').show();
					$('div.pagination').each(function () {
						$(this).html(data.find('div.pagination').innerHTML);
					});
				}
				else
				{
					$('ul.pagination').html('');
					$('div.pagination').hide();
				}
			}
			else
			{
				$('ul.pagination').html('');
				$('div.pagination').hide();
			}

			paginationButton(parseInt(result.categoryCount.replace(/[^0-9]/g,'')));
			ajaxLoaderOn = 0;

			// On submiting nb items form, relaod with the good nb of items
			$('div.pagination form').submit(function() {
				val = $('div.pagination .js-nb_item').val();
				$('div.pagination .js-nb_item').children().each(function(it, option) {
					if (option.value == val)
						$(option).attr('selected', true);
					else
						$(option).removeAttr('selected');
				});
				// Reload products and pagination
				reloadContent();
				return false;
			});
			if (typeof(ajaxCart) != "undefined")
				ajaxCart.overrideButtonsInThePage();

			if (typeof(reloadProductComparison) == 'function')
				reloadProductComparison();

			filters = result.filters;
			initFilters();
			initSliders();

			current_friendly_url = result.current_friendly_url;

			// Currente page url
			if (typeof(current_friendly_url) === 'undefined')
				current_friendly_url = '#';

			// Get all sliders value
			$(['price', 'weight']).each(function(it, sliderType)
			{
				if ($('#layered_'+sliderType+'_slider').length)
				{
					// Check if slider is enable & if slider is used
					if(typeof($('#layered_'+sliderType+'_slider').slider('values', 0)) != 'object')
					{
						if ($('#layered_'+sliderType+'_slider').slider('values', 0) != $('#layered_'+sliderType+'_slider').slider('option' , 'min')
						|| $('#layered_'+sliderType+'_slider').slider('values', 1) != $('#layered_'+sliderType+'_slider').slider('option' , 'max'))
							current_friendly_url += '/'+blocklayeredSliderName[sliderType]+'-'+$('#layered_'+sliderType+'_slider').slider('values', 0)+'-'+$('#layered_'+sliderType+'_slider').slider('values', 1)
					}
				}
				else if ($('#layered_'+sliderType+'_range_min').length)
				{
					current_friendly_url += '/'+blocklayeredSliderName[sliderType]+'-'+$('#layered_'+sliderType+'_range_min').val()+'-'+$('#layered_'+sliderType+'_range_max').val();
				}
			});

			if (current_friendly_url == '#')
				current_friendly_url = '#/';

			window.location.href = current_friendly_url;
			lockLocationChecking = true;

			if(slideUp)
				$.scrollTo('.product_list', 400);
			updateProductUrl();

			$('.hide-action').each(function() {
				hideFilterValueAction(this);
			});

			if (display instanceof Function) {
				var view = $.totalStorage('display');

				if (view && view != 'grid')
					display(view);
				blockHover();
			}
		}
	});
	ajaxQueries.push(ajaxQuery);
}

function initLocationChange(func, time)
{
	if(!time) time = 500;
	var current_friendly_url = getUrlParams();
	setInterval(function()
	{
		if(getUrlParams() != current_friendly_url && !lockLocationChecking)
		{
			// Don't reload page if current_friendly_url and real url match
			if (current_friendly_url.replace(/^#(\/)?/, '') == getUrlParams().replace(/^#(\/)?/, ''))
				return;

			lockLocationChecking = true;
			reloadContent('&selected_filters='+getUrlParams().replace(/^#/, ''));
		}
		else {
			lockLocationChecking = false;
			current_friendly_url = getUrlParams();
		}
	}, time);
}

function getUrlParams()
{
	if (typeof(current_friendly_url) === 'undefined')
		current_friendly_url = '#';

	var params = current_friendly_url;
	if(window.location.href.split('#').length == 2 && window.location.href.split('#')[1] != '')
		params = '#'+window.location.href.split('#')[1];
	return params;
}

function updateProductUrl()
{
	// Adding the filters to URL product
	if (typeof(param_product_url) != 'undefined' && param_product_url != '' && param_product_url !='#') {
		$.each($('ul.product_list li.ajax_block_product .product_img_link,'+
				'ul.product_list li.ajax_block_product h5 a,'+
				'ul.product_list li.ajax_block_product .product_desc a,'+
				'ul.product_list li.ajax_block_product .lnk_view'), function() {
			$(this).attr('href', $(this).attr('href') + param_product_url);
		});
	}
}


/**
 * Copy of the php function utf8_decode()
 */
function utf8_decode (utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);

		if (c < 128)
		{
			res += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224))
		{
			var c1 = utfstr.charCodeAt(i+1);
			res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
			i += 2;
		}
		else
		{
			var c1 = utfstr.charCodeAt(i+1);
			var c2 = utfstr.charCodeAt(i+2);
			res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
			i += 3;
		}
	}
	return res;
}
