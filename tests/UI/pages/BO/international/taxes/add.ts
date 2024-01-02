import BOBasePage from '@pages/BO/BObasePage';

import TaxData from '@data/faker/tax';

import {Page} from 'playwright';

/**
 * Add tax page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddTax extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly successfulUpdateStatusMessage: string;

  private readonly nameEnInput: string;

  private readonly nameFrInput: string;

  private readonly inputLangDropdownButton: string;

  private readonly inputLangChoiceSpan: (lang: string) => string;

  private readonly rateInput: string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly saveTaxButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add tax page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New tax • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing tax';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.nameEnInput = '#tax_name_1';
    this.nameFrInput = '#tax_name_2';
    this.inputLangDropdownButton = 'button#tax_name_dropdown';
    this.inputLangChoiceSpan = (lang: string) => `div.dropdown-menu span[data-locale='${lang}']`;
    this.rateInput = '#tax_rate';
    this.statusToggleInput = (toggle: number) => `#tax_is_enabled_${toggle}`;
    this.saveTaxButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Change language for input name
   * @param page {Page} Browser tab
   * @param lang {string} Value of language to change
   * @return {Promise<void>}
   */
  async changeInputLanguage(page: Page, lang: string): Promise<void> {
    await Promise.all([
      page.locator(this.inputLangDropdownButton).click(),
      this.waitForVisibleSelector(page, `${this.inputLangDropdownButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.locator(this.inputLangChoiceSpan(lang)).click(),
      this.waitForVisibleSelector(page, `${this.inputLangDropdownButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Fill form for add/edit tax
   * @param page {Page} Browser tab
   * @param taxData {TaxData} Data to set on new/edit tax page
   * @returns {Promise<string>}
   */
  async createEditTax(page: Page, taxData: TaxData): Promise<string> {
    await this.changeInputLanguage(page, 'en');
    await this.setValue(page, this.nameEnInput, taxData.name);
    await this.changeInputLanguage(page, 'fr');
    await this.setValue(page, this.nameFrInput, taxData.frName);
    await this.setValue(page, this.rateInput, taxData.rate);
    await this.setChecked(page, this.statusToggleInput(taxData.enabled ? 1 : 0));
    // Save Tax
    await this.clickAndWaitForURL(page, this.saveTaxButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddTax();
