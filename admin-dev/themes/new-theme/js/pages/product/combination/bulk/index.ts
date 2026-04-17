/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ImageSelector from '@pages/product/combination/form/image-selector';
import QuantityModeSwitcher from '@pages/product/combination/quantity-mode-switcher';
import CombinationBulkMap from '@pages/product/combination/bulk/combination-bulk-map';
import FormFieldToggler from '@components/form/form-field-toggler';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TranslatableInput',
    'EventEmitter',
    'DeltaQuantityInput',
    'DisablingSwitch',
    'ModifyAllShopsCheckbox',
  ]);
  new ImageSelector();
  new QuantityModeSwitcher();

  // DisablingSwitch is already used in low_stock_alert field to decide if form field is intended to be updated or not
  // so we toggle low_stock_threshold availability by low_stock_alert field here by initiating toggler manually.
  new FormFieldToggler({
    disablingInputSelector: CombinationBulkMap.lowStockAlertSwitch,
    targetSelector: CombinationBulkMap.lowStockThresholdValueInput,
  });

  // When impact input tax excluded is enabled/disabled so should the one with tax, and vice versa
  syncSwitches(CombinationBulkMap.priceTaxIncludedSwitch, CombinationBulkMap.priceTaxExcludedSwitch);

  // Handle update automatically values between price tax included and excluded
  const taxRateContainer = document.querySelector<HTMLInputElement>(CombinationBulkMap.taxRateContainer)!;
  const priceTaxExcludedInput = document.querySelector<HTMLInputElement>(CombinationBulkMap.priceTaxExcludedInput)!;
  const priceTaxIncludedInput = document.querySelector<HTMLInputElement>(CombinationBulkMap.priceTaxIncludedInput)!;
  const taxRatio: number = 1 + parseFloat(taxRateContainer?.dataset.rate ?? '0');

  priceTaxExcludedInput.addEventListener('keyup', () => {
    let value;

    if (priceTaxExcludedInput.value === '') {
      value = 0;
    } else {
      value = parseFloat(priceTaxExcludedInput.value);
    }
    priceTaxIncludedInput.value = (value * taxRatio).toString();
  });

  priceTaxIncludedInput.addEventListener('keyup', () => {
    let value;

    if (priceTaxIncludedInput.value === '') {
      value = 0;
    } else {
      value = parseFloat(priceTaxIncludedInput.value);
    }
    priceTaxExcludedInput.value = (value / taxRatio).toString();
  });

  function syncSwitches(switchSelectorA: string, switchSelectorB: string): void {
    forceSwitchValueToOtherSwitch(switchSelectorA, switchSelectorB);
    forceSwitchValueToOtherSwitch(switchSelectorB, switchSelectorA);
    function forceSwitchValueToOtherSwitch(switchA: string, switchB: string): void {
      document.querySelectorAll<HTMLInputElement>(switchA).forEach((input: HTMLInputElement) => {
        input.addEventListener('change', () => {
          let inputToCheckSelector = `${switchB}[value="0"]`;
          let inputToUncheckSelector = `${switchB}[value="1"]`;

          if (input.value === '1') {
            inputToCheckSelector = `${switchB}[value="1"]`;
            inputToUncheckSelector = `${switchB}[value="0"]`;
          }

          const inputToCheck = document.querySelector<HTMLInputElement>(inputToCheckSelector);
          const inputToUncheck = document.querySelector<HTMLInputElement>(inputToUncheckSelector);

          if (inputToCheck && !inputToCheck.checked) {
            inputToCheck.checked = true;
            inputToCheck.dispatchEvent(new Event('change'));
          }

          if (inputToUncheck && inputToUncheck.checked) {
            inputToUncheck.checked = false;
            inputToUncheck.dispatchEvent(new Event('change'));
          }
        });
      });
    }
  }
});
