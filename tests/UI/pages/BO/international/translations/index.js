require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Translations extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Translations • ';
    this.validationMessage = 'Translations successfully updated';

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
    this.growlMessage = '#growls-default div.growl-message';
    // Export language form
    this.exportLanguageForm = 'form[action*=\'translations/export\']';
    this.exportLanguageSelect = '#form_iso_code';
    this.exportLanguageThemeSelect = '#form_theme_name';
    this.exportLanguageButton = `${this.exportLanguageForm} .card-footer button`;
  }

  /*
  Methods
   */

  /**
   * Modify translation
   * @param translation
   * @param theme
   * @param language
   * @returns {Promise<void>}
   */
  async modifyTranslation(translation, theme, language) {
    await this.selectByVisibleText(this.typeOfTranslationSelect, translation);
    await this.selectByVisibleText(this.selectYourThemeSelect, theme);
    await this.selectByVisibleText(this.selectYourLanguageSelect, language);
    await this.clickAndWaitForNavigation(this.modifyTranslationsButton);
  }

  /**
   * Search translation
   * @param expression
   * @returns {Promise<void>}
   */
  async searchTranslation(expression){
    await this.setValue(this.searchInput, expression);
    await this.page.click(this.searchButton);
    await this.page.waitForSelector(this.translationTextarea);
    await this.page.waitForTimeout(2000);
  }

  /**
   * Translate an expression
   * @param expression
   * @param translation
   * @returns {Promise<string>}
   */
  async translateExpression(expression, translation) {
    await this.setValue(this.translationTextarea, translation);
    await this.page.click(this.saveTranslationButton);
    return this.getTextContent(this.growlMessage);
  }

  /**
   * Export language
   * @param page
   * @param language
   * @param theme
   * @return {Promise<*>}
   */
  async exportLanguage(page, language, theme) {
    await this.selectByVisibleText(page, this.exportLanguageSelect, language);
    await this.selectByVisibleText(page, this.exportLanguageThemeSelect, theme);

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.exportLanguageButton),
    ]);

    return download.path();
  }
}

module.exports = new Translations();
