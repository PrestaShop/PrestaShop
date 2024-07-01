import type {Page} from 'playwright';
import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

/**
 * Module configuration page for module : psgdpr, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsGdprTabCustomerActivityPage extends ModuleConfiguration {
  private tableAction: (action: string) => string;

  private tableCustomerLog: string;

  private tableCustomerLogHeadRow: string;

  private tableCustomerLogHeadRowNth: (nthRow: number) => string;

  private tableCustomerLogBodyRow: string;

  private tableCustomerLogRowNth: (nthRow: number) => string;

  private tableCustomerLogRowColumnNth: (nthRow: number, nthColumn: number) => string;

  /**
   * @constructs
   */
  constructor() {
    super();

    this.tableAction = (action: string) => `#customerLog_wrapper .dt-buttons button.buttons-${action}`;
    this.tableCustomerLog = '#customerLog';
    this.tableCustomerLogHeadRow = `${this.tableCustomerLog} thead tr`;
    this.tableCustomerLogHeadRowNth = (nthRow: number) => `${this.tableCustomerLogHeadRow} th:nth-child(${nthRow})`;
    this.tableCustomerLogBodyRow = `${this.tableCustomerLog} tbody tr`;
    this.tableCustomerLogRowNth = (nthRow: number) => `${this.tableCustomerLogBodyRow}:nth-child(${nthRow})`;
    this.tableCustomerLogRowColumnNth = (nthRow: number, nthColumn: number) => `${this.tableCustomerLogRowNth(nthRow)} td:`
      + `nth-child(${nthColumn})`;
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return page.locator(this.tableCustomerLogBodyRow).count();
  }

  /**
   * Get text from a column from table
   * @param page {Page} Browser tab
   * @param rowNth {number} Row on table
   * @param columnNth {number} Column on row
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, rowNth: number, columnNth: number): Promise<string> {
    return this.getTextContent(page, this.tableCustomerLogRowColumnNth(rowNth, columnNth));
  }

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {number} Column
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnNth: number): Promise<string[]> {
    const rowsNumber: number = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      allRowsContentTable.push(
        await this.getTextColumnFromTable(page, i, columnNth),
      );
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param columnNth {number} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, columnNth: number, sortDirection: string): Promise<void> {
    const sortColumnSorted = `${this.tableCustomerLogHeadRowNth(columnNth)}.sorting_${sortDirection}`;

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnSorted, 2000) && i < 2) {
      await page.locator(this.tableCustomerLogHeadRowNth(columnNth)).click();
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnSorted, 20000);
  }

  /**
   * Export the table in the clipboard
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async copyTable(page: Page): Promise<string> {
    await page.locator(this.tableAction('copy')).click();

    return this.getClipboardText(page);
  }

  /**
   * Export the table in file
   * @param page {Page} Browser tab
   * @param exportType {'csv' | 'excel' | 'pdf'} Browser tab
   * @return {Promise<string>}
   */
  async exportTable(page: Page, exportType: 'csv' | 'excel' | 'pdf'): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.tableAction(exportType));
  }
}

export default new PsGdprTabCustomerActivityPage();
