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
}

export default new Permissions();
