import $ from 'jquery';
import prestashop from 'prestashop';

prestashop.cart = prestashop.cart || {};

prestashop.cart.active_inputs = null;

/**
 * Attach Bootstrap TouchSpin event handlers
 */
function createSpin()
{
  $('input[name="product-quantity-spin"]').TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin js-touchspin js-increase-product-quantity',
    buttonup_class: 'btn btn-touchspin js-touchspin js-decrease-product-quantity',
    min: 1,
    max: 1000000
  });
}


$(document).ready(() => {
  let productLineInCartSelector = '.js-cart-line-product-quantity';

  prestashop.on('updateCart', () => {
    $('.quickview').modal('hide');
  });

  createSpin();

  prestashop.on('updatedCart', () => {
    createSpin();
  });

  let $body = $('body');

  function isTouchSpin($target) {
    return $target.hasClass('bootstrap-touchspin-up') || $target.hasClass('bootstrap-touchspin-down');
  }

  function shouldIncreaseProductQuantity($target) {
    return $target.hasClass('bootstrap-touchspin-up');
  }

  function findCartLineProductQuantityInput($target) {
    var $input = $target.parents('.bootstrap-touchspin').find(productLineInCartSelector);

    if ($input.is(':focus')) {
      return null;
    } else {
      return $input;
    }
  }

  function camelize(subject) {
    let actionTypeParts = subject.split('-');
    let i;
    let part;
    let camelizedSubject = '';

    for (i = 0; i < actionTypeParts.length; i++) {
      part = actionTypeParts[i];

      if (0 !== i) {
        part = part.substring(0, 1).toUpperCase() + part.substring(1);
      }

      camelizedSubject = camelizedSubject + part;
    }

    return camelizedSubject;
  }

  function parseCartAction($target) {
    if (!isTouchSpin($target)) {
      return {
        url: $target.attr('href'),
        type: camelize($target.data('link-action'))
      }
    }

    let $input = findCartLineProductQuantityInput($target);
    if (!$input) {
      return;
    }

    let cartAction = {};
    if (shouldIncreaseProductQuantity($target)) {
      cartAction = {
        url: $input.data('up-url'),
        type: 'increaseProductQuantity'
      };
    } else {
      cartAction = {
        url: $input.data('down-url'),
        type: 'decreaseProductQuantity'
      }
    }

    return cartAction;
  }

  $body.on(
    'click',
    '.js-cart .js-touchspin, [data-link-action="delete-from-cart"], [data-link-action="remove-voucher"]',
    (event) => {
      event.preventDefault();

      let $target = $(event.currentTarget);
      let cartAction = parseCartAction($target);
      let requestData = {
        ajax: '1',
        action: 'update'
      };

      $.post(cartAction.url, requestData, null, 'json').then(function() {
        // Refresh cart preview
        prestashop.emit('updateCart', {
          reason: $target.dataset
        });
      }).fail((resp) => {
        prestashop.emit('handleError', {
          eventType: 'updateProductInCart',
          resp: resp,
          cartAction: cartAction.type
        });
      });
    }
  );

  function updateProductQuantityInCart(event)
  {
    let $target = $(event.currentTarget);
    let updateQuantityInCartUrl = $target.data('update-url');
    let baseValue = $target.attr('value');

    // There should be a valid product quantity in cart
    let targetValue = $target.val();
    if (targetValue != parseInt(targetValue) || targetValue < 0) {
      return;
    }

    // There should be a new product quantity in cart
    let qty = targetValue - baseValue;
    if (qty == 0) {
      return;
    }

    let dir = (qty > 0) ? 'up' : 'down';

    var requestData = {
      ajax: '1',
      qty: Math.abs(qty),
      action: 'update',
      op: dir
    };

    $.post(updateQuantityInCartUrl, requestData, null, 'json').then(function() {
      // Refresh cart preview
      prestashop.emit('updateCart', {
        reason: $target.dataset
      });
    }).fail((resp) => {
        prestashop.emit('handleError', {eventType: 'updateProductQuantityInCart', resp: resp})
    });
  }

  $body.on(
    'focusout',
    productLineInCartSelector,
    (event) => {
      updateProductQuantityInCart(event);
    }
  );

  $body.on(
    'keyup',
    productLineInCartSelector,
    (event) => {
      if (event.keyCode == 13) {
        updateProductQuantityInCart(event);
      }
    }
  );
});


