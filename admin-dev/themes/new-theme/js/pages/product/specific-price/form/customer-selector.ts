/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    const customerSearchInput = this.initCustomerSearchInput();

    if (shopIdSelect !== null) {
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
