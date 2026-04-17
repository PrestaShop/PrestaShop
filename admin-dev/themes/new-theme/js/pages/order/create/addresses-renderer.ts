/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import createOrderMap from './create-order-map';
import Router from '../../../components/router';

const {$} = window;

/**
 * Renders Delivery & Invoice addresses select
 */
export default class AddressesRenderer {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  /**
   * @param {Array} addresses
   * @param {int} cartId
   */
  render(addresses: Record<string, any>, cartId: number): void {
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
  private renderDeliveryAddress(
    address: Record<string, any>,
    cartId: number,
  ): void {
    const deliveryAddressOption = {
      value: address.addressId,
      text: address.alias,
      selected: false,
    };

    if (address.delivery) {
      $(createOrderMap.deliveryAddressDetails).html(address.formattedAddress);
      deliveryAddressOption.selected = true;
      $(createOrderMap.deliveryAddressEditBtn).prop(
        'href',
        this.router.generate('admin_cart_addresses_edit', {
          addressId: address.addressId,
          cartId,
          addressType: 'delivery',
          liteDisplaying: 1,
          submitFormAjax: 1,
        }),
      );
    }

    $(createOrderMap.deliveryAddressSelect).append(
      $('<option>', deliveryAddressOption),
    );
  }

  /**
   * Renders invoice address content
   *
   * @param address
   * @param cartId
   *
   * @private
   */
  private renderInvoiceAddress(
    address: Record<string, any>,
    cartId: number,
  ): void {
    const invoiceAddressOption = {
      value: address.addressId,
      text: address.alias,
      selected: false,
    };

    if (address.invoice) {
      $(createOrderMap.invoiceAddressDetails).html(address.formattedAddress);
      invoiceAddressOption.selected = true;
      $(createOrderMap.invoiceAddressEditBtn).prop(
        'href',
        this.router.generate('admin_cart_addresses_edit', {
          addressId: address.addressId,
          cartId,
          addressType: 'invoice',
          liteDisplaying: 1,
          submitFormAjax: 1,
        }),
      );
    }

    $(createOrderMap.invoiceAddressSelect).append(
      $('<option>', invoiceAddressOption),
    );
  }

  /**
   * Shows addresses block
   *
   * @private
   */
  private showAddressesBlock(): void {
    $(createOrderMap.addressesBlock).removeClass('d-none');
  }

  /**
   * Empties addresses content
   *
   * @private
   */
  private cleanAddresses(): void {
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
  private showAddressesContent(): void {
    $(createOrderMap.addressesContent).removeClass('d-none');
    $(createOrderMap.addressesWarning).addClass('d-none');
  }

  /**
   * Hides addresses content and shows warning
   *
   * @private
   */
  private hideAddressesContent(): void {
    $(createOrderMap.addressesContent).addClass('d-none');
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Shows warning empty addresses warning
   *
   * @private
   */
  private showEmptyAddressesWarning(): void {
    $(createOrderMap.addressesWarning).removeClass('d-none');
  }

  /**
   * Hides empty addresses warning
   *
   * @private
   */
  private hideEmptyAddressesWarning(): void {
    $(createOrderMap.addressesWarning).addClass('d-none');
  }
}
