require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Employees page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Employees extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on employees page
   */
  constructor() {
    super();

    this.pageTitle = 'Employees';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewEmployeeLink = '#page-header-desc-configuration-add[title=\'Add new employee\']';
    this.profilesTab = '#subtab-AdminProfiles';

    // List of employees
    this.employeeGridPanel = '#employee_grid_panel';
    this.employeeGridTitle = `${this.employeeGridPanel} h3.card-header-title`;
    this.employeesListForm = '#employee_grid';
    this.employeesListTableRow = row => `${this.employeesListForm} tbody tr:nth-child(${row})`;
    this.employeesListTableColumn = (row, column) => `${this.employeesListTableRow(row)} td.column-${column}`;
    this.employeesListTableStatusColumn = row => `${this.employeesListTableColumn(row, 'active')} .ps-switch`;
    this.employeesListTableStatusColumnToggleInput = row => `${this.employeesListTableStatusColumn(row)} input`;
    this.employeesListTableColumnAction = row => this.employeesListTableColumn(row, 'actions');
    this.employeesListTableToggleDropDown = row => `${this.employeesListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.employeesListTableDeleteLink = row => `${this.employeesListTableColumnAction(row)} a.grid-delete-row-link`;
    this.employeesListTableEditLink = row => `${this.employeesListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.employeeFilterInput = filterBy => `${this.employeesListForm} #employee_${filterBy}`;
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
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pages selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.employeesListForm} .col-form-label`;
    this.paginationNextLink = `${this.employeesListForm} #pagination_next_url`;
    this.paginationPreviousLink = `${this.employeesListForm} [aria-label='Previous']`;
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
  async goToAddNewEmployeePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewEmployeeLink);
  }

  // Tab methods
  /**
   * Go to Profiles page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToProfilesPage(page) {
    await this.clickAndWaitForNavigation(page, this.profilesTab);
  }

  // Columns methods
  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.employeeGridTitle);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
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
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.employeesListTableColumn(row, column));
  }

  /**
   * Go to Edit employee page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditEmployeePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.employeesListTableEditLink(row));
  }

  /**
   * Filter list of employees
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @returns {Promise<void>}
   */
  async filterEmployees(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.employeeFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.employeeFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get value of column displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async getStatus(page, row) {
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
  async setStatus(page, row, valueWanted = true) {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.employeesListTableStatusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Delete employee
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteEmployee(page, row) {
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

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteEmployees(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Enable / disable employees by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all employees with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteBulkActions(page) {
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
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
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
  async sortTable(page, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
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
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getTextContent(page, this.paginationLabel);
  }
}

module.exports = new Employees();
