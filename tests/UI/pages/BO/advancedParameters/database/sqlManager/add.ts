import BOBasePage from '@pages/BO/BObasePage';

import type SqlQueryData from '@data/faker/sqlQuery';

import type {Page} from 'playwright';

/**
 * Add sql query page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddSQLQuery extends BOBasePage {
  public readonly pageTitle: string;

  private readonly sqlQueryNameInput: string;

  private readonly sqlQueryTextArea: string;

  private readonly saveButton: string;

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
  async createEditSQLQuery(page: Page, sqlQueryData: SqlQueryData): Promise<string> {
    await this.setValue(page, this.sqlQueryNameInput, sqlQueryData.name);
    await this.setValue(page, this.sqlQueryTextArea, sqlQueryData.sqlQuery);
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddSQLQuery();
