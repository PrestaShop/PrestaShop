require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add quick access page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddQuickAccess extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add quick access page
   */
  constructor() {
    super();

    this.pageTitle = 'Quick Access > Add new •';

    // Selectors
    this.nameInput = '#name_1';
    this.urlInput = '#link';
    this.newWindowToggle = toggle => `#new_window_${toggle}`;
    this.saveButton = '#quick_access_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Set quick access link
   * @param page {Page} Browser tab
   * @param quickAccessLinkData {{name: string, url: string,
   * openNewWindow: boolean}} Data to set on new quick access form
   * @returns {Promise<string>}
   */
  async setQuickAccessLink(page, quickAccessLinkData) {
    await this.setValue(page, this.nameInput, quickAccessLinkData.name);
    await this.setValue(page, this.urlInput, quickAccessLinkData.url);
    await page.check(this.newWindowToggle(quickAccessLinkData.openNewWindow ? 'on' : 'off'));
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddQuickAccess();
