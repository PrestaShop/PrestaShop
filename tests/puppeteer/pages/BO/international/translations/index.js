require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Translations extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Translations • ';

    // Selectors
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
   * Export language
   * @param language
   * @param theme
   * @return {Promise<void>}
   */
  async exportLanguage(language, theme) {
    await this.selectByVisibleText(this.exportLanguageSelect, language);
    await this.selectByVisibleText(this.exportLanguageThemeSelect, theme);
    await this.page.click(this.exportLanguageButton);
  }
};
