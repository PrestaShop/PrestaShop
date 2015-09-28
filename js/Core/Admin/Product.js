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
			$('form#product_catalog_list input[name="ls_products_filter_category"]').val($(this).val());
			$('form#product_catalog_list').submit();
		}
	});

	/*
	 * Click on a column header ordering icon to change orderBy / orderWay (location.href redirection)
	 */
	$('[psorderby][psorderway]', form).click(function() {
		var orderBy = $(this).attr('psorderby');
		var orderWay = $(this).attr('psorderway');
		var url = form.attr('orderingurl').replace(/name/, orderBy).replace(/desc/, orderWay);
		window.location.href = url;
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
	
	updateBulkMenu();
	updateFilterMenu();
});

function updateBulkMenu() {
	var selectedCount = $('form#product_catalog_list input:checked[name="bulk_action_selected_products[]"][disabled!="disabled"]').size();
	$('#product_bulk_menu').prop('disabled', (selectedCount == 0));
}

function updateFilterMenu() {
	var count = $('form#product_catalog_list tr.column-filters select option:selected[value!=""]').size();
	$('form#product_catalog_list tr.column-filters input[type="text"]').each(function() {
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
	$('form#product_catalog_list input[name="ls_products_filter_category"]').val('');
	$('form#product_catalog_list').submit();
}

function productColumnFilterReset(tr) {
	$('input:text', tr).val('');
	$('select option:selected', tr).prop("selected", false);
	$('form#product_catalog_list').submit();
}

function bulkProductAction(element, action) {
	var form = $('form#product_catalog_list');
	if ($('input:checked[name="bulk_action_selected_products[]"]', form).size() == 0) {
		return false;
	}

	// save action URL for redirection and update to post to bulk action instead
	// using form action URL allow to get route attributes and stay on the same page & ordering.
	var urlHandler = $(element).closest('[bulkurl]');
	var redirectionInput = $('<input>')
		.attr('type', 'hidden')
		.attr('name', 'redirect_url').val(urlHandler.attr('redirecturl'));
	form.append($(redirectionInput));
	var url = urlHandler.attr('bulkurl').replace(/activate_all/, action);
	form.attr('action', url);
	form.submit();
}

function bulkProductEdition(element, action) {
	var form = $('form#product_catalog_list');
	
	switch (action) {
	case 'quantity_edition':
		$('#bulk_edition_toolbar').show();
		$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', true);
		// TODO: boites de saisie!
		break;
	case 'cancel':
		$('#bulk_edition_toolbar').hide();
		$('input#bulk_action_select_all, input:checkbox[name="bulk_action_selected_products[]"]', form).prop('disabled', false);
		break;
	}
}
