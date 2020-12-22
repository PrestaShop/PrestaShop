require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddTax extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Taxes •';
    this.pageTitleEdit = 'Edit: ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.nameEnInput = '#tax_name_1';
    this.nameFrInput = '#tax_name_2';
    this.inputLangDropdownButton = 'button#tax_name';
    this.inputLangChoiceSpan = lang => `div.dropdown-menu span[data-locale='${lang}']`;
    this.rateInput = '#tax_rate';
    this.enabledSwitchLabel = id => `label[for='tax_is_enabled_${id}']`;
    this.saveTaxButton = 'div.card-footer button';
  }
  /*
  Methods
   */

  /**
   * Change language for input name
   * @param page
   * @param lang
   * @return {Promise<void>}
   */
  async changeInputLanguage(page, lang) {
    await Promise.all([
      page.click(this.inputLangDropdownButton),
      this.waitForVisibleSelector(page, `${this.inputLangDropdownButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.inputLangChoiceSpan(lang)),
      this.waitForVisibleSelector(page, `${this.inputLangDropdownButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Fill form for add/edit tax
   * @param page
   * @param taxData
   * @returns {Promise<string>}
   */
  async createEditTax(page, taxData) {
    await this.changeInputLanguage(page, 'en');
    await this.setValue(page, this.nameEnInput, taxData.name);
    await this.changeInputLanguage(page, 'fr');
    await this.setValue(page, this.nameFrInput, taxData.frName);
    await this.setValue(page, this.rateInput, taxData.rate);
    await page.click(this.enabledSwitchLabel(taxData.enabled ? 1 : 0));
    // Save Tax
    await this.clickAndWaitForNavigation(page, this.saveTaxButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}
module.exports = new AddTax();
