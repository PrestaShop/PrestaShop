import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Preview theme page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class PreviewEmailTheme extends BOBasePage {
  public readonly pageTitle: string;

  private readonly layoutBody: string;

  private readonly emailThemeTable: string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableColumns: (row: number) => string;

  private readonly tableActionColumn: (row: number) => string;

  private readonly tableActionColumnDropDownLink: (row: number) => string;

  private readonly tableActionColumnRawHtmlLink: (row: number) => string;

  private readonly tableActionColumnRawTextLink: (row: number) => string;

  private readonly backToConfigurationLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on preview theme page
   */
  constructor() {
    super();

    this.pageTitle = 'Previewing theme';

    // Selectors
    this.layoutBody = 'body pre';
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = (row: number) => `${this.tableRows}:nth-child(${row})`;
    this.tableColumns = (row: number) => `${this.tableRow(row)} td`;
    this.tableActionColumn = (row: number) => `${this.tableColumns(row)}.action-type`;
    this.tableActionColumnDropDownLink = (row: number) => `${this.tableActionColumn(row)} .dropdown-toggle`;
    this.tableActionColumnRawHtmlLink = (row: number) => `${this.tableActionColumn(row)} .raw-html-link`;
    this.tableActionColumnRawTextLink = (row: number) => `${this.tableActionColumn(row)} .raw-text-link`;
    this.backToConfigurationLink = '#back-to-configuration-link';
  }

  /* Methods */

  /**
   * Get number of layouts in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfLayoutInGrid(page: Page): Promise<number> {
    return (await page.$$(this.tableRows)).length;
  }

  /**
   * Click on back to configuration button
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goBackToEmailThemesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.backToConfigurationLink);
  }

  /**
   * View raw html
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<Page>}
   */
  async viewRawHtml(page: Page, row: number): Promise<Page> {
    // Click on dropdown
    await page.click(this.tableActionColumnDropDownLink(row));

    // Open link in new target and return URL
    const [newPage] = await Promise.all([
      page.waitForEvent('popup'),
      page.click(this.tableActionColumnRawHtmlLink(row)),
    ]);

    return newPage;
  }

  /**
   * View raw text
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<Page>}
   */
  async viewRawText(page: Page, row: number): Promise<Page> {
    // Click on dropdown
    await page.click(this.tableActionColumnDropDownLink(row));

    // Open link in new target and return URL
    const [newPage] = await Promise.all([
      page.waitForEvent('popup'),
      page.click(this.tableActionColumnRawTextLink(row)),
    ]);

    return newPage;
  }

  /**
   * Get text from view layout page
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getTextFromViewLayoutPage(page: Page): Promise<string> {
    return this.getTextContent(page, this.layoutBody);
  }
}

export default new PreviewEmailTheme();
