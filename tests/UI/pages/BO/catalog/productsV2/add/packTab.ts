// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Pack tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends CommonPage
 */
export default class PackTab extends BOBasePage {
  private readonly packTabLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pack tab
   */
  constructor() {
    super();

    // Selectors in pack tab
    this.packTabLink = '#product_stock-tab-nav';
  }

  /*
 Methods
  */

  /**
   * Add combination
   * @param page {Page} Browser tab
   * @param packData {object} Data of the pack
   * @returns {Promise<void>}
   */
  async setPackOfProducts(page, packData): Promise<void> {
    await page.type(this.searchAttributesButton, packData);
  }
}

module.exports = PackTab;
