require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewCustomer extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Information about customer';
    this.updateSuccessfulMessage = 'Update successful';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
    this.personnalInformationEditButton = `${this.personnalInformationDiv} a[data-original-title='Edit']`;
    // Orders
    this.ordersDiv = '.customer-orders-card';
    this.ordersViewButton = row => `${this.ordersDiv} tr:nth-child(${row}) a[data-original-title='View'] i`;
    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsViewButton = row => `${this.cartsDiv} tr:nth-child(${row}) a[data-original-title='View'] i`;
    // Viewed products
    this.viewedProductsDiv = '.customer-viewed-products-card';
    // Private note
    this.privateNoteDiv = '.customer-private-note-card';
    this.privateNoteTextArea = '#private_note_note';
    this.privateNoteSaveButton = `${this.privateNoteDiv} .btn-primary`;
    // Messages
    this.messagesDiv = '.customer-messages-card';
    // Vouchers
    this.vouchersDiv = '.customer-discounts-card';
    this.voucherEditButton = `${this.vouchersDiv} a[data-original-title='Edit']`;
    this.voucherToggleDropdown = `${this.vouchersDiv} a[data-toggle='dropdown']`;
    this.voucherDeleteButton = `${this.vouchersDiv} .dropdown-menu a`;
    // Last emails
    this.lastEmailsDiv = '.customer-sent-emails-card';
    // Last connections
    this.lastConnectionsDiv = '.customer-last-connections-card';
    // Groups
    this.groupsDiv = '.customer-groups-card';
    // Addresses
    this.addressesDiv = '.customer-addresses-card';
    this.addressesEditButton = row => `${this.addressesDiv} tr:nth-child(${row}) a[data-original-title='Edit'] i`;
    // Purchased products
    this.purchasedProductsDiv = '.customer-bought-products-card';
  }

  /*
  Methods
   */

  /**
   * Get number of element from title
   * @param page
   * @param cardTitle
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
   * @param page
   * @returns {Promise<string>}
   */
  getPersonalInformationTitle(page) {
    return this.getTextContent(page, this.personnalInformationDiv);
  }

  /**
   * Get text from element
   * @param page
   * @param element
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
   * @param page
   * @param note
   * @returns {Promise<string>}
   */
  async setPrivateNote(page, note) {
    await this.setValue(page, this.privateNoteTextArea, note);
    await page.click(this.privateNoteSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to edit customer page
   * @param page
   * @returns {Promise<void>}
   */
  async goToEditCustomerPage(page) {
    await this.clickAndWaitForNavigation(page, this.personnalInformationEditButton);
  }

  /**
   * Go to view/edit page
   * @param page
   * @param cardTitle
   * @param row
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
   * @param page
   * @returns {Promise<number>}
   */
  async getCustomerID(page) {
    return this.getNumberFromText(page, this.personnalInformationDiv);
  }
}

module.exports = new ViewCustomer();
