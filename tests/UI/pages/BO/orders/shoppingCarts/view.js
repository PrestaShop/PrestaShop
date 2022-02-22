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

    // Order Information Block
    this.orderInformationBlock = '#main-div div[data-role=\'order-information\']';
    this.orderInformationBlockBody = `${this.orderInformationBlock} .card-body`;

    // Cart Summary Block
    this.cartSummaryBlock = '#main-div div[data-role=\'cart-summary\']';
    this.cartSummaryBlockBody = `${this.cartSummaryBlock} .card-body`;
    this.cartSummaryTable = `${this.cartSummaryBlockBody} .table`;
    this.cartSummaryTableBody = `${this.cartSummaryTable} tbody`;
    this.cartSummaryTableRow = row => `${this.cartSummaryTableBody} tr:nth-child(${row})`;
    this.cartSummaryTableColumn = (column, row) => `${this.cartSummaryTableRow(row)} td:nth-child(${column})`;
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

  /**
   * Get Order information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderInformation(page) {
    return this.getTextContent(page, this.orderInformationBlockBody);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param column {Number} Column on table
   * @param row {Number} Row on table
   * @returns {Promise<string>}
   */
  async getCartSummary(page, column, row) {
    return this.getTextContent(page, this.cartSummaryTableColumn(column, row));
  }
}

module.exports = new ViewShoppingCarts();
