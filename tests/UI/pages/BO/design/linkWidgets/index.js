require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class LinkWidgets extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Link Widget •';

    // Header Selectors
    this.newBlockLink = '#page-header-desc-configuration-add';
    this.gridPanel = hookId => `#link_widget_grid_${hookId}_grid_panel`;
    this.gridHeaderTitle = hookId => `${this.gridPanel(hookId)} h3.card-header-title`;
    this.gridTable = hookId => `table#link_widget_grid_${hookId}_grid_table`;
    this.tableRow = (hookId, row) => `${this.gridTable(hookId)} tbody tr:nth-child(${row})`;
    this.tableColumn = (hookId, row, column) => `${this.tableRow(hookId, row)} td.column-${column}`;
    this.actionsColumn = (hookId, row) => `${this.tableRow(hookId, row)} td.column-actions`;
    this.dropdownToggleButton = (hookId, row) => `${this.actionsColumn(hookId, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (hookId, row) => `${this.actionsColumn(hookId, row)} div.dropdown-menu`;
    this.deleteRowLink = (hookId, row) => `${this.dropdownToggleMenu(hookId, row)} a[data-url*='/delete']`;
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
   * @param hookId, table to get number from
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(hookId) {
    return this.getNumberFromText(this.gridHeaderTitle(hookId));
  }

  /**
   * Delete link widget
   * @param hookId, table to delete from
   * @param row, row to delete
   * @returns {Promise<string>}
   */
  async deleteLinkWidget(hookId, row) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(hookId, row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton(hookId, row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(hookId, row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
