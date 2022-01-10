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
import specificPriceMap from '@pages/specific-price/specific-price-map';

/**
 * Disables price input once "leave initial price" is checked
 */
export default class PriceInputToggle {
  public constructor() {
    this.init();
  }

  private init(): void {
    const initialPriceCheckbox = document.querySelector(specificPriceMap.leaveInitialPriceCheckbox) as HTMLInputElement;
    this.toggle(initialPriceCheckbox.checked);

    initialPriceCheckbox.addEventListener('change', () => this.toggle(initialPriceCheckbox.checked));
  }

  private toggle(disable: boolean): void {
    const priceInput = document.querySelector(specificPriceMap.priceInput) as HTMLInputElement;
    priceInput.classList.toggle('disabled', disable);
  }
}
