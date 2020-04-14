require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Information about customer';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
    this.personnalInformationEditButton = `${this.personnalInformationDiv} a[data-original-title='Edit']`;
    this.socialTitleDiv = `${this.personnalInformationDiv} .customer-social-title`;
    this.birthDayDiv = `${this.personnalInformationDiv} .customer-birthday`;
    this.registrationDateDiv = `${this.personnalInformationDiv} .customer-registration-date`;
    this.lastVisitDateDiv = `${this.personnalInformationDiv} .customer-last-visit-date`;
    this.languageNameDiv = `${this.personnalInformationDiv} .customer-language-name`;
    this.newsLetterSubscriptionSpan = `${this.personnalInformationDiv} .customer-newsletter-subscription-status`;
    this.partnerOfferStatusSpan = `${this.personnalInformationDiv} .customer-partner-offers-status`;
    this.lastUpdateDiv = `${this.personnalInformationDiv} .customer-latest-update`;
    this.statusSpan = `${this.personnalInformationDiv} .customer-status`;
    // Orders
    this.ordersDiv = '.customer-orders-card';
    this.ordersTitleDiv = `${this.ordersDiv} .card-header`;
    this.invalidOrdersTr = `${this.ordersDiv} .customer-invalid-order`;
    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsTitleDiv = `${this.cartsDiv} .card-header`;
    this.cartTr = `${this.cartsDiv} .customer-cart`;
    // Viewed products
    this.viewedProductsDiv = '.customer-viewed-products-card';
    this.viewedProductsTitleDiv = `${this.viewedProductsDiv} .card-header`;
    this.viewedProductTr = `${this.viewedProductsDiv} .customer-viewed-product`;
    // Private note
    this.privateNoteDiv = '.customer-private-note-card';
    this.privateNoteTextArea = '#private_note_note';
    this.privateNoteSaveButton = `${this.privateNoteDiv} .btn-primary`;

    // Messages
    this.messagesDiv = '.customer-messages-card';
    this.messagesTitleDiv = `${this.messagesDiv} .card-header`;
    this.messagesTr = `${this.messagesDiv} .customer-message`;
    // Vouchers
    this.vouchersDiv = '.customer-discounts-card';
    this.vouchersTitleDiv = `${this.vouchersDiv} .card-header`;
    // Last emails
    this.lastEmailsDiv = '.customer-sent-emails-card';
    this.lastEmailsTitleDiv = `${this.lastEmailsDiv} .card-header`;
    // Last connections
    this.lastConnectionsDiv = '.customer-last-connections-card';
    this.lastConnectionsTitleDiv = `${this.lastConnectionsDiv} .card-header`;
    this.lastConnectionTr = `${this.lastConnectionsDiv} .customer-last-connection`;
    // Groups
    this.groupsDiv = '.customer-groups-card';
    this.groupsTitleDiv = `${this.groupsDiv} .card-header`;
    // Addresses
    this.addressesDiv = '.customer-addresses-card';
    this.addresstr = `${this.addressesDiv} .customer-address`;
  }

  /*
  Methods
   */

  /**
   * get text from card header
   * @return {Promise<textContent>}
   */
  async getTextFromPersonnelInformationForm() {
    return this.getTextContent(this.personnalInformationDiv);
  }

  /**
   * Get number of element from title
   * @param cardTitle
   * @returns {Promise<string>}
   */
  getNumberOfElementFromTitle(cardTitle) {
    let selector;
    switch (cardTitle) {
      case 'Orders':
        selector = this.ordersTitleDiv;
        break;
      case 'Carts':
        selector = this.cartsTitleDiv;
        break;
      case 'Viewed products':
        selector = this.viewedProductsTitleDiv;
        break;
      case 'Messages':
        selector = this.messagesTitleDiv;
        break;
      case 'Vouchers':
        selector = this.vouchersTitleDiv;
        break;
      case 'LAst emails':
        selector = this.lastEmailsTitleDiv;
        break;
      case 'Last connections':
        selector = this.lastConnectionsTitleDiv;
        break;
      case 'Groups':
        selector = this.groupsTitleDiv;
        break;
      default:
        throw new Error(`${cardTitle} was not found`);
    }
    return this.getTextContent(`${selector} span`);
  }

  getOrders() {
    return this.getTextContent(this.invalidOrdersTr);
  }

  getCustomerCarts() {
    return this.getTextContent(this.cartTr);
  }

  getViewedProduct() {
    return this.getTextContent(this.viewedProductTr);
  }

  getAddress() {
    return this.getTextContent(this.addresstr);
  }

  getLastConnections() {
    return this.getTextContent(this.lastConnectionTr);
  }

  getMessages() {
    return this.getTextContent(this.messagesTr);
  }

  async setPrivateNote(note) {
    await this.setValue(this.privateNoteTextArea, note);
    await this.page.click(this.privateNoteSaveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  async goToEditCustomerPage() {
    await this.clickAndWaitForNavigation(this.personnalInformationEditButton);
  }
};
