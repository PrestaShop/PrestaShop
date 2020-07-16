require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class WebService extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewWebserviceKeyPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewWebserviceLink);
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.webserviceGridTitle);
  }

  /**
   * Reset input filters
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * get text from a column from table
   * @param page
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.webserviceListTableColumn(row, column));
  }

  /**
   * Go to edit webservice key page
   * @param page
   * @param row, row in table
   * @returns {Promise<void>}
   */
  async goToEditWebservicePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.webserviceListTableEditLink(row));
  }

  /**
   * Filter list of webservice
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @returns {Promise<void>}
   */
  async filterWebserviceTable(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.webserviceFilterInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.webserviceFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get Value of column displayed
   * @param page
   * @param row, row in table
   * @returns {Promise<boolean>}
   */
  async getToggleColumnValue(page, row) {
    return this.elementVisible(page, this.webserviceListColumnValidIcon(row), 100);
  }

  /**
   * Quick edit toggle column value
   * @param page
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(page, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.webserviceListTableColumn(row, 'active'), 2000);
    if (await this.getToggleColumnValue(page, row) !== valueWanted) {
      await page.click(this.webserviceListTableColumn(row, 'active'));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.webserviceListColumnValidIcon(row) : this.webserviceListColumnNotValidIcon(row)),
      );
      return true;
    }
    return false;
  }

  /**
   * Delete webservice key
   * @param page
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteWebserviceKey(page, row) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.webserviceListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.webserviceListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.webserviceListTableDeleteLink(row));
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get validation message
   * @param page
   * @returns {Promise<string>}
   */
  getValidationMessage(page) {
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new WebService();
