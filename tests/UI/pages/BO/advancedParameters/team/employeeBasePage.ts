import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Employee base page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
export default class EmployeeBasePage extends BOBasePage {
  public readonly pageTitleEdit: (firstName: string, lastName: string) => string;

  protected readonly firstNameInput: string;

  protected readonly lastNameInput: string;

  protected readonly emailInput: string;

  private readonly defaultPageSpan: string;

  private readonly searchDefaultPageInput: string;

  protected readonly languageSelect: string;

  protected readonly statusToggleInput: (toggle: number) => string;

  protected readonly permissionProfileSelect: string;

  protected readonly saveButton: string;

  private readonly cancelButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Employee page
   */
  constructor() {
    super();

    this.pageTitleEdit = (firstName: string, lastName: string) => `Editing ${firstName} ${lastName}'s profile • `
      + `${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.firstNameInput = '#employee_firstname';
    this.lastNameInput = '#employee_lastname';
    this.emailInput = '#employee_email';
    this.defaultPageSpan = '.select2-selection[aria-labelledby=\'select2-employee_default_page-container\']';
    this.searchDefaultPageInput = '.select2-search__field';
    this.languageSelect = '#employee_language';
    this.statusToggleInput = (toggle: number) => `#employee_active_${toggle}`;
    this.permissionProfileSelect = '#employee_profile';
    this.saveButton = '#save-button';
    this.cancelButton = '#cancel-link';
  }

  /*
  Methods
   */

  /**
   * Select default Page
   * @param page {Page} Browser tab
   * @param defaultPage {string} Page name to set on input
   * @returns {Promise<void>}
   */
  async selectDefaultPage(page: Page, defaultPage: string): Promise<void> {
    await Promise.all([
      page.locator(this.defaultPageSpan).click(),
      this.waitForVisibleSelector(page, `${this.defaultPageSpan}[aria-expanded='true']`),
    ]);
    await this.setValue(page, this.searchDefaultPageInput, defaultPage);
    await page.keyboard.press('Enter');
  }

  /**
   * Cancel the creation or the update and return to the listing page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async cancel(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.cancelButton);
  }
}
