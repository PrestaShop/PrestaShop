require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add sql query page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddSQLQuery extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add sql query page
   */
  constructor() {
    super();

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryNameInput = '#sql_request_name';
    this.sqlQueryTextArea = '#sql_request_sql';
    this.saveButton = '#sql-query-save-btn';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit sql query
   * @param page {Page} Browser tab
   * @param sqlQueryData {sqlQueryData} Data to set on create/edit sql query
   * @returns {Promise<string>}
   */
  async createEditSQLQuery(page, sqlQueryData) {
    await this.setValue(page, this.sqlQueryNameInput, sqlQueryData.name);
    await this.setValue(page, this.sqlQueryTextArea, sqlQueryData.sqlQuery);
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddSQLQuery();
