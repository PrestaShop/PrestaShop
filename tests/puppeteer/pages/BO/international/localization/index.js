require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

module.exports = class Localization extends LocalizationBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Localization • ';
    this.importLocalizationPackSuccessfulMessage = 'Localization pack imported successfully.';

    // Import localization pack selectors
    this.importLocalizationPackForm = 'form[name=\'import_localization_pack\']';
    this.importlocalizationPackSelect = '#import_localization_pack_iso_localization_pack';
    this.importStatesCheckbox = '#import_localization_pack_content_to_import_0';
    this.importTaxesCheckbox = '#import_localization_pack_content_to_import_1';
    this.importCurrenciesCheckbox = '#import_localization_pack_content_to_import_2';
    this.importLanguagesCheckbox = '#import_localization_pack_content_to_import_3';
    this.importUnitsCheckbox = '#import_localization_pack_content_to_import_4';
    this.updatepriceDisplayForGroupsCHeckbox = '#import_localization_pack_content_to_import_5';
    this.downloadPackDataSwitch = 'label[for=\'import_localization_pack_download_pack_data_%ID\']';
    this.importButton = `${this.importLocalizationPackForm} .card-footer button`;
    // Configuration form selectors
    this.defaultLanguageSelector = '#form_configuration_default_language';
    this.languageFromBrowserLabel = 'label[for=\'form_configuration_detect_language_from_browser_%TOGGLE\']';
    this.saveConfigurationFormButton = '#main-div form[name=\'form\'] .card-footer button';
  }

  /* Methods */
  /**
   * Import a localization pack
   * @param country
   * @param contentToImport
   * @param downloadPackData
   * @return {Promise<void>}
   */
  async importLocalizationPack(country, contentToImport, downloadPackData = true) {
    // Choose which country to import
    await this.selectByVisibleText(this.importlocalizationPackSelect, country);
    // Set content import checkboxes
    await this.updateCheckboxValue(this.importStatesCheckbox, contentToImport.importStates);
    await this.updateCheckboxValue(this.importTaxesCheckbox, contentToImport.importTaxes);
    await this.updateCheckboxValue(this.importCurrenciesCheckbox, contentToImport.importCurrencies);
    await this.updateCheckboxValue(this.importLanguagesCheckbox, contentToImport.importLanguages);
    await this.updateCheckboxValue(this.importUnitsCheckbox, contentToImport.importUnits);
    await this.updateCheckboxValue(
      this.updatepriceDisplayForGroupsCHeckbox,
      contentToImport.updatePriceDisplayForGroups,
    );
    // Choose if we download pack of data
    await this.page.click(this.downloadPackDataSwitch.replace('%ID', downloadPackData ? 1 : 0));
    // Import the pack
    await this.clickAndWaitForNavigation(this.importButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }


  /**
   * Select default language
   * @param language
   * @param languageFromBrowser
   * @returns {Promise<string>}
   */
  async setDefaultLanguage(language, languageFromBrowser = true) {
    await this.selectByVisibleText(this.defaultLanguageSelector, language);
    await this.waitForSelectorAndClick(this.languageFromBrowserLabel.replace('%TOGGLE', languageFromBrowser ? 1 : 0));
    await this.waitForSelectorAndClick(this.saveConfigurationFormButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
