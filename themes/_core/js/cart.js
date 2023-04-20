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
import $ from 'jquery';
import prestashop from 'prestashop';
import {refreshCheckoutPage} from './common';

$(() => {
  prestashop.on('updateCart', (event) => {
    prestashop.cart = event.resp.cart;
    const getCartViewUrl = $('.js-cart').data('refresh-url');

    if (!getCartViewUrl) {
      return;
    }

    let requestData = {};

    if (event && event.reason) {
      requestData = {
        id_product_attribute: event.reason.idProductAttribute,
        id_product: event.reason.idProduct,
      };
    }

    $.post(getCartViewUrl, requestData)
      .then((resp) => {
        $(prestashop.selectors.cart.detailedTotals).replaceWith(
          resp.cart_detailed_totals,
        );
        $(prestashop.selectors.cart.summaryItemsSubtotal).replaceWith(
          resp.cart_summary_items_subtotal,
        );
        $(prestashop.selectors.cart.summarySubTotalsContainer).replaceWith(
          resp.cart_summary_subtotals_container,
        );
        $(prestashop.selectors.cart.summaryProducts).replaceWith(
          resp.cart_summary_products,
        );
        $(prestashop.selectors.cart.summaryTotals).replaceWith(
          resp.cart_summary_totals,
        );
        $(prestashop.selectors.cart.detailedActions).replaceWith(
          resp.cart_detailed_actions,
        );
        $(prestashop.selectors.cart.voucher).replaceWith(resp.cart_voucher);
        $(prestashop.selectors.cart.overview).replaceWith(resp.cart_detailed);
        $(prestashop.selectors.cart.summaryTop).replaceWith(
          resp.cart_summary_top,
        );

        $(prestashop.selectors.cart.productCustomizationId).val(0);

        $(prestashop.selectors.cart.lineProductQuantity).each(
          (index, input) => {
            const $input = $(input);
            $input.attr('value', $input.val());
          },
        );

        if ($(prestashop.selectors.checkout.cartPaymentStepRefresh).length) {
          // we get the refresh flag : on payment step we need to refresh page to be sure
          // amount is correctly updated on payment modules
          refreshCheckoutPage();
        }

        prestashop.emit('updatedCart', {eventType: 'updateCart', resp});
      })
      .fail((resp) => {
        prestashop.emit('handleError', {eventType: 'updateCart', resp});
      });
  });

  const $body = $('body');

  $body.on('click', '[data-button-action="add-to-cart"]', (event) => {
    event.preventDefault();

    const $form = $(event.currentTarget.form);
    const query = `${$form.serialize()}&add=1&action=update`;
    const actionURL = $form.attr('action');
    const addToCartButton = $(event.currentTarget);

    addToCartButton.prop('disabled', true);

    const isQuantityInputValid = ($input) => {
      let validInput = true;

      $input.each((index, input) => {
        const $currentInput = $(input);
        const minimalValue = parseInt($currentInput.attr('min'), 10);

        if (minimalValue && $currentInput.val() < minimalValue) {
          onInvalidQuantity($currentInput);
          validInput = false;
        }
      });

      return validInput;
    };

    let onInvalidQuantity = ($input) => {
      $input
        .parents(prestashop.selectors.product.addToCart)
        .first()
        .find(prestashop.selectors.product.minimalQuantity)
        .addClass('error');
      $input
        .parent()
        .find('label')
        .addClass('error');
    };

    const $quantityInput = $form.find('input[min]');

    if (!isQuantityInputValid($quantityInput)) {
      onInvalidQuantity($quantityInput);

      return;
    }

    $.post(actionURL, query, null, 'json')
      .then((resp) => {
        if (!resp.hasError) {
          prestashop.emit('updateCart', {
            reason: {
              idProduct: resp.id_product,
              idProductAttribute: resp.id_product_attribute,
              idCustomization: resp.id_customization,
              linkAction: 'add-to-cart',
              cart: resp.cart,
            },
            resp,
          });
        } else {
          prestashop.emit('handleError', {
            eventType: 'addProductToCart',
            resp,
          });
        }
      })
      .fail((resp) => {
        prestashop.emit('handleError', {
          eventType: 'addProductToCart',
          resp,
        });
      })
      .always(() => {
        setTimeout(() => {
          addToCartButton.prop('disabled', false);
        }, 1000);
      });
  });

  $body.on('submit', '[data-link-action="add-voucher"]', (event) => {
    event.preventDefault();

    const $addVoucherForm = $(event.currentTarget);
    const getCartViewUrl = $addVoucherForm.attr('action');

    if ($addVoucherForm.find('[name=action]').length === 0) {
      $addVoucherForm.append(
        $('<input>', {type: 'hidden', name: 'ajax', value: 1}),
      );
    }
    if ($addVoucherForm.find('[name=action]').length === 0) {
      $addVoucherForm.append(
        $('<input>', {type: 'hidden', name: 'action', value: 'update'}),
      );
    }

    $.post(getCartViewUrl, $addVoucherForm.serialize(), null, 'json')
      .then((resp) => {
        if (resp.hasError) {
          $('.js-error')
            .show()
            .find('.js-error-text')
            .text(resp.errors[0]);

          return;
        }

        // Refresh cart preview
        prestashop.emit('updateCart', {
          reason: event.target.dataset,
          resp,
        });
      })
      .fail((resp) => {
        prestashop.emit('handleError', {eventType: 'updateCart', resp});
      });
  });
});
