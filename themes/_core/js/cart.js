/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('updateCart', (event) => {
    var getCartViewUrl = $('.js-cart').data('refresh-url');
    var requestData = {};

    if (event && event.reason) {
      requestData = {
        id_product_attribute: event.reason.idProductAttribute,
        id_product: event.reason.idProduct
      };
    }

    var productPriceSelector = '.product-price strong';

    var updatePrices = function (pricesInCart, $cartOverview, $newCart) {
      $.each(pricesInCart, function (index, priceInCart) {
        var productLabel = $($(priceInCart).parents('.product-line-grid')[0]).find('a.label');
        var productUrl = productLabel.attr('href');
        var customizationId = productLabel.data('id_customization');
        var productAnchorSelector = '.label[href="' + productUrl + '"][data-id_customization="' + customizationId + '"]';
        var newProductAnchor = $newCart.find(productAnchorSelector);
        var $cartItem = $($cartOverview.find(productAnchorSelector).parents('.cart-item')[0]);

        if (newProductAnchor.length > 0) {
          let $newCartItem = newProductAnchor.parents('.cart-item');
          let $productCartItems = $cartOverview.find(productAnchorSelector).parents('.cart-item');

          $.each($productCartItems, function (index, productCartItem) {
            let $productCartItem = $(productCartItem);
            // Case when a gift previously added to cart has been removed
            if ($productCartItem.find('.gift').length > 0 && 0 === $newCartItem.find('.gift').length) {
              $productCartItem.remove();
            }
          });

          if (
            $newCartItem.find('.gift').length === 1 &&
            $productCartItems.find('.gift').length === 1 &&
            $productCartItems.length > 1
          ) {
            // Case when a product added manually has been removed and
            // the same product has been given away
            let $manuallyAddedProducts = $productCartItems.filter(function (index, productCartItem) {
              return $(productCartItem).find('.gift').length === 0;
            });
            $manuallyAddedProducts.remove();
          }
        }

        // Remove cart item if response does not contain current product link
        if (0 === newProductAnchor.length) {
          $cartItem.remove();

          return;
        }

        var $newCartItem = $($newCart.find(productAnchorSelector).parents('.cart-item')[0]);

        var newPrice;
        if ($newCartItem.find(productPriceSelector).find('.gift').length > 0) {
          newPrice = $newCartItem.find(productPriceSelector).html(); // Preserve gift tag
          $cartItem.find(productPriceSelector).html(newPrice);
        } else {
          newPrice = $newCartItem.find(productPriceSelector).text();
          $cartItem.find(productPriceSelector).text(newPrice);
        }
      });
    };

    var appendGiftProducts = function ($cartOverview, $newCart) {
      $newCart = $newCart.filter('.js-cart');
      let $productAnchors = $newCart.find('.label[href]');

      $.each($productAnchors, function (index, productAnchor) {
        let $productAnchor = $(productAnchor);
        let productUrl = $productAnchor.attr('href');
        let $cartItems = $cartOverview.find('.cart-items');
        let $newCartItem = $productAnchor.parents('.cart-item');

        if (0 === $cartItems.find('.label[href="' + productUrl + '"]').length) {
          $cartItems.append($productAnchor.parents('.cart-item'));
        } else {
          let $cartItem = $cartItems.find('.label[href="' + productUrl + '"]').parents('.cart-item');
          if ($cartItem.find('.gift').length === 0 && $newCartItem.find('.gift').length > 0) {
            $cartItems.append($newCartItem);
          }
        }
      });
    };

    $.post(getCartViewUrl, requestData).then((resp) => {
      var $newCart = $(resp.cart_detailed);
      var $cartOverview = $('.cart-overview');
      var pricesInCart = $cartOverview.find(productPriceSelector);

      if ($newCart.find('.no-items').length > 0) {
        $cartOverview.replaceWith(resp.cart_detailed);
      } else {
        updatePrices(pricesInCart, $cartOverview, $newCart);
        appendGiftProducts($cartOverview, $newCart);
      }

      $('.cart-detailed-totals').replaceWith(resp.cart_detailed_totals);
      $('.cart-summary-items-subtotal').replaceWith(resp.cart_summary_items_subtotal);
      $('.cart-summary-totals').replaceWith(resp.cart_summary_totals);
      $('.cart-detailed-actions').replaceWith(resp.cart_detailed_actions);
      $('.cart-voucher').replaceWith(resp.cart_voucher);

      $('.js-cart-line-product-quantity').each((index, input) => {
        var $input = $(input);
        $input.attr('value', $input.val());
      });

      prestashop.emit('updatedCart');
    }).fail((resp) => {
      prestashop.emit('handleError', {eventType: 'updateCart', resp: resp})
    });
  });

  var $body = $('body');

  $body.on(
    'click',
    '[data-button-action="add-to-cart"]',
    (event) => {
      event.preventDefault();

      var $form = $($(event.target).closest('form'));
      var query = $form.serialize() + '&add=1&action=update';
      var actionURL = $form.attr('action');

      let isQuantityInputValid = ($input) => {
        var validInput = true;

        $input.each((index, input) => {
          let $input = $(input);
          let minimalValue = parseInt($input.attr('min'), 10);
          if (minimalValue && $input.val() < minimalValue) {
              onInvalidQuantity($input);
              validInput = false;
          }
        });

        return validInput;
      };

      let onInvalidQuantity = ($input) => {
        $($input.parents('.product-add-to-cart')[0]).find('.product-minimal-quantity')
            .addClass('error');
        $input.parent().find('label').addClass('error');
      };

      let $quantityInput = $form.find('input[min]' );
      if (!isQuantityInputValid($quantityInput)) {
        onInvalidQuantity($quantityInput);

        return;
      }

      $.post(actionURL, query, null, 'json').then((resp) => {
        prestashop.emit('updateCart', {
          reason: {
            idProduct: resp.id_product,
            idProductAttribute: resp.id_product_attribute,
            linkAction: 'add-to-cart'
          }
        });
      }).fail((resp) => {
        prestashop.emit('handleError', {eventType: 'addProductToCart', resp: resp});
      });
    }
  );

  $body.on(
    'submit',
    '[data-link-action="add-voucher"]',
    (event) => {
      event.preventDefault();

      let $addVoucherForm = $(event.currentTarget);
      let getCartViewUrl = $addVoucherForm.attr('action');

      if (0 === $addVoucherForm.find('[name=action]').length) {
        $addVoucherForm.append($('<input>', {'type': 'hidden', 'name': 'ajax', "value": 1}));
      }
      if (0 === $addVoucherForm.find('[name=action]').length) {
        $addVoucherForm.append($('<input>', {'type': 'hidden', 'name': 'action', "value": "update"}));
      }

      $.post(getCartViewUrl, $addVoucherForm.serialize(), null, 'json').then((resp) => {
        if (resp.hasError) {
          $('.js-error').show().find('.js-error-text').text(resp.errors[0]);

          return;
        }

        // Refresh cart preview
        prestashop.emit('updateCart', {reason: event.target.dataset});
      }).fail((resp) => {
        prestashop.emit('handleError', {eventType: 'addVoucher', resp: resp});
      })
    }
  );
});
