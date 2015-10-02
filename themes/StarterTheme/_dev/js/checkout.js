import $ from 'jquery';

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

    if (!getSelectedPaymentOption()) {
      show = false;
    }

    $('#payment-confirmation button').attr('disabled', !show);
  }

  function confirmPayment () {
    var option = getSelectedPaymentOption();
    if (option) {
      $('#pay-with-' + option + '-form').submit();
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
  if ($('#payment-options').data('uses-advanced-payment-api')) {
    setupAdvancedCheckout();
  } else {
    setupRegularCheckout();
  }
});
