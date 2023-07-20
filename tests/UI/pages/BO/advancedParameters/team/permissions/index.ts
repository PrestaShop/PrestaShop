import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Permissions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Permissions extends BOBasePage {
  public readonly pageTitle: string;

  private readonly profileSubTab: (profileName: string) => string;

  private readonly profileAccess: (className: string, access: string) => string;

  private readonly menuTable: string;

  private readonly permissionCheckbox: string;

  private readonly menuTableHeaderCheckbox: (permission: string) => string;

  private readonly menuTableRow: (row: number) => string;

  private readonly permissionCheckboxRow: (row: number, access: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on permissions page
   */
  constructor() {
    super();

    this.pageTitle = 'Permissions';

    // Selectors
    this.growlMessageBlock = `${this.growlDiv} .growl-message`;
    this.growlCloseButton = `${this.growlDiv} .growl-close`;
    this.successfulUpdateMessage = 'Update successful';
    this.profileSubTab = (profileName: string) => `a[id^="profile-"]:text("${profileName}")`;
    this.profileAccess = (className: string, access: string) => `input[data-type="${access}"][data-classname="${
      className}"]:visible`;
    this.menuTable = '#table_2';
    this.menuTableHeaderCheckbox = (permission: string) => `#table_2 thead tr th input.${permission}all`;
    this.menuTableRow = (row: number) => `${this.menuTable} tr:nth-child(${row})`;
    this.permissionCheckbox = `${this.menuTable} tr td input[data-type='all']`;
    this.permissionCheckboxRow = (row: number, permission: string) => `${this.menuTable} tr:nth-child(${row})`
      + ` td input[data-type='${permission}']`;
  }

  /*
  Methods
   */
  /**
   * Go to specific Profile sub tab
   * @param page {Page} Browser tab
   * @param profileName {string} Name of the SubTab
   * @returns {Promise<boolean>}
   */
  async goToProfileSubTab(page: Page, profileName: string): Promise<boolean> {
    await page.click(this.profileSubTab(profileName));
    return this.elementVisible(page, `${this.profileSubTab(profileName)}.selected.active`, 1000);
  }

  /**
   * Set a specific permission on a specific page
   * @param page {Page} Browser tab
   * @param className {string} Name of the className
   * @param access {string} Access name
   * @returns {Promise<boolean>}
   */
  async setPermission(page: Page, className: string, access: string): Promise<boolean> {
    await this.closeGrowlMessage(page);
    if (await this.isChecked(page, this.profileAccess(className, access))) {
      return true;
    }
    await page.click(this.profileAccess(className, access));
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);
    await this.closeGrowlMessage(page);

    return growlTextMessage === this.successfulUpdateMessage;
  }

  /**
   * Bulk set a specific permission on a specific page
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @param toCheck {boolean} True if we need to click on checkbox, false if not
   * @returns {Promise<boolean>}
   */
  async bulkSetPermission(page: Page, permission: string, toCheck: boolean = true): Promise<boolean> {
    await this.closeGrowlMessage(page);
    if (toCheck && await this.isChecked(page, this.menuTableHeaderCheckbox(permission))) {
      return true;
    }
    await page.click(this.menuTableHeaderCheckbox(permission));
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);

    return growlTextMessage === this.successfulUpdateMessage;
  }

  /**
   * Get number of menu
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInMenu(page: Page): Promise<number> {
    return (await page.$$(this.permissionCheckbox)).length;
  }

  /**
   * Is bulk permission performed
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @param isChecked {boolean} True if we need to click on checkbox, false if not
   * @returns {Promise<boolean>}
   */
  async isBulkPermissionPerformed(page: Page, permission: string, isChecked: boolean = true): Promise<boolean> {
    const menuNumber = await this.getNumberOfElementInMenu(page);

    let i: number = 1;
    let isVisible = isChecked;
    while (isVisible === isChecked && i < menuNumber) {
      isVisible = await this.isChecked(page, this.permissionCheckboxRow(i, permission));
      i += 1;
    }

    return isVisible;
  }

  /**
   * Get number of checkbox unchecked
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @returns {Promise<number>}
   */
  async getNumberOfCheckBoxUnChecked(page: Page, permission: string): Promise<number> {
    const menuNumber = await this.getNumberOfElementInMenu(page);

    let checked = 0;

    for (let i = 1; i <= menuNumber; i++) {
      if (await this.isChecked(page, this.permissionCheckboxRow(i, permission))) {
        checked += 1;
      }
    }

    return menuNumber - checked;
  }

  /**
   * Is menu checked
   * @param page {Page} Browser tab
   * @param className {string} Name of the className
   * @param access {string} Access name
   * @returns {Promise<boolean>}
   */
  async isMenuChecked(page: Page, className: string, access: string): Promise<boolean> {
    return this.isChecked(page, this.profileAccess(className, access));
  }
}

export default new Permissions();
