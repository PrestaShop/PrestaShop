require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Addresses extends BOBasePage {
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
    this.deleteAddressModal = '#address_grid_confirm_modal.show';
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
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.addressGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of addresses
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
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
   * @param page
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextContent(page, this.addressesListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableAddresses(page, i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to address Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewAddressPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewAddressLink);
  }

  /**
   * Go to Edit address page
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditAddressPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.addressesListTableEditLink(row));
  }

  /**
   * Delete address
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteAddress(page, row) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.addressesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.addressesListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await page.click(this.addressesListTableDeleteLink(row));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all addresses with Bulk Actions
   * @param page
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
    await page.click(this.deleteCustomerModalDeleteButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
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
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  // Set required field
  /**
   * Set required fields
   * @param page
   * @param id
   * @param valueWanted
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
