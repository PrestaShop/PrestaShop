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

import ChoiceTable from '@js/components/choice-table';
import NavbarHandler from '@js/components/navbar-handler';
import CarrierFormManager from '@pages/carrier/form/carrier-form-manager';
import CarrierRanges from '@pages/carrier/form/carrier-range-modal';
import CarrierFormMap from '@pages/carrier/form/carrier-form-map';
import NavbarFormErrorHandler from '@js/components/navbar-form-error-handler';

$(() => {
  // Initialize components
  window.prestashop.component.initComponents([
    'TranslatableInput',
    'EventEmitter',
    'MultipleZoneChoice',
    'ChoiceTable',
  ]);

  // Initialize the ranges selection modal
  new CarrierRanges(window.prestashop.instance.eventEmitter);

  new ChoiceTable();

  // Initialize the carrier form manager
  new CarrierFormManager(window.prestashop.instance.eventEmitter);

  const carrierForm = document.querySelector(CarrierFormMap.form);

  if (carrierForm instanceof HTMLElement) {
    new NavbarFormErrorHandler({
      form: carrierForm,
      navbarHandler: new NavbarHandler($(CarrierFormMap.navigationBar)),
    });
  }
});
