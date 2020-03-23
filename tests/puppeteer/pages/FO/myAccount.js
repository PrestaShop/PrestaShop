require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class MyAccount extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'My account';

    // Selectors
    this.historyLink = '#history-link';
  }

  /*
  Methods
   */

  /**
   * Go to order history page
   * @returns {Promise<void>}
   */
  async goToHistoryAndDetailsPage() {
    await this.waitForSelectorAndClick(this.historyLink);
  }
};
