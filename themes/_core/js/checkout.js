/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import setUpAddress from './checkout-address';
import setUpDelivery from './checkout-delivery';
import setUpPayment from './checkout-payment';
import Steps from './checkout-steps';

function setUpCheckout() {
  setUpAddress();
  setUpDelivery();
  setUpPayment();

  handleCheckoutStepChange();
  handleSubmitButton();
}

function handleCheckoutStepChange() {
  const steps = new Steps();
  const clickableSteps = steps.getClickableSteps();

  clickableSteps.on('click', (event) => {
    const clickedStep = Steps.getClickedStep(event);

    if (!clickedStep.isUnreachable()) {
      steps.makeCurrent(clickedStep);
      if (clickedStep.hasContinueButton()) {
        clickedStep.disableAllAfter();
      } else {
        clickedStep.enableAllBefore();
      }
    }
    prestashop.emit('changedCheckoutStep', {event});
  });
}

function handleSubmitButton() {
  // prevents rage clicking on submit button and related issues
  const formSelector = prestashop.selectors.checkout.form;
  $(formSelector).on('submit', function (e) {
    if ($(this).data('disabled') === true) {
      e.preventDefault();
    }
    $(this).data('disabled', true);
    $('button[type="submit"]', this).addClass('disabled');
  });
}

$(() => {
  if ($('#checkout').length === 1) {
    setUpCheckout();
  }
});
