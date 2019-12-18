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

import OrderViewPageMap from '../OrderViewPageMap';
import OrderProductEdit from "./order-product-edit";
import Router from "../../../components/router";

const $ = window.$;

export default class OrderProductRenderer {
  constructor() {
    this.router = new Router();
  }

  addOrUpdateProductToList(orderProductId, newRow) {
    const $productRow = $(OrderViewPageMap.productsTableRow(orderProductId));
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
    $(OrderViewPageMap.productAddActionBtn).removeClass('d-none');
    $(OrderViewPageMap.productAddRow).removeClass('d-none');
    $('html,body').animate({scrollTop: 0}, 'slow');
  }

  moveProductPanelToOriginalPosition() {
    $(OrderViewPageMap.productModificationPosition).closest('.row').addClass('d-none');

    $(OrderViewPageMap.productsPanel).detach().appendTo(OrderViewPageMap.productOriginalPosition);

    $(OrderViewPageMap.productActionBtn).removeClass('d-none');
    $(OrderViewPageMap.productAddActionBtn).addClass('d-none');
    $(OrderViewPageMap.productAddRow).addClass('d-none');
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
    $(OrderViewPageMap.productAddActionBtn).attr('disabled', 'disabled');
  }

  resetEditRow(orderProductId) {
    const $productRow = $(OrderViewPageMap.productsTableRow(orderProductId));
    const $productEditRow = $(OrderViewPageMap.productsTableRowEdited(orderProductId));
    $productEditRow.remove();
    $productRow.removeClass('d-none');
  }

  paginate(orderId, numPage, results) {
    this.paginateUpdateControls(numPage);
    // Remove all rows...
    $(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]').remove();
    // ... and recreate them
    results.products.forEach(result => this.paginateRowCreate(result, orderId));
  }

  paginateUpdateControls(numPage) {
    const totalPage = $(OrderViewPageMap.productsTablePagination).find('li.page-item').length - 2;
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

  paginateRowCreate(result, orderId) {
    const $productRow = $(OrderViewPageMap.productTemplateRow).clone();
    $productRow.attr('id', `orderProduct_${result.orderDetailId}`);
    if (result.imagePath) {
      $productRow.find('td.cellProductImg img').attr('src', result.imagePath);
      $productRow.find('td.cellProductImg img').attr('alt', result.name);
    } else {
      $productRow.find('td.cellProductImg img').remove();
    }
    $productRow.find('td.cellProductName a').attr('href', this.router.generate('admin_product_form', {orderId: result.id}));
    $productRow.find('td.cellProductName p.productName').html(result.name);
    if (result.supplierReference) {
      $productRow.find('td.cellProductName p.productSupplierReference').append(result.supplierReference);
    } else {
      $productRow.find('td.cellProductName p.productSupplierReference').remove();
    }
    if (result.reference) {
      $productRow.find('td.cellProductName p.productReference').append(result.reference);
    } else {
      $productRow.find('td.cellProductName p.productReference').remove();
    }
    $productRow.find('td.cellProductUnitPrice').html(result.unitPrice);
    if (result.quantity > 1) {
      $productRow.find('td.cellProductQuantity span.badge').html(result.quantity);
    } else {
      $productRow.find('td.cellProductQuantity span.badge').replaceWith(result.quantity);
    }
    $productRow.find('td.cellProductQuantity input').val(result.quantity);
    $productRow.find('td.cellProductLocation').html(result.location);
    $productRow.find('td.cellProductAvailableQuantity').html(result.availableQuantity);
    $productRow.find('td.cellProductTotalPrice').html(result.totalPrice);
    if (!result.delivered) {
      $productRow.find('td.cellProductActions .js-order-product-edit-btn')
        .data('orderDetailId', result.orderDetailId)
        .data('productQuantity', result.quantity)
        .data('productPriceTaxIncl', result.unitPriceTaxInclRaw)
        .data('productPriceTaxExcl', result.unitPriceTaxExclRaw)
        .data('taxRate', result.taxRate)
        .data('location', result.location)
        .data('availableQuantity', result.availableQuantity);
      $productRow.find('td.cellProductActions .js-order-product-delete-btn')
        .data('orderId', orderId)
        .data('orderDetailId', result.orderDetailId);
    } else {
      $productRow.find('td.cellProductActions').remove();
    }
    $(OrderViewPageMap.productAddRow).before($productRow.removeClass('d-none'));
  }
}
