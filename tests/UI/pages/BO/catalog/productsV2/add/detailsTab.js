require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Details tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DetailsTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on details tab
   */
  constructor() {
    super();

    // Selectors in details tab
    this.detailsTabLink = '#product_specifications-tab-nav';
    this.productReferenceInput = '#product_specifications_references_reference';
  }

  /*
  Methods
   */

  /**
   * Set product details
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in details form
   * @returns {Promise<void>}
   */
  async setProductDetails(page, productData) {
    await this.waitForSelectorAndClick(page, this.detailsTabLink);
    await this.setValue(page, this.productReferenceInput, productData.reference);
  }
}

module.exports = new DetailsTab();
