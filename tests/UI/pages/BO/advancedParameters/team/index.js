require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Employees extends BOBasePage {
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
    this.employeesListTableColumnAction = row => this.employeesListTableColumn(row, 'actions');
    this.employeesListTableToggleDropDown = row => `${this.employeesListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.employeesListTableDeleteLink = row => `${this.employeesListTableColumnAction(row)} a[data-url]`;
    this.employeesListTableEditLink = row => `${this.employeesListTableColumnAction(row)} a[href*='edit']`;
    this.employeesListColumnValidIcon = row => `${this.employeesListTableColumn(row, 'active')
    } i.grid-toggler-icon-valid`;
    this.employeesListColumnNotValidIcon = row => `${this.employeesListTableColumn(row, 'active')
    } i.grid-toggler-icon-not-valid`;
    // Filters
    this.employeeFilterInput = filterBy => `${this.employeesListForm} #employee_${filterBy}`;
    this.filterSearchButton = `${this.employeesListForm} button[name='employee[actions][search]']`;
    this.filterResetButton = `${this.employeesListForm} button[name='employee[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.employeesListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.employeesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.employeesListForm} #employee_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.employeesListForm} #employee_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.employeesListForm} #employee_grid_bulk_action_delete_selection`;
  }

  /*
  Methods
   */

  /**
   * Go to new Page Employee page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewEmployeePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewEmployeeLink);
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.employeeGridTitle);
  }

  /**
   * Reset input filters
   * @param page
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
   * @param page
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.employeesListTableColumn(row, column));
  }

  /**
   * Go to Edit employee page
   * @param page
   * @param row, row in table
   * @returns {Promise<void>}
   */
  async goToEditEmployeePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.employeesListTableEditLink(row));
  }

  /**
   * Filter list of employees
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @returns {Promise<void>}
   */
  async filterEmployees(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.employeeFilterInput(filterBy), value.toString());
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
   * Get Value of column Displayed
   * @param page
   * @param row, row in table
   * @returns {Promise<boolean>}
   */
  async getToggleColumnValue(page, row) {
    return this.elementVisible(page, this.employeesListColumnValidIcon(row), 100);
  }

  /**
   * Quick edit toggle column value
   * @param page
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(page, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.employeesListTableColumn(row, 'active'), 2000);
    if (await this.getToggleColumnValue(page, row) !== valueWanted) {
      page.click(this.employeesListTableColumn(row, 'active'));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.employeesListColumnValidIcon(row) : this.employeesListColumnNotValidIcon(row)),
      );
      return true;
    }
    return false;
  }

  /**
   * Delete employee
   * @param page
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteEmployee(page, row) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.employeesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.employeesListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.employeesListTableDeleteLink(row));
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable employees by Bulk Actions
   * @param page
   * @param enable
   * @returns {Promise<string>}
   */
  async changeEnabledColumnBulkActions(page, enable = true) {
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
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all employees with Bulk Actions
   * @param page
   * @returns {Promise<string>}
   */
  async deleteBulkActions(page) {
    this.dialogListener(page);
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
    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Go to Profiles page
   * @param page
   * @returns {Promise<void>}
   */
  async goToProfilesPage(page) {
    await this.clickAndWaitForNavigation(page, this.profilesTab);
  }
}

module.exports = new Employees();
