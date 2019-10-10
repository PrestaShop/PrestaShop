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
 * Manupulates UI of Shipping block in Order creation page
 */
export default class ShippingRenderer {
  constructor() {
    this.$container = $(createOrderPageMap.shippingBlock);
    this.$form = $(createOrderPageMap.shippingFormBlock);
    this.$noCarrierBlock = $(createOrderPageMap.noCarrierBlock);
  }

  render(shipping) {
    if (shipping !== null && shipping.length !== 0) {
      this._hideNoCarrierBlock();
      this._showContainer();
      this._renderForm(shipping);
    } else {
      this._showContainer();
      this._hideForm();
      this._showNoCarrierBlock();
    }
  }

  _renderForm(shipping) {
    this._renderDeliveryOptions(shipping.deliveryOptions, shipping.carrierId);
    //@todo: render total
    //@todo: render switch
    this._showForm();
  }

  _renderDeliveryOptions(deliveryOptions, selectedVal) {
    const $deliveryOptionSelect = $(createOrderPageMap.deliveryOptionSelect);
    $deliveryOptionSelect.empty();

    for (const key in Object.keys(deliveryOptions)) {
      const option = deliveryOptions[key];

      const deliveryOption = {
        value: option.carrierId,
        text: `${option.carrierName} - ${option.carrierDelay}`,
      };

      if (selectedVal === deliveryOption.value) {
        deliveryOption.selected = 'selected';
      }

      $deliveryOptionSelect.append($('<option>', deliveryOption));
    }
  }

  _showContainer() {
    this.$container.removeClass('d-none');
  }

  _hideContainer() {
    this.$container.addClass('d-none');
  }

  _showForm() {
    this.$form.removeClass('d-none');
  }

  _hideForm() {
    this.$form.addClass('d-none');
  }

  _showNoCarrierBlock() {
    this.$noCarrierBlock.removeClass('d-none');
  }

  _hideNoCarrierBlock() {
    this.$noCarrierBlock.addClass('d-none');
  }
}
