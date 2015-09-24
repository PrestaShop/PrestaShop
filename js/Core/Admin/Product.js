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
	// click on a radio button in the categories tree filter
	$('div#product_catalog_category_tree_filter div.radio > label > input:radio').change(function() {
		if ($(this).is(':checked')) {
			$('form[name="product_catalog_list"] input[name="ls_products_filter_category"]').val($(this).val());
			$('form[name="product_catalog_list"]').submit();
		}
	});
});

function productCategoryFilterReset(div) {
	$('div#product_catalog_category_tree_filter div.radio > label > input:radio').prop('checked', false);
	$('form[name="product_catalog_list"] input[name="ls_products_filter_category"]').val('');
	$('form[name="product_catalog_list"]').submit();
}

function productColumnFilterReset(tr) {
	$('input:text', tr).val('');
	$('select option:selected', tr).prop("selected", false);
	$('form[name="product_catalog_list"]').submit();
}

function testBulkAction1(form) {
	if ($('input:checked[name="bulk_action_selected_products[]"]', form).size() == 0) {
		return false;
	}
	// TODO
	console.log(form.serialize());
}
