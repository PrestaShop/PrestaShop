import type {Page} from 'playwright';

// Import pages
import BOBasePage from '@pages/BO/BObasePage';

/**
 * Stocks tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class StocksTab extends BOBasePage {
  private readonly stocksTabLink: string;

  private readonly productQuantityInput: string;

  private readonly productMinimumQuantityInput: string;

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
  async setProductStock(page:Page, productData:object): Promise<void> {
    await this.waitForSelectorAndClick(page, this.stocksTabLink);
    await this.setValue(page, this.productQuantityInput, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
  }
}

export default new StocksTab();
