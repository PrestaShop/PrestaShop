require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddSQLQuery extends BOBasePage {
  constructor(page) {
    super(page);

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
   * @param sqlQueryData
   * @return {Promise<textContent>}
   */
  async createEditSQLQuery(sqlQueryData) {
    await this.setValue(this.sqlQueryNameInput, sqlQueryData.name);
    await this.setValue(this.sqlQueryTextArea, sqlQueryData.sqlQuery);
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
