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
    this.resetPasswordSuccessMessage = 'Your password has been successfully reset and a confirmation has been sent to'
      + ' your email address:';

    // Selectors
    this.accountInformationLink = '#identity-link';
    this.accountHistoryLink = '#history-link';
    this.accountAddressesLink = '#addresses-link';
    this.accountFirstAddressLink = '#address-link';
    this.accountVouchersLink = '#discounts-link';
    this.merchandiseReturnsLink = '#returns-link';
    this.successMessageAlert = '#notifications article.alert-success';
    this.logoutFooterLink = '#main footer a[href*="mylogout"]';
    this.psgdprLink = '#psgdpr-link';
  }

  /*
  Methods
   */
  /**
   * Get success message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getSuccessMessageAlert(page) {
    return this.getTextContent(page, this.successMessageAlert);
  }

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
    await this.clickAndWaitForNavigation(page, this.accountHistoryLink);
  }

  /**
   * Is add first address link visible
   * @param page page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddFirstAddressLinkVisible(page) {
    return this.elementVisible(page, this.accountFirstAddressLink);
  }

  /**
   * Go to addresses page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddressesPage(page) {
    if (await this.elementVisible(page, this.accountFirstAddressLink, 2000)) {
      await this.clickAndWaitForNavigation(page, this.accountFirstAddressLink);
    } else {
      await this.clickAndWaitForNavigation(page, this.accountAddressesLink);
    }
  }

  /**
   * Go to vouchers page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToVouchersPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountVouchersLink);
  }

  /**
   * Go to merchandise returns page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMerchandiseReturnsPage(page) {
    await this.clickAndWaitForNavigation(page, this.merchandiseReturnsLink);
  }

  /**
   * Logout from FO
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async logout(page) {
    await this.clickAndWaitForNavigation(page, this.logoutFooterLink);
  }

  /**
   * Go to my GDPR personal data page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMyGDPRPersonalDataPage(page) {
    await this.clickAndWaitForNavigation(page, this.psgdprLink);
  }
}

module.exports = new MyAccount();
