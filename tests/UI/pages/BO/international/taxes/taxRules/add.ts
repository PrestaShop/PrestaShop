import BOBasePage from '@pages/BO/BObasePage';

import type TaxRuleData from '@data/faker/taxRule';
import type TaxRulesGroupData from '@data/faker/taxRulesGroup';

import type {Page} from 'playwright';

/**
 * Add tax rules page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddTaxRules extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly addNewTaxRuleButton: string;

  private readonly taxRuleGroupForm: string;

  private readonly nameInput: string;

  private readonly statusInput: (id: string) => string;

  private readonly saveTaxButton: string;

  private readonly taxRuleForm: string;

  private readonly countrySelect: string;

  private readonly behaviourSelect: string;

  private readonly taxSelect: string;

  private readonly descriptionInput: string;

  private readonly saveAndStayButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add tax rules page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Tax Rules > Add new';
    this.pageTitleEdit = 'Tax Rules > Edit';

    // Selectors
    // Header buttons
    this.addNewTaxRuleButton = 'a[data-role=page-header-desc-tax_rule-link]';

    // New tax rule group form
    this.taxRuleGroupForm = '#tax_rules_group_form';
    this.nameInput = `${this.taxRuleGroupForm} #name`;
    this.statusInput = (id: string) => `${this.taxRuleGroupForm} input#active_${id}`;
    this.saveTaxButton = `${this.taxRuleGroupForm} #tax_rules_group_form_submit_btn`;

    // New tax rule form
    this.taxRuleForm = '#tax_rule_form';
    this.countrySelect = `${this.taxRuleForm} #country`;
    this.behaviourSelect = `${this.taxRuleForm} #behavior`;
    this.taxSelect = `${this.taxRuleForm} #id_tax`;
    this.descriptionInput = `${this.taxRuleForm} #description`;
    this.saveAndStayButton = `${this.taxRuleForm} #tax_rule_form_submit_btn_1`;
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit tax rules group
   * @param page {Page} Browser tab
   * @param taxRuleGroupData {TaxRulesGroupData} Data to set on tax rule group data
   * @returns {Promise<string>}
   */
  async createEditTaxRulesGroup(page: Page, taxRuleGroupData: TaxRulesGroupData): Promise<string> {
    await this.setValue(page, this.nameInput, taxRuleGroupData.name);
    await this.setChecked(page, this.statusInput(taxRuleGroupData.enabled ? 'on' : 'off'));
    // Save Tax rules group
    await this.clickAndWaitForURL(page, this.saveTaxButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Fill form for add/edit tax rules group
   * @param page {Page} Browser tab
   * @param taxRuleData {TaxRuleData} Data to set on new/edit tax rule data
   * @returns {Promise<string>}
   */
  async createEditTaxRules(page: Page, taxRuleData: TaxRuleData): Promise<string> {
    await this.selectByVisibleText(page, this.countrySelect, taxRuleData.country);
    await this.selectByVisibleText(page, this.behaviourSelect, taxRuleData.behaviour);
    await this.selectByVisibleText(page, this.taxSelect, taxRuleData.name);
    await this.setValue(page, this.descriptionInput, taxRuleData.description);
    // Save Tax rules
    await page.locator(this.saveAndStayButton).click();

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Click on add new tax rule
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnAddNewTaxRule(page: Page): Promise<void> {
    await page.locator(this.addNewTaxRuleButton).click();
  }
}
export default new AddTaxRules();
