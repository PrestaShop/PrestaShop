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

import createOrderPageMap from './create-order-map';

const $ = window.$;

/**
 * Renders Delivery & Invoice addresses select
 */
export default class AddressesRenderer {

  /**
   * @param {Array} addresses
   */
  render(addresses) {
    let deliveryAddressDetailsContent = '';
    let invoiceAddressDetailsContent = '';

    const $deliveryAddressDetails = $(createOrderPageMap.deliveryAddressDetails);
    const $invoiceAddressDetails = $(createOrderPageMap.invoiceAddressDetails);
    const $deliveryAddressSelect = $(createOrderPageMap.deliveryAddressSelect);
    const $invoiceAddressSelect = $(createOrderPageMap.invoiceAddressSelect);

    const $addressesContent = $(createOrderPageMap.addressesContent);
    const $addressesWarningContent = $(createOrderPageMap.addressesWarning);

    $deliveryAddressDetails.empty();
    $invoiceAddressDetails.empty();
    $deliveryAddressSelect.empty();
    $invoiceAddressSelect.empty();

    if (addresses.length === 0) {
      $addressesWarningContent.removeClass('d-none');
      $addressesContent.addClass('d-none');

      return;
    }

    $addressesContent.removeClass('d-none');
    $addressesWarningContent.addClass('d-none');

    for (const key in Object.keys(addresses)) {
      const address = addresses[key];

      const deliveryAddressOption = {
        value: address.addressId,
        text: address.alias,
      };

      const invoiceAddressOption = {
        value: address.addressId,
        text: address.alias,
      };

      if (address.delivery) {
        deliveryAddressDetailsContent = address.formattedAddress;
        deliveryAddressOption.selected = 'selected';
      }

      if (address.invoice) {
        invoiceAddressDetailsContent = address.formattedAddress;
        invoiceAddressOption.selected = 'selected';
      }

      $deliveryAddressSelect.append($('<option>', deliveryAddressOption));
      $invoiceAddressSelect.append($('<option>', invoiceAddressOption));
    }

    if (deliveryAddressDetailsContent) {
      $deliveryAddressDetails.html(deliveryAddressDetailsContent);
    }

    if (invoiceAddressDetailsContent) {
      $invoiceAddressDetails.html(invoiceAddressDetailsContent);
    }

    this._showAddressesBlock();
  }

  /**
   * Shows addresses block
   *
   * @private
   */
  _showAddressesBlock() {
    $(createOrderPageMap.addressesBlock).removeClass('d-none');
  }
}
