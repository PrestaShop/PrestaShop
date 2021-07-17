require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class LinkWidgets extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Link List •';

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
   * @param page
   * @return {Promise<void>}
   */
  async goToNewLinkWidgetPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBlockLink);
  }

  /* Table methods */
  /**
   * Get Number of element in grid
   * @param page
   * @param hookId, table to get number from
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page, hookId) {
    return this.getNumberFromText(page, this.gridHeaderTitle(hookId));
  }

  /**
   * Delete link widget
   * @param page
   * @param hookId, table to delete from
   * @param row, row to delete
   * @returns {Promise<string>}
   */
  async deleteLinkWidget(page, hookId, row) {
    this.dialogListener(page, true);
    await Promise.all([
      page.click(this.dropdownToggleButton(hookId, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(hookId, row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(hookId, row));
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new LinkWidgets();
