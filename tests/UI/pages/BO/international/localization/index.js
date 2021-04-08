require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

class Localization extends LocalizationBasePage {
  constructor() {
    super();

    this.pageTitle = 'Localization • ';
    this.importLocalizationPackSuccessfulMessage = 'Localization pack imported successfully.';
    this.successfulSettingsUpdateMessage = 'Update successful';

    // Import localization pack selectors
    this.importlocalizationPackSelect = '#import_localization_pack_iso_localization_pack';
    this.importStatesCheckbox = '#import_localization_pack_content_to_import_0';
    this.importTaxesCheckbox = '#import_localization_pack_content_to_import_1';
    this.importCurrenciesCheckbox = '#import_localization_pack_content_to_import_2';
    this.importLanguagesCheckbox = '#import_localization_pack_content_to_import_3';
    this.importUnitsCheckbox = '#import_localization_pack_content_to_import_4';
    this.updatepriceDisplayForGroupsCHeckbox = '#import_localization_pack_content_to_import_5';
    this.downloadPackDataToggleInput = toggle => `#import_localization_pack_download_pack_data_${toggle}`;
    this.importButton = '#form-import-localization-save-button';
    // Configuration form selectors
    this.defaultLanguageSelector = '#form_default_language';
    this.languageFromBrowserToggleInput = toggle => `#form_detect_language_from_browser_${toggle}`;
    this.defaultCurrencySelect = '#form_default_currency';
    this.defaultCountrySelect = '#form_default_country';
    this.saveConfigurationFormButton = '#form-configuration-save-button';
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
    await this.changeCheckboxValue(page, this.importStatesCheckbox, contentToImport.importStates);
    await this.changeCheckboxValue(page, this.importTaxesCheckbox, contentToImport.importTaxes);
    await this.changeCheckboxValue(page, this.importCurrenciesCheckbox, contentToImport.importCurrencies);
    await this.changeCheckboxValue(page, this.importLanguagesCheckbox, contentToImport.importLanguages);
    await this.changeCheckboxValue(page, this.importUnitsCheckbox, contentToImport.importUnits);
    await this.changeCheckboxValue(
      page,
      this.updatepriceDisplayForGroupsCHeckbox,
      contentToImport.updatePriceDisplayForGroups,
    );

    // Choose if we download pack of data
    await page.check(this.downloadPackDataToggleInput(downloadPackData ? 1 : 0));

    // Import the pack
    await this.clickAndWaitForNavigation(page, this.importButton);
    return this.getAlertSuccessBlockParagraphContent(page);
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
    await page.check(this.languageFromBrowserToggleInput(languageFromBrowser ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveConfigurationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default country
   * @param page
   * @param country
   * @return {Promise<string>}
   */
  async setDefaultCountry(page, country) {
    await this.selectByVisibleText(page, this.defaultCountrySelect, country);
    await this.clickAndWaitForNavigation(page, this.saveConfigurationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}
module.exports = new Localization();
