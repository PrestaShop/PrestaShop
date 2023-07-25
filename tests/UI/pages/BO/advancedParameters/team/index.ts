import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Employees page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Employees extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  public readonly errorDeleteOwnAccountMessage: string;

  private readonly addNewEmployeeLink: string;

  private readonly rolesTab: string;

  private readonly permissionsTab: string;

  private readonly employeeGridPanel: string;

  private readonly employeeGridTitle: string;

  private readonly employeesListForm: string;

  private readonly employeesListTableRow: (row: number) => string;

  private readonly employeesListTableColumn: (row: number, column: string) => string;

  private readonly employeesListTableStatusColumn: (row: number) => string;

  private readonly employeesListTableStatusColumnToggleInput: (row: number) => string;

  private readonly employeesListTableColumnAction: (row: number) => string;

  private readonly employeesListTableToggleDropDown: (row: number) => string;

  private readonly employeesListTableDeleteLink: (row: number) => string;

  private readonly employeesListTableEditLink: (row: number) => string;

  private readonly employeeFilterInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on employees page
   */
  constructor() {
    super();

    this.pageTitle = 'Employees';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.errorDeleteOwnAccountMessage = 'You cannot disable or delete your own account.';

    // Selectors
    // Header links
    this.addNewEmployeeLink = '#page-header-desc-configuration-add[title=\'Add new employee\']';
    this.rolesTab = '#subtab-AdminProfiles';
    this.permissionsTab = '#subtab-AdminAccess';

    // List of employees
    this.employeeGridPanel = '#employee_grid_panel';
    this.employeeGridTitle = `${this.employeeGridPanel} h3.card-header-title`;
    this.employeesListForm = '#employee_grid';
    this.employeesListTableRow = (row: number) => `${this.employeesListForm} tbody tr:nth-child(${row})`;
    this.employeesListTableColumn = (row: number, column: string) => `${this.employeesListTableRow(row)} td.column-${column}`;
    this.employeesListTableStatusColumn = (row: number) => `${this.employeesListTableColumn(row, 'active')} .ps-switch`;
    this.employeesListTableStatusColumnToggleInput = (row: number) => `${this.employeesListTableStatusColumn(row)} input`;
    this.employeesListTableColumnAction = (row: number) => this.employeesListTableColumn(row, 'actions');
    this.employeesListTableToggleDropDown = (row: number) => `${this.employeesListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.employeesListTableDeleteLink = (row: number) => `${this.employeesListTableColumnAction(row)} a.grid-delete-row-link`;
    this.employeesListTableEditLink = (row: number) => `${this.employeesListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.employeeFilterInput = (filterBy: string) => `${this.employeesListForm} #employee_${filterBy}`;
    this.filterSearchButton = `${this.employeesListForm} .grid-search-button`;
    this.filterResetButton = `${this.employeesListForm} .grid-reset-button`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.employeesListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.employeesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.employeesListForm} #employee_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.employeesListForm} #employee_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.employeesListForm} #employee_grid_bulk_action_delete_selection`;

    // Delete modal
    this.confirmDeleteModal = '#employee-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.employeeGridPanel} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pages selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.employeesListForm} .col-form-label`;
    this.paginationNextLink = `${this.employeesListForm} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.employeesListForm} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */
  // Header methods
  /**
   * Go to new Employee page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewEmployeePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewEmployeeLink);
  }

  // Tab methods
  /**
   * Go to roles page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToRolesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.rolesTab);
  }

  /**
   * Go to Permissions tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async goToPermissionsTab(page: Page): Promise<boolean> {
    await this.clickAndWaitForURL(page, this.permissionsTab);
    return this.elementVisible(page, `${this.permissionsTab}.current`, 1000);
  }

  // Columns methods
  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.employeeGridTitle);
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
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.employeesListTableColumn(row, column));
  }

  /**
   * Go to Edit employee page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditEmployeePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.employeesListTableEditLink(row));
  }

  /**
   * Filter list of employees
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @returns {Promise<void>}
   */
  async filterEmployees(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.employeeFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.employeeFilterInput(filterBy), value === '1' ? 'Yes' : 'No');
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
      `${this.employeesListTableStatusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForURL(page, this.employeesListTableStatusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Delete employee
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   * @private
   */
  async deleteEmployeeAction(page: Page, row: number): Promise<void> {
    // Click on dropDown
    await Promise.all([
      page.click(this.employeesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.employeesListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.employeesListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteEmployees(page);
  }

  /**
   * Delete employee and fetch success message
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteEmployee(page: Page, row: number): Promise<string> {
    await this.deleteEmployeeAction(page, row);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete employee and fetch error message
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteEmployeeAndFail(page: Page, row: number): Promise<string> {
    await this.deleteEmployeeAction(page, row);

    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteEmployees(page: Page): Promise<void> {
    await page.click(this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);
  }

  /**
   * Enable / disable employees by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    await this.elementNotVisible(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all employees with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteBulkActions(page: Page): Promise<string> {
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
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteEmployees(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to filter
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(page, this.employeesListTableColumn(i, column));

      if (column === 'active') {
        rowContent = (await this.getStatus(page, i)).toString();
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Pagination methods
  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getTextContent(page, this.paginationLabel);
  }
}

export default new Employees();
