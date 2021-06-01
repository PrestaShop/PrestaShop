require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class PreviewEmailTheme extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preview Theme';

    // Selectors
    this.layoutBody = 'body pre';
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableColumns = row => `${this.tableRow(row)} td`;
    this.tableActionColumn = row => `${this.tableColumns(row)}.action-type`;
    this.tableActionColumnDropDownLink = row => `${this.tableActionColumn(row)} .dropdown-toggle`;
    this.tableActionColumnRawHtmlLink = row => `${this.tableActionColumn(row)} .raw-html-link`;
    this.tableActionColumnRawTextLink = row => `${this.tableActionColumn(row)} .raw-text-link`;
    this.backToConfigurationLink = '#back-to-configuration-link';
  }

  /* Methods */

  /**
   * Get number of layouts in grid
   * @param page
   * @return {Promise<number>}
   */
  async getNumberOfLayoutInGrid(page) {
    return (await page.$$(this.tableRows)).length;
  }

  /**
   * Click on back to configuration button
   * @param page
   * @return {Promise<void>}
   */
  async goBackToEmailThemesPage(page) {
    await this.clickAndWaitForNavigation(page, this.backToConfigurationLink);
  }

  /**
   * View raw html
   * @param page
   * @param row
   * @return {Promise<Page>}
   */
  async viewRawHtml(page, row) {
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
   * @param page
   * @param row
   * @return {Promise<*>}
   */
  async viewRawText(page, row) {
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
   * @param page
   * @return {Promise<string>}
   */
  getTextFromViewLayoutPage(page) {
    return this.getTextContent(page, this.layoutBody);
  }
}

module.exports = new PreviewEmailTheme();
