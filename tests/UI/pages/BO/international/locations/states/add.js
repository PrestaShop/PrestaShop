require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add state page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddState extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add state page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'States > Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#name';
    this.isoCodeInput = '#iso_code';
    this.countrySelect = '#id_country';
    this.zoneSelect = '#id_zone';
    this.statusToggle = toggle => `#active_${toggle}`;
    this.saveStateButton = '#state_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit state
   * @param page {Page} Browser tab
   * @param stateData {StateData} Data to set on new/edit state form
   * @returns {Promise<string>}
   */
  async createEditState(page, stateData) {
    // Fill form
    await this.setValue(page, this.nameInput, stateData.name);
    await this.setValue(page, this.isoCodeInput, stateData.isoCode);
    await this.selectByVisibleText(page, this.countrySelect, stateData.country);
    await this.selectByVisibleText(page, this.zoneSelect, stateData.zone);
    await page.check(this.statusToggle(stateData.status ? 'on' : 'off'));

    // Save zone
    await this.clickAndWaitForNavigation(page, this.saveStateButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddState();
