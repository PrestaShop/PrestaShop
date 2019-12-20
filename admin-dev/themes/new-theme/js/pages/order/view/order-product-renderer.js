/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderProductEdit from '@pages/order/view/order-product-edit';
import Router from '@components/router';

const $ = window.$;

export default class OrderProductRenderer {
  constructor() {
    this.router = new Router();
  }

  addOrUpdateProductToList($productRow, newRow) {
    if ($productRow.length > 0) {
      $productRow.html($(newRow).html());
    } else {
      $(OrderViewPageMap.productAddRow).before($(newRow).hide().fadeIn());
    }
  }

  updateNumProducts(numProducts) {
    $(OrderViewPageMap.productsCount).html(numProducts);
  }

  editProductFromList(orderProductId, quantity, priceTaxIncl, priceTaxExcl, taxRate, location, availableQuantity) {
    const $orderEdit = new OrderProductEdit(orderProductId);
    $orderEdit.displayProduct({
      price_tax_excl: priceTaxExcl,
      price_tax_incl: priceTaxIncl,
      tax_rate: taxRate,
      quantity,
      location,
      availableQuantity
    });
    $(OrderViewPageMap.productAddActionBtn).addClass('d-none');
    $(OrderViewPageMap.productAddRow).addClass('d-none');
  }

  moveProductsPanelToModificationPosition() {
    const $modificationPosition = $(OrderViewPageMap.productModificationPosition);

    $(OrderViewPageMap.productsPanel).detach().appendTo($modificationPosition);

    $modificationPosition.closest('.row').removeClass('d-none');

    $(OrderViewPageMap.productActionBtn).addClass('d-none');
    $(`${OrderViewPageMap.productAddActionBtn}, ${OrderViewPageMap.productAddRow}`).removeClass('d-none');
    $('html,body').animate({scrollTop: 0}, 'slow');
  }

  moveProductPanelToOriginalPosition() {
    $(OrderViewPageMap.productModificationPosition).closest('.row').addClass('d-none');

    $(OrderViewPageMap.productsPanel).detach().appendTo(OrderViewPageMap.productOriginalPosition);

    $(OrderViewPageMap.productActionBtn).removeClass('d-none');
    $(`${OrderViewPageMap.productAddActionBtn}, ${OrderViewPageMap.productAddRow}`).addClass('d-none');
  }

  resetAddRow() {
    $(OrderViewPageMap.productAddIdInput).val('');
    $(OrderViewPageMap.productSearchInput).val('');
    $(OrderViewPageMap.productAddCombinationsSelect).val('');
    $(OrderViewPageMap.productAddPriceTaxExclInput).val('');
    $(OrderViewPageMap.productAddPriceTaxInclInput).val('');
    $(OrderViewPageMap.productAddQuantityInput).val('');
    $(OrderViewPageMap.productAddAvailableText).html('');
    $(OrderViewPageMap.productAddLocationText).html('');
    $(OrderViewPageMap.productAddActionBtn).prop('disabled', true);
  }

  resetEditRow(orderProductId) {
    const $productRow = $(OrderViewPageMap.productsTableRow(orderProductId));
    const $productEditRow = $(OrderViewPageMap.productsTableRowEdited(orderProductId));
    $productEditRow.remove();
    $productRow.removeClass('d-none');
  }

  paginate(orderId, numPage) {
    this.paginateUpdateControls(numPage);
    // Hide displayed rows...
    $(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]:not(.d-none)').addClass('d-none');
    // ... and display good ones
    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
    const numRowsPerPage = parseInt($tablePagination.data('numPerPage'), 10);
    const startRow = ((numPage - 1) * numRowsPerPage) + 1;
    const endRow = numPage * numRowsPerPage;
    $(OrderViewPageMap.productsTable).find(`tr[id^="orderProduct_"]:nth-child(n+${startRow}):nth-child(-n+${endRow})`)
        .removeClass('d-none');
  }

  paginateUpdateControls(numPage) {
    // Why 3 ? Next & Prev & Template
    const totalPage = $(OrderViewPageMap.productsTablePagination).find('li.page-item').length - 3;
    $(OrderViewPageMap.productsTablePagination).find('.active').removeClass('active');
    $(OrderViewPageMap.productsTablePagination).find(`li:has(> [data-page="${numPage}"])`).addClass('active');
    $(OrderViewPageMap.productsTablePaginationPrev).removeClass('disabled');
    if (numPage === 1) {
      $(OrderViewPageMap.productsTablePaginationPrev).addClass('disabled');
    }
    $(OrderViewPageMap.productsTablePaginationNext).removeClass('disabled');
    if (numPage === totalPage) {
      $(OrderViewPageMap.productsTablePaginationNext).addClass('disabled');
    }
  }

  paginationAddPage(numPage) {
    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
    $tablePagination.data('numPages', numPage);
    const $linkPagination = $(OrderViewPageMap.productsTablePaginationTemplate).clone();
    $linkPagination.find('span').attr('data-page', numPage);
    $linkPagination.find('span').html(numPage);
    $(OrderViewPageMap.productsTablePaginationTemplate).before($linkPagination.removeClass('d-none'));
  }

  paginationRemovePage(numPage) {
    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
    const numPages = $tablePagination.data('numPages');
    $tablePagination.data('numPages', numPages - 1);
    $(OrderViewPageMap.productsTablePagination).find(`li:has(> [data-page="${numPage}"])`).remove();
  }
}
