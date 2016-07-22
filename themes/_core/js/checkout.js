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

  var module_name = $(`#${option}`).data('module-name');

  if ($('#' + option).hasClass('binary')) {
    var payment_option = `.js-payment-${module_name}`;
    $('#payment-confirmation').hide();
    $(payment_option).show();
    if (show) {
      $(payment_option).removeClass('disabled');
    } else {
      $(payment_option).addClass('disabled');
    }
  } else {
    $('.js-payment-binary').hide();
    $('#payment-confirmation').show();
    $('#payment-confirmation button').attr('disabled', !show);
    if (show) {
      $('.js-alert-payment-condtions').hide();
    } else {
      $('.js-alert-payment-condtions').show();
    }
  }
}

function confirmPayment () {
  var option = getSelectedPaymentOption();
  if (option) {
    $('#pay-with-' + option + '-form form').submit();
  }
}

function refreshDeliveryOptions (event) {
  let params = $('#delivery-method').serialize();
  $.post($('#delivery-method').data('url-update'), params).then(function (resp) {
    $('#checkout-cart-summary').replaceWith(resp.preview);
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

function setupCheckoutScripts () {
  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-section input[type="checkbox"][disabled]').attr('disabled', false);
  $('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="payment-option"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[type="checkbox"][data-action="hideOrShow"]', hideOrShow);

  $('.js-edit-addresses').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
  });

  $('.js-edit-delivery').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-delivery-step').trigger('click');
  });

  changeCurrentCheckoutStep();
  collapsePaymentOptions();
}

function changeCurrentCheckoutStep() {
  $('.checkout-step').off('click');

  $('.-js-current').prevAll().add($('#checkout-personal-information-step.-js-current').nextAll()).on('click', function(event) {
    $('.-js-current, .-current').removeClass('-js-current -current');
    $(event.target).closest('.checkout-step').toggleClass('-js-current');
    prestashop.emit('change current checkout step');
  });

  $('.-js-current:not(#checkout-personal-information-step)').nextAll().on('click', function(event) {
    $('.-js-current button.continue').click();
    prestashop.emit('change current checkout step');
  });
}

$(document).ready(() => {
  if ($('body#checkout').length === 1) {
    setupCheckoutScripts();
  }

  prestashop.on('change current checkout step', function(event) {
    changeCurrentCheckoutStep();
  });
});
