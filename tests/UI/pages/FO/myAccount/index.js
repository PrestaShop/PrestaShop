require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class MyAccount extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'My account';

    // Selectors
    this.historyLink = '#history-link';
    this.addressesLink = '#addresses-link';
  }

  /*
  Methods
   */

  /**
   * Go to order history page
   * @param page
   * @returns {Promise<void>}
   */
  async goToHistoryAndDetailsPage(page) {
    await this.waitForSelectorAndClick(page, this.historyLink);
  }

  /**
   * Go to addresses page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddressesPage(page) {
    await this.clickAndWaitForNavigation(page, this.addressesLink);
  }
}

module.exports = new MyAccount();
