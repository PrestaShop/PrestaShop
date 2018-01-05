/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
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
