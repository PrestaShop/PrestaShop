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
    this.inputLangChoiceSpan = 'div.dropdown-menu span[data-locale=\'%LANG\']';
    this.rateInput = '#tax_rate';
    this.enabledSwitchlabel = 'label[for=\'tax_is_enabled_%ID\']';
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
      this.page.waitForSelector(`${this.inputLangDropdownButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.inputLangChoiceSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.inputLangDropdownButton}[aria-expanded='false']`),
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
    if (taxData.enabled) {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '1'));
    } else {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '0'));
    }
    // Save Tax
    await this.clickAndWaitForNavigation(this.saveTaxButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
