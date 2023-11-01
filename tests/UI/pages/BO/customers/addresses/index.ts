import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Addresses page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Addresses extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewAddressLink: string;

  private readonly addressGridPanel: string;

  private readonly addressGridTitle: string;

  private readonly addressesListForm: string;

  private readonly addressesListTableRow: (row: number) => string;

  private readonly addressesListTableColumn: (row: number, column: string) => string;

  private readonly addressesListTableColumnAction: (row: number) => string;

  private readonly addressesListTableToggleDropDown: (row: number) => string;

  private readonly addressesListTableDeleteLink: (row: number) => string;

  private readonly addressesListTableEditLink: (row: number) => string;

  private readonly addressFilterColumnInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly deleteAddressModal: string;

  private readonly deleteAddressModalDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly setRequiredFieldsButton: string;

  private readonly requiredFieldCheckBox: (id: number) => string;

  private readonly requiredFieldsForm: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on addresses page
   */
  constructor() {
    super();

    this.pageTitle = `Addresses • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Header links
    this.addNewAddressLink = '#page-header-desc-configuration-add[title=\'Add new address\']';

    // List of addresses
    this.addressGridPanel = '#address_grid_panel';
    this.addressGridTitle = `${this.addressGridPanel} h3.card-header-title`;
    this.addressesListForm = '#address_grid';
    this.addressesListTableRow = (row: number) => `${this.addressesListForm} tbody tr:nth-child(${row})`;
    this.addressesListTableColumn = (row: number, column: string) => `${this.addressesListTableRow(row)} td.column-${column}`;
    this.addressesListTableColumnAction = (row: number) => this.addressesListTableColumn(row, 'actions');
    this.addressesListTableToggleDropDown = (row: number) => `${this.addressesListTableColumnAction(row)}`
      + ' a[data-toggle=\'dropdown\']';
    this.addressesListTableDeleteLink = (row: number) => `${this.addressesListTableColumnAction(row)} a.grid-delete-row-link`;
    this.addressesListTableEditLink = (row: number) => `${this.addressesListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.addressFilterColumnInput = (filterBy: string) => `${this.addressesListForm} #address_${filterBy}`;
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
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.addressGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.addressGridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.addressGridPanel} [data-role='previous-page-link']`;

    // Required field section
    this.setRequiredFieldsButton = 'button[data-target=\'#addressRequiredFieldsContainer\']';
    this.requiredFieldCheckBox = (id: number) => `#required_fields_address_required_fields_${id}`;
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
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await page.click(this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.addressGridTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
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
  async filterAddresses(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
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
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.addressesListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

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
  async goToAddNewAddressPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewAddressLink);
  }

  /**
   * Go to Edit address page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditAddressPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.addressesListTableEditLink(row));
  }

  /**
   * Delete address
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteAddress(page: Page, row: number): Promise<string> {
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
  async deleteAddressesBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
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
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
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
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Number of pagination to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);
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
  async setRequiredFields(page: Page, id: number, valueWanted: boolean = true): Promise<string> {
    // Check if form is open
    if (await this.elementNotVisible(page, `${this.requiredFieldsForm}.show`, 1000)) {
      await Promise.all([
        this.waitForSelectorAndClick(page, this.setRequiredFieldsButton),
        this.waitForVisibleSelector(page, `${this.requiredFieldsForm}.show`),
      ]);
    }

    // Click on checkbox if not selected
    await this.setCheckedWithIcon(page, this.requiredFieldCheckBox(id), valueWanted);

    // Save setting
    await page.click(this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Addresses();
