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
    this.customerInformationBlock = '#main-div div[data-role="customer-information"]';
    this.customerInformationCartBody = `${this.customerInformationBlock} .card-body`;

    // Order Information Block
    this.orderInformationBlock = '#main-div div[data-role="order-information"]';
    this.orderInformationBlockBody = `${this.orderInformationBlock} .card-body`;

    // Cart Summary Block
    this.cartSummaryBlock = '#main-div div[data-role="cart-summary"]';
    this.cartSummaryBlockBody = `${this.cartSummaryBlock} .card-body`;
    this.cartSummaryTable = `${this.cartSummaryBlockBody} .table`;
    this.cartSummaryTableBody = `${this.cartSummaryTable} tbody`;
    this.cartSummaryTableRow = row => `${this.cartSummaryTableBody} tr:nth-child(${row})`;
    this.cartSummaryTableColumn = (row, column) => `${this.cartSummaryTableRow(row)} td:nth-child(${column})`;
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
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param columnName {string} Column on table
   * @param row {number} Row on table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row = 1) {
    let columnSelector;

    switch (columnName) {
      case 'image':
        columnSelector = `${this.cartSummaryTableColumn(row, 1)} img`;
        break;

      case 'title':
        columnSelector = this.cartSummaryTableColumn(row, 2);
        break;

      case 'unit_price':
        columnSelector = this.cartSummaryTableColumn(row, 3);
        break;

      case 'quantity':
        columnSelector = this.cartSummaryTableColumn(row, 4);
        break;

      case 'stock_available':
        columnSelector = this.cartSummaryTableColumn(row, 5);
        break;

      case 'total':
        columnSelector = this.cartSummaryTableColumn(row, 6);
        break;

      case 'total_cost_products':
        columnSelector = this.cartSummaryTableColumn(row + 1, 2);
        break;

      case 'total_cost_shipping':
        columnSelector = this.cartSummaryTableColumn(row + 2, 2);
        break;

      case 'total_cart':
        columnSelector = this.cartSummaryTableColumn(row + 3, 2);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if (columnName === 'image') {
      return this.getAttributeContent(page, columnSelector, 'src');
    }

    return this.getTextContent(page, columnSelector);
  }
}

module.exports = new ViewShoppingCarts();
