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

import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';
import ComponentsMap from '@components/components-map';

$(() => {
  // Initialize components
  window.prestashop.component.initComponents([
   'TranslatableInput',
   'EventEmitter',
   'CarrierRanges',
   'MultipleZoneChoice',
  ]);
  // Retrieve the event emitter
  const eventEmitter = window.prestashop.instance.eventEmitter;

  // -- Carrier ranges --
  // Retrieve the carrier shipping method select element and the event emitter
  const $shippingMethod = $('#carrier_shipping_settings_shipping_method');
  const shippingMethodsUnits = $shippingMethod.data('units');

  // Emit a carrier shipping method change event with the symbol to use for the ranges
  function updateRangeUnits() {
    const value = <number> $shippingMethod.find('input[name="carrier[shipping_settings][shipping_method]"]:checked').val();
    eventEmitter.emit(CarrierFormEventMap.shippingMethodChange, shippingMethodsUnits[value] || '');
  }

  // Listen to the change event on the carrier shipping method select element
  // and emit a carrier shipping method change event with the symbol to use for the ranges
  $shippingMethod.on('change', () => {
    updateRangeUnits();
  });
  updateRangeUnits();
});
