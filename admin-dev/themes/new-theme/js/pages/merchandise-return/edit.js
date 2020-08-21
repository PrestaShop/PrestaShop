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

$(() => {
  listenForProductDelete();
  listenForProductPagination();
  const merchandiseReturnProductRenderer = new MerchandiseReturnProductRenderer();

  function listenForProductDelete() {
    $(MerchandiseReturnEditPageMap.productDeleteBtn)
      .off('click')
      .on('click', (event) => handleDeleteProductEvent(event));
  }

  function listenForProductPagination() {
    $(MerchandiseReturnEditPageMap.productsTablePagination).on('click', MerchandiseReturnEditPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        orderId: $btn.data('merchadiseReturnId'),
        numPage: $btn.data('page'),
      });
    });
    $(MerchandiseReturnEditPageMap.productsTablePaginationNext).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = getActivePage();
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        orderId: $(activePage).data('orderId'),
        numPage: parseInt($(activePage).html(), 10) + 1,
      });
    });
    $(MerchandiseReturnEditPageMap.productsTablePaginationPrev).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = getActivePage();
      EventEmitter.emit(MerchandiseReturnEditPageMap.productListPaginated, {
        orderId: $(activePage).data('orderId'),
        numPage: parseInt($(activePage).html(), 10) - 1,
      });
    });

    EventEmitter.on(MerchandiseReturnEditPageMap.productListPaginated, (event) => {
      merchandiseReturnProductRenderer.paginate(event.numPage);
      listenForProductDelete();
    });
  }

  function handleDeleteProductEvent(event) {
    event.preventDefault();

    const $btn = $(event.currentTarget);
    const confirmed = window.confirm($btn.data('deleteMessage'));
    if (!confirmed) {
      return;
    }

    $btn.pstooltip('dispose');
    $btn.prop('disabled', true);
    deleteProduct($btn.data('merchandiseReturnId'), $btn.data('merchandiseReturnDetailId'));
  }

  function deleteProduct(merchandiseReturnId, merchandiseReturnDetailId) {
    const router = new Router();
    $.ajax(router.generate('admin_merchandise_returns_delete_product', {merchandiseReturnId, merchandiseReturnDetailId}), {
      method: 'POST',
    }).then(() => {
      EventEmitter.emit(MerchandiseReturnEditPageMap.productDeletedFromMerchandiseReturn, {
        oldOrderDetailId: merchandiseReturnDetailId,
        merchandiseReturnId,
      });
    }, (response) => {
      if (response.message) {
        $.growl.error({message: response.message});
      }
    });
  }

  function getActivePage() {
    return $(MerchandiseReturnEditPageMap.productsTablePagination).find('.active span').get(0);
  }
});
