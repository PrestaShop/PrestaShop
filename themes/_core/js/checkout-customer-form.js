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

export default function () {
  const $body = $('body');
  const {checkoutNewCustomerRelatedBlock} = prestashop.selectors.checkout;
  const checkoutLoginAlertClass = 'js-checkout-login-alert';
  const {checkoutCustomerForm} = prestashop.selectors.checkout;
  const $checkoutNewCustomerRelatedBlock = $(checkoutNewCustomerRelatedBlock);
  const $checkoutCustomerForm = $(checkoutCustomerForm);
  const $checkoutCustomerFormSubmitBtn = $checkoutCustomerForm.find('[type="submit"]');
  const $checkoutFormEmailInput = $checkoutCustomerForm.find('[name="email"]');
  const $checkoutFormPasswordInput = $checkoutCustomerForm.find('[name="password"]');
  let timeout;

  const handleBlockDisplay = ({customerExists}) => {
    $checkoutNewCustomerRelatedBlock.toggleClass('hidden-xs-up', customerExists);
    if (customerExists === true) {
      $checkoutFormPasswordInput.val('');
    }
  };

  const handleAlertMessage = ({alert}) => {
    $(`.${checkoutLoginAlertClass}`).remove();

    if (alert.message) {
      // help-block class only to keep styling consistent with themes/classic/templates/_partials/form-errors.tpl
      const $alertBlock = $('<div>').addClass(`alert help-block ${checkoutLoginAlertClass}`).text(alert.message);
      const alertTypeClass = alert.type === 'danger' ? 'alert-danger' : 'alert-info';

      $alertBlock.addClass(alertTypeClass);
      $checkoutFormEmailInput.after($alertBlock);
    }
  };

  const handleFormSubmitting = ({guestAllowed, customerExists}) => {
    $checkoutCustomerFormSubmitBtn.attr('disabled', !guestAllowed && customerExists);
  };

  const handleRequest = (email) => {
    const url = $checkoutCustomerForm.attr('action');

    $.post(url, {
      ajax: 1,
      action: 'checkCustomerInformation',
      email,
    })
      .then((resp) => {
        handleBlockDisplay(resp);
        handleFormSubmitting(resp);
        handleAlertMessage(resp);
      });
  };

  const fetchForCustomerInformation = () => {
    const email = $checkoutFormEmailInput.val();

    clearTimeout(timeout);
    timeout = setTimeout(() => handleRequest(email), 300);
  };

  $body.on('keyup', $checkoutFormEmailInput, fetchForCustomerInformation);
}
