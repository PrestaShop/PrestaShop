/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import setUpAddress from './checkout-address'
import setUpDelivery from './checkout-delivery'
import setUpPayment from './checkout-payment'

function setUpCheckout() {
  setUpAddress();
  setUpDelivery();
  setUpPayment();

  handleCheckoutStepChange();
  handleSubmitButton();
}

function handleCheckoutStepChange() {
  $('.checkout-step').off('click');

  const currentStepClass = 'js-current-step';
  const currentStepSelector = '.' + currentStepClass;
  let $previousSteps = $(currentStepSelector).prevAll();
  $previousSteps = $(currentStepSelector).add($previousSteps);
  //We use this class to mark previously completed steps
  $previousSteps.addClass('-clickable');
  $previousSteps.on(
    'click',
    (event) => {
      const $clickedStep = $(event.target).closest('.checkout-step');
      if (!$clickedStep.hasClass('-unreachable')) {
        $(currentStepSelector + ', .-current').removeClass(currentStepClass + ' -current');
        $clickedStep.toggleClass('-current');
        $clickedStep.toggleClass(currentStepClass);

        if ($('button.continue', $clickedStep).length == 0) {
          //If the step has no continue button, the previously completed steps are clickable
          const $nextSteps = $clickedStep.nextAll('.checkout-step.-clickable');
          $nextSteps.removeClass('-unreachable').addClass('-complete');
          $('.step-title', $nextSteps).removeClass('not-allowed');
        } else {
          //If the step has a continue button, all next steps are disabled in order to force the user to click on continue
          const $nextSteps = $clickedStep.nextAll();
          $nextSteps.addClass('-unreachable').removeClass('-complete');
          $('.step-title', $nextSteps).addClass('not-allowed');
        }
      }
      prestashop.emit('changedCheckoutStep', {event: event});
    }
  );
}

function handleSubmitButton() {
  // prevents rage clicking on submit button and related issues
  const submitSelector = '.js-current-step button[type="submit"]';
  $(document).on('click', submitSelector, function () {
    $(this).addClass('disabled');
    $('input[required]').on('invalid', function (e) {
      $(submitSelector).removeClass('disabled');
    });
  });
}

$(document).ready(() => {
  if ($('#checkout').length === 1) {
    setUpCheckout();
  }
});
