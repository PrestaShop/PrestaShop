// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Options tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class OptionsTab extends BOBasePage {
  private readonly productVisibilityRadio: string;

  private readonly productAvailableForOrderRadio: string;

  private readonly productShowPricesRadio: string;

  private readonly productOnlineOnlyRadio: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Shipping tab
   */
  constructor() {
    super();

    // Selectors in stocks tab
    this.productVisibilityRadio = 'input[name="product[options][visibility][visibility]"]';
    this.productAvailableForOrderRadio = '#product_options_visibility_available_for_order_1';
    this.productShowPricesRadio = '#product_options_visibility_show_price_1';
    this.productOnlineOnlyRadio = '#product_options_visibility_online_only_1';
  }

  /*
  Methods
   */

  /**
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'available_for_order':
        return (await this.isChecked(page, this.productAvailableForOrderRadio)) ? '1' : '0';
      case 'online_only':
        return (await this.isChecked(page, this.productShowPricesRadio)) ? '1' : '0';
      case 'show_price':
        return (await this.isChecked(page, this.productShowPricesRadio)) ? '1' : '0';
      case 'visibility':
        return this.getAttributeContent(page, `${this.productVisibilityRadio}[checked="checked"]`, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new OptionsTab();
