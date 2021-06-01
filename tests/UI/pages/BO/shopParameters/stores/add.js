require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add store page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddStore extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on add store page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Stores > Add new •';
    this.pageTitleEdit = 'Stores > Edit:';

    // Form selectors
    this.storeForm = '#store_form';
    this.nameInput = '#name_1';
    this.address1Input = '#address1_1';
    this.address2Input = '#address2_1';
    this.postcodeInput = '#postcode';
    this.cityInput = '#city';
    this.countrySelect = '#id_country';
    this.latitudeInput = '#latitude';
    this.longitudeInput = '#longitude';
    this.phoneInput = '#phone';
    this.faxInput = '#fax';
    this.emailInput = '#email';
    this.noteTextarea = '#note_1';
    this.statusToggle = toggle => `${this.storeForm} #active_${toggle}`;
    this.hoursInput = pos => `input[name='hours[${pos}][1]']`;
    this.saveButton = '#store_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';
  }

  /* Methods */

  /**
   * Fill creation / edition form for store and save it
   * @param page {Page} Browser tab
   * @param storeData {StoreData} Data to set on store form
   * @return {Promise<string>}
   */
  async createEditStore(page, storeData) {
    // Set name
    await this.setValue(page, this.nameInput, storeData.name);

    // Set address inputs
    await this.setValue(page, this.address1Input, storeData.address1);
    await this.setValue(page, this.address2Input, storeData.address2);
    await this.setValue(page, this.postcodeInput, storeData.postcode);
    await this.setValue(page, this.cityInput, storeData.city);
    await this.selectByVisibleText(page, this.countrySelect, storeData.country);
    await this.setValue(page, this.latitudeInput, storeData.latitude);
    await this.setValue(page, this.longitudeInput, storeData.longitude);

    // Set phone inputs
    await this.setValue(page, this.phoneInput, storeData.phone);
    await this.setValue(page, this.faxInput, storeData.fax);

    // Set email and notes inputs
    await this.setValue(page, this.emailInput, storeData.email);
    await this.setValue(page, this.noteTextarea, storeData.note);

    // Set store status
    await page.check(this.statusToggle(storeData.status ? 'on' : 'off'));

    // Set opening hours
    for (let day = 1; day <= 7; day++) {
      await this.setValue(page, this.hoursInput(day), storeData.hours[day - 1]);
    }

    // Save store
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddStore();
