require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Email extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'E-mail •';

    // Selectors
    // List of emails
    this.emailGridPanel = '#email_logs_grid_panel';
    this.emailGridTitle = `${this.emailGridPanel} h3.card-header-title`;
    this.emailsListForm = '#email_logs_grid_table';
    // Filters
    this.emailFilterColumnInput = '#email_logs_%FILTERBY';
    this.filterSearchButton = `${this.emailsListForm} button[name='email_logs[actions][search]']`;
    this.filterResetButton = `${this.emailsListForm} button[name='email_logs[actions][reset]']`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @returns {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.emailGridTitle);
  }

  /**
   * Filter list of emails
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterEmails(filterType, filterBy, value = '') {
    await this.resetFilter();
    switch (filterType) {
      case 'input':
        await this.setValue(this.emailFilterColumnInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          this.emailFilterColumnInput.replace('%FILTERBY', filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }
};
