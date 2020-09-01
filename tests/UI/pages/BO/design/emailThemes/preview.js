require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class PreviewEmailTheme extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preview Theme';

    // Selectors
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
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
}

module.exports = new PreviewEmailTheme();
