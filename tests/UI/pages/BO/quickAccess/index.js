require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Quick access page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class QuickAccess extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on quick access page
   */
  constructor() {
    super();

    this.pageTitle = 'Quick Access •';

    // Selectors
    // Header selectors
    this.addNewQuickAccessButton = '#page-header-desc-quick_access-new_quick_access';
  }

  /*
  Methods
   */

  /**
   * Go to add new quick access page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewQuickAccessPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewQuickAccessButton);
  }
}

module.exports = new QuickAccess();
