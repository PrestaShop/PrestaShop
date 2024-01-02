import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import type {Page} from 'playwright';

/**
 * Localization page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class Localization extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly importLocalizationPackSuccessfulMessage: string;

  public readonly successfulSettingsUpdateMessage: string;

  private readonly importlocalizationPackSelect: string;

  private readonly importStatesCheckbox: string;

  private readonly importTaxesCheckbox: string;

  private readonly importCurrenciesCheckbox: string;

  private readonly importLanguagesCheckbox: string;

  private readonly importUnitsCheckbox: string;

  private readonly updatepriceDisplayForGroupsCHeckbox: string;

  private readonly downloadPackDataToggleInput: (toggle: number) => string;

  private readonly importButton: string;

  private readonly defaultLanguageSelector: string;

  private readonly languageFromBrowserToggleInput: (toggle: number) => string;

  private readonly defaultCurrencySelect: string;

  private readonly defaultCountrySelect: string;

  private readonly saveConfigurationFormButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on localization page
   */
  constructor() {
    super();

    this.pageTitle = `Localization • ${global.INSTALL.SHOP_NAME}`;
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
    this.downloadPackDataToggleInput = (toggle: number) => `#import_localization_pack_download_pack_data_${toggle}`;
    this.importButton = '#form-import-localization-save-button';

    // Configuration form selectors
    this.defaultLanguageSelector = '#form_default_language';
    this.languageFromBrowserToggleInput = (toggle: number) => `#form_detect_language_from_browser_${toggle}`;
    this.defaultCurrencySelect = '#form_default_currency';
    this.defaultCountrySelect = '#form_default_country';
    this.saveConfigurationFormButton = '#form-configuration-save-button';
  }

  /* Methods */
  /**
   * Import a localization pack
   * @param page {Page} Browser tab
   * @param country {string} Country to select
   * @param contentToImport {ImportContent} Data of content to import to choose
   * @param downloadPackData {boolean} True if we need to download pack data
   * @return {Promise<string>}
   */
  async importLocalizationPack(
    page: Page,
    country: string,
    contentToImport: ImportContent,
    downloadPackData: boolean = true,
  ): Promise<string> {
    // Choose which country to import
    await this.selectByVisibleText(page, this.importlocalizationPackSelect, country);

    // Set content import checkboxes
    await this.setHiddenCheckboxValue(page, this.importStatesCheckbox, contentToImport.importStates);
    await this.setHiddenCheckboxValue(page, this.importTaxesCheckbox, contentToImport.importTaxes);
    await this.setHiddenCheckboxValue(page, this.importCurrenciesCheckbox, contentToImport.importCurrencies);
    await this.setHiddenCheckboxValue(page, this.importLanguagesCheckbox, contentToImport.importLanguages);
    await this.setHiddenCheckboxValue(page, this.importUnitsCheckbox, contentToImport.importUnits);
    await this.setHiddenCheckboxValue(
      page,
      this.updatepriceDisplayForGroupsCHeckbox,
      contentToImport.updatePriceDisplayForGroups,
    );

    // Choose if we download pack of data
    await this.setChecked(page, this.downloadPackDataToggleInput(downloadPackData ? 1 : 0));

    // Import the pack
    await page.locator(this.importButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select default language
   * @param page {Page} Browser tab
   * @param language {string} Language to select
   * @param languageFromBrowser {boolean} True if we need to use language from browser
   * @returns {Promise<string>}
   */
  async setDefaultLanguage(page: Page, language: string, languageFromBrowser: boolean = true): Promise<string> {
    await this.selectByVisibleText(page, this.defaultLanguageSelector, language);
    await this.setChecked(page, this.languageFromBrowserToggleInput(languageFromBrowser ? 1 : 0));
    await page.locator(this.saveConfigurationFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default currency
   * @param page {Page} Browser tab
   * @param currency {string} Value of currency to select
   * @returns {Promise<string>}
   */
  async setDefaultCurrency(page: Page, currency: string): Promise<string> {
    await this.dialogListener(page);
    await this.selectByVisibleText(page, this.defaultCurrencySelect, currency);
    await this.waitForSelectorAndClick(page, this.saveConfigurationFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default country
   * @param page {Page} Browser tab
   * @param country {string} Value of country to select
   * @return {Promise<string>}
   */
  async setDefaultCountry(page: Page, country: string): Promise<string> {
    await this.selectByVisibleText(page, this.defaultCountrySelect, country);
    await page.locator(this.saveConfigurationFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Localization();
