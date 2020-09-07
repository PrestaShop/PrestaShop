require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Countries extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Countries •';

    // Selectors
    // Header selectors
    this.addNewCountryButton = '#page-header-desc-country-new_country';
    // Form selectors
    this.gridForm = '#form-country';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;
    this.gridTable = '#table-country';
    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='countryFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncountry';
    this.filterResetButton = 'button[name=\'submitResetcountry\']';
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.editRowLink = row => `${this.tableRow(row)} a.edit`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td:nth-child(${column})`;
    // Bulk Actions
    this.bulkActionsToggleButton = `${this.gridForm} button.dropdown-toggle`;
    this.selectAllRowsLabel = `${this.gridForm} a[onclick*='checkDelBoxes']`;
    this.bulkActionsDeleteButton = `${this.gridForm} a[onclick*='Delete']`;
  }

  /*
  Methods
   */
  /**
   * Go to add new country page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewCountryPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCountryButton);
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
   * Reset and get number of lines
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to edit country page
   * @param page
   * @param countryID
   * @returns {Promise<void>}
   */
  async goToEditCountryPage(page, countryID = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(countryID));
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
   * Delete country
   * @param page
   * @returns {Promise<unknown>}
   */
  async deleteCountryByBulkActions(page) {
    this.dialogListener(page, true);
    // Click on Button Bulk actions
    await page.click(this.bulkActionsToggleButton);
    // Click on Select All
    await page.click(this.selectAllRowsLabel);
    // Click on Button Bulk actions
    await page.click(this.bulkActionsToggleButton);
    // Click on delete
    await page.click(this.bulkActionsDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new Countries();
