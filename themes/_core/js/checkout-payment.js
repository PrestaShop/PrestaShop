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
import $ from 'jquery'

class Payment {
  constructor() {
    this.confirmationSelector = '#payment-confirmation';
    this.paymentSelector = '#payment-section';
    this.conditionsSelector = '#conditions-to-approve';
    this.conditionAlertSelector = '.js-alert-payment-conditions';
    this.additionalInformatonSelector = '.js-additional-information';
    this.optionsForm = '.js-payment-option-form';
  }

  init() {
    $(this.paymentSelector + ' input[type="checkbox"][disabled]').attr('disabled', false);

    let $body = $('body');

    $body.on('change', this.conditionsSelector + ' input[type="checkbox"]', $.proxy(this.toggleOrderButton, this));
    $body.on('change', 'input[name="payment-option"]', $.proxy(this.toggleOrderButton, this));
    $body.on('click', this.confirmationSelector + ' button', $.proxy(this.confirm, this));

    this.collapseOptions();
  }

  collapseOptions() {
    $(this.additionalInformatonSelector + ', ' + this.optionsForm).hide();
  }

  getSelectedOption() {
    return $('input[name="payment-option"]:checked').attr('id');
  }

  hideConfirmation() {
    $(this.confirmationSelector).hide();
  }

  showConfirmation() {
    $(this.confirmationSelector).show();
  }

  toggleOrderButton() {
    var show = true;
    $(this.conditionsSelector + ' input[type="checkbox"]').each((_, checkbox) => {
      if (!checkbox.checked) {
        show = false;
      }
    });

    this.collapseOptions();

    var selectedOption = this.getSelectedOption();
    if (!selectedOption) {
      show = false;
    }

    $('#' + selectedOption + '-additional-information').show();
    $('#pay-with-' + selectedOption + '-form').show();

    $('.js-payment-binary').hide();
    if ($('#' + selectedOption).hasClass('binary')) {
      var paymentOption = this.getPaymentOptionSelector(selectedOption);
      this.hideConfirmation();
      $(paymentOption).show();

      if (show) {
        $(paymentOption).removeClass('disabled');
      } else {
        $(paymentOption).addClass('disabled');
      }
    } else {
      this.showConfirmation();
      $(this.confirmationSelector + ' button').attr('disabled', !show);

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

  confirm() {
    var option = this.getSelectedOption();
    if (option) {
      $('#pay-with-' + option + '-form form').submit();
    }
  }
}

export default function () {
  let payment = new Payment();
  payment.init();

  return payment;
}
