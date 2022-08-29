require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Description tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DescriptionTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on description tab
   */
  constructor() {
    super();

    // Selectors in description tab
    this.descriptionTabLink = '#product_description-tab-nav';
    this.productSummary = '#product_description_description_short';
    this.productdescription = '#product_description_description';
  }

  /*
  Methods
   */

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param selector {string} Value of selector to use
   * @param value {string} Text to set on tinymce input
   * @returns {Promise<void>}
   */
  async setValueOnTinymceInput(page, selector, value) {
    // Select all
    await page.click(`${selector} .mce-edit-area`, {clickCount: 3});

    // Delete all text
    await page.keyboard.press('Backspace');

    // Fill the text
    await page.keyboard.type(value);
  }

  /**
   * Set product description
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in description form
   * @returns {Promise<void>}
   */
  async setProductDescription(page, productData) {
    await this.waitForSelectorAndClick(page, this.descriptionTabLink);

    await this.setValueOnTinymceInput(page, this.productSummary, productData.summary);
    await this.setValueOnTinymceInput(page, this.productdescription, productData.description);
  }
}

module.exports = new DescriptionTab();
