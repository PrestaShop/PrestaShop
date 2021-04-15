require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Translations extends BOBasePage {
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
    this.themeTranslationRadio = '#form_themes_selectors_themes_type';
    this.exportLanguageThemeSelect = '#form_themes_selectors_selected_value';
    this.exportLanguageButton = '#form-export-language-button';
  }

  /*
  Methods
   */

  /**
   * Modify translation
   * @param page
   * @param translation
   * @param theme
   * @param language
   * @returns {Promise<void>}
   */
  async modifyTranslation(page, translation, theme, language) {
    await this.selectByVisibleText(page, this.typeOfTranslationSelect, translation);
    await this.selectByVisibleText(page, this.selectYourThemeSelect, theme);
    await this.selectByVisibleText(page, this.selectYourLanguageSelect, language);
    await this.clickAndWaitForNavigation(page, this.modifyTranslationsButton);
  }

  /**
   * Search translation
   * @param page
   * @param expression
   * @returns {Promise<void>}
   */
  async searchTranslation(page, expression) {
    await this.setValue(page, this.searchInput, expression);
    await page.click(this.searchButton);
    await this.waitForAttachedSelector(page, this.translationTextarea);
    await page.waitForTimeout(2000);
  }

  /**
   * Translate an expression
   * @param page
   * @param translation
   * @returns {Promise<string>}
   */
  async translateExpression(page, translation) {
    await this.setValue(page, this.translationTextarea, translation);
    await page.click(this.saveTranslationButton);
    return this.getTextContent(page, this.growlMessage);
  }

  /**
   * Reset translation
   * @param page
   * @returns {Promise<string>}
   */
  async resetTranslation(page) {
    await this.waitForSelectorAndClick(page, this.resetTranslationButton);
    await page.click(this.saveTranslationButton);
    return this.getTextContent(page, this.growlMessage);
  }

  /**
   * Add/Update language
   * @param page
   * @param language
   * @returns {Promise<string>}
   */
  async addUpdateLanguage(page, language) {
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
  async selectExportLanguage(page, language) {
    await this.selectByVisibleText(page, this.exportLanguageSelect, language);
  }

  /**
   * Export theme translation
   * @param page {Page} Browser tab
   * @param language {string} language to export
   * @param theme {string} Theme to export
   * @returns {Promise<string>}
   */
  async exportThemeTranslations(page, language, theme = 'classic') {
    await this.selectExportLanguage(page, language);
    await page.click(this.themeTranslationRadio);
    await this.selectByVisibleText(page, this.exportLanguageThemeSelect, theme);

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.exportLanguageButton),
    ]);

    return download.path();
  }
}

module.exports = new Translations();
