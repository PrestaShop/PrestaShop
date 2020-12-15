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

const editAddress = psGetRequestParameter('editAddress');
const useSameAddress = psGetRequestParameter('use_same_address');

export default function () {
  $(prestashop.selectors.checkout.editAddresses).on('click', (event) => {
    event.stopPropagation();
    $(prestashop.selectors.checkout.addressesStep).trigger('click');
    prestashop.emit('editAddress');
  });

  $(prestashop.selectors.checkout.deliveryAddressRadios).on(
    'click',
    function () {
      $(prestashop.selectors.checkout.addressItem).removeClass('selected');
      $(prestashop.selectors.checkout.addressItemChecked).addClass('selected');

      const idFailureAddress = $(prestashop.selectors.checkout.addressError)
        .prop('id')
        .split('-')
        .pop();
      const notValidAddresses = $(
        prestashop.selectors.checkout.notValidAddresses,
      ).val();
      const addressType = this.name.split('_').pop();
      const $addressError = $(
        `${prestashop.selectors.checkout.addressError}[name=alert-${addressType}]`,
      );

      switchEditAddressButtonColor(false, idFailureAddress, addressType);

      if (notValidAddresses !== '' && editAddress === null) {
        if (notValidAddresses.split(',').indexOf(this.value) >= 0) {
          $addressError.show();
          switchEditAddressButtonColor(true, this.value, addressType);
          $(prestashop.selectors.checkout.addressError).prop(
            'id',
            `id-failure-address-${this.value}`,
          );
        } else {
          $addressError.hide();
        }
      } else {
        $addressError.hide();
      }

      const $visibleAddressError = $(
        `${prestashop.selectors.checkout.addressError}:visible`,
      );
      switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
    },
  );
}

$(window).on('load', () => {
  let $visibleAddressError = $(
    `${prestashop.selectors.checkout.addressError}:visible`,
  );

  if (parseInt(useSameAddress, 10) === 0) {
    $(prestashop.selectors.checkout.invoiceAddresses).trigger('click');
  }
  if (
    editAddress !== null
    || $(`${prestashop.selectors.checkout.addressForm}:visible`).length > 1
  ) {
    $visibleAddressError.hide();
  }

  if ($visibleAddressError.length > 0) {
    const idFailureAddress = $(prestashop.selectors.checkout.addressError)
      .prop('id')
      .split('-')
      .pop();

    $visibleAddressError.each(function () {
      switchEditAddressButtonColor(
        true,
        idFailureAddress,
        $(this)
          .attr('name')
          .split('-')
          .pop(),
      );
    });
  }
  $visibleAddressError = $(
    `${prestashop.selectors.checkout.addressError}:visible`,
  ); // Refresh after possible hide
  switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
});

/**
 * Change the color of the edit button for the wrong address
 * @param {Boolean} enabled
 * @param {Number} id
 * @param {String} type
 */
const switchEditAddressButtonColor = function switchEditAddressButtonColor(
  enabled,
  id,
  type,
) {
  const addressBtn = $(`#id-address-${type}-address-${id} a.edit-address`);
  const classesToToggle = ['text-info', 'address-item-invalid'];

  $(`#${type}-addresses a.edit-address`).removeClass(classesToToggle);

  addressBtn.toggleClass(classesToToggle, enabled);
};

/**
 * Enable/disable the continue address button
 */
const switchConfirmAddressesButtonState = function switchConfirmAddressesButtonState(
  enable,
) {
  $('button[name=confirm-addresses]').prop('disabled', !enable);
};
