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
        var productLink = $($(priceInCart).parents('.product-line-grid')[0]).find('a.label').attr('href');
        var productLinkSelector = '.label[href="' + productLink + '"]';
        var newProductLink = $newCart.find(productLinkSelector);
        var $cartItem = $($cartOverview.find(productLinkSelector).parents('.cart-item')[0]);

        // Remove cart item if response does not contain current product link
        if (0 === newProductLink.length) {
          $cartItem.remove();

          return;
        }

        var $newCartItem = $($newCart.find(productLinkSelector).parents('.cart-item')[0]);
        var newPrice = $newCartItem.find(productPriceSelector).text();

        $cartItem.find(productPriceSelector).text(newPrice);
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
