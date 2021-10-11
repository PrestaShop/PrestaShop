/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

// This had to be commented because of TS2451: Cannot redeclare block-scoped variable '$'.
// But in other index.ts there is no such issue
// const {$} = window;
import categoryTree from '@pages/product/components/category-tree-search';

import CreateProductModal from '@pages/product/components/create-product-modal';

$(() => {
  const grid = new window.prestashop.component.Grid('product');

  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  new CreateProductModal();
  grid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(grid));

  /*
   * Tree behavior: collapse/expand system and radio button change event.
   */
  categoryTree('div#product_catalog_category_tree_filter');

  $('#product_catalog_category_tree_filter_reset').on('click', function () {
    categoryTree('#product_categories', 'unselect');
    $('form#product_filter_form input[name="product[id_category]"]').val('');
    $('form#product_filter_form').submit();
  })

  $('#product_catalog_category_tree_filter_expand').on('click', function () {
    categoryTree('#product_categories', 'unfold')
  })

  $('#product_catalog_category_tree_filter_collapse').on('click', function () {
    categoryTree('#product_categories', 'fold');
  })

  $('div#product_catalog_category_tree_filter div.radio > label > input:radio').on('change',function () {
    if ($(this).is(':checked')) {
      // @ts-ignore
      let categoryId = $(this).val().toString();
      $('form#product_filter_form input[name="product[id_category]"]').val(categoryId);
      $('form#product_filter_form').submit();
    }
  });

});
