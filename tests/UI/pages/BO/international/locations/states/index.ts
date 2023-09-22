import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * States page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class States extends BOBasePage {
  public readonly pageTitle: string;

  private readonly successfulUpdateStatusMessage: string;

  private readonly addNewStateLink: string;

  private readonly gridPanelDiv: string;

  private readonly gridHeaderTitle: string;

  private readonly bulkActionsToggleButton: string;

  private readonly enableSelectionButton: string;

  private readonly disableSelectionButton: string;

  private readonly deleteSelectionButton: string;

  private readonly selectAllLabel: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnName: (row: number) => string;

  private readonly tableColumnIsoCode: (row: number) => string;

  private readonly tableColumnZone: (row: number) => string;

  private readonly tableColumnCountry: (row: number) => string;

  private readonly tableColumnStatusLink: (row: number) => string;

  private readonly tableColumnStatusToggle: (row: number) => string;

  private readonly tableColumnStatusToggleInput: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly columnActionsEditLink: (row: number) => string;

  private readonly columnActionsDropdownButton: (row: number) => string;

  private readonly columnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes: string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkEnableLink: string;

  private readonly bulkDisableLink: string;

  private readonly bulkDeleteLink: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on states page
   */
  constructor() {
    super();

    this.pageTitle = 'States •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.successfulUpdateMessage = 'Update successful';
    this.successfulMultiDeleteMessage = 'Successful deletion';

    // Header selectors
    this.addNewStateLink = '#page-header-desc-configuration-add[title=\'Add new state\']';

    // Grid
    this.gridPanelDiv = '#state_grid_panel';
    this.gridHeaderTitle = `${this.gridPanelDiv} h3.card-header-title`;
    this.bulkActionsToggleButton = `${this.gridPanelDiv} button.js-bulk-actions-btn`;
    this.enableSelectionButton = `${this.gridPanelDiv} #state_grid_bulk_action_enable_selection`;
    this.disableSelectionButton = `${this.gridPanelDiv} #state_grid_bulk_action_disable_selection`;
    this.deleteSelectionButton = `${this.gridPanelDiv} #state_grid_bulk_action_delete_selection`;
    this.selectAllLabel = `${this.gridPanelDiv} #state_grid tr.column-filters .md-checkbox i`;

    // Form selectors
    this.gridForm = '#form-state';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#state_grid';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='state[${filterBy}]']`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnIsoCode = (row: number) => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnZone = (row: number) => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnCountry = (row: number) => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnStatusLink = (row: number) => `${this.tableBodyColumn(row)}:nth-child(7) a`;
    this.tableColumnStatusToggle = (row: number) => `${this.tableBodyColumn(row)}:nth-child(7) .ps-switch`;
    this.tableColumnStatusToggleInput = (row: number) => `${this.tableColumnStatusToggle(row)} input`;

    // Column actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.columnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.grid-edit-row-link`;
    this.columnActionsDropdownButton = (row: number) => `${this.tableColumnActions(row)} a[data-toggle='dropdown']`;
    this.columnActionsDeleteLink = (row: number) => `${this.tableColumnActions(row)} a.grid-delete-row-link`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_state';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
    this.confirmDeleteModal = '#state-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanelDiv} .col-form-label`;
    this.paginationNextLink = `${this.gridPanelDiv} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanelDiv} [data-role='previous-page-link']`;
  }

  /* Header methods */
  /**
   * Go To add new state page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewStatePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewStateLink);
  }

  /* Filter Methods */
  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of states
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElement(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Get number of states in the current grid page
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return (await page.$$(`${this.tableBodyRows}:not(.empty_row)`)).length;
  }

  /**
   * Reset and get number of states
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElement(page);
  }

  /**
   * Filter states
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterStates(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    let textValue: string = value;

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;

      case 'select':
        if (filterBy === 'active') {
          textValue = value === '1' ? 'Yes' : 'No';
        }
        await this.selectByVisibleText(page, this.filterColumn(filterBy), textValue);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }

    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector;

    switch (columnName) {
      case 'id_state':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'iso_code':
        columnSelector = this.tableColumnIsoCode(row);
        break;

      case 'id_zone':
        columnSelector = this.tableColumnZone(row);
        break;

      case 'id_country':
        columnSelector = this.tableColumnCountry(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get state status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStateStatus(page: Page, row: number): Promise<boolean> {
    const inputValue = await this.getAttributeContent(
      page,
      `${this.tableColumnStatusToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Set state status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStateStatus(page: Page, row: number, wantedStatus: boolean): Promise<boolean> {
    if (wantedStatus !== await this.getStateStatus(page, row)) {
      // Click and wait for message
      const [message] = await Promise.all([
        this.getGrowlMessageContent(page),
        page.click(this.tableColumnStatusToggle(row)),
      ]);

      await this.closeGrowlMessage(page);
      return message === this.successfulUpdateStatusMessage;
    }

    return false;
  }

  /**
   * Go to edit state page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditStatePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.columnActionsEditLink(row));
  }

  /**
   * Delete state
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @return {Promise<string>}
   */
  async deleteState(page: Page, row: number): Promise<string> {
    // Add listener to dialog to accept deletion
    await this.dialogListener(page, true);
    // Click on dropDown
    await Promise.all([
      page.click(this.columnActionsDropdownButton(row)),
      this.waitForVisibleSelector(page, `${this.columnActionsDropdownButton(row)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.columnActionsDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Bulk actions methods */

  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page): Promise<void> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
  }

  /**
   * Bulk delete states
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteStates(page: Page): Promise<string> {
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteSelectionButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk set states status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page: Page, wantedStatus: boolean): Promise<string> {
    // Select all rows
    await this.bulkSelectRows(page);

    // Set status
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await this.clickAndWaitForURL(page, wantedStatus ? this.enableSelectionButton : this.disableSelectionButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort table method */

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector: string;
    let sortColumnSpanButton: string;

    switch (sortBy) {
      case 'id_state':
        columnSelector = this.sortColumnDiv(2);
        sortColumnSpanButton = this.sortColumnSpanButton(2);
        break;

      case 'name':
        columnSelector = this.sortColumnDiv(3);
        sortColumnSpanButton = this.sortColumnSpanButton(3);
        break;

      case 'iso_code':
        columnSelector = this.sortColumnDiv(4);
        sortColumnSpanButton = this.sortColumnSpanButton(4);
        break;

      case 'id_zone':
        columnSelector = this.sortColumnDiv(5);
        sortColumnSpanButton = this.sortColumnSpanButton(5);
        break;

      case 'id_country':
        columnSelector = this.sortColumnDiv(6);
        sortColumnSpanButton = this.sortColumnSpanButton(6);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnDiv = `${columnSelector} [data-sort-direction='${sortDirection}']`;

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
   * @param number {number} Value of pagination limit to select
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
}

export default new States();
