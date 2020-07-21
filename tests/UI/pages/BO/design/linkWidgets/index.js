require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class LinkWidgets extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Link Widget •';

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
    this.deleteRowLink = (hookName, row) => `${this.dropdownToggleMenu(hookName, row)} a[data-url*='/delete']`;
  }

  /* Header methods */
  /**
   * Go to new Block page
   * @return {Promise<void>}
   */
  async goToNewLinkWidgetPage() {
    await this.clickAndWaitForNavigation(this.newBlockLink);
  }

  /* Table methods */
  /**
   * Get Number of element in grid
   * @param hookName, table to get number from
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(hookName) {
    return this.getNumberFromText(this.gridHeaderTitle(hookName));
  }

  /**
   * Delete link widget
   * @param hookName, table to delete from
   * @param row, row to delete
   * @returns {Promise<string>}
   */
  async deleteLinkWidget(hookName, row) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(hookName, row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton(hookName, row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(hookName, row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
