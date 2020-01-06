require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class EmailThemes extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Email Theme •';

    // Selectors
    this.blockDiv = 'div.content-div div.justify-content-center:nth-child(%ID)';
    // Email Theme table selectors
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.columnName = 'td.data-type:nth-child(1)';
    this.columnActionPreviewLink = 'td.action-type a[href*=\'/preview\']';
  }

  /* Methods */

  /**
   * Preview email theme
   * @param name
   * @return {Promise<void>}
   */
  async previewEmailTheme(name) {
    const tableRows = await this.page.$$(this.tableRows);
    let found = false;
    for (let i = 0; i < tableRows.length; i++) {
      const textColumnName = await tableRows[i].$eval(this.columnName, columnName => columnName.textContent);
      if (textColumnName.includes(name)) {
        await Promise.all([
          tableRows[i].$eval(this.columnActionPreviewLink, el => el.click()),
          this.page.waitForNavigation({waitUntil: 'networkidle0'}),
        ]);
        found = true;
        break;
      }
    }
    if (!found) {
      throw Error(`${name} was not found in theme emails table`);
    }
  }
};
