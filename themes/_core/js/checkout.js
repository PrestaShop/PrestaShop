import $ from 'jquery';
import prestashop from 'prestashop';
import setUpAddress from './checkout-payment'
import setUpDelivery from './checkout-delivery'
import setUpPayment from './checkout-payment'

function setUpCheckout() {
  setUpAddress();
  setUpDelivery();
  setUpPayment();

  handleCheckoutStepChange();
}

function handleCheckoutStepChange() {
  $('.checkout-step').off('click');

  let currentStepClass = 'js-current-step';
  let currentStepSelector = '.' + currentStepClass;
  let stepsAfterPersonalInformation = $('#checkout-personal-information-step').nextAll();

  $(currentStepSelector).prevAll().add(stepsAfterPersonalInformation).on(
    'click',
    (event) => {
      let $nextStep = $(event.target).closest('.checkout-step');
      if (!$nextStep.hasClass('-unreachable')) {
        $(currentStepSelector + ', .-current').removeClass(currentStepClass + ' -current');
        $nextStep.toggleClass('-current');
        $nextStep.toggleClass(currentStepClass);
      }
      prestashop.emit('changedCheckoutStep', {event: event});
    }
  );

  $(currentStepSelector + ':not(#checkout-personal-information-step)').nextAll().on(
    'click',
    (event) => {
      $(currentStepSelector + ' button.continue').click();
      prestashop.emit('changedCheckoutStep', {event: event});
    }
  );
}

$(document).ready(() => {
  if ($('#checkout').length === 1) {
    setUpCheckout();
  }
});
