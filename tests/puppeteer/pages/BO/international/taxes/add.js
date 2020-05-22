require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddTax extends BOBasePage {
  constructor(page) {
    super(page);

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
   * @param lang
   * @return {Promise<void>}
   */
  async changeInputLanguage(lang) {
    await Promise.all([
      this.page.click(this.inputLangDropdownButton),
      this.waitForVisibleSelector(`${this.inputLangDropdownButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.inputLangChoiceSpan(lang)),
      this.waitForVisibleSelector(`${this.inputLangDropdownButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Fill form for add/edit tax
   * @param taxData
   * @return {Promise<textContent>}
   */
  async createEditTax(taxData) {
    await this.changeInputLanguage('en');
    await this.setValue(this.nameEnInput, taxData.name);
    await this.changeInputLanguage('fr');
    await this.setValue(this.nameFrInput, taxData.frName);
    await this.setValue(this.rateInput, taxData.rate);
    await this.page.click(this.enabledSwitchLabel(taxData.enabled ? 1 : 0));
    // Save Tax
    await this.clickAndWaitForNavigation(this.saveTaxButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
