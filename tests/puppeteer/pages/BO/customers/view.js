require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Information about customer';

    // Selectors
    // Personnel information
    this.personnalInformationDiv = '.customer-personal-informations-card';
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
    // Carts
    this.cartsDiv = '.customer-carts-card';
    this.cartsTitleDiv = `${this.cartsDiv} .card-header`;
    // Viewed products
    this.viewedProductsDiv = '.customer-viewed-products-card';
    this.viewedProductsTitleDiv = `${this.viewedProductsDiv} .card-header`;
    // Messages
    this.messagesDiv = '.customer-messages-card';
    this.messagesTitleDiv = `${this.messagesDiv} .card-header`;
    // Vouchers
    this.vouchersDiv = '.customer-discounts-card';
    this.vouchersTitleDiv = `${this.vouchersDiv} .card-header`;
    // Last emails
    this.lastEmailsDiv = '.customer-sent-emails-card';
    this.lastEmailsTitleDiv = `${this.lastEmailsDiv} .card-header`;
    // Last connections
    this.lastConnectionsDiv = '.customer-last-connections-card';
    this.lastConnectionsTitleDiv = `${this.lastConnectionsDiv} .card-header`;
    // Groups
    this.groupsDiv = '.customer-groups-card';
    this.groupsTitleDiv = `${this.groupsDiv} .card-header`;
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
};
