require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Files extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Files • ';

    // Selectors header
    this.newAttachmentLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#attachment_grid_panel';
    this.gridTable = '#attachment_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #attachment_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='attachment[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='attachment[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.editRowLink = `${this.actionsColumn} a[data-original-title='Edit']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.viewRowLink = `${this.dropdownToggleMenu} a[href*='/view']`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
  }

  /* Header Methods */
  /**
   * Go to New attachment Page
   * @return {Promise<void>}
   */
  async goToAddNewFilePage() {
    await this.clickAndWaitForNavigation(this.newAttachmentLink);
  }

  /* Column Methods */
  /**
   * Go to edit file
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async goToEditFilePage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%ROW', row));
  }

  /**
   * View (download) file
   * @param row
   * @return {Promise<void>}
   */
  async viewFile(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.page.click(this.viewRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteFile(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterBy, value = '') {
    await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value);
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }
};
