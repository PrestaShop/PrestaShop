require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddSQLQuery extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryNameInput = '#sql_request_name';
    this.sqlQueryTextArea = '#sql_request_sql';
    this.saveButton = '#main-div button.btn-primary';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit sql query
   * @param page
   * @param sqlQueryData
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
