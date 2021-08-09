/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
  $(formSelector).submit(function (e) {
    if ($(this).data('disabled') === true) {
      e.preventDefault();
    }
    $(this).data('disabled', true);
    $('button[type="submit"]', this).addClass('disabled');
  });
}

$(document).ready(() => {
  if ($('#checkout').length === 1) {
    setUpCheckout();
  }
});
