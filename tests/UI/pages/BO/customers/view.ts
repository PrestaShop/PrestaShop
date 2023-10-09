import BOBasePage from '@pages/BO/BObasePage';

import type {Frame, Page} from 'playwright';

/**
 * View customer page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewCustomer extends BOBasePage {
  public readonly pageTitle: (customerName: string) => string;

  public readonly updateSuccessfulMessage: string;

  private readonly personnalInformationDiv: string;

  private readonly personnalInformationEditButton: string;

  private readonly ordersDiv: string;

  private readonly ordersViewButton: (row: number) => string;

  private readonly cartsDiv: string;

  private readonly cartsTableRow: (row: number) => string;

  private readonly cartsTableColumn: (row: number, column: string) => string;

  private readonly cartsViewButton: (row: number) => string;

  private readonly viewedProductsDiv: string;

  private readonly privateNoteDiv: string;

  private readonly privateNoteTextArea: string;

  private readonly privateNoteSaveButton: string;

  private readonly messagesDiv: string;

  private readonly vouchersDiv: string;

  private readonly voucherEditButton: (row: number) => string;

  private readonly voucherToggleDropdown: (row: number) => string;

  private readonly voucherDeleteButton: string;

  private readonly voucherDeleteConfirmButton: string;

  private readonly voucherDeleteModal: string;

  private readonly lastEmailsDiv: string;

  private readonly lastConnectionsDiv: string;

  private readonly lastConnectionTableRow: (row: number) => string;

  private readonly lastConnectionTableColumn: (row: number, column: string) => string;

  private readonly groupsDiv: string;

  private readonly addressesDiv: string;

  private readonly addressesEditButton: (row: number) => string;

  private readonly purchasedProductsDiv: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view customer page
   */
  constructor() {
    super();

    this.pageTitle = (customerName: string) => `Customer ${customerName} • ${global.INSTALL.SHOP_NAME}`;
    this.updateSuccessfulMessage = 'Update successful';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
    this.personnalInformationEditButton = `${this.personnalInformationDiv} a.edit-link`;

    // Orders
    this.ordersDiv = '.customer-orders-card';
    this.ordersViewButton = (row: number) => `${this.ordersDiv} tr:nth-child(${row}) a.grid-view-row-link i`;

    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsViewButton = (row: number) => `${this.cartsDiv} tr:nth-child(${row}) a.grid-view-row-link i`;
    this.cartsTableRow = (row: number) => `.customer-carts-card tr:nth-child(${row})`;
    this.cartsTableColumn = (row: number, column: string) => `${this.cartsTableRow(row)} td.column-${column}`;

    // Viewed products
    this.viewedProductsDiv = '.customer-viewed-products-card';

    // Private note
    this.privateNoteDiv = '.customer-private-note-card';
    this.privateNoteTextArea = '#private_note_note';
    this.privateNoteSaveButton = '#save-private-note';

    // Messages
    this.messagesDiv = '.customer-messages-card';

    // Vouchers
    this.vouchersDiv = '.customer-discounts-card';
    this.voucherEditButton = (row: number) => `${this.vouchersDiv} tr:nth-child(${row}) a.grid-edit-row-link`;
    this.voucherToggleDropdown = (row: number) => `${this.vouchersDiv} tr:nth-child(${row}) a[data-toggle='dropdown']`;
    this.voucherDeleteButton = `${this.vouchersDiv} .dropdown-menu button.grid-delete-row-link`;
    this.voucherDeleteModal = '#customer_discount-grid-confirm-modal';
    this.voucherDeleteConfirmButton = `${this.voucherDeleteModal} button.btn-confirm-submit`;

    // Last emails
    this.lastEmailsDiv = '.customer-sent-emails-card';

    // Last connections
    this.lastConnectionsDiv = '.customer-last-connections-card';
    this.lastConnectionTableRow = (row: number) => `tr.customer-last-connection:nth-child(${row})`;
    this.lastConnectionTableColumn = (row: number, column: string) => `${this.lastConnectionTableRow(row)} `
      + `td.customer-last-connection-${column}`;

    // Groups
    this.groupsDiv = '.customer-groups-card';

    // Addresses
    this.addressesDiv = '.customer-addresses-card';
    this.addressesEditButton = (row: number) => `${this.addressesDiv} tr:nth-child(${row}) a.grid-edit-row-link i`;

    // Purchased products
    this.purchasedProductsDiv = '.customer-bought-products-card';
  }

  /*
  Methods
   */

  /**
   * Get number of element from title
   * @param page {Frame|Page} Browser tab
   * @param cardTitle {string} Value of card title to get number of elements
   * @returns {Promise<string>}
   */
  async getNumberOfElementFromTitle(page: Frame | Page, cardTitle: string): Promise<string> {
    let selector: string;

    switch (cardTitle) {
      case 'Orders':
        selector = this.ordersDiv;
        break;
      case 'Carts':
        selector = this.cartsDiv;
        break;
      case 'Viewed products':
        selector = this.viewedProductsDiv;
        break;
      case 'Messages':
        selector = this.messagesDiv;
        break;
      case 'Vouchers':
        selector = this.vouchersDiv;
        break;
      case 'Last emails':
        selector = this.lastEmailsDiv;
        break;
      case 'Last connections':
        selector = this.lastConnectionsDiv;
        break;
      case 'Groups':
        selector = this.groupsDiv;
        break;
      case 'Addresses':
        selector = this.addressesDiv;
        break;
      case 'Purchased products':
        selector = this.purchasedProductsDiv;
        break;
      default:
        throw new Error(`${cardTitle} was not found`);
    }

    return this.getTextContent(page, `${selector} .card-header span`);
  }

  /**
   * Get personal information title
   * @param page {Page|Frame} Browser tab
   * @returns {Promise<string>}
   */
  async getPersonalInformationTitle(page: Page | Frame): Promise<string> {
    return this.getTextContent(page, this.personnalInformationDiv);
  }

  /**
   * Get text from element
   * @param page {Page} Browser tab
   * @param element {string} Value of element to get text content
   * @returns {Promise<string>}
   */
  async getTextFromElement(page: Page, element: string): Promise<string> {
    let selector: string;

    switch (element) {
      case 'Personal information':
        selector = this.personnalInformationDiv;
        break;
      case 'Orders':
        selector = this.ordersDiv;
        break;
      case 'Carts':
        selector = this.cartsDiv;
        break;
      case 'Viewed products':
        selector = this.viewedProductsDiv;
        break;
      case 'Addresses':
        selector = this.addressesDiv;
        break;
      case 'Messages':
        selector = this.messagesDiv;
        break;
      case 'Vouchers':
        selector = this.vouchersDiv;
        break;
      case 'Last emails':
        selector = this.lastEmailsDiv;
        break;
      case 'Last connections':
        selector = this.lastConnectionsDiv;
        break;
      case 'Groups':
        selector = this.groupsDiv;
        break;
      case 'Purchased products':
        selector = this.purchasedProductsDiv;
        break;
      default:
        throw new Error(`${element} was not found`);
    }

    return this.getTextContent(page, `${selector} .card-body`);
  }

  /**
   * Get text column from table last connection
   * @param page {Page} Browser tab
   * @param column {string} Column name in table last connections
   * @param row {number} Row number in table Last connections
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableLastConnections(page: Page, column: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.lastConnectionTableColumn(row, column));
  }

  /**
   * Get text column from carts table
   * @param page {Page} Browser tab
   * @param column {string} Column name in table carts
   * @param row {number} Row number in table carts
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCarts(page: Page, column: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.cartsTableColumn(row, column));
  }

  /**
   * Is private note block visible
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPrivateNoteBlockVisible(page: Frame | Page): Promise<boolean> {
    return this.elementVisible(page, this.privateNoteDiv, 1000);
  }

  /**
   * Set private note
   * @param page {Page} Browser tab
   * @param note {string} Value of private note to set
   * @returns {Promise<string>}
   */
  async setPrivateNote(page: Page, note: string): Promise<string> {
    await this.setValue(page, this.privateNoteTextArea, note);
    await page.click(this.privateNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to edit customer page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToEditCustomerPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.personnalInformationEditButton);
  }

  /**
   * Go to view/edit page
   * @param page {Page} Browser tab
   * @param cardTitle {string} Value of page title to go
   * @param row {number} Row on table where to click
   * @returns {Promise<void>}
   */
  async goToPage(page: Page, cardTitle: string, row: number = 1): Promise<void> {
    let selector: (row: number) => string;

    switch (cardTitle) {
      case 'Orders':
        selector = this.ordersViewButton;
        break;
      case 'Carts':
        selector = this.cartsViewButton;
        break;
      case 'Addresses':
        selector = this.addressesEditButton;
        break;
      case 'Vouchers':
        selector = this.voucherEditButton;
        break;
      default:
        throw new Error(`${cardTitle} was not found`);
    }

    return this.clickAndWaitForURL(page, selector(row));
  }

  /**
   * Delete voucher
   * @param page {Page} Browser tab
   * @param row {number} Row in vouchers table
   * @returns {Promise<string>}
   */
  async deleteVoucher(page: Page, row: number) {
    await page.locator(this.voucherToggleDropdown(row)).click();
    await page.locator(this.voucherDeleteButton).click();
    await this.waitForVisibleSelector(page, this.voucherDeleteModal);
    await page.locator(this.voucherDeleteConfirmButton).click();

    return this.getTextContent(page, `${this.alertSuccessBlock}[role='alert']`);
  }

  /**
   * Get customer ID
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCustomerID(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.personnalInformationDiv);
  }
}

export default new ViewCustomer();
