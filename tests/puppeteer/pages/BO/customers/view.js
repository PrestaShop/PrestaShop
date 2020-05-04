require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Information about customer';
    this.updateSuccessfulMessage = 'Update successful';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
    this.personnalInformationEditButton = `${this.personnalInformationDiv} a[data-original-title='Edit']`;
    // Orders
    this.ordersDiv = '.customer-orders-card';
    this.ordersViewButton = `${this.ordersDiv} tr:nth-child(%ID) a[data-original-title='View'] i`;
    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsViewButton = `${this.cartsDiv} tr:nth-child(%ID) a[data-original-title='View'] i`;
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
    this.addressesEditButton = `${this.addressesDiv} tr:nth-child(%ID) a[data-original-title='Edit'] i`;
    // Purchased products
    this.purchasedProductsDiv = '.customer-bought-products-card';
  }

  /*
  Methods
   */

  /**
   * Get number of element from title
   * @param cardTitle
   * @returns {Promise<string>}
   */
  getNumberOfElementFromTitle(cardTitle) {
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
    return this.getTextContent(`${selector} .card-header span`);
  }

  /**
   * Get personal information title
   * @returns {Promise<string>}
   */
  getPersonalInformationTitle() {
    return this.getTextContent(this.personnalInformationDiv);
  }

  /**
   * Get text from element
   * @param element
   * @returns {Promise<string>}
   */
  getTextFromElement(element) {
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
    return this.getTextContent(`${selector} .card-body`);
  }

  /**
   * Set private note
   * @param note
   * @returns {Promise<string>}
   */
  async setPrivateNote(note) {
    await this.setValue(this.privateNoteTextArea, note);
    await this.page.click(this.privateNoteSaveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Go to edit customer page
   * @returns {Promise<void>}
   */
  async goToEditCustomerPage() {
    await this.clickAndWaitForNavigation(this.personnalInformationEditButton);
  }

  /**
   * Go to view/edit page
   * @param cardTitle
   * @param id
   * @returns {Promise<void>}
   */
  async goToPage(cardTitle, id = 1) {
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
    return this.clickAndWaitForNavigation(selector.replace('%ID', id));
  }

  /**
   * Go to edit voucher page
   * @returns {Promise<void>}
   */
  async goToEditVoucherPage() {
    await this.clickAndWaitForNavigation(this.voucherEditButton);
  }

  /**
   * Delete voucher
   * @returns {Promise<string|*>}
   */
  async deleteVoucher() {
    await this.waitForSelectorAndClick(this.voucherToggleDropdown);
    await this.waitForSelectorAndClick(this.voucherDeleteButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
