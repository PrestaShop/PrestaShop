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

import createOrderMap from './create-order-map';
import Router from '../../../components/router';

const $ = window.$;

/**
 * Renders Delivery & Invoice addresses select
 */
export default class AddressesRenderer {
  constructor() {
    this.router = new Router();
  }
  /**
   * @param {Array} addresses
   */
  render(addresses) {
    this._cleanAddresses();
    if (addresses.length === 0) {
      this._hideAddressesContent();
      this._showEmptyAddressesWarning();
      this._showAddressesBlock();

      return;
    }

    this._showAddressesContent();
    this._hideEmptyAddressesWarning();

    for (const key in addresses) {
      const address = addresses[key];

      this._renderDeliveryAddress(address);
      this._renderInvoiceAddress(address);
    }

    // @todo: router.generate(admin_addresses_create) when pr #15300 merged
    $(createOrderMap.addressAddBtn).prop('href', this.router.generate('admin_addresses_create'));
    this._showAddressesBlock();
  }

  /**
   * Renders delivery address content
   *
   * @param address
   *
   * @private
   */
  _renderDeliveryAddress(address) {
    const deliveryAddressOption = {
      value: address.addressId,
      text: address.alias,
    };

    if (address.delivery) {
      $(createOrderMap.deliveryAddressDetails).html(address.formattedAddress);
      deliveryAddressOption.selected = 'selected';
    }

    $(createOrderMap.deliveryAddressSelect).append($('<option>', deliveryAddressOption));
    // @todo: router.generate(admin_addresses_edit) when pr #15300 merged
    $(createOrderMap.deliveryAddressEditBtn).prop('href', this.router.generate('admin_addresses_edit', {
      addressId: address.addressId,
    }));
  }

  /**
   * Renders invoice address content
   *
   * @param address
   *
   * @private
   */
  _renderInvoiceAddress(address) {
    const invoiceAddressOption = {
      value: address.addressId,
      text: address.alias,
    };

    if (address.invoice) {
      $(createOrderMap.invoiceAddressDetails).html(address.formattedAddress);
      invoiceAddressOption.selected = 'selected';
    }

    $(createOrderMap.invoiceAddressSelect).append($('<option>', invoiceAddressOption));
    // @todo: router.generate(admin_addresses_edit) when pr #15300 merged
    $(createOrderMap.invoiceAddressEditBtn).prop('href', this.router.generate('admin_addresses_edit', {
      addressId: address.addressId,
    }));
  }

  /**
   * Shows addresses block
   *
   * @private
   */
  _showAddressesBlock() {
    $(createOrderMap.addressesBlock).removeClass('d-none');
  }

  /**
   * Empties addresses content
   *
   * @private
   */
  _cleanAddresses() {
    $(createOrderMap.deliveryAddressDetails).empty();
    $(createOrderMap.deliveryAddressSelect).empty();
    $(createOrderMap.invoiceAddressDetails).empty();
    $(createOrderMap.invoiceAddressSelect).empty();
  }

  /**
   * Shows addresses content and hides warning
   *
   * @private
   */
  _showAddressesContent() {
    $(createOrderMap.addressesContent).removeClass('d-none');
    $(createOrderMap.addressesWarning).addClass('d-none');
  }

  /**
   * Hides addresses content and shows warning
   *
   * @private
   */
  _hideAddressesContent() {
    $(createOrderMap.addressesContent).addClass('d-none');
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Shows warning empty addresses warning
   *
   * @private
   */
  _showEmptyAddressesWarning() {
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Hides empty addresses warning
   *
   * @private
   */
  _hideEmptyAddressesWarning() {
    $(createOrderMap.addressesWarning).addClass('d-none');
  }
}
