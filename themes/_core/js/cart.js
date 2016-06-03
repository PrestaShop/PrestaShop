import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('cart updated', function (event) {
    var refreshURL = $('.-js-cart').data('refresh-url');
    var requestData = {};

    if (event && event.reason) {
      requestData = {
        id_product_attribute: event.reason.idProductAttribute,
        id_product: event.reason.idProduct
      };
    }

    $.post(refreshURL, requestData).then(function (resp) {
      $('.cart-overview').replaceWith(resp.cart_detailed);
      $('.cart-detailed-totals').replaceWith(resp.cart_detailed_totals);
      $('.cart-summary-items-subtotal').replaceWith(resp.cart_summary_items_subtotal);
      $('.cart-summary-totals').replaceWith(resp.cart_summary_totals);
      $('.cart-detailed-actions').replaceWith(resp.cart_detailed_actions);
      $('.cart-voucher').replaceWith(resp.cart_voucher);

      prestashop.emit('cart dom updated');
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

      $.post(actionURL, $(this).serialize(), null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.target.dataset
        });
      });
    }
  );
});


