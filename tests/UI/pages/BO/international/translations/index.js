require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Translations extends BOBasePage {
  constructor() {
    super();

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
