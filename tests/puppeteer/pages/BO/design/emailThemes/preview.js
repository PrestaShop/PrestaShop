require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class PreviewEmailTheme extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Preview Theme';

    // Selectors
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.backToConfigurationLink = '.card-footer a';
  }

  /* Methods */

  /**
   * Get number of layouts in grid
   * @return {Promise<*>}
   */
  async getNumberOfLayoutInGrid() {
    return (await this.page.$$(this.tableRows)).length;
  }

  /**
   * Click on back to configuration button
   * @return {Promise<void>}
   */
  async goBackToEmailThemesPage() {
    await this.clickAndWaitForNavigation(this.backToConfigurationLink);
  }
};
