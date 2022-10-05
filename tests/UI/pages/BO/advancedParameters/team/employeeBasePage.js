require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Employee base page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
module.exports = class EmployeeBasePage extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on Employee page
   */
  constructor() {
    super();

    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.firstNameInput = '#employee_firstname';
    this.lastNameInput = '#employee_lastname';
    this.emailInput = '#employee_email';
    this.defaultPageSpan = '.select2-selection[aria-labelledby=\'select2-employee_default_page-container\']';
    this.searchDefaultPageInput = '.select2-search__field';
    this.languageSelect = '#employee_language';
    this.statusToggleInput = toggle => `#employee_active_${toggle}`;
    this.permissionProfileSelect = '#employee_profile';
    this.saveButton = '#save-button';
    this.cancelButton = '#cancel-link';
  }

  /*
  Methods
   */

  /**
   * Select default Page
   * @param page {Page} Browser tab
   * @param defaultPage {string} Page name to set on input
   * @returns {Promise<void>}
   */
  async selectDefaultPage(page, defaultPage) {
    await Promise.all([
      page.click(this.defaultPageSpan),
      this.waitForVisibleSelector(page, `${this.defaultPageSpan}[aria-expanded='true']`),
    ]);
    await this.setValue(page, this.searchDefaultPageInput, defaultPage);
    await page.keyboard.press('Enter');
  }

  /**
   * Cancel the creation or the update and return to the listing page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async cancel(page) {
    await this.clickAndWaitForNavigation(page, this.cancelButton);
  }
};
