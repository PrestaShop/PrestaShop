require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class WebService extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Webservice •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewWebserviceLink = '#page-header-desc-configuration-add[title=\'Add new webservice key\']';
    // List of webservices
    this.webserviceGridPanel = '#webservice_key_grid_panel';
    this.webserviceGridTitle = `${this.webserviceGridPanel} h3.card-header-title`;
    this.webserviceListForm = '#webservice_key_grid';
    this.webserviceListTableRow = row => `${this.webserviceListForm} tbody tr:nth-child(${row})`;
    this.webserviceListTableColumn = (row, column) => `${this.webserviceListTableRow(row)} td.column-${column}`;
    this.webserviceListTableColumnAction = row => this.webserviceListTableColumn(row, 'actions');
    this.webserviceListTableToggleDropDown = row => `${this.webserviceListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.webserviceListTableDeleteLink = row => `${this.webserviceListTableColumnAction(row)} a[data-url]`;
    this.webserviceListTableEditLink = row => `${this.webserviceListTableColumnAction(row)} a[href*='edit']`;
    this.webserviceListColumnValidIcon = row => `${this.webserviceListTableColumn(row, 'active')
    } i.grid-toggler-icon-valid`;
    this.webserviceListColumnNotValidIcon = row => `${this.webserviceListTableColumn(row, 'active')
    } i.grid-toggler-icon-not-valid`;
    // Filters
    this.webserviceFilterInput = filterBy => `${this.webserviceListForm} #webservice_key_${filterBy}`;
    this.filterSearchButton = `${this.webserviceListForm} button[name='webservice_key[actions][search]']`;
    this.filterResetButton = `${this.webserviceListForm} button[name='webservice_key[actions][reset]']`;
  }

  /*
  Methods
   */

  /**
   * Go to new webservice key page
   * @return {Promise<void>}
   */
  async goToAddNewWebserviceKeyPage() {
    await this.clickAndWaitForNavigation(this.addNewWebserviceLink);
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.webserviceGridTitle);
  }

  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberOfElementInGrid();
  }

  /**
   * get text from a column from table
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(this.webserviceListTableColumn(row, column));
  }

  /**
   * Go to edit webservice key page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditWebservicePage(row) {
    await this.clickAndWaitForNavigation(this.webserviceListTableEditLink(row));
  }

  /**
   * Filter list of webservice
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterWebserviceTable(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.webserviceFilterInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.webserviceFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Value of column displayed
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row) {
    return this.elementVisible(this.webserviceListColumnValidIcon(row), 100);
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    await this.waitForVisibleSelector(this.webserviceListTableColumn(row, 'active'), 2000);
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.page.click(this.webserviceListTableColumn(row, 'active'));
      await this.waitForVisibleSelector(
        (valueWanted ? this.webserviceListColumnValidIcon(row) : this.webserviceListColumnNotValidIcon(row)),
      );
      return true;
    }
    return false;
  }

  /**
   * Delete webservice key
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteWebserviceKey(row) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.webserviceListTableToggleDropDown(row)),
      this.waitForVisibleSelector(`${this.webserviceListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.webserviceListTableDeleteLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get validation message
   * @returns {Promise<string>}
   */
  getValidationMessage() {
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
