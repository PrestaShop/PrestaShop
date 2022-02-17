require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');


/**
 * View shopping page, contains functions that can be used on view shopping cart page
 * @class
 * @extends BOBasePage
 */
class ViewShoppingCarts extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view shopping cart page
   */
  constructor() {
    super();

    this.pageTitle = 'View';

    // Selectors
    this.cartSubtitle = '#box-kpi-cart div.subtitle';
    this.cartTotal = '#box-kpi-cart div.value';

    // Customer Block
    this.customerInformationBlock = '#main-div div[data-role=\'customer-information\']';
    this.customerInformationCartBody = `${this.customerInformationBlock} .card-body`;
  }

  /*
  Methods
   */
  /**
   * Get cart ID
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCartId(page) {
    return this.getTextContent(page, this.cartSubtitle);
  }

  /**
   * Get cart Total
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCartTotal(page) {
    return this.getPriceFromText(page, this.cartTotal);
  }

  /**
   * Get Customer Information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCustomerInformation(page) {
    return this.getTextContent(page, this.customerInformationCartBody);
  }
}

module.exports = new ViewShoppingCarts();
