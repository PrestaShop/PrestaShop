require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CatalogPriceRules extends BOBasePage {
  constructor() {
    super();

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
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td .btn-group-action`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} button.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} ul.dropdown-menu`;
    this.confirmDeleteButton = '#popup_ok';
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a.delete`;
  }

  /* Methods */
  /**
   * Go to add new Catalog price rule page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewCatalogPriceRulePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCatalogPriceRuleButton);
  }

  /**
   * Filter table by rule name
   * @param page
   * @param name
   * @returns {Promise<void>}
   */
  async filterTableByRuleName(page, name) {
    await this.setValue(page, this.filterNameColumn, name);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Delete catalog price rule
   * @param page
   * @param ruleName
   * @returns {Promise<string>}
   */
  async deleteCatalogPriceRule(page, ruleName) {
    await this.dialogListener(page, true);
    if (await this.elementVisible(page, this.filterNameColumn)) {
      await this.filterTableByRuleName(page, ruleName);
    }
    await this.waitForSelectorAndClick(page, this.dropdownToggleButton(1));
    await Promise.all([
      page.click(this.deleteRowLink(1)),
      this.waitForVisibleSelector(page, this.confirmDeleteButton),
    ]);
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new CatalogPriceRules();
