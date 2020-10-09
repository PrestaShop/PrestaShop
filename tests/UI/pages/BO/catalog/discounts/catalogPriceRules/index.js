require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CatalogPriceRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Catalog Price Rules •';

    // Selectors header
    this.addNewCatalogPriceRuleButton = '#page-header-desc-specific_price_rule-new_specific_price_rule';

    // Form selectors
    this.gridForm = '#form-specific_price_rule';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Selectors grid panel
    this.gridPanel = '#attachment_grid_panel';
    this.gridTable = '#table-specific_price_rule';

    // Filters
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='specific_price_ruleFilter_${filterBy}']`;
    this.filterSearchButton = `${this.gridTable} #submitFilterButtonspecific_price_rule`;
    this.filterResetButton = 'button[name=\'submitResetspecific_price_rule\']';

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td:nth-child(${column})`;

    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td .btn-group-action`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} button.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} ul.dropdown-menu`;
    this.confirmDeleteButton = '#popup_ok';
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a.delete`;
    this.editRowLink = row => `${this.actionsColumn(row)} a.edit`;
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
   * Reset all filters
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get Number of cart rules
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of catalog price rules
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to edit catalog price rule page
   * @param page
   * @param ruleName
   * @returns {Promise<void>}
   */
  async goToEditCatalogPriceRulePage(page, ruleName) {
    if (await this.elementVisible(page, this.filterColumn('a!name'))) {
      await this.filterPriceRules(page, 'select', 'a!name', ruleName);
    }
    await this.clickAndWaitForNavigation(page, this.editRowLink(1));
  }

  /**
   * Filter catalog price rules table
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterPriceRules(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Delete catalog price rule
   * @param page
   * @param ruleName
   * @returns {Promise<string>}
   */
  async deleteCatalogPriceRule(page, ruleName) {
    if (await this.elementVisible(page, this.filterColumn('a!name'))) {
      await this.filterPriceRules(page, 'select', 'a!name', ruleName);
    }
    await this.waitForSelectorAndClick(page, this.dropdownToggleButton(1));
    await Promise.all([
      page.click(this.deleteRowLink(1)),
      this.waitForVisibleSelector(page, this.confirmDeleteButton),
    ]);
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get text from column in table
   * @param page
   * @param row
   * @param columnName
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_specific_price_rule':
        columnSelector = this.tableColumn(row, 2);
        break;

      case 'a!name':
        columnSelector = this.tableColumn(row, 3);
        break;

      case 's!name':
        columnSelector = this.tableColumn(row, 4);
        break;

      case 'cul!name':
        columnSelector = this.tableColumn(row, 5);
        break;

      case 'cl!name':
        columnSelector = this.tableColumn(row, 6);
        break;

      case 'gl!name':
        columnSelector = this.tableColumn(row, 7);
        break;

      case 'from_quantity':
        columnSelector = this.tableColumn(row, 8);
        break;

      case 'a!reduction_type':
        columnSelector = this.tableColumn(row, 9);
        break;

      case 'reduction':
        columnSelector = this.tableColumn(row, 10);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }
}

module.exports = new CatalogPriceRules();
