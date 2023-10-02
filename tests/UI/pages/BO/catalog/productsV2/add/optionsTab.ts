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

  private readonly productOptionVisibilityRadio: (option: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on options tab
   */
  constructor() {
    super();

    // Selectors in options tab
    this.productOptionVisibilityRadio = (option: number) => `product_options_visibility_visibility_${option}`;
    this.productVisibilityRadio = 'input[name="product[options][visibility][visibility]"]';
    this.productAvailableForOrderRadio = '#product_options_visibility_available_for_order_1';
    this.productShowPricesRadio = '#product_options_visibility_show_price_1';
    this.productOnlineOnlyRadio = '#product_options_visibility_online_only_1';
  }

  /*
  Methods
   */
  async setVisibility(page: Page, visibility: string): Promise<void> {
    switch (visibility) {
      case 'everywhere':
        await this.setChecked(page, this.productOptionVisibilityRadio(0));
        break;
      case 'catalog_only':
        await this.setChecked(page, this.productOptionVisibilityRadio(1));
        break;
      case 'search_only':
        await this.setChecked(page, this.productOptionVisibilityRadio(2));
        break;
      case 'nowhere':
        await this.setChecked(page, this.productOptionVisibilityRadio(3));
        break;
      default:
        throw new Error(`Option ${visibility} was not found`);
    }
  }


  /**
   * Returns the value of a form element
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
