require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Quick access page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class QuickAccess extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on quick access page
   */
  constructor() {
    super();

    this.pageTitle = 'Quick Access •';

    // Selectors
    // Header selectors
    this.addNewQuickAccessButton = '#page-header-desc-quick_access-new_quick_access';

    // Table selectors
    this.gridTable = '#table-quick_access';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='quick_accessFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonquick_access';
    this.filterResetButton = 'button[name=\'submitResetquick_access\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnLink = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnIsNewWindow = row => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_quick_access';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /*
  Methods
   */

  /**
   * Go to add new quick access page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewQuickAccessPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewQuickAccessButton);
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param columnName {string} Column name in the table
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_quick_access':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'link':
        columnSelector = this.tableColumnLink(row);
        break;

      case 'new_window':
        columnSelector = this.tableColumnIsNewWindow(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Filter quick access table
   * @param page {Page} Browser tab
   * @param filterType {string} Type of the filter (input or select)
   * @param filterBy {string} Value to use for the select type filter
   * @param value {string|number} Value for the select filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Bulk delete link
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteQuickAccessLink(page) {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new QuickAccess();
