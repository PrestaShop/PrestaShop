require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add tax rules page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddTaxRules extends BOBasePage {
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
    this.addNewTaxRuleButton = '#page-header-desc-tax_rule-new';

    // New tax rule group form
    this.taxRuleGroupForm = '#tax_rules_group_form';
    this.nameInput = `${this.taxRuleGroupForm} #name`;
    this.statusInput = id => `${this.taxRuleGroupForm} input#active_${id}`;
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
  async createEditTaxRulesGroup(page, taxRuleGroupData) {
    await this.setValue(page, this.nameInput, taxRuleGroupData.name);
    await page.check(this.statusInput(taxRuleGroupData.enabled ? 'on' : 'off'));
    // Save Tax rules group
    await this.clickAndWaitForNavigation(page, this.saveTaxButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Fill form for add/edit tax rules group
   * @param page {Page} Browser tab
   * @param taxRuleData {TaxRuleData} Data to set on new/edit tax rule data
   * @returns {Promise<string>}
   */
  async createEditTaxRules(page, taxRuleData) {
    await this.selectByVisibleText(page, this.countrySelect, taxRuleData.country);
    await this.selectByVisibleText(page, this.behaviourSelect, taxRuleData.behaviour);
    await this.selectByVisibleText(page, this.taxSelect, taxRuleData.tax);
    await this.setValue(page, this.descriptionInput, taxRuleData.description);
    // Save Tax rules
    await this.clickAndWaitForNavigation(page, this.saveAndStayButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Click on add new tax rule
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnAddNewTaxRule(page) {
    await page.click(this.addNewTaxRuleButton);
  }
}
module.exports = new AddTaxRules();
