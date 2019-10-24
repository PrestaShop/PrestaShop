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
import './checkout-steps'

function setUpCheckout() {
  setUpAddress();
  setUpDelivery();
  setUpPayment();

  handleCheckoutStepChange();
  handleSubmitButton();
}

function handleCheckoutStepChange() {
  const $steps = $('.checkout-step').step();
  const $activeSteps = $steps.getActiveSteps();

  $activeSteps.makeClickable();
  $activeSteps.on(
    'click',
    (event) => {
      const $clickedStep = $(event.target).closest('.checkout-step');
      if (!$clickedStep.isUnreachable()) {
        $steps.makeCurrent($clickedStep);
        if ($clickedStep.hasContinueButton()) {
          // If the step has a continue button, all next steps are disabled in order to force the user to click on continue
          $clickedStep.disableAllAfter();
        } else {
          // If the step has no continue button, the previously completed steps are clickable
          $clickedStep.enableAllBefore();
        }
      }
      prestashop.emit('changedCheckoutStep', {event});
    });
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
