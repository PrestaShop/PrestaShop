require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Addresses extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Addresses •';

    // Selectors
    // Header links
    this.addNewAddressLink = '#page-header-desc-configuration-add[title=\'Add new address\']';
    // List of addresses
    this.addressGridPanel = '#address_grid_panel';
    this.addressGridTitle = `${this.addressGridPanel} h3.card-header-title`;
    this.addressesListForm = '#address_grid';
    this.addressesListTableRow = `${this.addressesListForm} tbody tr:nth-child(%ROW)`;
    this.addressesListTableColumn = `${this.addressesListTableRow} td.column-%COLUMN`;
    this.addressesListTableColumnAction = this.addressesListTableColumn.replace('%COLUMN', 'actions');
    this.addressesListTableToggleDropDown = `${this.addressesListTableColumnAction} a[data-toggle='dropdown']`;
    this.addressesListTableDeleteLink = `${this.addressesListTableColumnAction} a[data-url]`;
    this.addressesListTableEditLink = `${this.addressesListTableColumnAction} a[href*='edit']`;
    // Filters
    this.addressFilterColumnInput = `${this.addressesListForm} #address_%FILTERBY`;
    this.filterSearchButton = `${this.addressesListForm} button[name='address[actions][search]']`;
    this.filterResetButton = `${this.addressesListForm} button[name='address[actions][reset]']`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.addressGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter list of addresses
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterAddresses(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.addressFilterColumnInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(
          this.addressFilterColumnInput.replace('%FILTERBY', filterBy),
          value,
        );
        break;
      default:
        // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextContent(this.addressesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
  }

  /**
   * Go to address Page
   * @return {Promise<void>}
   */
  async goToAddNewAddressPage() {
    await this.clickAndWaitForNavigation(this.addNewAddressLink);
  }

  /**
   * Go to Edit address page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditAddressPage(row) {
    await this.clickAndWaitForNavigation(
      this.addressesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
    );
  }

  /**
   * Delete address
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteAddress(row) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.addressesListTableToggleDropDown.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.addressesListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`, {visible: true},
      ),
    ]);
    // Click on delete
    await this.page.click(this.addressesListTableDeleteLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
