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
    this.addressesListTableRow = row => `${this.addressesListForm} tbody tr:nth-child(${row})`;
    this.addressesListTableColumn = (row, column) => `${this.addressesListTableRow(row)} td.column-${column}`;
    this.addressesListTableColumnAction = row => this.addressesListTableColumn(row, 'actions');
    this.addressesListTableToggleDropDown = row => `${this.addressesListTableColumnAction(row)}`
      + ' a[data-toggle=\'dropdown\']';
    this.addressesListTableDeleteLink = row => `${this.addressesListTableColumnAction(row)} a[data-url]`;
    this.addressesListTableEditLink = row => `${this.addressesListTableColumnAction(row)} a[href*='edit']`;
    // Filters
    this.addressFilterColumnInput = filterBy => `${this.addressesListForm} #address_${filterBy}`;
    this.filterSearchButton = `${this.addressesListForm} button[name='address[actions][search]']`;
    this.filterResetButton = `${this.addressesListForm} button[name='address[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.addressesListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.addressesListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = '#address_grid_bulk_action_delete_selection';
    // Modal Dialog
    this.deleteAddressModal = '#address-grid-confirm-modal.show';
    this.deleteCustomerModalDeleteButton = `${this.deleteAddressModal} button.btn-confirm-submit`;
    // Sort Selectors
    this.tableHead = `${this.addressesListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.addressGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.addressGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.addressGridPanel} [aria-label='Previous']`;
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
        await this.setValue(this.addressFilterColumnInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.addressFilterColumnInput(filterBy), value);
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
    return this.getTextContent(this.addressesListTableColumn(row, column));
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
    await this.clickAndWaitForNavigation(this.addressesListTableEditLink(row));
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
      this.page.click(this.addressesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.addressesListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.page.click(this.addressesListTableDeleteLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all addresses with Bulk Actions
   * @return {Promise<string>}
   */
  async deleteAddressesBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
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
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel() {
    return this.getTextContent(this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(number) {
    await this.selectByVisibleText(this.paginationLimitSelect, number);
    return this.getPaginationLabel();
  }

  /**
   * Click on next
   * @returns {Promise<string>}
   */
  async paginationNext() {
    await this.clickAndWaitForNavigation(this.paginationNextLink);
    return this.getPaginationLabel();
  }

  /**
   * Click on previous
   * @returns {Promise<string>}
   */
  async paginationPrevious() {
    await this.clickAndWaitForNavigation(this.paginationPreviousLink);
    return this.getPaginationLabel();
  }
};
