require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Permissions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Permissions extends BOBasePage {
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
    this.profileSubTab = profileName => `a[id^="profile-"]:text("${profileName}")`;
    this.profileAccess = (className, access) => `input[data-type="${access}"][data-classname="${className}"]:visible`;
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
  async goToProfileSubTab(page, profileName) {
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
  async setPermission(page, className, access) {
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

module.exports = new Permissions();
