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

    $.post(getCartViewUrl, requestData).then((resp) => {
      $('.cart-overview').replaceWith(resp.cart_detailed);
      $('.cart-detailed-totals').replaceWith(resp.cart_detailed_totals);
      $('.cart-summary-items-subtotal').replaceWith(resp.cart_summary_items_subtotal);
      $('.cart-summary-totals').replaceWith(resp.cart_summary_totals);
      $('.cart-detailed-actions').replaceWith(resp.cart_detailed_actions);
      $('.cart-voucher').replaceWith(resp.cart_voucher);

      prestashop.emit('updatedCart');
    }).fail((resp) => {
      prestashop.emit('handleError', {eventType: 'updateCart', resp: resp})
    });
  });

  $('body').on(
    'click',
    '[data-button-action="add-to-cart"]',
    function(event) {
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

      $.post(actionURL, query, null, 'json').then(function(resp) {
        prestashop.emit('cart updated', {
          reason: {
            idProduct: resp.id_product,
            idProductAttribute: resp.id_product_attribute,
            linkAction: 'add-to-cart'
          }
        });
      });
    }
  );

  $('body').on(
    'submit',
    '[data-link-action="add-voucher"]',
    function(event) {
      event.preventDefault();

      $(this).append($('<input>')
        .attr('type', 'hidden')
        .attr('name', 'ajax').val('1')
      );
      $(this).append($('<input>')
        .attr('type', 'hidden')
        .attr('name', 'action').val('update')
      );

      // First perform the action using AJAX
      var actionURL = $(this).attr('action');

      $.post(actionURL, $(this).serialize(), null, 'json').then(function(res) {
        if(res.hasError){
          return $('.js-error').show().find('.js-error-text').text(res.errors[0]);
        }
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.target.dataset
        });
      });
    }
  );
});
