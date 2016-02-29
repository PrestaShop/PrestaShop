/* global document */

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

function refreshDeliveryOptions (event) {
  let params = $('#delivery-method').serialize() + '&action=selectDeliveryOption';
  $.post('', params).then(resp => {
    $('#delivery-options').replaceWith(resp);
    psShowHide();
    prestashop.emit('cart updated', {
      reason: event.target.dataset
    });
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
  $('.address-item').removeClass('selected');
  $(event.target).parents('.address-item').addClass('selected');
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
  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-section input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="payment-option"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[type="checkbox"][data-action="hideOrShow"]', hideOrShow);
  $('body').on('change', '.js-address-selector input', selectAddress);



  if($('.js-cancel-address').length !== 0){
    $('.checkout-step:not(.-js-current) .step-title').addClass('not-allowed');
  }

  $('body').on('click', '.checkout-step.-reachable h1', function (event) {
    if($('.js-cancel-address').length === 0){
      $('.-js-current, .-current').removeClass('-js-current -current');
      $(event.target).closest('.checkout-step').toggleClass('-js-current');
    }
  });

  collapsePaymentOptions();

  prestashop.on('cart updated', function () {
    $.post(location.href, null, null, 'json').then(function (resp) {
      $('#checkout-cart-summary').replaceWith(resp.preview);
    });
  });

  $('.js-customer-form').on('invalid.bs.validator',(event)=>{
    $(event.relatedTarget).next('.tooltip').css('opacity',1).show();
  });
}

$(document).ready(() => {
    if ($('body#checkout').length === 1) {
        setupCheckoutScripts();
    }
});
