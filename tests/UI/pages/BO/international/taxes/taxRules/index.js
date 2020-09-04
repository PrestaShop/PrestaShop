require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class TaxRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Tax Rules •';

    // Selectors
    // HEADER buttons
    this.addNewTaxRulesGroupLink = 'a#page-header-desc-tax_rules_group-new_tax_rules_group';
    // Form selectors
    this.gridForm = '#form-tax_rules_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;
    this.gridTable = '#table-tax_rules_group';
    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='tax_rules_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtontax_rules_group';
    this.filterResetButton = `${this.filterRow} button[name='submitResettax_rules_group']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.editRowLink = row => `${this.tableRow(row)} a.edit`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td:nth-child(${column})`;
    this.toggleDropDown = row => `${this.tableRow(row)} button[data-toggle='dropdown']`;
    this.deleteRowLink = row => `${this.tableRow(row)} a.delete`;
    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';
  }

  /*
  Methods
   */

  /**
   * Go to add tax Rules group Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewTaxRulesGroupPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTaxRulesGroupLink);
  }

  /**
   * Go to edit tax rule page
   * @param page
   * @param id
   * @returns {Promise<void>}
   */
  async goToEditTaxRulePage(page, id = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(id));
  }

  /**
   * Reset filter
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of element in grid
   * @param page
   * @returns {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Get text column from table
   * @param page
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Reset and get number of lines
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter table
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Delete Tax Rule
   * @param page
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteTaxRule(page, row = 1) {
    // Click on dropDown
    await page.click(this.toggleDropDown(row));
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(row));
    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);
    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new TaxRules();
