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

prestashop.checkout = prestashop.checkout || {};

prestashop.checkout.onCheckOrderableCartResponse = (resp, paymentObject) => {
  if (resp.errors === true) {
    prestashop.emit('orderConfirmationErrors', {
      resp,
      paymentObject,
    });
    return true;
  }
  return false;
};

class Payment {
  constructor() {
    this.confirmationSelector = prestashop.selectors.checkout.confirmationSelector;
    this.conditionsSelector = prestashop.selectors.checkout.conditionsSelector;
    this.conditionAlertSelector = prestashop.selectors.checkout.conditionAlertSelector;
    this.additionalInformatonSelector = prestashop.selectors.checkout.additionalInformatonSelector;
    this.optionsForm = prestashop.selectors.checkout.optionsForm;
    this.termsCheckboxSelector = prestashop.selectors.checkout.termsCheckboxSelector;
  }

  init() {
    // eslint-disable-next-line no-unused-vars
    prestashop.on('orderConfirmationErrors', ({resp, paymentObject}) => {
      if (resp.cartUrl !== '') {
        location.href = resp.cartUrl;
      }
    });

    const $body = $('body');

    $body.on('change', `${this.conditionsSelector} input[type="checkbox"]`, $.proxy(this.toggleOrderButton, this));
    $body.on('change', 'input[name="payment-option"]', $.proxy(this.toggleOrderButton, this));
    // call toggle once on init to handle situation where everything
    // is already ok (like 0 price order, payment already preselected and so on)
    this.toggleOrderButton();

    $body.on('click', `${this.confirmationSelector} button`, $.proxy(this.confirm, this));

    if (!this.getSelectedOption()) {
      this.collapseOptions();
    }
  }

  collapseOptions() {
    $(`${this.additionalInformatonSelector}, ${this.optionsForm}`).hide();
  }

  getSelectedOption() {
    return $('input[name="payment-option"]:checked').attr('id');
  }

  haveTermsBeenAccepted() {
    return $(this.termsCheckboxSelector).prop('checked');
  }

  hideConfirmation() {
    $(this.confirmationSelector).hide();
  }

  showConfirmation() {
    $(this.confirmationSelector).show();
  }

  toggleOrderButton() {
    let show = true;
    $(`${this.conditionsSelector} input[type="checkbox"]`).each((_, checkbox) => {
      if (!checkbox.checked) {
        show = false;
      }
    });

    prestashop.emit('termsUpdated', {
      isChecked: show,
    });

    this.collapseOptions();

    const selectedOption = this.getSelectedOption();

    if (!selectedOption) {
      show = false;
    }

    $(`#${selectedOption}-additional-information`).show();
    $(`#pay-with-${selectedOption}-form`).show();

    $(prestashop.selectors.checkout.paymentBinary).hide();

    if ($(`#${selectedOption}`).hasClass('binary')) {
      const paymentOption = this.getPaymentOptionSelector(selectedOption);
      this.hideConfirmation();
      $(paymentOption).show();

      document.querySelectorAll(`${paymentOption} button, ${paymentOption} input`).forEach((element) => {
        if (show) {
          element.removeAttribute('disabled');
        } else {
          element.setAttribute('disabled', !show);
        }
      });

      if (show) {
        $(paymentOption).removeClass('disabled');
      } else {
        $(paymentOption).addClass('disabled');
      }
    } else {
      this.showConfirmation();
      $(`${this.confirmationSelector} button`).toggleClass('disabled', !show);
      // Next line provides backward compatibility for Classic Theme < 1.7.8
      $(`${this.confirmationSelector} button`).attr('disabled', !show);

      if (show) {
        $(this.conditionAlertSelector).hide();
      } else {
        $(this.conditionAlertSelector).show();
      }
    }
  }

  getPaymentOptionSelector(option) {
    const moduleName = $(`#${option}`).data('module-name');

    return `.js-payment-${moduleName}`;
  }

  showNativeFormErrors() {
    $(`input[name=payment-option], ${this.termsCheckboxSelector}`).each(function () {
      this.reportValidity();
    });
  }

  async confirm() {
    const option = this.getSelectedOption();
    const termsAccepted = this.haveTermsBeenAccepted();

    if (option === undefined || termsAccepted === false) {
      this.showNativeFormErrors();
      return;
    }

    // We ask cart controller, if everything in the cart is still orderable
    const resp = await $.post(window.prestashop.urls.pages.order, {
      ajax: 1,
      action: 'checkCartStillOrderable',
    });

    // We process the information and allow other modules to intercept this
    const isRedirected = prestashop.checkout.onCheckOrderableCartResponse(resp, this);

    // If there is a redirect, we deny the form submit below, to allow the redirect to complete
    if (isRedirected) return;

    $(`${this.confirmationSelector} button`).addClass('disabled');
    $(`#pay-with-${option}-form form`).submit();
  }
}

export default function () {
  const payment = new Payment();
  payment.init();

  return payment;
}
