import BOBasePage from '@pages/BO/BObasePage';

import type {Frame, Page} from 'playwright';

/**
 * View shopping page, contains functions that can be used on view shopping cart page
 * @class
 * @extends BOBasePage
 */
class ViewShoppingCarts extends BOBasePage {
  public readonly pageTitle: (cardID: string) => string;

  private readonly cartSubtitle: string;

  private readonly cartTotal: string;

  private readonly customerInformationBlock: string;

  private readonly customerInformationCartBody: string;

  private readonly orderInformationBlock: string;

  private readonly orderInformationBlockBody: string;

  private readonly orderInformationButtonCreateOrder: string;

  private readonly orderInformationLinkOrder: string;

  private readonly cartSummaryBlock: string;

  private readonly cartSummaryBlockBody: string;

  private readonly cartSummaryTable: string;

  private readonly cartSummaryTableBody: string;

  private readonly cartSummaryTableRow: (row: number) => string;

  private readonly cartSummaryTableColumn: (row: number, column: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view shopping cart page
   */
  constructor() {
    super();

    this.pageTitle = (cartID: string) => `Cart #${cartID} • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.cartSubtitle = '#box-kpi-cart div.subtitle';
    this.cartTotal = '#box-kpi-cart div.value';

    // Customer Block
    this.customerInformationBlock = '#main-div div[data-role="customer-information"]';
    this.customerInformationCartBody = `${this.customerInformationBlock} .card-body`;

    // Order Information Block
    this.orderInformationBlock = '#main-div div[data-role="order-information"]';
    this.orderInformationBlockBody = `${this.orderInformationBlock} .card-body`;
    this.orderInformationButtonCreateOrder = `${this.orderInformationBlockBody} #create-order-from-cart`;
    this.orderInformationLinkOrder = `${this.orderInformationBlockBody} h2 a`;

    // Cart Summary Block
    this.cartSummaryBlock = '#main-div div[data-role="cart-summary"]';
    this.cartSummaryBlockBody = `${this.cartSummaryBlock} .card-body`;
    this.cartSummaryTable = `${this.cartSummaryBlockBody} .table`;
    this.cartSummaryTableBody = `${this.cartSummaryTable} tbody`;
    this.cartSummaryTableRow = (row: number) => `${this.cartSummaryTableBody} tr:nth-child(${row})`;
    this.cartSummaryTableColumn = (row: number, column: number) => `${this.cartSummaryTableRow(row)} td:nth-child(${column})`;
  }

  /*
  Methods
   */
  /**
   * Get cart ID
   * @param page {Page|Frame} Browser tab
   * @returns {Promise<string>}
   */
  async getCartId(page: Frame|Page): Promise<string> {
    return this.getTextContent(page, this.cartSubtitle);
  }

  /**
   * Get cart Total
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCartTotal(page: Frame|Page): Promise<number> {
    return this.getPriceFromText(page, this.cartTotal);
  }

  /**
   * Get Customer Information
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCustomerInformation(page: Frame|Page): Promise<string> {
    return this.getTextContent(page, this.customerInformationCartBody);
  }

  /**
   * Get Order information
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderInformation(page: Frame|Page): Promise<string> {
    return this.getTextContent(page, this.orderInformationBlockBody);
  }

  /**
   * Get text from column in table
   * @param page {Frame|Page} Browser tab
   * @param columnName {string} Column on table
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Frame|Page, columnName: string, row: number = 1): Promise<string | null> {
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

  /**
   * Check if the button "Create an order from this cart." exists
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasButtonCreateOrderFromCart(page: Frame|Page): Promise<boolean> {
    return this.elementVisible(page, this.orderInformationButtonCreateOrder, 1000);
  }

  /**
   * Click on the "Create an order from this cart." button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async createOrderFromThisCart(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.orderInformationButtonCreateOrder);
  }

  /**
   * Click on the Order link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToOrderPage(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.orderInformationLinkOrder);
  }
}

export default new ViewShoppingCarts();
