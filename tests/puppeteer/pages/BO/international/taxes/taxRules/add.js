require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddTaxRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Tax Rules > Add new • prestashop';
    this.pageTitleEdit = 'Tax Rules > Edit: FR tax Rule • prestashop';

    // Selectors
    // Header buttons
    this.addNewTaxRuleButton = '#page-header-desc-tax_rule-new';

    // New tax rule group form
    this.taxRuleGroupForm = '#tax_rules_group_form';
    this.nameInput = `${this.taxRuleGroupForm} #name`;
    this.enabledSwitchlabel = `${this.taxRuleGroupForm} label[for='active_%ID']`;
    this.saveTaxButton = `${this.taxRuleGroupForm} #tax_rules_group_form_submit_btn`;
    // New tax rule form
    this.taxRuleForm = '#tax_rule_form';
    this.countrySelect = `${this.taxRuleForm} #country`;
    this.zipCodeInput = `${this.taxRuleForm} #zipcode`;
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
   * @param taxRuleGroupData
   * @return {Promise<textContent>}
   */
  async createEditTaxRulesGroup(taxRuleGroupData) {
    await this.setValue(this.nameInput, taxRuleGroupData.name);
    await this.page.click(this.enabledSwitchlabel.replace('%ID', taxRuleGroupData.enabled ? 'on' : 'off'));
    // Save Tax rules group
    await this.clickAndWaitForNavigation(this.saveTaxButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Fill form for add/edit tax rules group
   * @param taxRuleData
   * @return {Promise<textContent>}
   */
  async createEditTaxRules(taxRuleData) {
    await this.selectByVisibleText(this.countrySelect, taxRuleData.country);
    // await this.page.type(this.zipCodeInput, taxRuleData.zip);
    await this.selectByVisibleText(this.behaviourSelect, taxRuleData.behaviour);
    await this.selectByVisibleText(this.taxSelect, taxRuleData.tax);
    await this.setValue(this.descriptionInput, taxRuleData.description);
    // Save Tax rules
    await this.clickAndWaitForNavigation(this.saveAndStayButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Click on Add new tax rule
   * @return {Promise<void>}
   */
  async clickOnAddNewTaxRule() {
    await this.page.click(this.addNewTaxRuleButton);
  }
};
