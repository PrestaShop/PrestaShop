require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Addresses extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Addresses •';
    this.successfulUpdateMessage = 'Update successful';

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
    // Bulk Actions
    this.selectAllRowsLabel = `${this.addressesListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.addressesListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = '#address_grid_bulk_action_delete_selection';
    // Modal Dialog
    this.deleteAddressModal = '#address_grid_confirm_modal.show';
    this.deleteCustomerModalDeleteButton = `${this.deleteAddressModal} button.btn-confirm-submit`;
    // Sort Selectors
    this.tableHead = `${this.addressesListForm} thead`;
    this.sortColumnDiv = `${this.tableHead} div.ps-sortable-column[data-sort-col-name='%COLUMN']`;
    this.sortColumnSpanButton = `${this.sortColumnDiv} span.ps-sort`;
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
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableAddresses(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
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
      this.waitForVisibleSelector(
        `${this.addressesListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.page.click(this.addressesListTableDeleteLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all addresses with Bulk Actions
   * @return {Promise<string>}
   */
  async deleteAddressesBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(this.deleteAddressModal),
    ]);
    await this.page.click(this.deleteCustomerModalDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv.replace('%COLUMN', sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton.replace('%COLUMN', sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
