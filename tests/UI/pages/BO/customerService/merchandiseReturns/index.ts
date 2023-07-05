import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Merchandise returns page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class MerchandiseReturns extends BOBasePage {
  public readonly pageTitle: string;

  public readonly errorDeletionMessage: string;

  private readonly gridTable: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly generalForm: string;

  private readonly enableOrderReturnLabel: (toggle: string) => string;

  private readonly returnsPrefixInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on merchandise return page
   */
  constructor() {
    super();

    this.pageTitle = 'Merchandise Returns •';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';
    this.errorDeletionMessage = 'You need at least one product.';

    // Selectors
    // Merchandise returns table
    this.gridTable = '#table-order_return';
    this.filterColumn = (filterBy: string) => `${this.gridTable} input[name='order_returnFilter_${filterBy}']`;
    this.filterSearchButton = `${this.gridTable} #submitFilterButtonorder_return`;
    this.filterResetButton = `${this.gridTable} button[name='submitResetorder_return']`;
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;

    // Options
    this.generalForm = '#order_return_fieldset_general';
    this.enableOrderReturnLabel = (toggle: string) => `${this.generalForm} #PS_ORDER_RETURN_${toggle}`;
    this.returnsPrefixInput = '#conf_id_PS_RETURN_PREFIX input[name=\'PS_RETURN_PREFIX_1\']';
    this.saveButton = `${this.generalForm} button[name='submitOptionsorder_return']`;
  }

  /*
    Methods
  */

  /**
   * Filter merchandise returns table
   * @param page {Page} Browser tab
   * @param filterBy {string} The column name to filter by
   * @param value {string} Value to filter with
   * @returns {Promise<void>}
   */
  async filterMerchandiseReturnsTable(page: Page, filterBy: string, value: string): Promise<void> {
    if (await this.elementVisible(page, this.filterColumn(filterBy), 2000)) {
      await this.setValue(page, this.filterColumn(filterBy), value);
      // click on search
      await this.clickAndWaitForURL(page, this.filterSearchButton);
    }
  }

  /**
   * Get text column from merchandise returns table
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on the table
   * @param row {number} Row on the table
   * @returns {Promise<string>}
   */
  getTextColumnFromMerchandiseReturnsTable(page: Page, columnName: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, columnName));
  }

  /**
   * Go to merchandise return page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToMerchandiseReturnPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumn(row, 'id_order_return'));
  }

  /**
   * Enable/Disable merchandise returns
   * @param page {Page} Browser tab
   * @param status {boolean} Status to set on the order return
   * @returns {Promise<string>}
   */
  async setOrderReturnStatus(page: Page, status: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableOrderReturnLabel(status ? 'on' : 'off'));
    await this.clickAndWaitForLoadState(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Update returns prefix
   * @param page {Page} Browser tab
   * @param prefix {string} Value of prefix to set on return prefix input
   * @returns {Promise<string>}
   */
  async setReturnsPrefix(page: Page, prefix: string): Promise<string> {
    await this.setValue(page, this.returnsPrefixInput, prefix);
    await this.clickAndWaitForLoadState(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

export default new MerchandiseReturns();
