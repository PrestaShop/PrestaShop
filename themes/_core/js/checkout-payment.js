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
    let $body = $('body');

    $body.on('change', `${this.conditionsSelector} input[type="checkbox"]`, $.proxy(this.toggleOrderButton, this));
    $body.on('change', 'input[name="payment-option"]', $.proxy(this.toggleOrderButton, this));
    $body.on('click', `${this.confirmationSelector} button`, $.proxy(this.confirm, this));

    this.collapseOptions();
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
    var show = true;
    $(`${this.conditionsSelector} input[type="checkbox"]`).each((_, checkbox) => {
      if (!checkbox.checked) {
        show = false;
      }
    });

    prestashop.emit('termsUpdated', {
      isChecked: show,
    });

    this.collapseOptions();

    var selectedOption = this.getSelectedOption();
    if (!selectedOption) {
      show = false;
    }

    $(`#${selectedOption}-additional-information`).show();
    $(`#pay-with-${selectedOption}-form`).show();

    $(prestashop.selectors.checkout.paymentBinary).hide();

    if ($(`#${selectedOption}`).hasClass('binary')) {
      var paymentOption = this.getPaymentOptionSelector(selectedOption);
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

      if (show) {
        $(this.conditionAlertSelector).hide();
      } else {
        $(this.conditionAlertSelector).show();
      }
    }
  }

  getPaymentOptionSelector(option) {
    var moduleName = $(`#${option}`).data('module-name');

    return `.js-payment-${moduleName}`;
  }

  showNativeFormErrors() {
    $(`input[name=payment-option], ${this.termsCheckboxSelector}`).each(function () {
      this.reportValidity();
    });
  }

  confirm() {
    const option = this.getSelectedOption();
    const termsAccepted = this.haveTermsBeenAccepted();

    if (option === undefined || termsAccepted === false) {
      this.showNativeFormErrors();

      return;
    }

    $(`${this.confirmationSelector} button`).addClass('disabled');
    $(`#pay-with-${option}-form form`).submit();
  }
}

export default function () {
  let payment = new Payment();
  payment.init();

  return payment;
}
