/*
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Combination */

var posC = true;
$(document).ready(function() {
	$('#desc-product-newCombination').click(function() {
		if (posC == true)
			removeButtonCombination('add');
		else
			addButtonCombination('add');
	});
});

function removeButtonCombination(item)
{
	$('#add_new_combination').show();
	$('.process-icon-newCombination').removeClass('toolbar-new');
	$('.process-icon-newCombination').addClass('toolbar-cancel');
	$('#desc-product-newCombination div').html($('#ResetBtn').val());
	$('id_product_attribute').val(0);
	init_elems();
	posC = false;
}

function addButtonCombination(item)
{
	$('#add_new_combination').hide();
	$('.process-icon-newCombination').removeClass('toolbar-cancel');
	$('.process-icon-newCombination').addClass('toolbar-new');
	$('#desc-product-newCombination div').html(msg_new_combination);
	posC = true;
}

function deleteProductAttribute(url, parent)
{
	$.ajax({
		url: url,
		data: {
			id_product: id_product,
			action: 'deleteProductAttribute',
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.message);
				parent.remove();
			}
			else
				showErrorMessage(data.message);
		}
	});
}

function defaultProductAttribute(url, parent)
{
	$.ajax({
		url: url,
		data: {
			id_product: id_product,
			action: 'defaultProductAttribute',
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.message);
				$('table.table').find('tr').attr('style', function() {
					var style = $(this).attr('style');
					if (style)
					{
						$(this).attr('style', '');
						var ids = $(this).find('a.edit').attr('ids');
						var token = $(this).find('a.edit').attr('token');
						$(this).find('a.edit').after("<a title=\"Default\" onclick=\"javascript:defaultProductAttribute('"+ids+"', '"+token+"', $(this).parent('td').parent('tr'));\" class=\"pointer default\"><img alt=\"Default\" src=\"../img/admin/asterisk.gif\"></a>");
					}
				});
				parent.find('a.default').hide();
				parent.css('background','#BDE5F8');
			}
			else
				showErrorMessage(data.message);
		}
	});
}

function editProductAttribute(url, parent)
{
	$.ajax({
		url: url,
		data: {
			id_product: id_product,
			ajax: true,
			action: 'editProductAttribute'
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			// color the selected line
			parent.siblings().removeClass('selected-line');
			parent.addClass('selected-line');

			$('#add_new_combination').show();
			$('#attribute_quantity').show();
			$('#product_att_list').html('');
			removeButtonCombination('update');
			$.scrollTo('#add_new_combination', 1200, { offset: -100 });
			var wholesale_price = Math.abs(data[0]['wholesale_price']);
			var price = Math.abs(data[0]['price']);
			var weight = Math.abs(data[0]['weight']);
			var unit_impact = Math.abs(data[0]['unit_price_impact']);
			var reference = data[0]['reference'];
			var ean = data[0]['ean13'];
			var quantity = data[0]['quantity'];
			var image = false;
			var product_att_list = new Array();
			for(i=0;i<data.length;i++)
			{
				product_att_list.push(data[i]['group_name']+' : '+data[i]['attribute_name']);
				product_att_list.push(data[i]['id_attribute']);
			}

			var id_product_attribute = data[0]['id_product_attribute'];
			var default_attribute = data[0]['default_on'];
			var eco_tax = data[0]['ecotax'];
			var upc = data[0]['upc'];
			var minimal_quantity = data[0]['minimal_quantity'];
			var available_date = data[0]['available_date'];
			var virtual_product_name_attribute = data[0]['display_filename'];
			var virtual_product_filename_attribute = data[0]['display_filename'];
			var virtual_product_nb_downloable = data[0]['nb_downloadable'];
			var virtual_product_expiration_date_attribute = data[0]['date_expiration'];
			var virtual_product_nb_days = data[0]['nb_days_accessible'];
			var is_shareable = data[0]['is_shareable'];
			if (wholesale_price != 0 && wholesale_price > 0)
			{
				$("#attribute_wholesale_price_full").show();
				$("#attribute_wholesale_price_blank").hide();
			}
			else
			{
				$("#attribute_wholesale_price_full").hide();
				$("#attribute_wholesale_price_blank").show();
			}
			fillCombination(
				wholesale_price,
				price,
				weight,
				unit_impact,
				reference,
				ean,
				quantity,
				image,
				product_att_list,
				id_product_attribute,
				default_attribute,
				eco_tax,
				upc,
				minimal_quantity,
				available_date,
				virtual_product_name_attribute,
				virtual_product_filename_attribute,
				virtual_product_nb_downloable,
				virtual_product_expiration_date_attribute,
				virtual_product_nb_days,
				is_shareable
			);
			calcImpactPriceTI();
		}
	});
}
/* END Combination */

/**
 * Get a single tab or recursively get tabs in stack then display them
 *
 * @param int id position of the tab in the product page
 * @param boolean selected is the tab selected
 * @param int index current index in the stack (or 0)
 * @param array stack list of tab ids to load (or null)
 */
