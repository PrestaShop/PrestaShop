import BOBasePage from '@pages/BO/BObasePage';

import type {ElementHandle, Page} from 'playwright';

/**
 * Email theme page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class EmailTheme extends BOBasePage {
  public readonly pageTitle: string;

  public readonly emailThemeConfigurationSuccessfulMessage: string;

  private readonly defaultEmailThemeSelect: string;

  private readonly configurationFormSaveButton: string;

  private readonly emailThemeTable: string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly columnName: string;

  private readonly columnActionPreviewLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on email theme page
   */
  constructor() {
    super();

    this.pageTitle = `Email theme • ${global.INSTALL.SHOP_NAME}`;
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
  async selectDefaultEmailTheme(page: Page, emailTheme: string): Promise<string> {
    await this.selectByVisibleText(page, this.defaultEmailThemeSelect, emailTheme);
    await this.clickAndWaitForLoadState(page, this.configurationFormSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Email themes grid methods */
  /**
   * Preview email theme
   * @param page {Page} Browser tab
   * @param name {string} Value of theme to choose
   * @return {Promise<void>}
   */
  async previewEmailTheme(page: Page, name: string): Promise<void> {
    const tableRows: ElementHandle<HTMLElement | SVGElement>[] = await page.$$(this.tableRows);
    let found: boolean = false;

    for (let i = 0; i < tableRows.length; i++) {
      const textColumnName: string | null = await tableRows[i].$eval(this.columnName, (columnName) => columnName.textContent);

      if (textColumnName && textColumnName.includes(name)) {
        /* eslint-disable no-loop-func */
        await Promise.all([
          tableRows[i].$eval(this.columnActionPreviewLink, (el: HTMLElement) => el.click()),
          page.waitForURL(''),
        ]);
        /* eslint-enable no-loop-func */
        found = true;
        break;
      }
    }
    if (!found) {
      throw Error(`${name} was not found in theme emails table`);
    }
  }
}

export default new EmailTheme();
