require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Currency extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = global.INSTALL.SHOP_NAME;
    // Selectors
    this.productPrice = '#js-product-list div.product-description span.price';
  }

  /**
   * Check that currency is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCurrencyVisible(page) {
    return this.elementVisible(page, this.currencySelectorDiv, 1000);
  }

  /**
   * Get the product Currency
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductCurrency(page) {
    return this.getTextContent(page, this.productPrice);
  }

  /**
   * Get the product price value
   * @param page
   * @returns {Promise<number>}
   */
  async getProductPrice(page) {
    return this.getPriceFromText(page, this.productPrice);
  }
}

module.exports = new Currency();
