require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddZone extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Zones > Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#name';
    this.statusToggle = toggle => `label[for='active_${toggle}']`;
    this.saveZoneButton = '#zone_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit zone
   * @param page
   * @param zoneData
   * @returns {Promise<string>}
   */
  async createEditZone(page, zoneData) {
    await this.setValue(page, this.nameInput, zoneData.name);
    await page.click(this.statusToggle(zoneData.status ? 'on' : 'off'));

    // Save zone
    await this.clickAndWaitForNavigation(page, this.saveZoneButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddZone();
