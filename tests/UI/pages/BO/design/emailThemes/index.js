require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Email theme page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class EmailTheme extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on email theme page
   */
  constructor() {
    super();

    this.pageTitle = 'Email Theme •';
    this.emailThemeConfigurationSuccessfulMessage = 'Email theme configuration saved successfully';

    // Configuration form selectors
    this.defaultEmailThemeSelect = '#form_defaultTheme';
    this.configurationFormSaveButton = '#save-configuration-form';

    // Email Theme table selectors
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.columnName = 'td.column-name';
    this.columnActionPreviewLink = 'td.action-type a.preview-link';
  }

  /* Configuration form methods */

  /**
   * Choose default email theme and save configuration
   * @param page {Page} Browser tab
   * @param emailTheme {string} Value of email theme to select
   * @return {Promise<string>}
   */
  async selectDefaultEmailTheme(page, emailTheme) {
    await this.selectByVisibleText(page, this.defaultEmailThemeSelect, emailTheme);
    await this.clickAndWaitForNavigation(page, this.configurationFormSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Email themes grid methods */
  /**
   * Preview email theme
   * @param page {Page} Browser tab
   * @param name {string} Value of theme to choose
   * @return {Promise<void>}
   */
  async previewEmailTheme(page, name) {
    const tableRows = await page.$$(this.tableRows);
    let found = false;

    for (let i = 0; i < tableRows.length; i++) {
      const textColumnName = await tableRows[i].$eval(this.columnName, columnName => columnName.textContent);

      if (textColumnName.includes(name)) {
        await Promise.all([
          tableRows[i].$eval(this.columnActionPreviewLink, el => el.click()),
          page.waitForNavigation(),
        ]);
        found = true;
        break;
      }
    }
    if (!found) {
      throw Error(`${name} was not found in theme emails table`);
    }
  }
}

module.exports = new EmailTheme();
