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

  private readonly menuTableProfileAccess: (className: string, access: string) => string;

  private readonly menuTable: string;

  private readonly modulesTable: string;

  private readonly menuTablePermissionCheckboxAll: string;

  private readonly menuTableHeaderCheckbox: (permission: string) => string;

  private readonly modulesTableHeaderCheckbox: (permission: string) => string;

  private readonly menuTableRow: (row: number) => string;

  private readonly modulesTableRow: (row: number) => string;

  private readonly menuTablePermissionCheckboxRow: (row: number, access: string) => string;

  private readonly modulesTablePermissionCheckboxRow: (row: number, permission: string) => string;

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
    // Selectors for menu table
    this.menuTable = '#table_2';
    this.menuTableHeaderCheckbox = (permission: string) => `${this.menuTable} thead tr th input.${permission}all`;
    this.menuTableProfileAccess = (className: string, access: string) => `input[data-type="${access}"][data-classname="${
      className}"]:visible`;
    this.menuTablePermissionCheckboxAll = `${this.menuTable} tr td input[data-type='all']`;
    this.menuTableRow = (row: number) => `${this.menuTable} tr:nth-child(${row})`;
    this.menuTablePermissionCheckboxRow = (row: number, permission: string) => `${this.menuTable} tr:nth-child(${row})`
      + ` td input[data-type='${permission}']`;
    // Selectors for modules table
    this.modulesTable = '#table_module_2';
    this.modulesTableHeaderCheckbox = (permission: string) => `${this.modulesTable} thead tr th input[data-rel*='${permission}']`;
    this.modulesTableRow = (row: number) => `${this.modulesTable} tr:nth-child(${row})`;
    this.modulesTablePermissionCheckboxRow = (row: number, permission: string) => `${this.modulesTable} tr:nth-child(${row})`
      + ` td input[data-rel*='${permission}']`;
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

  // Methods for menu table
  /**
   * Set a specific permission on a specific page
   * @param page {Page} Browser tab
   * @param className {string} Name of the className
   * @param access {string} Access name
   * @returns {Promise<boolean>}
   */
  async setPermission(page: Page, className: string, access: string): Promise<boolean> {
    await this.closeGrowlMessage(page);
    if (await this.isChecked(page, this.menuTableProfileAccess(className, access))) {
      return true;
    }
    await page.click(this.menuTableProfileAccess(className, access));
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);
    await this.closeGrowlMessage(page);

    return growlTextMessage === this.successfulUpdateMessage;
  }

  /**
   * Bulk set a specific permission on all pages
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @param toCheck {boolean} True if we need to click on checkbox, false if not
   * @returns {Promise<boolean>}
   */
  async setPermissionOnAllPages(page: Page, permission: string, toCheck: boolean = true): Promise<boolean> {
    await this.closeGrowlMessage(page);
    if (toCheck && await this.isChecked(page, this.menuTableHeaderCheckbox(permission))) {
      return true;
    }
    await page.click(this.menuTableHeaderCheckbox(permission));
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);

    return growlTextMessage === this.successfulUpdateMessage;
  }

  /**
   * Get number of pages
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInMenu(page: Page): Promise<number> {
    return (await page.$$(this.menuTablePermissionCheckboxAll)).length;
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
      isVisible = await this.isChecked(page, this.menuTablePermissionCheckboxRow(i, permission));
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
  async getNumberOfPagesUnChecked(page: Page, permission: string): Promise<number> {
    const menuNumber = await this.getNumberOfElementInMenu(page);

    let checked = 0;

    for (let i = 1; i <= menuNumber; i++) {
      if (await this.isChecked(page, this.menuTablePermissionCheckboxRow(i, permission))) {
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
    return this.isChecked(page, this.menuTableProfileAccess(className, access));
  }

  // Methods for modules table
  /**
   * Bulk set a specific permission on all modules
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @param toCheck {boolean} True if we need to click on checkbox, false if not
   * @returns {Promise<boolean>}
   */
  async setPermissionOnAllModules(page: Page, permission: string, toCheck: boolean = true): Promise<boolean> {
    await this.closeGrowlMessage(page);
    if (toCheck && await this.isChecked(page, this.modulesTableHeaderCheckbox(permission))) {
      return true;
    }
    await page.click(this.modulesTableHeaderCheckbox(permission));
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);

    return growlTextMessage === this.successfulUpdateMessage;
  }

  /**
   * Is bulk permission performed
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @param isChecked {boolean} True if we need to click on checkbox, false if not
   * @returns {Promise<boolean>}
   */
  async isAllPermissionPerformed(page: Page, permission: string, isChecked: boolean = true): Promise<boolean> {
    const menuNumber = await this.getNumberOfModules(page);

    let i: number = 1;
    let isVisible = isChecked;
    while (isVisible === isChecked && i < menuNumber) {
      isVisible = await this.isChecked(page, this.modulesTablePermissionCheckboxRow(i, permission));
      i += 1;
    }

    return isVisible;
  }

  /**
   * Get number of menu
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfModules(page: Page): Promise<number> {
    return (await page.$$(`${this.modulesTable} body tr`)).length;
  }

  /**
   * Get number of checkbox unchecked
   * @param page {Page} Browser tab
   * @param permission {string} Name of permission
   * @returns {Promise<number>}
   */
  async getNumberOfModulesUnChecked(page: Page, permission: string): Promise<number> {
    const modulesNumber = await this.getNumberOfModules(page);

    let checked = 0;

    for (let i = 1; i <= modulesNumber; i++) {
      if (await this.isChecked(page, this.modulesTablePermissionCheckboxRow(i, permission))) {
        checked += 1;
      }
    }

    return modulesNumber - checked;
  }
}

export default new Permissions();
