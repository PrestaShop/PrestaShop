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
import {psGetRequestParameter} from './common';

let editAddress = psGetRequestParameter('editAddress');
let useSameAddress = psGetRequestParameter('use_same_address');

export default function () {
  $(prestashop.selectors.checkout.editAddresses).on('click', (event) => {
    event.stopPropagation();
    $(prestashop.selectors.checkout.addressesStep).trigger('click');
    prestashop.emit('editAddress');
  });

  $(prestashop.selectors.checkout.deliveryAddressRadios).on('click', function () {
    $(prestashop.selectors.checkout.addressItem).removeClass('selected');
    $(prestashop.selectors.checkout.addressItemChecked).addClass('selected');

    let idFailureAddress = $(prestashop.selectors.checkout.addressError).prop('id').split('-').pop();
    let notValidAddresses = $(prestashop.selectors.checkout.notValidAddresses).val();
    let addressType = this.name.split('_').pop();
    let $addressError = $(prestashop.selectors.checkout.addressError + '[name=alert-' + addressType + ']');

    switchEditAddressButtonColor(false, idFailureAddress, addressType);

    if (notValidAddresses !== '' && editAddress === null) {
      if (notValidAddresses.split(',').indexOf(this.value) >= 0) {
        $addressError.show();
        switchEditAddressButtonColor(true, this.value, addressType);
        $(prestashop.selectors.checkout.addressError).prop('id', 'id-failure-address-' + this.value);
      } else {
        $addressError.hide();
      }
    } else {
      $addressError.hide();
    }

    let $visibleAddressError = $(prestashop.selectors.checkout.addressError + ':visible');
    switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
  });
}

$(window).on('load', () => {
  let $visibleAddressError = $(prestashop.selectors.checkout.addressError + ':visible');

  if (parseInt(useSameAddress) === 0) {
    $(prestashop.selectors.checkout.invoiceAddresses).trigger('click');
  }
  if (editAddress !== null || $(prestashop.selectors.checkout.addressForm + ':visible').length > 1) {
    $visibleAddressError.hide();
  }

  if ($visibleAddressError.length > 0) {
    let idFailureAddress = $(prestashop.selectors.checkout.addressError).prop('id').split('-').pop();

    $visibleAddressError.each(function () {
      switchEditAddressButtonColor(true, idFailureAddress, $(this).attr('name').split('-').pop());
    });
  }
  $visibleAddressError = $(prestashop.selectors.checkout.addressError + ':visible'); // Refresh after possible hide
  switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
});

/**
 * Change the color of the edit button for the wrong address
 * @param {Boolean} enabled
 * @param {Number} id
 * @param {String} type
 */
const switchEditAddressButtonColor = function switchEditAddressButtonColor(enabled, id, type) {
  let color = '#7a7a7a';

  if (enabled) {
    $('#' + type + '-addresses a.edit-address').prop('style', 'color: #7a7a7a !important');
    color = '#2fb5d2';
  }

  $('#id-address-' + type + '-address-' + id + ' a.edit-address').prop('style', 'color: ' + color + ' !important');
};

/**
 * Enable/disable the continue address button
 */
const switchConfirmAddressesButtonState = function switchConfirmAddressesButtonState(enable) {
  $('button[name=confirm-addresses]').prop('disabled', !enable);
};
