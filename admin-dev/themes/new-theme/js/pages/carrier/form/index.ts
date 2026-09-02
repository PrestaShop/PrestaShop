/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
