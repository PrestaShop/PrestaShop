/* global document */

import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('cart updated', function(event) {
    var refreshURL = $('.-js-cart').data('refresh-url');
    var requestData = {};

    if (event && event.reason) {
      requestData = {
        id_product_attribute: event.reason.idProductAttribute,
        id_product: event.reason.idProduct,
        action: event.reason.linkAction
      };
    }

    $.post(refreshURL, requestData).then(function(resp) {
      $('.cart-overview').replaceWith(resp.cart_detailed);
      $('.cart-detailed-totals').replaceWith(resp.cart_detailed_totals);
      $('.cart-summary-items-subtotal').replaceWith(resp.cart_summary_items_subtotal);
      $('.cart-summary-totals').replaceWith(resp.cart_summary_totals);
      $('.cart-voucher').replaceWith(resp.cart_voucher);
      $('input[name="product-quantity-spin"]').TouchSpin({
        verticalbuttons: true,
        verticalupclass: 'material-icons touchspin-up',
        verticaldownclass: 'material-icons touchspin-down',
        buttondown_class: 'btn btn-touchspin js-touchspin',
        buttonup_class: 'btn btn-touchspin js-touchspin'
      });
    });
  });
  $('body').on(
    'click',
    '.js-touchspin, [data-link-action="remove-from-cart"], [data-link-action="remove-voucher"]',
    function(event) {
      event.preventDefault();
      // First perform the action using AJAX
      var actionURL = null;

      if ($(event.currentTarget).hasClass('bootstrap-touchspin-up')) {
        actionURL = $('[data-up-url]').data('up-url');
      } else if ($(event.currentTarget).hasClass('bootstrap-touchspin-down')) {
        actionURL = $('[data-down-url]').data('down-url');
      } else{
        actionURL = $(event.target).attr('href');
      }

      $.post(actionURL, {
        ajax: '1'
      }, null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.target.dataset
        });

      });
    }
  );
  $('body').on(
    'click',
    '[data-button-action="add-to-cart"]',
    function(event) {
      event.preventDefault();
      var $form = $($(event.target).closest('form'));
      var query = $form.serialize() + '&add=1';
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

      // First perform the action using AJAX
      var actionURL = event.target.action;
      $.post(actionURL, $(this).serialize(), null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.target.dataset
        });
      });
    }
  );

  $('input[name="product-quantity-spin"]').TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin js-touchspin',
    buttonup_class: 'btn btn-touchspin js-touchspin'
  });
});
