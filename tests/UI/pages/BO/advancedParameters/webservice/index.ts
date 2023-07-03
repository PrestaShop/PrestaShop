import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Webservice page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class WebService extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly addNewWebserviceLink: string;

  private readonly webserviceGridPanel: string;

  private readonly webserviceGridTitle: string;

  private readonly webserviceListForm: string;

  private readonly webserviceListTableRow: (row: number) => string;

  private readonly webserviceListTableColumn: (row: number, column: string) => string;

  private readonly webserviceListTableStatusColumn: (row: number) => string;

  private readonly webserviceListTableStatusColumnToggleInput: (row: number) => string;

  private readonly webserviceListTableColumnAction: (row: number) => string;

  private readonly webserviceListTableToggleDropDown: (row: number) => string;

  private readonly webserviceListTableDeleteLink: (row: number) => string;

  private readonly webserviceListTableEditLink: (row: number) => string;

  private readonly webserviceFilterInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly selectAllRowsDiv: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly deleteModal: string;

  private readonly modalDeleteButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly configurationForm: string;

  private readonly enableWebserviceToggleInput: (status: number) => string;

  private readonly configurationFormSubmit: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on webservice page
   */
  constructor() {
    super();

    this.pageTitle = 'Webservice •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Header links
    this.addNewWebserviceLink = '#page-header-desc-configuration-add[title=\'Add new webservice key\']';

    // List of webservices
    this.webserviceGridPanel = '#webservice_key_grid_panel';
    this.webserviceGridTitle = `${this.webserviceGridPanel} h3.card-header-title`;
    this.webserviceListForm = '#webservice_key_grid';
    this.webserviceListTableRow = (row: number) => `${this.webserviceListForm} tbody tr:nth-child(${row})`;
    this.webserviceListTableColumn = (row: number, column: string) => `${this.webserviceListTableRow(row)} td.column-${column}`;
    this.webserviceListTableStatusColumn = (row: number) => `${this.webserviceListTableColumn(row, 'active')} .ps-switch`;
    this.webserviceListTableStatusColumnToggleInput = (row: number) => `${this.webserviceListTableStatusColumn(row)} input`;
    this.webserviceListTableColumnAction = (row: number) => this.webserviceListTableColumn(row, 'actions');
    this.webserviceListTableToggleDropDown = (row: number) => `${this.webserviceListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.webserviceListTableDeleteLink = (row: number) => `${this.webserviceListTableColumnAction(row)} a.grid-delete-row-link`;
    this.webserviceListTableEditLink = (row: number) => `${this.webserviceListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.webserviceFilterInput = (filterBy: string) => `${this.webserviceListForm} #webservice_key_${filterBy}`;
    this.filterSearchButton = `${this.webserviceListForm} .grid-search-button`;
    this.filterResetButton = `${this.webserviceListForm} .grid-reset-button`;

    // Delete modal
    this.confirmDeleteModal = '#webservice_key-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.webserviceListForm} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Bulk Actions
    this.selectAllRowsDiv = `${this.webserviceListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.webserviceListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.webserviceListForm} #webservice_key_grid_bulk_action_delete_selection`;
    this.bulkActionsEnableButton = `${this.webserviceListForm}
    #webservice_key_grid_bulk_action_webservice_enable_selection`;
    this.bulkActionsDisableButton = `${this.webserviceListForm}
    #webservice_key_grid_bulk_action_webservice_disable_selection`;

    // Modal Dialog
    this.deleteModal = '#webservice_key-grid-confirm-modal.show';
    this.modalDeleteButton = `${this.deleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.webserviceGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.webserviceGridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.webserviceGridPanel} [data-role='previous-page-link']`;

    // Form selectors
    this.configurationForm = '#configuration_form';
    this.enableWebserviceToggleInput = (status: number) => `${this.configurationForm} #form_enable_webservice_${status}`;
    this.configurationFormSubmit = `${this.configurationForm} div.card-footer button`;
  }

  /*
  Methods
   */

  /**
   * Go to new webservice key page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewWebserviceKeyPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewWebserviceLink);
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.webserviceGridTitle);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Get text from a column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.webserviceListTableColumn(row, column));
  }

  /**
   * Go to edit webservice key page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditWebservicePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.webserviceListTableEditLink(row));
  }

  /**
   * Filter list of webservice
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string|boolean} Value to put on filter
   * @returns {Promise<void>}
   */
  async filterWebserviceTable(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.webserviceFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.webserviceFilterInput(filterBy), value === '1' ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get value of column displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.webserviceListTableStatusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable status, false if not
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(this.webserviceListTableStatusColumn(row));

      return true;
    }

    return false;
  }

  /**
   * Delete webservice key
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteWebserviceKey(page: Page, row: number): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.click(this.webserviceListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.webserviceListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await Promise.all([
      page.click(this.webserviceListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteWebService(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteWebService(page: Page): Promise<void> {
    await page.click(this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);
  }

  /**
   * Get validation message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getValidationMessage(page: Page): Promise<string> {
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all sql queries with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, (el: HTMLElement) => el.click()),
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
      this.waitForVisibleSelector(page, this.deleteModal),
    ]);
    await this.confirmDeleteWebService(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable / disable by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on enable/Disable and wait for modal
    await page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    await this.elementNotVisible(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text value
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} column to sort with
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
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

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

  /* Form methods */
  /**
   * Set the webservice status
   * @param page {Page} Browser tab
   * @param status {boolean} Status of the Webservice
   * @returns {Promise<string>}
   */
  async setWebserviceStatus(page: Page, status: boolean): Promise<string> {
    await this.setChecked(page, this.enableWebserviceToggleInput(status ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.configurationFormSubmit);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new WebService();
