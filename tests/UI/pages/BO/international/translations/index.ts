import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Translations page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Translations extends BOBasePage {
  public readonly pageTitle: string;

  public readonly validationMessage: string;

  private readonly validationResetMessage: string;

  public readonly successAlertMessage: string;

  private readonly typeOfTranslationSelect: string;

  private readonly selectYourThemeSelect: string;

  private readonly selectYourLanguageSelect: string;

  private readonly modifyTranslationsButton: string;

  private readonly searchInput: string;

  private readonly searchButton: string;

  private readonly translationTextarea: string;

  private readonly saveTranslationButton: string;

  private readonly resetTranslationButton: string;

  private readonly growlMessage: string;

  private readonly addUpdateLanguageForm: string;

  private readonly languageToAddSelect: string;

  private readonly searchLanguageInput: string;

  private readonly searchLanguageResult: string;

  private readonly addUpdateLanguageButton: string;

  private readonly exportLanguageSelect: string;

  private readonly prestashopTranslationRadio: string;

  private readonly prestashopTranslationTypeCheckbox: (position: number) => string;

  private readonly themeTranslationRadio: string;

  private readonly exportLanguageThemeSelect: string;

  private readonly exportLanguageButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on translations page
   */
  constructor() {
    super();

    this.pageTitle = 'Translations • ';
    this.validationMessage = 'Translations successfully updated';
    this.validationResetMessage = 'Translations successfully reset';
    this.successAlertMessage = 'The translations have been successfully added.';

    // Selectors
    // Modify translation form
    this.typeOfTranslationSelect = '#form_translation_type';
    this.selectYourThemeSelect = '#form_theme';
    this.selectYourLanguageSelect = '#form_language';
    this.modifyTranslationsButton = 'form[action*=\'translations/modify\'] button';
    this.searchInput = '#search input';
    this.searchButton = '#search button';
    this.translationTextarea = 'textarea.form-control';
    this.saveTranslationButton = '#app button[type=\'submit\']';
    this.resetTranslationButton = '#app  button[class*=\'btn-outline-secondary\']';
    this.growlMessage = '#growls-default div.growl-message';

    // Add/Update language form
    this.addUpdateLanguageForm = 'form[action*=\'add-update-language\']';
    this.languageToAddSelect = '#select2-form_iso_localization_pack-container';
    this.searchLanguageInput = 'span.select2-search.select2-search--dropdown input';
    this.searchLanguageResult = 'li.select2-results__option--highlighted';
    this.addUpdateLanguageButton = `${this.addUpdateLanguageForm} .card-footer button`;

    // Export language form
    this.exportLanguageSelect = '#form_iso_code';

    // Prestashop translation
    this.prestashopTranslationRadio = '#form_core_selectors_core_type';
    this.prestashopTranslationTypeCheckbox = (position: number) => `#form_core_selectors_selected_value_${position}`;

    // Theme translation
    this.themeTranslationRadio = '#form_themes_selectors_themes_type';
    this.exportLanguageThemeSelect = '#form_themes_selectors_selected_value';
    this.exportLanguageButton = '#form-export-language-button';
  }

  /*
  Methods
   */

  /**
   * Modify translation
   * @param page {Page} Browser tab
   * @param translation {string} Value to choose on translation select
   * @param theme {string} Value of theme to choose on theme select
   * @param language {string} Value of language to select on language select
   * @returns {Promise<void>}
   */
  async modifyTranslation(page: Page, translation: string, theme: string, language: string): Promise<void> {
    await this.selectByVisibleText(page, this.typeOfTranslationSelect, translation);
    await this.selectByVisibleText(page, this.selectYourThemeSelect, theme);
    await this.selectByVisibleText(page, this.selectYourLanguageSelect, language);
    await this.clickAndWaitForNavigation(page, this.modifyTranslationsButton);
  }

  /**
   * Search translation
   * @param page {Page} Browser tab
   * @param expression {string} Expression to set on search input
   * @returns {Promise<void>}
   */
  async searchTranslation(page: Page, expression: string): Promise<void> {
    await this.setValue(page, this.searchInput, expression);
    await page.click(this.searchButton);
    await this.waitForAttachedSelector(page, this.translationTextarea);
    await page.waitForTimeout(2000);
  }

  /**
   * Translate an expression
   * @param page {Page} Browser tab
   * @param translation {string} Value of translation to set
   * @returns {Promise<string>}
   */
  async translateExpression(page: Page, translation: string): Promise<string> {
    await this.setValue(page, this.translationTextarea, translation);
    await page.click(this.saveTranslationButton);

    return this.getTextContent(page, this.growlMessage);
  }

  /**
   * Reset translation
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async resetTranslation(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.resetTranslationButton);
    await page.click(this.saveTranslationButton);

    return this.getTextContent(page, this.growlMessage);
  }

  /**
   * Add/Update language
   * @param page {Page} Browser tab
   * @param language {string} Language to set on language input
   * @returns {Promise<string>}
   */
  async addUpdateLanguage(page: Page, language: string): Promise<string> {
    await this.waitForSelectorAndClick(page, this.languageToAddSelect);
    await this.setValue(page, this.searchLanguageInput, language);
    await this.waitForSelectorAndClick(page, this.searchLanguageResult);
    await page.click(this.addUpdateLanguageButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select export language
   * @param page {Page} Browser tab
   * @param language {string} language to export
   * @return {Promise<void>}
   */
  async selectExportLanguage(page: Page, language: string): Promise<void> {
    await this.selectByVisibleText(page, this.exportLanguageSelect, language);
  }

  /**
   * Export Prestashop translation by type (BO, FO, Email or Other)
   * @param page {Page} Browser type
   * @param language {string} language to export
   * @param types {Array<string>} Array of strings of what to export
   * @returns {Promise<string|null>}
   */
  async exportPrestashopTranslations(page: Page, language: string, types: string[] = ['Other']): Promise<string|null> {
    await this.selectExportLanguage(page, language);
    await page.click(this.prestashopTranslationRadio);

    for (let i = 0; i < types.length; i++) {
      let selector;

      switch (types[i]) {
        case 'Back office':
          selector = this.prestashopTranslationTypeCheckbox(0);
          break;

        case 'Front office':
          selector = this.prestashopTranslationTypeCheckbox(1);
          break;

        case 'Email':
          selector = this.prestashopTranslationTypeCheckbox(2);
          break;

        case 'Other':
          selector = this.prestashopTranslationTypeCheckbox(3);
          break;

        default:
          throw new Error(`${types[i]} was not found as a translation option`);
      }

      await this.setHiddenCheckboxValue(page, selector, true);
    }

    return this.clickAndWaitForDownload(page, this.exportLanguageButton);
  }
}

export default new Translations();
