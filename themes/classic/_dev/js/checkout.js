import $ from 'jquery';
import prestashop from 'prestashop';

import {psShowHide} from './common';

function collapsePaymentOptions() {
  $('.js-additional-information, .js-payment-option-form').hide();
}

function getSelectedPaymentOption () {
  return $('input[name="payment-option"]:checked').attr('id');
}

function enableOrDisableOrderButton() {
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

function refreshDeliveryOptions () {
  let params = $('#delivery-method').serialize() + '&action=selectDeliveryOption';
  $.post('', params).then(resp => {
    $('#delivery-options').replaceWith(resp);
    psShowHide();
  });
}

function hideOrShow () {
  var elm = this.getAttribute('data-action-target');
  var show = this.checked;

  if (show) {
    $('body #'+elm).show();
  } else {
    $('body #'+elm).hide();
  }
}

function selectAddress (event) {
  const form = $(event.target).closest('form');
  $
    .post('', form.serialize(), null, 'json')
    .then(resp => {
      // TODO
    })
    .fail(resp => {
      // TODO
    })
  ;
}

function setupCheckoutScripts () {
  if (!$('body#order')) {
    return;
  }

  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-section input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="payment-option"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[type="checkbox"][data-action="hideOrShow"]', hideOrShow);
  $('body').on('change', '.js-address-selector input', selectAddress);
  $('body').on('click', '.checkout-step.-reachable h1', function (event) {
    $('.-js-current, .-current').removeClass('-js-current -current');
    $(event.target).closest('.checkout-step').toggleClass('-js-current');
  });

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
