/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import MerchandiseReturnEditPageMap from '@pages/merchandise-return/MerchandiseReturnEditPageMap';
import {EventEmitter} from '@components/event-emitter';
import Router from '@components/router';
import MerchandiseReturnProductRenderer from '@pages/merchandise-return/merchandise-return-product-renderer';

const {$} = window;

export default class MerchandiseReturnViewPage {
  constructor() {
    this.merchandiseReturnProductRenderer = new MerchandiseReturnProductRenderer();
    this.listenToEvents();
  }

  listenToEvents() {
    EventEmitter.on(MerchandiseReturnEditPageMap.productDeletedFromMerchandiseReturn, (event) => {
      // Remove the row
      const $row = $(MerchandiseReturnEditPageMap.productsTableRow(event.oldMerchandiseReturnDetailId));
      const $next = $row.next();
      $row.remove();
      if ($next.hasClass('merchandise-return-product-customization')) {
        $next.remove();
      }

      const $tablePagination = $(MerchandiseReturnEditPageMap.productsTablePagination);
      const numPages = $tablePagination.data('numPages');
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const numRows = $(MerchandiseReturnEditPageMap.productsTable).find('tr[id^="merchandiseReturnProduct_"]:not(.d-none)').length;
      let currentPage = parseInt($(MerchandiseReturnEditPageMap.productsTablePaginationActive).html(), 10);
      const numProducts = parseInt($(MerchandiseReturnEditPageMap.productsCount).html(), 10);
      if ((numProducts - 1) % numRowsPerPage === 0) {
        this.merchandiseReturnProductRenderer.paginationRemovePage(numPages);
      }
      if (numRows === 1 && currentPage === numPages) {
        currentPage -= 1;
      }
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        merchandiseReturnId: event.merchandiseReturnId,
        numPage: currentPage,
      });

      this.merchandiseReturnProductRenderer.updateNumProducts(numProducts - 1);
    });
  }

  listenForProductDelete() {
    $(MerchandiseReturnEditPageMap.productDeleteBtn)
      .off('click')
      .on('click', (event) => this.handleDeleteProductEvent(event));
  }

  listenForProductPagination() {
    $(MerchandiseReturnEditPageMap.productsTablePagination).on('click', MerchandiseReturnEditPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        merchadiseReturnId: $btn.data('merchadiseReturnId'),
        numPage: $btn.data('page'),
      });
    });

    $(MerchandiseReturnEditPageMap.productsTablePaginationNext).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = MerchandiseReturnViewPage.getActivePage();
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        merchadiseReturnId: $(activePage).data('merchadiseReturnId'),
        numPage: parseInt($(activePage).html(), 10) + 1,
      });
    });

    $(MerchandiseReturnEditPageMap.productsTablePaginationPrev).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = MerchandiseReturnViewPage.getActivePage();
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        merchadiseReturnId: $(activePage).data('merchadiseReturnId'),
        numPage: parseInt($(activePage).html(), 10) - 1,
      });
    });

    EventEmitter.on(MerchandiseReturnEditPageMap.productListPaginated, (event) => {
      this.merchandiseReturnProductRenderer.paginate(event.numPage);
      this.listenForProductDelete();
    });
  }

  handleDeleteProductEvent(event) {
    event.preventDefault();

    const $btn = $(event.currentTarget);
    const confirmed = window.confirm($btn.data('deleteMessage'));
    if (!confirmed) {
      return;
    }

    $btn.pstooltip('dispose');
    $btn.prop('disabled', true);
    this.deleteProduct($btn.data('merchandiseReturnId'), $btn.data('merchandiseReturnDetailId'));
  }

  deleteProduct(merchandiseReturnId, merchandiseReturnDetailId) {
    const router = new Router();
    $.ajax(router.generate('admin_merchandise_returns_delete_product', {merchandiseReturnId, merchandiseReturnDetailId}), {
      method: 'POST',
    }).then(() => {
      EventEmitter.emit(MerchandiseReturnEditPageMap.productDeletedFromMerchandiseReturn, {
        oldMerchandiseReturnDetailId: merchandiseReturnDetailId,
        merchandiseReturnId,
      });
    }, (response) => {
      if (response.message) {
        $.growl.error({message: response.message});
      }
    });
  }

  getActivePage() {
    return $(MerchandiseReturnEditPageMap.productsTablePagination).find('.active span').get(0);
  }
}
