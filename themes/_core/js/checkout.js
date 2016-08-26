import $ from 'jquery';
import prestashop from 'prestashop';
import setUpDelivery from './checkout-delivery'

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
      $('.js-alert-payment-conditions').hide();
    } else {
      $('.js-alert-payment-conditions').show();
    }
  }
}

function confirmPayment () {
  var option = getSelectedPaymentOption();
  if (option) {
    $('#pay-with-' + option + '-form form').submit();
  }
}

function setupCheckoutScripts () {
  $('#payment-confirmation button').on('click', confirmPayment);
  $('#payment-section input[type="checkbox"][disabled]').attr('disabled', false);
  setUpDelivery();
  $('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
  $('body').on('change', 'input[name="payment-option"]', enableOrDisableOrderButton);

  $('.js-edit-addresses').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
  });

  changeCheckoutStep();
  collapsePaymentOptions();
}

function changeCheckoutStep() {
  $('.checkout-step').off('click');

  let currentStepClass = 'js-current-step';
  let currentStepSelector = '.' + currentStepClass;
  let stepsAfterPersonalInformation = $('#checkout-personal-information-step' + currentStepSelector).nextAll();

  $(currentStepSelector).prevAll().add(stepsAfterPersonalInformation).on(
    'click',
    (event) => {
      $(currentStepSelector + ', .-current').removeClass(currentStepClass + ' -current');
      $(event.target).closest('.checkout-step').toggleClass('-current');
      $(event.target).closest('.checkout-step').toggleClass(currentStepClass);
      prestashop.emit('changedCheckoutStep');
    }
  );

  $(currentStepSelector + ':not(#checkout-personal-information-step)').nextAll().on(
    'click',
    () => {
      $(currentStepSelector + ' button.continue').click();
      prestashop.emit('changedCheckoutStep');
    }
  );
}

$(document).ready(() => {
  if ($('#checkout').length === 1) {
    setupCheckoutScripts();
  }
});
