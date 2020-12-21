require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class EmailThemes extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Email Theme •';
    this.emailThemeConfigurationSuccessfulMessage = 'Email theme configuration saved successfully';

    // Configuration form selectors
    this.configurationForm = 'form[action*=\'save-configuration\']';
    this.defaultEmailThemeSelect = '#form_configuration_defaultTheme';
    this.configurationFormSaveButton = `${this.configurationForm} .card-footer button`;

    // Email Theme table selectors
    this.emailThemeTable = 'table.grid-table';
    this.tableBody = `${this.emailThemeTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.columnName = 'td.data-type:nth-child(1)';
    this.columnActionPreviewLink = 'td.action-type a[href*=\'/preview\']';
  }

  /* Configuration form methods */

  /**
   * Choose default email theme and save configuration
   * @param page
   * @param emailTheme
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
   * @param page
   * @param name
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

module.exports = new EmailThemes();
