require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class CatalogPriceRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Catalog Price Rules •';

    // Selectors header
    this.addNewCatalogPriceRuleButton = '#page-header-desc-specific_price_rule-new_specific_price_rule';
    // Selectors grid panel
    this.gridPanel = '#attachment_grid_panel';
    this.gridTable = '#table-specific_price_rule';
    // Filters
    this.filterNameColumn = `${this.gridTable} input[name='specific_price_ruleFilter_a!name']`;
    this.filterSearchButton = `${this.gridTable} #submitFilterButtonspecific_price_rule`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td .btn-group-action`;
    this.dropdownToggleButton = `${this.actionsColumn} button.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} ul.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a.delete`;
  }

  /* Methods */
  /**
   * Go to add new Catalog price rule page
   * @returns {Promise<void>}
   */
  async goToAddNewCatalogPriceRulePage() {
    await this.clickAndWaitForNavigation(this.addNewCatalogPriceRuleButton);
  }

  /**
   * FilterTableByName
   * @param name
   * @returns {Promise<void>}
   */
  async filterTableByRuleName(name) {
    await this.setValue(this.filterNameColumn, name);
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Delete catalog price rule
   * @param ruleName
   * @returns {Promise<string>}
   */
  async deleteCatalogPriceRule(ruleName) {
    await this.dialogListener(true);
    if (await this.elementVisible(this.filterNameColumn)) {
      await this.filterTableByRuleName(ruleName);
    }
    await this.waitForSelectorAndClick(this.dropdownToggleButton.replace('%ROW', 1));
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', 1));
    return this.getTextContent(this.alertSuccessBlock);
  }
};