function displayTabProductById(id, selected, index, stack)
{
	var myurl = $('#link-'+id).attr("href")+"&ajax=1";
	var tab_selector = $("#product-tab-content-"+id);
	// Used to check if the tab is already in the process of being loaded
	tab_selector.addClass('loading');

	if (selected)
		$('#product-tab-content-wait').show();

	$.ajax({
		url : myurl,
		async : true,
		cache: false, // cache needs to be set to false or IE will cache the page with outdated product values
		type: 'POST',
		success : function(data)
		{
			tab_selector.html(data);
			tab_selector.removeClass('not-loaded');

			if (selected)
			{
				$("#link-"+id).addClass('selected');
				tab_selector.show();
			}
		},
		complete : function(data)
		{
			$("#product-tab-content-"+id).removeClass('loading');
			if (selected)
			{
				$('#product-tab-content-wait').hide();
				tab_selector.trigger('displayed');
			}
			tab_selector.trigger('loaded');
			if (stack && stack[index + 1])
				displayTabProductById(stack[index + 1], selected, index + 1, stack);
		},
		beforeSend : function(data)
		{
			// don't display the loading notification bar
			if (typeof(ajax_running_timeout) !== 'undefined')
				clearTimeout(ajax_running_timeout);
		}
	});
}

/**
 * Update the manufacturer select element with the list of existing manufacturers
 */
function getManufacturers()
{
	$.ajax({
			url: 'ajax-tab.php',
			cache: false,
			dataType: 'json',
			data: {
				ajaxProductManufacturers:"1",
				ajax : '1',
				token : token,
				controller : 'AdminProducts',
				action : 'productManufacturers'
			},
			success: function(j) {
				var options = $('select#id_manufacturer').html();
				if (j)
				for (var i = 0; i < j.length; i++)
					options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
				$("select#id_manufacturer").html(options);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
			}
	});
}

/**
 * hide save and save-and-stay buttons
 * 
 * @access public
 * @return void
 */
function disableSave()
{
		$('#desc-product-save').hide();
		$('#desc-product-save-and-stay').hide();
}

/**
 * show save and save-and-stay buttons
 *
 * @access public
 * @return void
 */
function enableSave()
{
		// if no item left in the pack, disable save buttons
		if ($("#disablePackMessage").length)
			$("#disablePackMessage").remove();

		$('#desc-product-save').show();
		$('#desc-product-save-and-stay').show();
}

function handleSaveForPack()
{
	// if no item left in the pack, disable save buttons
	$("#disablePackMessage").remove();
	if ($("#inputPackItems").val() == "")
	{
		disableSave();
		$(".leadin").append('<div id="disablePackMessage" class="warn">' + empty_pack_msg + '</div>');
	}
	else
		enableSave();
}

function enableProductName()
{
	$('.copy2friendlyUrl').removeAttr('disabled');
}

function toggleSpecificPrice()
{
	$('#show_specific_price').click(function()
	{
		$('#add_specific_price').slideToggle();

		$('#add_specific_price').append('<input type="hidden" name="submitPriceAddition"/>');

		$('#hide_specific_price').show();
		$('#show_specific_price').hide();
		return false;
	});

	$('#hide_specific_price').click(function()
	{
		$('#add_specific_price').slideToggle();
		$('#add_specific_price').find('input[name=submitPriceAddition]').remove();

		$('#hide_specific_price').hide();
		$('#show_specific_price').show();
		return false;
	});
}

/**
 * Ajax call to delete a specific price
 *
 * @param ids
 * @param token
 * @param parent
 */
function deleteSpecificPrice(url, parent)
{
	$.ajax({
		url: url,
		data: {
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.message);
				parent.remove();
			}
			else
				showErrorMessage(data.message);
		}
	});
}

/**
 * Execute a callback function when a specific tab has finished loading or right now if the tab is already loaded
 *
 * @param tab_name name of the tab that is checked for loading
 * @param callback_function function to call
 */
function onTabLoad(tab_name, callback_function)
{
	var target_tab = $('#product-tab-content-' + tab_name);
	if (!target_tab)
		return false;
	if (target_tab.hasClass('not-loaded'))
		target_tab.bind('loaded', callback_function);
	else
		callback_function();
}

/* function autocomplete */
urlToCall = null;

$(document).ready(function() {
	updateCurrentText();
	updateFriendlyURL();

	// Pressing enter in an input field should not submit the form
	$('#product_form').delegate('input', 'keypress', function(e){
			var code = null;
		code = (e.keyCode ? e.keyCode : e.which);
		return (code == 13) ? false : true;
	});

	// Enable writing of the product name when the friendly url field in tab SEO is loaded
	onTabLoad('Seo', enableProductName);

	// Bind to show/hide new specific price form
	onTabLoad('Prices', toggleSpecificPrice);

	// Bind to delete specific price link
	onTabLoad('Prices', function(){
		$('#specific_prices_list').delegate('a[name="delete_link"]', 'click', function(){
			deleteSpecificPrice(this.href, $(this).parents('tr'));
			return false;
		})
	});

	// Bind action edition on attribute list
	onTabLoad('Combinations', function(){
		$('table[name=list_table]').delegate('a.edit', 'click', function(e){
			e.preventDefault();
			editProductAttribute(this.href, $(this).closest('tr'));
		});

		$('table[name=list_table]').delegate('a.delete', 'click', function(e){
			e.preventDefault();
			deleteProductAttribute(this.href, $(this).closest('tr'));
		});

		$('table[name=list_table]').delegate('a.default', 'click', function(e){
			e.preventDefault();
			defaultProductAttribute(this.href, $(this).closest('tr'));
		});
	});
});
