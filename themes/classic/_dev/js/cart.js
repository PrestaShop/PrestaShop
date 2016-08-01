import $ from 'jquery';
import prestashop from 'prestashop';

prestashop.cart = prestashop.cart || {};

prestashop.cart.active_inputs = null;

$(document).ready(() => {
  prestashop.on('cart dom updated', function(event) {
    createSpin();
  });
  prestashop.on('cart updated', function(event) {
    $('.quickview').modal('hide');
  });

  $('body').on(
    'click',
    '.-js-cart .js-touchspin, [data-link-action="delete-from-cart"], [data-link-action="remove-voucher"]',
    function(event) {
      event.preventDefault();
      var el = $(event.currentTarget);
      // First perform the action using AJAX
      var actionURL = null;
      if (el.hasClass('bootstrap-touchspin-up') || el.hasClass('bootstrap-touchspin-down')) {
        var input = el.parents('.bootstrap-touchspin').find('input.cart-line-product-quantity');
        if (input.is(':focus')) {
          return;
        }
        actionURL = el.hasClass('bootstrap-touchspin-up') ? input.data('up-url') : input.data('down-url');
      } else{
        actionURL = $(event.currentTarget).attr('href');
      }
      $.post(actionURL, {
        ajax: '1',
        action: 'update'
      }, null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: el.dataset
        });
      });
    }
  );

  $('body').on(
    'focusout',
    'input.cart-line-product-quantity',
    function(event) {
      updateQty(event);
    }
  );

  $('body').on(
    'keyup',
    'input.cart-line-product-quantity',
    function(event) {
      if (event.keyCode == 13) {
        updateQty(event);
      }
    }
  );

  createSpin();
});

function createSpin()
{
  $('input[name="product-quantity-spin"]').TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin js-touchspin',
    buttonup_class: 'btn btn-touchspin js-touchspin',
    max: 1000000
  });
}

function updateQty(event)
{
  var el = $(event.currentTarget);
  var actionURL = el.data('update-url');
  var baseValue = el.attr('value');
  var targetValue = el.val();
  if (targetValue != parseInt(targetValue) || targetValue < 0) {
    return;
  }
  var qty = targetValue - baseValue;
  if (qty == 0) {
    return;
  }
  var dir = (qty > 0) ? 'up' : 'down';
  $.post(actionURL, {
    ajax: '1',
    qty: Math.abs(qty),
    action: 'update',
    op: dir
  }, null, 'json').then(function() {
    // If succesful, refresh cart preview
    prestashop.emit('cart updated', {
      reason: el.dataset
    });
  });
}
