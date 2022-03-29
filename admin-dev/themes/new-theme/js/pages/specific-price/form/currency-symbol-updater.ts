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
import SpecificPriceMap from '@pages/specific-price/specific-price-map';

/**
 * Change symbol when the currency select is changed
 */
export default class CurrencySymbolUpdater {
  public constructor() {
    this.init();
  }

  private init(): void {
    const selectCurrency = document.querySelector(SpecificPriceMap.currencyId) as HTMLSelectElement;
    this.change(CurrencySymbolUpdater.getSymbol());

    selectCurrency.addEventListener('change', () => this.change(CurrencySymbolUpdater.getSymbol()));
  }

  private change(symbol: string): void {
    if (!symbol) {
      return;
    }

    // Specific Price
    const priceSymbol = document.querySelector(SpecificPriceMap.priceSymbol) as HTMLInputElement;
    priceSymbol.innerHTML = symbol;

    // Reduction Amount
    const reductionTypeSelect = document.querySelector(SpecificPriceMap.reductionTypeSelect) as HTMLSelectElement;

    if (reductionTypeSelect.options[reductionTypeSelect.selectedIndex].value === 'amount') {
      const reductionTypeAmountSymbol = document.querySelector(SpecificPriceMap.reductionTypeAmountSymbol) as HTMLInputElement;
      reductionTypeAmountSymbol.innerHTML = symbol;
    }
  }

  public static getSymbol(): string {
    const select = document.querySelector(SpecificPriceMap.currencyId) as HTMLSelectElement;
    const selectItem = select.item(select.selectedIndex);

    if (!selectItem) {
      return '';
    }
    return selectItem.getAttribute('symbol') ?? '';
  }
}
