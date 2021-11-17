require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add zone page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddZone extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add zone page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#zone_name';
    this.statusToggle = toggle => `#zone_enabled_${toggle}`;
    this.saveZoneButton = '#save-button';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit zone
   * @param page {Page} Browser tab
   * @param zoneData {ZoneData} Data to set on new/edit zone page
   * @returns {Promise<string>}
   */
  async createEditZone(page, zoneData) {
    await this.setValue(page, this.nameInput, zoneData.name);
    await page.check(this.statusToggle(zoneData.status ? 1 : 0));

    // Save zone
    await this.clickAndWaitForNavigation(page, this.saveZoneButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddZone();
