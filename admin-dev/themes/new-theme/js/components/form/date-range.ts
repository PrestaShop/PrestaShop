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

const {$} = window;

export default class DateRange {
  constructor(containerSelector: string) {
    this.initListeners(containerSelector);
  }

  initListeners(containerSelector: string): void {
    const checkbox = document.querySelector(containerSelector + ' #specific_price_date_range_unlimited') as HTMLInputElement;
    const startDateField = document.querySelector(containerSelector + ' #specific_price_date_range_from') as HTMLInputElement;
    const endDateField = document.querySelector(containerSelector + ' #specific_price_date_range_to') as HTMLInputElement;

    if (checkbox === null || startDateField === null || endDateField === null) return;

    checkbox.addEventListener('change', (e) => {
      if (e.currentTarget === null) return;

      const {checked} = e.currentTarget as HTMLInputElement;

      if (endDateField === null || startDateField === null) return;

      if (checked) {
        endDateField.value = '';
        endDateField.disabled = true;
      } else {
        endDateField.value = startDateField.value;
        endDateField.disabled = false;
      }
    });

    endDateField.addEventListener('click', (e) => {
      if (e.currentTarget === null) return;

      const {value} = e.currentTarget as HTMLInputElement;

      if (value === '') {
        checkbox.checked = false;
      }
    });
  }
}
