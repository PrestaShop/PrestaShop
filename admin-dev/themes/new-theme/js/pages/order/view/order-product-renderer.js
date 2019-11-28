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

  removeProductFromList(orderDetailId) {
    const $productRow = $(`#orderProduct_${orderDetailId}`);
    $productRow.hide('fast', () => $productRow.remove());

    const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);
    $(OrderViewPageMap.productsCount).html(numProducts - 1);
  }

  addOrUpdateProductFromToList(orderProductId, newRow) {
    const $productRow = $(`#orderProduct_${orderProductId}`);
    if ($productRow.length > 0) {
      $productRow.html($(newRow).html());
    } else {
      $(OrderViewPageMap.productAddRow).before($(newRow).hide().fadeIn());
    }

    const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);
    $(OrderViewPageMap.productsCount).html(numProducts + 1);
  }

  editProductFromToList(orderProductId, quantity, priceTaxIncl, priceTaxExcl, taxRate) {
    const $orderEdit = new OrderProductEdit(orderProductId);
    $orderEdit.displayProduct({
      price_tax_excl: priceTaxExcl,
      price_tax_incl: priceTaxIncl,
      tax_rate: taxRate,
      quantity
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
    $(OrderViewPageMap.productSearchInput).val('');
    $(OrderViewPageMap.productAddCombinationsSelect).val('');
    $(OrderViewPageMap.productAddPriceTaxExclInput).val('');
    $(OrderViewPageMap.productAddPriceTaxInclInput).val('');
    $(OrderViewPageMap.productAddQuantityInput).val('');
    $(OrderViewPageMap.productAddAvailableText).html('');
  }

  resetEditRow(orderProductId) {
    const $productRow = $(`#orderProduct_${orderProductId}`);
    const $productEditRow = $(`#orderProduct_${orderProductId}_edit`);
    $productEditRow.remove();
    $productRow.removeClass('d-none');
  }

  paginate(orderId, numPage, results) {
    const totalPage = $(OrderViewPageMap.productsTablePagination).find('li.page-item').length - 2;
    $(OrderViewPageMap.productsTablePagination).find('.active').removeClass('active');
    $(OrderViewPageMap.productsTablePagination).find(`li:has(> [data-page="${numPage}"])`).addClass('active');
    $(OrderViewPageMap.productsTablePaginationPrev).removeClass('disabled');
    if (numPage == 1) {
      $(OrderViewPageMap.productsTablePaginationPrev).addClass('disabled');
    }
    $(OrderViewPageMap.productsTablePaginationNext).removeClass('disabled');
    if (numPage == totalPage) {
      $(OrderViewPageMap.productsTablePaginationNext).addClass('disabled');
    }
    $(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]').remove();
    results.products.forEach(result => {
      const $productRow = $(OrderViewPageMap.productTemplateRow).clone();
      $productRow.attr('id', `orderProduct_${result.orderDetailId}`);
      // Cell 1
      if (result.imagePath) {
        $productRow.find('td:nth-child(1) img').attr('src', result.imagePath);
        $productRow.find('td:nth-child(1) img').attr('alt', result.name);
      } else {
        $productRow.find('td:nth-child(1) img').remove();
      }
      // Cell 2
      $productRow.find('td:nth-child(2) a').attr('href', this.router.generate('admin_product_form', {orderId: result.id}));
      $productRow.find('td:nth-child(2) p:nth-child(1)').html(result.name);
      if (result.supplierReference) {
        $productRow.find('td:nth-child(2) p:nth-child(3)').append(result.supplierReference);
      } else {
        $productRow.find('td:nth-child(2) p:nth-child(3)').remove();
      }
      if (result.reference) {
        $productRow.find('td:nth-child(2) p:nth-child(2)').append(result.reference);
      } else {
        $productRow.find('td:nth-child(2) p:nth-child(2)').remove();
      }
      // Cell 3
      $productRow.find('td:nth-child(3)').html(result.unitPrice);
      // Cell 4
      if (result.quantity > 1) {
        $productRow.find('td:nth-child(4) span.badge').html(result.quantity);
      } else {
        $productRow.find('td:nth-child(4) span.badge').replaceWith(result.quantity);
      }
      $productRow.find('td:nth-child(4) input').val(result.quantity);
      // Cell 5
      $productRow.find('td:nth-child(5)').html(result.location);
      // Cell 6
      $productRow.find('td:nth-child(6)').html(result.availableQuantity);
      // Cell 7
      $productRow.find('td:nth-child(7)').html(result.totalPrice);
      // Cell 8
      if (!result.delivered) {
        $productRow.find('td:nth-child(8) a:nth-child(1) i')
          .attr('data-order-detail-id', result.orderDetailId)
          .attr('data-product-quantity', result.quantity)
          .attr('data-product-price-tax-incl', result.unitPriceTaxInclRaw)
          .attr('data-product-price-tax-excl', result.unitPriceTaxExclRaw)
          .attr('data-tax-rate', result.taxRate);
        $productRow.find('td:nth-child(8) a:nth-child(2)')
          .attr('data-order-id', orderId)
          .attr('data-order-detail-id', result.orderDetailId);
      } else {
        $productRow.find('td:nth-child(8)').remove();
      }
      $(OrderViewPageMap.productAddRow).before($productRow.removeClass('d-none'));
    });
  }
}
