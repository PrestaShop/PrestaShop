require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Link list page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class LinkList extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on link list page
   */
  constructor() {
    super();

    this.pageTitle = 'Link List •';

    // Header Selectors
    this.newBlockLink = '#page-header-desc-configuration-add';
    this.gridPanel = hookName => `div[data-hook-name='${hookName}']`;
    this.gridHeaderTitle = hookName => `${this.gridPanel(hookName)} h3.card-header-title`;
    this.gridTable = hookName => `${this.gridPanel(hookName)} table.grid-table`;
    this.tableRow = (hookName, row) => `${this.gridTable(hookName)} tbody tr:nth-child(${row})`;
    this.tableColumn = (hookName, row, column) => `${this.tableRow(hookName, row)} td.column-${column}`;
    this.actionsColumn = (hookName, row) => `${this.tableRow(hookName, row)} td.column-actions`;
    this.dropdownToggleButton = (hookName, row) => `${this.actionsColumn(hookName, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (hookName, row) => `${this.actionsColumn(hookName, row)} div.dropdown-menu`;
    this.deleteRowLink = (hookName, row) => `${this.dropdownToggleMenu(hookName, row)} a.grid-delete-row-link`;
  }

  /* Header methods */
  /**
   * Go to new Block page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewLinkWidgetPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBlockLink);
  }

  /* Table methods */
  /**
   * Get number of element in grid
   * @param page {Page} Browser tab
   * @param hookName {string} Table name to get number of elements
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page, hookName) {
    return this.getNumberFromText(page, this.gridHeaderTitle(hookName));
  }

  /**
   * Delete link widget
   * @param page {Page} Browser tab
   * @param hookName {string} Table name to delete from
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteLinkWidget(page, hookName, row) {
    this.dialogListener(page, true);
    await Promise.all([
      page.click(this.dropdownToggleButton(hookName, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(hookName, row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(hookName, row));

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new LinkList();
