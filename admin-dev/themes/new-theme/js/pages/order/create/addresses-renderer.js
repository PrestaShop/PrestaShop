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

import createOrderMap from './create-order-map';
import Router from '../../../components/router';

const {$} = window;

/**
 * Renders Delivery & Invoice addresses select
 */
export default class AddressesRenderer {
  constructor() {
    this.router = new Router();
  }

  /**
   * @param {Array} addresses
   * @param {int} cartId
   */
  render(addresses, cartId) {
    this.cleanAddresses();
    if (addresses.length === 0) {
      this.hideAddressesContent();
      this.showEmptyAddressesWarning();
      this.showAddressesBlock();

      return;
    }

    this.showAddressesContent();
    this.hideEmptyAddressesWarning();

    Object.values(addresses).forEach((address) => {
      this.renderDeliveryAddress(address, cartId);
      this.renderInvoiceAddress(address, cartId);
    });

    this.showAddressesBlock();
  }

  /**
   * Renders delivery address content
   *
   * @param address
   * @param cartId
   *
   * @private
   */
  renderDeliveryAddress(address, cartId) {
    const deliveryAddressOption = {
      value: address.addressId,
      text: address.alias,
    };

    if (address.delivery) {
      $(createOrderMap.deliveryAddressDetails).html(address.formattedAddress);
      deliveryAddressOption.selected = 'selected';
      $(createOrderMap.deliveryAddressEditBtn).prop('href', this.router.generate('admin_cart_addresses_edit', {
        addressId: address.addressId,
        cartId,
        addressType: 'delivery',
        liteDisplaying: 1,
        submitFormAjax: 1,
      }));
    }

    $(createOrderMap.deliveryAddressSelect).append($('<option>', deliveryAddressOption));
  }

  /**
   * Renders invoice address content
   *
   * @param address
   * @param cartId
   *
   * @private
   */
  renderInvoiceAddress(address, cartId) {
    const invoiceAddressOption = {
      value: address.addressId,
      text: address.alias,
    };

    if (address.invoice) {
      $(createOrderMap.invoiceAddressDetails).html(address.formattedAddress);
      invoiceAddressOption.selected = 'selected';
      $(createOrderMap.invoiceAddressEditBtn).prop('href', this.router.generate('admin_cart_addresses_edit', {
        addressId: address.addressId,
        cartId,
        addressType: 'invoice',
        liteDisplaying: 1,
        submitFormAjax: 1,
      }));
    }

    $(createOrderMap.invoiceAddressSelect).append($('<option>', invoiceAddressOption));
  }

  /**
   * Shows addresses block
   *
   * @private
   */
  showAddressesBlock() {
    $(createOrderMap.addressesBlock).removeClass('d-none');
  }

  /**
   * Empties addresses content
   *
   * @private
   */
  cleanAddresses() {
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
  showAddressesContent() {
    $(createOrderMap.addressesContent).removeClass('d-none');
    $(createOrderMap.addressesWarning).addClass('d-none');
  }

  /**
   * Hides addresses content and shows warning
   *
   * @private
   */
  hideAddressesContent() {
    $(createOrderMap.addressesContent).addClass('d-none');
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Shows warning empty addresses warning
   *
   * @private
   */
  showEmptyAddressesWarning() {
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Hides empty addresses warning
   *
   * @private
   */
  hideEmptyAddressesWarning() {
    $(createOrderMap.addressesWarning).addClass('d-none');
  }
}
