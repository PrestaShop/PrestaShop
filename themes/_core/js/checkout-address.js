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
import prestashop from 'prestashop'
import {psGetRequestParameter} from './common';

let editAddress = psGetRequestParameter('editAddress');
let useSameAddress = psGetRequestParameter('use_same_address');

export default function () {
  $('.js-edit-addresses').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
    prestashop.emit('editAddress');
  });

  $('#delivery-addresses input[type=radio], #invoice-addresses input[type=radio]').on('click', function () {
    $('.address-item').removeClass('selected');
    $('.address-item:has(input[type=radio]:checked)').addClass('selected');

    let idFailureAddress = $(".js-address-error").prop('id').split('-').pop();
    let notValidAddresses = $('#not-valid-addresses').val();
    let addressType = this.name.split('_').pop();
    let $addressError = $('.js-address-error[name=alert-' + addressType + ']');

    switchEditAddressButtonColor(false, idFailureAddress, addressType);

    if (notValidAddresses !== "" && editAddress === null) {
      if (notValidAddresses.split(',').indexOf(this.value) >= 0) {
        $addressError.show();
        switchEditAddressButtonColor(true, this.value, addressType);
        $(".js-address-error").prop('id', "id-failure-address-" + this.value);
      } else {
        $addressError.hide();
      }
    } else {
      $addressError.hide();
    }

    let $visibleAddressError = $('.js-address-error:visible');
    switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
  });
}

$(window).load(() => {
  let $visibleAddressError = $('.js-address-error:visible');

  if (parseInt(useSameAddress) === 0) {
    $('#invoice-addresses input[type=radio]:checked').trigger('click');
  }
  if (editAddress !== null || $('.js-address-form:visible').length > 1) {
    $visibleAddressError.hide();
  }

  if ($visibleAddressError.length > 0) {
    let idFailureAddress = $(".js-address-error").prop('id').split('-').pop();

    $visibleAddressError.each(function () {
      switchEditAddressButtonColor(true, idFailureAddress, $(this).attr('name').split('-').pop());
    });
  }
  $visibleAddressError = $('.js-address-error:visible'); // Refresh after possible hide
  switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
});

/**
 * Change the color of the edit button for the wrong address
 * @param {Boolean} enabled
 * @param {Number} id
 * @param {String} type
 */
const switchEditAddressButtonColor = function switchEditAddressButtonColor(enabled, id, type) {
  let color = "#7a7a7a";

  if (enabled) {
    $('#' + type + '-addresses a.edit-address').prop('style', 'color: #7a7a7a !important');
    color = "#2fb5d2";
  }

  $('#id-address-' + type + '-address-' + id + ' a.edit-address').prop('style', 'color: ' + color + ' !important');
};

/**
 * Enable/disable the continue address button
 */
const switchConfirmAddressesButtonState = function switchConfirmAddressesButtonState(enable) {
  $('button[name=confirm-addresses]').prop("disabled", !enable);
};
