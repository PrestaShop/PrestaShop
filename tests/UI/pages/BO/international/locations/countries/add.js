require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add country page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCountry extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add country page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Countries > Add new •';
    this.pageTitleEdit = 'Edit: ';

    // Selectors
    this.nameInput = '#name_1';
    this.isoCodeInput = '#iso_code';
    this.callPrefixInput = '#call_prefix';
    this.defaultCurrencySelect = '#id_currency';
    this.zoneSelect = '#id_zone';
    this.needZipCodeLabel = toggle => `#need_zip_code_${toggle}`;
    this.zipCodeFormatInput = '#zip_code_format';
    this.activeLabel = toggle => `#active_${toggle}`;
    this.containsStatesLabel = toggle => `#contains_states_${toggle}`;
    this.needIdentificationNumberLabel = toggle => `#need_identification_number_${toggle}`;
    this.displayTaxLabel = toggle => `#display_tax_label_${toggle}`;
    this.saveCountryButton = '#country_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit country
   * @param page {Page} Browser tab
   * @param countryData {CountryData} Data to set on new country form
   * @returns {Promise<string>}
   */
  async createEditCountry(page, countryData) {
    await this.setValue(page, this.nameInput, countryData.name);
    await this.setValue(page, this.isoCodeInput, countryData.isoCode);
    await this.setValue(page, this.callPrefixInput, countryData.callPrefix);
    await this.selectByVisibleText(page, this.defaultCurrencySelect, countryData.currency);
    await this.selectByVisibleText(page, this.zoneSelect, countryData.zone);
    await page.check(this.needZipCodeLabel(countryData.needZipCode ? 'on' : 'off'));
    await this.setValue(page, this.zipCodeFormatInput, countryData.zipCodeFormat);
    await page.check(this.activeLabel(countryData.active ? 'on' : 'off'));
    await page.check(this.containsStatesLabel(countryData.containsStates ? 'on' : 'off'));
    await page.check(this.needIdentificationNumberLabel(countryData.needIdentificationNumber ? 'on' : 'off'));
    await page.check(this.displayTaxLabel(countryData.displayTaxNumber ? 'on' : 'off'));
    // Save country
    await this.clickAndWaitForNavigation(page, this.saveCountryButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddCountry();
