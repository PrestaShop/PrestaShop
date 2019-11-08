require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Employees extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.employeesListTableRow = `${this.employeesListForm} tbody tr:nth-child(%ROW)`;
    this.employeesListTableColumn = `${this.employeesListTableRow} td.column-%COLUMN`;
    this.employeesListTableColumnAction = this.employeesListTableColumn.replace('%COLUMN', 'actions');
    this.employeesListTableToggleDropDown = `${this.employeesListTableColumnAction} a[data-toggle='dropdown']`;
    this.employeesListTableDeleteLink = `${this.employeesListTableColumnAction} a[data-url]`;
    this.employeesListTableEditLink = `${this.employeesListTableColumnAction} a[href*='edit']`;
    this.employeesListColumnValidIcon = `${this.employeesListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-valid`;
    this.employeesListColumnNotValidIcon = `${this.employeesListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-not-valid`;
    // Filters
    this.employeeFilterInput = `${this.employeesListForm} #employee_%FILTERBY`;
    this.filterSearchButton = `${this.employeesListForm} button[name='employee[actions][search]']`;
    this.filterResetButton = `${this.employeesListForm} button[name='employee[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.employeesListForm} .md-checkbox label`;
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
   * @return {Promise<void>}
   */
  async goToAddNewEmployeePage() {
    await this.clickAndWaitForNavigation(this.addNewEmployeeLink);
  }

  /**
   * Reset input filters
   * @return {Promise<textContent>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberFromText(this.employeeGridTitle);
  }

  /**
   * get text from a column from table
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(this.employeesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
  }

  /**
   * Go to Edit employee page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditEmployeePage(row) {
    await this.clickAndWaitForNavigation(this.employeesListTableEditLink.replace('%ROW', row));
  }

  /**
   * Filter list of employees
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterEmployees(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.employeeFilterInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.employeeFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row) {
    if (await this.elementVisible(
      this.employeesListColumnValidIcon.replace('%ROW', row),
      100,
    )) return true;
    return false;
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      this.page.click(this.employeesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'active'));
      if (valueWanted) {
        await this.page.waitForSelector(this.employeesListColumnValidIcon.replace('%ROW', row));
      } else {
        await this.page.waitForSelector(
          this.employeesListColumnNotValidIcon.replace('%ROW', row));
      }
      return true;
    }
    return false;
  }

  /**
   * Delete employee
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteEmployee(row) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.employeesListTableToggleDropDown.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.employeesListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete
    await Promise.all([
      this.page.click(this.employeesListTableDeleteLink.replace('%ROW', row)),
      this.page.waitForSelector(this.alertSuccessBlockParagraph),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable employees by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all employees with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteBulkActions() {
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.page.waitForSelector(this.alertSuccessBlockParagraph),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Go to Profiles page
   * @return {Promise<void>}
   */
  async goToProfilesPage() {
    await this.clickAndWaitForNavigation(this.profilesTab);
  }
};
