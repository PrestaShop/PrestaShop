require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * My account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyAccount extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on my account page
   */
  constructor() {
    super();

    this.pageTitle = 'My account';

    // Selectors
    this.accountInformationLink = '#identity-link';
    this.accountHistoryLink = '#history-link';
    this.accountAddressesLink = '#addresses-link';
    this.accountFirstAddressLink = '#address-link';
    this.accountVouchersLink = '#discounts-link';
  }

  /*
  Methods
   */

  /**
   * Go to account information page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToInformationPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountInformationLink);
  }

  /**
   * Go to order history page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToHistoryAndDetailsPage(page) {
    await this.waitForSelectorAndClick(page, this.accountHistoryLink);
  }

  /**
   * Go to addresses page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddressesPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountAddressesLink);
  }

  /**
   * Go to add first address page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddFirstAddressPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountFirstAddressLink);
  }

  /**
   * Go to vouchers page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToVouchersPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountVouchersLink);
  }
}

module.exports = new MyAccount();
