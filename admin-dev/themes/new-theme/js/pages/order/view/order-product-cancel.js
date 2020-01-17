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

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const $ = window.$;

/**
 * manages all product cancel actions, that includes all refund operations
 */
export default class OrderProductCancel {
  constructor() {
    this.router = new Router();
    this.cancelProductForm = $(OrderViewPageMap.cancelProductForm);
    this.orderId = this.cancelProductForm.data('orderId');
  }

  showPartialRefund() {
    $(OrderViewPageMap.toggleStandardRefundForm).hide();
    $(OrderViewPageMap.togglePartialRefundForm).show();
    $(OrderViewPageMap.actionColumnElements).hide();
    this.listenForInputs();
    console.log(this.orderId);
    this.updateForm(
      $(OrderViewPageMap.cancelProductSaveBtn).data('partialRefundLabel'),
      this.router.generate('admin_orders_partial_refund', {orderId: this.orderId})
    );
  }

  showStandardRefund() {
    $(OrderViewPageMap.toggleStandardRefundForm).show();
    $(OrderViewPageMap.togglePartialRefundForm).hide();
    $(OrderViewPageMap.actionColumnElements).hide();
    this.listenForInputs();
    this.updateForm(
      $(OrderViewPageMap.cancelProductSaveBtn).data('standardRefundLabel'),
      ''
    );
  }

  updateForm(actionName, formAction) {
    $(OrderViewPageMap.cancelProductForm).attr('action', formAction);
    $(OrderViewPageMap.cancelProductSaveBtn).html(actionName);
    $(OrderViewPageMap.cancelProductColumnHeader).html(actionName);
  }

  listenForInputs() {
    $(OrderViewPageMap.cancelProductQuantityInput).off('change').on('change', (event) => {
      const $productQuantityInput = $(event.target);
      const $parentCell = $productQuantityInput.parents(OrderViewPageMap.cancelProductTableCell);
      const $productAmount = $parentCell.find(OrderViewPageMap.cancelProductAmountInput);
      const productQuantity = parseInt($productQuantityInput.val(), 10);
      if (productQuantity === 0) {
        $productAmount.val(0);
        return;
      }
      const isTaxIncluded = parseInt($productQuantityInput.data('isTaxIncluded'), 10);
      const priceFieldName = isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
      const productUnitPrice = parseFloat($productQuantityInput.data(priceFieldName));
      const amountRefundable = parseFloat($productQuantityInput.data('amountRefundable'));
      const guessedAmount = productUnitPrice * productQuantity < amountRefundable ?
        productUnitPrice * productQuantity : amountRefundable;
      const amountValue = parseFloat($productAmount.val());
      if (amountValue === 0 || amountValue > guessedAmount) {
        $productAmount.val(guessedAmount);
      }
    });
  }
}
