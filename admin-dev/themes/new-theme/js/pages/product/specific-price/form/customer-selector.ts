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
import SpecificPriceMap from '@pages/product/specific-price/specific-price-map';
import SpecificPriceEventMap from '@pages/product/specific-price/specific-price-event-map';
import CustomerSearchInput from '@components/form/customer-search-input';

export default class CustomerSelector {
  constructor() {
    this.init();
  }

  private init(): void {
    // This check is here for when the multishop is not enabled.
    // The selector returned by the this.getShopIdSelect does not exist when multishop is not enabled.
    const shopIdSelect = this.getShopIdSelect();

    if (shopIdSelect !== null) {
      const customerSearchInput = this.initCustomerSearchInput();
      // clear selected customers whenever shop is changed, because customers may differ between shops
      shopIdSelect.addEventListener('change', () => customerSearchInput.setValues([]));
    }
  }

  private initCustomerSearchInput(): CustomerSearchInput {
    return new CustomerSearchInput(
      SpecificPriceMap.customerSearchContainer,
      SpecificPriceMap.customerItem,
      () => Number(this.getShopIdSelect()?.value) ?? null,
      SpecificPriceEventMap.switchCustomer,
    );
  }

  /**
   * ShopIdSelector might not exist in some forms, and it is legit. In that case it returns null.
   *
   * @private
   */
  private getShopIdSelect(): HTMLSelectElement|null {
    return <HTMLSelectElement> document.querySelector(
      `${SpecificPriceMap.formContainer} ${SpecificPriceMap.shopIdSelect}`,
    );
  }
}
