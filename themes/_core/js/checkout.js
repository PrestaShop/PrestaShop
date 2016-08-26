import $ from 'jquery';
import prestashop from 'prestashop';
import setUpDelivery from './checkout-delivery'
import setUpPayment from './checkout-payment'

function setupCheckoutScripts () {
  setUpDelivery();
  let payment = setUpPayment();

  $('.js-edit-addresses').on('click', (event) => {
  payment.collapseOptions();
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
  });

  changeCheckoutStep();
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
