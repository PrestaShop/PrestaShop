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
    // this.cartSummaryTableColumn = (column, row) => `${this.cartSummaryTableRow(row)} td:nth-child(${column})`;
    this.cartSummaryTableColumn = row => `${this.cartSummaryTableRow(row)} td`;

    // Columns selectors:
    this.cartSummaryTableColumnProductTitle = row => `${this.cartSummaryTableColumn(row)}:nth-child(2)`;
    this.cartSummaryTableColumnProductUnitPrice = row => `${this.cartSummaryTableColumn(row)}:nth-child(3)`;
    this.cartSummaryTableColumnProductQuantity = row => `${this.cartSummaryTableColumn(row)}:nth-child(4)`;
    this.cartSummaryTableColumnProductStockAvailable = row => `${this.cartSummaryTableColumn(row)}:nth-child(5)`;
    this.cartSummaryTableColumnProductTotal = row => `${this.cartSummaryTableColumn(row)}:nth-child(6)`;
    this.cartSummaryTableColumnCostTotalProducts = `${this.cartSummaryTableColumn(2)}:nth-child(2)`;
    this.cartSummaryTableColumnCostTotalShipping = `${this.cartSummaryTableColumn(3)}:nth-child(2)`;
    this.cartSummaryTableColumnTotal = `${this.cartSummaryTableColumn(4)}:nth-child(2)`;
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
   * @param columnName {String} Column on table
   * @param row {Number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumn(page, columnName, row) {
    let columnSelector;

    switch (columnName) {
      case 'product_title':
        columnSelector = this.cartSummaryTableColumnProductTitle(row);
        break;

      case 'product_unit_price':
        columnSelector = this.cartSummaryTableColumnProductUnitPrice(row);
        break;

      case 'product_quantity':
        columnSelector = this.cartSummaryTableColumnProductQuantity(row);
        break;

      case 'product_stock_available':
        columnSelector = this.cartSummaryTableColumnProductStockAvailable(row);
        break;

      case 'product_total':
        columnSelector = this.cartSummaryTableColumnProductTotal(row);
        break;

      case 'total_cost_products':
        columnSelector = this.cartSummaryTableColumnCostTotalProducts;
        break;

      case 'total_cost_shipping':
        columnSelector = this.cartSummaryTableColumnCostTotalShipping;
        break;

      case 'total_cart':
        columnSelector = this.cartSummaryTableColumnTotal;
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get Total price for each column
   * @param page {Page} Browser tab
   * @param columnName {string} Column to get text value
   * @returns {Promise<Number>}
   */
  async getPriceColumnTotal(page, columnName) {
    switch (columnName) {
      case 'total_cost_products':
        return (this.getPriceFromText(page, this.cartSummaryTableColumnCostTotalProducts));
      case 'total_cost_shipping':
        return (this.getPriceFromText(page, this.cartSummaryTableColumnCostTotalShipping));
      case 'total_cart':
        return (this.getPriceFromText(page, this.cartSummaryTableColumnTotal));
      default:
        throw new Error(`Column ${columnName} was not found`);
    }
  }
}

module.exports = new ViewShoppingCarts();
