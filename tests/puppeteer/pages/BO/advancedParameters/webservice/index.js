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
    this.webserviceListTableRow = `${this.webserviceListForm} tbody tr:nth-child(%ROW)`;
    this.webserviceListTableColumn = `${this.webserviceListTableRow} td.column-%COLUMN`;
    this.webserviceListTableColumnAction = this.webserviceListTableColumn.replace('%COLUMN', 'actions');
    this.webserviceListTableToggleDropDown = `${this.webserviceListTableColumnAction} a[data-toggle='dropdown']`;
    this.webserviceListTableDeleteLink = `${this.webserviceListTableColumnAction} a[data-url]`;
    this.webserviceListTableEditLink = `${this.webserviceListTableColumnAction} a[href*='edit']`;
    this.webserviceListColumnValidIcon = `${this.webserviceListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-valid`;
    this.webserviceListColumnNotValidIcon = `${this.webserviceListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-not-valid`;
    // Filters
    this.webserviceFilterInput = `${this.webserviceListForm} #webservice_key_%FILTERBY`;
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
    return this.getTextContent(this.webserviceListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
  }

  /**
   * Go to edit webservice key page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditWebservicePage(row) {
    await this.clickAndWaitForNavigation(this.webserviceListTableEditLink.replace('%ROW', row));
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
        await this.setValue(this.webserviceFilterInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.webserviceFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
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
    return this.elementVisible(this.webserviceListColumnValidIcon.replace('%ROW', row),
      100,
    );
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      this.page.click(this.webserviceListTableColumn.replace('%ROW', row).replace('%COLUMN', 'active'));
      if (valueWanted) {
        await this.page.waitForSelector(this.webserviceListColumnValidIcon.replace('%ROW', row));
      } else {
        await this.page.waitForSelector(
          this.webserviceListColumnNotValidIcon.replace('%ROW', row),
        );
      }
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
      this.page.click(this.webserviceListTableToggleDropDown.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.webserviceListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`, {visible: true},
      ),
    ]);
    // Click on delete
    await Promise.all([
      this.page.click(this.webserviceListTableDeleteLink.replace('%ROW', row)),
      this.page.waitForSelector(this.alertSuccessBlockParagraph),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
