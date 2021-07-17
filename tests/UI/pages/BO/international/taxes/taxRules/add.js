require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddTaxRules extends BOBasePage {
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
    this.enabledSwitchLabel = id => `${this.taxRuleGroupForm} label[for='active_${id}']`;
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
   * @param page
   * @param taxRuleGroupData
   * @returns {Promise<string>}
   */
  async createEditTaxRulesGroup(page, taxRuleGroupData) {
    await this.setValue(page, this.nameInput, taxRuleGroupData.name);
    await page.click(this.enabledSwitchLabel(taxRuleGroupData.enabled ? 'on' : 'off'));
    // Save Tax rules group
    await this.clickAndWaitForNavigation(page, this.saveTaxButton);
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Fill form for add/edit tax rules group
   * @param page
   * @param taxRuleData
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
   * Click on Add new tax rule
   * @param page
   * @return {Promise<void>}
   */
  async clickOnAddNewTaxRule(page) {
    await page.click(this.addNewTaxRuleButton);
  }
}
module.exports = new AddTaxRules();
