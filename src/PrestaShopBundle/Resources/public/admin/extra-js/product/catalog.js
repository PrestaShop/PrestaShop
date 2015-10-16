/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {
	var form = $('form#product_catalog_list');

	/*
	 * Click on a radio button in the categories tree filter (through radio.change() event)
	 */
	$('div#product_catalog_category_tree_filter div.radio > label > input:radio').change(function() {
		if ($(this).is(':checked')) {
			$('form#product_catalog_list input[name="filter_category"]').val($(this).val());
			$('form#product_catalog_list').submit();
		}
	});

	/*
	 * Click on a column header ordering icon to change orderBy / orderWay (location.href redirection)
	 */
	$('[psorderby][psorderway]', form).click(function() {
		var orderBy = $(this).attr('psorderby');
		var orderWay = $(this).attr('psorderway');
		productOrderTable(orderBy, orderWay);
	});

	/*
	 * Checkboxes behavior with bulk actions
	 */
	$('input:checkbox[name="bulk_action_selected_products[]"]', form).change(function() {
		updateBulkMenu();
	});

	/*
	 * Filter columns buttons behavior
	 */
	$('tr.column-filters input:text, tr.column-filters select', form).change(function() {
		updateFilterMenu();
	});

	/*
	 * Form submit pre action
	 */
	form.submit(function(e) {
	    e.preventDefault();
	    $('#filter_column_price', form).val($('#filter_column_price', form).attr('sql'));
	    $('#filter_column_sav_quantity', form).val($('#filter_column_sav_quantity', form).attr('sql'));
	    this.submit();
	    return false;
	});

	updateBulkMenu();
	updateFilterMenu();
});

function productOrderTable(orderBy, orderWay) {
	var form = $('form#product_catalog_list');
	var url = form.attr('orderingurl').replace(/name/, orderBy).replace(/desc/, orderWay);
	window.location.href = url;
}

function updateBulkMenu() {
	var selectedCount = $('form#product_catalog_list input:checked[name="bulk_action_selected_products[]"][disabled!="disabled"]').size();
	$('#product_bulk_menu').prop('disabled', (selectedCount == 0));
}

function updateFilterMenu() {
	var count = $('form#product_catalog_list tr.column-filters select option:selected[value!=""]').size();
	$('form#product_catalog_list tr.column-filters input[type="text"]:visible').each(function() {
		if ($(this).val()!="") count ++;
	});
	$('form#product_catalog_list tr.column-filters input[type="text"][sql!=""][sql]').each(function() {
		if ($(this).val()!="") count ++;
	});
	$('input[name="products_filter_submit"]').prop('disabled', (count == 0));
	if (count == 0)
		$('input[name="products_filter_reset"]').hide();
	else
		$('input[name="products_filter_reset"]').show();
}

function productCategoryFilterReset(div) {
	$('div#product_catalog_category_tree_filter div.radio > label > input:radio').prop('checked', false);
	$('form#product_catalog_list input[name="filter_category"]').val('');
	$('form#product_catalog_list').submit();
}

function productColumnFilterReset(tr) {
	$('input:text', tr).val('');
	$('select option:selected', tr).prop("selected", false);
	$('input#filter_column_price', tr).bootstrapSlider('setValue', [
	    $('input#filter_column_price', tr).bootstrapSlider('getAttribute', 'min'),
	    $('input#filter_column_price', tr).bootstrapSlider('getAttribute', 'max')
	]);
	$('form#product_catalog_list').submit();
}

function bulkProductAction(element, action) {
	var form = $('form#product_catalog_list');
	var postUrl = '';
	var redirectUrl = '';

	switch (action) {
		// these cases needs checkboxes to be checked.
		case 'activate_all':
		case 'deactivate_all':
		case 'delete_all':
			if ($('input:checked[name="bulk_action_selected_products[]"]', form).size() == 0) {
				return false;
			}
			var urlHandler = $(element).closest('[bulkurl]');
			postUrl = urlHandler.attr('bulkurl').replace(/activate_all/, action);
			redirectUrl = urlHandler.attr('redirecturl');
			break;
		// this case will brings to the next page
		case 'edition_next':
			alert('+1 page !');
			// TODO !2: add 1 page at offset for redirection (go to next page in redirecturl)
		// this case will post inline edition command
		case 'edition':
			var editionAction = $('#bulk_edition_toolbar input:submit').attr('editionaction');
			alert(editionAction);
			// TODO !2: specific work here: submit form with another URL (different than bulkurl...
			break;
		// unknown cases...
		default:
			return false;
	}

	if (postUrl != '' && redirectUrl != '') {
		// save action URL for redirection and update to post to bulk action instead
		// using form action URL allow to get route attributes and stay on the same page & ordering.
		var redirectionInput = $('<input>')
			.attr('type', 'hidden')
			.attr('name', 'redirect_url').val(redirectUrl);
		form.append($(redirectionInput));
		form.attr('action', postUrl);
		form.submit();
	}
	return false;
}

function unitProductAction(element, action) {
	var form = $('form#product_catalog_list');
	
	// save action URL for redirection and update to post to bulk action instead
	// using form action URL allow to get route attributes and stay on the same page & ordering.
	var urlHandler = $(element).closest('[uniturl]');
	var redirectUrlHandler = $(element).closest('[redirecturl]');
	var redirectionInput = $('<input>')
		.attr('type', 'hidden')
		.attr('name', 'redirect_url').val(redirectUrlHandler.attr('redirecturl'));
	form.append($(redirectionInput));
	var url = urlHandler.attr('uniturl').replace(/duplicate/, action);
	form.attr('action', url);
	form.submit();
}

function bulkProductEdition(element, action) {
	var form = $('form#product_catalog_list');
	
	switch (action) {
		case 'quantity_edition':
			$('#bulk_edition_toolbar').show();
			$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', true);

			i = 1;
			$('td.product-sav-quantity', form).each(function() {
				$quantity = $(this).attr('productquantityvalue');
				$product_id = $(this).closest('tr[productid]').attr('productid');
				$input = $('<input>').attr('type', 'text').attr('name', 'bulk_action_edit_quantity['+$product_id+']')
					.attr('tabindex', i++)
					.attr('onkeydown', 'if (event.keyCode == 13) return bulkProductAction(this, "edition_next"); if (event.keyCode == 27) return bulkProductEdition(this, "cancel");')
					.val($quantity);
				$(this).html($input);
				
			});
			$('#bulk_edition_toolbar input:submit').attr('tabindex', i++);
			$('#bulk_edition_toolbar input:button').attr('tabindex', i++);
			$('#bulk_edition_toolbar input:submit').attr('editionaction', action);

			$('td.product-sav-quantity input', form).first().focus();
			break;
		case 'cancel':
			// quantity inputs
			$('td.product-sav-quantity', form).each(function() {
				$(this).html($(this).attr('productquantityvalue'));
			});

			$('#bulk_edition_toolbar input:submit').removeAttr('editionaction');
			$('#bulk_edition_toolbar').hide();
			$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', false);
			break;
	}
}
