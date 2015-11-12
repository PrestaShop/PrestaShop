import $ from 'jquery';
import prestashop from 'prestashop';

function collapsePaymentOptions() {
  $('.js-additional-information, .js-payment-option-form').hide();
}

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

  collapsePaymentOptions();

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

function setupCheckoutScripts () {
  if (!$('body#order')) {
    return;
  }

  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-options input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="advanced-payment-option"]', enableOrDisableOrderButton);

  collapsePaymentOptions();

  prestashop.on('cart updated', function () {
    $.get('', {
      action: 'getCartSummary'
    }).then(resp => {
      $('#cart-summary').html(resp);
    });
  });
}

$(document).ready(setupCheckoutScripts);
