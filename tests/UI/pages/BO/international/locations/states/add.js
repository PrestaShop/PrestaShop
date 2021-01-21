require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddState extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'States > Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#name';
    this.isoCodeInput = '#iso_code';
    this.countrySelect = '#id_country';
    this.zoneSelect = '#id_zone';
    this.statusToggle = toggle => `label[for='active_${toggle}']`;
    this.saveStateButton = '#state_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit state
   * @param page
   * @param stateData
   * @returns {Promise<string>}
   */
  async createEditState(page, stateData) {
    // Fill form
    await this.setValue(page, this.nameInput, stateData.name);
    await this.setValue(page, this.isoCodeInput, stateData.isoCode);
    await this.selectByVisibleText(page, this.countrySelect, stateData.country);
    await this.selectByVisibleText(page, this.zoneSelect, stateData.zone);
    await page.click(this.statusToggle(stateData.status ? 'on' : 'off'));

    // Save zone
    await this.clickAndWaitForNavigation(page, this.saveStateButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddState();
