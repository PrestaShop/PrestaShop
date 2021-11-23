require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View customer page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewCustomer extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view customer page
   */
  constructor() {
    super();

    this.pageTitle = 'Information about customer';
    this.updateSuccessfulMessage = 'Update successful';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
    this.personnalInformationEditButton = `${this.personnalInformationDiv} a.edit-link`;

    // Orders
    this.ordersDiv = '.customer-orders-card';
    this.ordersViewButton = row => `${this.ordersDiv} tr:nth-child(${row}) a.grid-view-row-link i`;

    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsViewButton = row => `${this.cartsDiv} tr:nth-child(${row}) a.grid-view-row-link i`;

    // Viewed products
    this.viewedProductsDiv = '.customer-viewed-products-card';

    // Private note
    this.privateNoteTextArea = '#private_note_note';
    this.privateNoteSaveButton = '#save-private-note';

    // Messages
    this.messagesDiv = '.customer-messages-card';

    // Vouchers
    this.vouchersDiv = '.customer-discounts-card';
    this.voucherEditButton = `${this.vouchersDiv} a.grid-edit-row-link`;
    this.voucherToggleDropdown = `${this.vouchersDiv} a[data-toggle='dropdown']`;
    this.voucherDeleteButton = `${this.vouchersDiv} .dropdown-menu a.grid-delete-row-link`;

    // Last emails
    this.lastEmailsDiv = '.customer-sent-emails-card';

    // Last connections
    this.lastConnectionsDiv = '.customer-last-connections-card';

    // Groups
    this.groupsDiv = '.customer-groups-card';

    // Addresses
    this.addressesDiv = '.customer-addresses-card';
    this.addressesEditButton = row => `${this.addressesDiv} tr:nth-child(${row}) a.grid-edit-row-link i`;

    // Purchased products
    this.purchasedProductsDiv = '.customer-bought-products-card';
  }

  /*
  Methods
   */

  /**
   * Get number of element from title
   * @param page {Page} Browser tab
   * @param cardTitle {string} Value of card title to get number of elements
   * @returns {Promise<string>}
   */
  getNumberOfElementFromTitle(page, cardTitle) {
    let selector;

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
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPersonalInformationTitle(page) {
    return this.getTextContent(page, this.personnalInformationDiv);
  }

  /**
   * Get text from element
   * @param page {Page} Browser tab
   * @param element {string} Value of element to get text content
   * @returns {Promise<string>}
   */
  getTextFromElement(page, element) {
    let selector;

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
   * Set private note
   * @param page {Page} Browser tab
   * @param note {string} Value of private note to set
   * @returns {Promise<string>}
   */
  async setPrivateNote(page, note) {
    await this.setValue(page, this.privateNoteTextArea, note);
    await page.click(this.privateNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to edit customer page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToEditCustomerPage(page) {
    await this.clickAndWaitForNavigation(page, this.personnalInformationEditButton);
  }

  /**
   * Go to view/edit page
   * @param page {Page} Browser tab
   * @param cardTitle {string} Value of page title to go
   * @param row {number} Row on table where to click
   * @returns {Promise<void>}
   */
  async goToPage(page, cardTitle, row = 1) {
    let selector;

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
      default:
        throw new Error(`${cardTitle} was not found`);
    }

    return this.clickAndWaitForNavigation(page, selector(row));
  }

  /**
   * Get customer ID
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCustomerID(page) {
    return this.getNumberFromText(page, this.personnalInformationDiv);
  }
}

module.exports = new ViewCustomer();
