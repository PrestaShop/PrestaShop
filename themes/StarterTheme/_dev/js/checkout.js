import $ from 'jquery';
import prestashop from 'prestashop';

function setupRegularCheckout () {
  function hideSubmitButton () {
    $('#conditions-to-approve button').hide();
    $('#delivery-method button').hide();
  }

  function refreshPaymentOptions () {
    let params = $('#conditions-to-approve').serialize() + '&action=getPaymentOptions';
    $.post('', params).then(resp => {
      $('#payment-options').replaceWith(resp);
      hideSubmitButton();
    });
  }

  function refreshDeliveryOptions () {
    let params = $('#delivery-method').serialize() + '&action=selectDeliveryOption';
    $.post('', params).then(resp => {
      $('#delivery-options').replaceWith(resp);
      prestashop.emit('cart updated');
      hideSubmitButton();
    });
  }

  hideSubmitButton();
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', refreshPaymentOptions);
  $('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);


}

function setupAdvancedCheckout () {
  function getSelectedPaymentOption () {
    return $('#payment-options input[name="advanced-payment-option"]:checked').attr('id');
  }

  function enableOrDisableOrderButton () {
    var show = true;
    $('#conditions-to-approve input[type="checkbox"]').each((_, checkbox) => {
      if (!checkbox.checked) {
        show = false;
      }
    });

    $('.additional-information, .payment-option-form').each(function( index ) {
      $(this).hide();
    });

    var option = getSelectedPaymentOption();
    if (!option) {
      show = false;
    }

    $('#' + option + '-additional-information').show();
    $('#pay-with-' + option + '-form').show();
    $('#payment-confirmation button').attr('disabled', !show);
  }

  function confirmPayment () {
    var option = getSelectedPaymentOption();
    if (option) {
      $('#pay-with-' + option + '-form form').submit();
    }
  }

  $('#payment-confirmation button').on('click', function () {
    confirmPayment();
  });


  $('#payment-options input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="advanced-payment-option"]', enableOrDisableOrderButton);
}

$(document).ready(function setupCheckoutScripts () {
  if (!$('body#order')) {
    return;
  }

  prestashop.on('cart updated', function () {
    $.get('', {
      action: 'getCartSummary'
    }).then(resp => {
      $('#cart-summary').html(resp);
    });
  });

  setupAdvancedCheckout();
});
