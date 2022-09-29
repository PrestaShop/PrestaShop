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

import ImageSelector from '@pages/product/combination/form/image-selector';
import QuantityModeSwitcher from '@pages/product/combination/quantity-mode-switcher';
import CombinationFormMapping from '@pages/product/combination/form/combination-form-mapping';

// @ts-ignore
const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'EventEmitter',
    'DeltaQuantityInput',
    'DisablingSwitch',
  ]);
  new ImageSelector();
  new QuantityModeSwitcher();

  const priceExcludedTaxId = CombinationFormMapping['price.excludedTaxId'];
  const priceIncludedTaxId = CombinationFormMapping['price.includedTaxId'];
  const vatRate = CombinationFormMapping['price.vatRateFormId'];
  const priceDiv: HTMLDivElement = document.getElementById(vatRate) as HTMLDivElement;
  const priceExcludedTaxInput: HTMLInputElement = document.getElementById(priceExcludedTaxId) as HTMLInputElement;
  const priceIncludedTaxInput: HTMLInputElement = document.getElementById(priceIncludedTaxId) as HTMLInputElement;
  const rate: number = 1 + parseFloat((priceDiv.dataset.rate as string));

  priceExcludedTaxInput.addEventListener('keyup', () => {
    let value;

    if (priceExcludedTaxInput.value === '') {
      value = 0;
    } else {
      value = parseFloat(priceExcludedTaxInput.value);
    }
    priceIncludedTaxInput.value = (value * rate).toString();
  });

  priceIncludedTaxInput.addEventListener('keyup', () => {
    let value;

    if (priceIncludedTaxInput.value === '') {
      value = 0;
    } else {
      value = parseFloat(priceIncludedTaxInput.value);
    }
    priceExcludedTaxInput.value = (value / rate).toString();
  });
});
