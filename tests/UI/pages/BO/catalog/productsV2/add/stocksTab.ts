// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import type ProductData from '@data/faker/product';

import type {Page} from 'playwright';

/**
 * Stocks tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class StocksTab extends BOBasePage {
  private readonly stocksTabLink: string;

  private readonly productQuantityInput: string;

  private readonly productMinimumQuantityInput: string;

  private readonly productLowStockThresholdCheckbox: string;

  private readonly productLowStockThresholdInput: string;

  private readonly productLabelAvailableNowInput: string;

  private readonly productLabelAvailableLaterInput: string;

  private readonly productAvailableDateInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on stocks tab
   */
  constructor() {
    super();

    // Selectors in stocks tab
    this.stocksTabLink = '#product_stock-tab-nav';
    this.productQuantityInput = '#product_stock_quantities_delta_quantity_delta';
    this.productMinimumQuantityInput = '#product_stock_quantities_minimal_quantity';
    this.productLowStockThresholdCheckbox = '#product_stock_options_disabling_switch_low_stock_threshold_1';
    this.productLowStockThresholdInput = '#product_stock_options_low_stock_threshold';
    this.productLabelAvailableNowInput = '#product_stock_availability_available_now_label';
    this.productLabelAvailableLaterInput = '#product_stock_availability_available_later_label';
    this.productAvailableDateInput = '#product_stock_availability_available_date';
  }

  /*
  Methods
   */

  /**
   * Set product stock
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in stock form
   * @returns {Promise<void>}
   */
  async setProductStock(page:Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.stocksTabLink);
    await this.setValue(page, this.productQuantityInput, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
  }

  /**
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    switch (inputName) {
      case 'available_date':
        return this.getAttributeContent(page, this.productAvailableDateInput, 'value');
      case 'available_later':
        return this.getAttributeContent(page, `${this.productLabelAvailableLaterInput}_${languageId}`, 'value');
      case 'available_now':
        return this.getAttributeContent(page, `${this.productLabelAvailableNowInput}_${languageId}`, 'value');
      case 'low_stock_threshold':
        return this.getAttributeContent(page, this.productLowStockThresholdInput, 'value');
      case 'low_stock_threshold_enabled':
        return (await this.isChecked(page, this.productLowStockThresholdCheckbox)) ? '1' : '0';
      case 'minimal_quantity':
        return this.getAttributeContent(page, this.productMinimumQuantityInput, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new StocksTab();
