require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class States extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'States •';

    // Form selectors
    this.gridForm = '#form-state';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-state';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='stateFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonstate';
    this.filterResetButton = 'button[name=\'submitResetstate\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnIsoCode = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnZone = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnCountry = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnStatusLink = row => `${this.tableBodyColumn(row)}:nth-child(7) a`;
    this.tableColumnStatusEnableLink = row => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = row => `${this.tableColumnStatusLink(row)}.action-disabled`;
  }

  /* Filter Methods */
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
   * Get Number of states
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of states
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter states
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterStates(page, filterType, filterBy, value) {
    let filterValue = value;
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), filterValue.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        if (typeof value === 'boolean') {
          filterValue = value ? 'Yes' : 'No';
        }

        await Promise.all([
          this.selectByVisibleText(page, this.filterColumn(filterBy), filterValue),
          page.waitForNavigation({waitUntil: 'networkidle'}),
        ]);

        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */

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
      case 'id_state':
        columnSelector = this.tableColumnId(row);
        break;

      case 'a!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'iso_code':
        columnSelector = this.tableColumnIsoCode(row);
        break;

      case 'z!id_zone':
        columnSelector = this.tableColumnZone(row);
        break;

      case 'cl!id_country':
        columnSelector = this.tableColumnCountry(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get state status
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  getStateStatus(page, row) {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set state status
   * @param page
   * @param row
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async setStateStatus(page, row, wantedStatus) {
    if (wantedStatus !== await this.getStateStatus(page, row)) {
      await this.clickAndWaitForNavigation(page, this.tableColumnStatusLink(row));
    }
  }
}
module.exports = new States();
