require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Addresses page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Addresses extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on addresses page
   */
  constructor() {
    super();

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
    this.addressesListTableDeleteLink = row => `${this.addressesListTableColumnAction(row)} a.grid-delete-row-link`;
    this.addressesListTableEditLink = row => `${this.addressesListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.addressFilterColumnInput = filterBy => `${this.addressesListForm} #address_${filterBy}`;
    this.filterSearchButton = `${this.addressesListForm} .grid-search-button`;
    this.filterResetButton = `${this.addressesListForm} .grid-reset-button`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.addressesListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.addressesListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = '#address_grid_bulk_action_delete_selection';

    // Modal Dialog
    this.deleteAddressModal = '#address-grid-confirm-modal.show';
    this.deleteAddressModalDeleteButton = `${this.deleteAddressModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.addressesListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.addressGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.addressGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.addressGridPanel} [aria-label='Previous']`;

    // Required field section
    this.setRequiredFieldsButton = 'button[data-target=\'#addressRequiredFieldsContainer\']';
    this.requiredFieldCheckBox = id => `#required_fields_address_required_fields_${id}`;
    this.requiredFieldsForm = '#addressRequiredFieldsContainer';
    this.saveButton = `${this.requiredFieldsForm} button`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.addressGridTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of addresses
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterAddresses(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.addressFilterColumnInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.addressFilterColumnInput(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextContent(page, this.addressesListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableAddresses(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to address Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewAddressPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewAddressLink);
  }

  /**
   * Go to Edit address page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditAddressPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.addressesListTableEditLink(row));
  }

  /**
   * Delete address
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteAddress(page, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.addressesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.addressesListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.addressesListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, this.deleteAddressModal),
    ]);
    await page.click(this.deleteAddressModalDeleteButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all addresses with Bulk Actions
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async deleteAddressesBulkActions(page) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, this.deleteAddressModal),
    ]);
    await page.click(this.deleteAddressModalDeleteButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Number of pagination to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  // Set required field
  /**
   * Set required fields
   * @param page {Page} Browser tab
   * @param id {number} Id of the checkbox
   * @param valueWanted {boolean}True if we want to check the checkbox
   * @returns {Promise<string>}
   */
  async setRequiredFields(page, id, valueWanted = true) {
    // Check if form is open
    if (await this.elementNotVisible(page, `${this.requiredFieldsForm}.show`, 1000)) {
      await Promise.all([
        this.waitForSelectorAndClick(page, this.setRequiredFieldsButton),
        this.waitForVisibleSelector(page, `${this.requiredFieldsForm}.show`),
      ]);
    }

    // Click on checkbox if not selected
    const isCheckboxSelected = await this.isCheckboxSelected(page, this.requiredFieldCheckBox(id));
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.requiredFieldCheckBox(id)} + i`, el => el.click());
    }

    // Save setting
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Addresses();
