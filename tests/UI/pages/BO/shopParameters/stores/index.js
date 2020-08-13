require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Stores extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Stores •';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newStoreLink = '#page-header-desc-store-new_store';

    // Form selectors
    this.gridForm = '#form-store';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-store';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='storeFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonstore';
    this.filterResetButton = 'button[name=\'submitResetstore\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;


    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnAddress = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnCity = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnPostalCode = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnState = row => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnCountry = row => `${this.tableBodyColumn(row)}:nth-child(8)`;
    this.tableColumnPhone = row => `${this.tableBodyColumn(row)}:nth-child(9)`;
    this.tableColumnFax = row => `${this.tableBodyColumn(row)}:nth-child(10)`;
    this.tableColumnStatus = row => `${this.tableBodyColumn(row)}:nth-child(11)`;
  }

  /* Header methods */
  /**
   * Go to new store page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewStorePage(page) {
    await this.clickAndWaitForNavigation(page, this.newStoreLink);
  }

  /* Filter methods */

  /**
   * Get Number of stores
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
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
   * Reset and get number of stores
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter stores
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
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
      case 'id_store':
        columnSelector = this.tableColumnId(row);
        break;

      case 'sl!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'sl!address1':
        columnSelector = this.tableColumnAddress(row);
        break;

      case 'city':
        columnSelector = this.tableColumnCity(row);
        break;

      case 'postcode':
        columnSelector = this.tableColumnPostalCode(row);
        break;

      case 'st!name':
        columnSelector = this.tableColumnState(row);
        break;

      case 'cl!name':
        columnSelector = this.tableColumnCountry(row);
        break;

      case 'phone':
        columnSelector = this.tableColumnPhone(row);
        break;

      case 'fax':
        columnSelector = this.tableColumnFax(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get Store status
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  getStoreStatus(page, row) {
    return this.elementVisible(page, `${this.tableColumnStatus(row)} a.action-enabled`, 1000);
  }

  /**
   * Set store status by clicking on column status
   * @param page
   * @param row
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async setStoreStatus(page, row, wantedStatus) {
    const actualStatus = await this.getStoreStatus(page, row);

    if (actualStatus !== wantedStatus) {
      await this.clickAndWaitForNavigation(page, `${this.tableColumnStatus(row)} a`);
    }
  }
}

module.exports = new Stores();
