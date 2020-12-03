require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

class Localization extends LocalizationBasePage {
  constructor() {
    super();

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
    this.downloadPackDataSwitch = id => `label[for='import_localization_pack_download_pack_data_${id}']`;
    this.importButton = `${this.importLocalizationPackForm} .card-footer button`;
    // Configuration form selectors
    this.defaultLanguageSelector = '#form_configuration_default_language';
    this.languageFromBrowserLabel = toggle => `label[for='form_configuration_detect_language_from_browser_${toggle}']`;
    this.defaultCurrencySelect = '#form_configuration_default_currency';
    this.saveConfigurationFormButton = '#main-div form[name=\'form\'] .card-footer button';
  }

  /* Methods */
  /**
   * Import a localization pack
   * @param page
   * @param country
   * @param contentToImport
   * @param downloadPackData
   * @return {Promise<void>}
   */
  async importLocalizationPack(page, country, contentToImport, downloadPackData = true) {
    // Choose which country to import
    await this.selectByVisibleText(page, this.importlocalizationPackSelect, country);
    // Set content import checkboxes
    await this.updateCheckboxValue(page, this.importStatesCheckbox, contentToImport.importStates);
    await this.updateCheckboxValue(page, this.importTaxesCheckbox, contentToImport.importTaxes);
    await this.updateCheckboxValue(page, this.importCurrenciesCheckbox, contentToImport.importCurrencies);
    await this.updateCheckboxValue(page, this.importLanguagesCheckbox, contentToImport.importLanguages);
    await this.updateCheckboxValue(page, this.importUnitsCheckbox, contentToImport.importUnits);
    await this.updateCheckboxValue(
      page,
      this.updatepriceDisplayForGroupsCHeckbox,
      contentToImport.updatePriceDisplayForGroups,
    );
    // Choose if we download pack of data
    await page.click(this.downloadPackDataSwitch(downloadPackData ? 1 : 0));
    // Import the pack
    await this.clickAndWaitForNavigation(page, this.importButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }


  /**
   * Select default language
   * @param page
   * @param language
   * @param languageFromBrowser
   * @returns {Promise<string>}
   */
  async setDefaultLanguage(page, language, languageFromBrowser = true) {
    await this.selectByVisibleText(page, this.defaultLanguageSelector, language);
    await this.waitForSelectorAndClick(page, this.languageFromBrowserLabel(languageFromBrowser ? 1 : 0));
    await this.waitForSelectorAndClick(page, this.saveConfigurationFormButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Set default currency
   * @param page
   * @param currency
   * @returns {Promise<string>}
   */
  async setDefaultCurrency(page, currency) {
    this.dialogListener(page);
    await this.selectByVisibleText(page, this.defaultCurrencySelect, currency);
    await this.waitForSelectorAndClick(page, this.saveConfigurationFormButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}
module.exports = new Localization();
