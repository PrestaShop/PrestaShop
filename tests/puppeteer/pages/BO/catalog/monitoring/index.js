require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Monitoring extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Monitoring •';

    // Selectors
    this.gridPanel = '#%GRID_grid_panel';
    this.gridTable = '#%GRID_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} div.card-header h3`;
    // Filters
    this.filterColumn = `${this.gridTable} #%GRID_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='%GRID[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='%GRID[actions][reset]']`;
    // Table
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.editRowLink = `${this.actionsColumn} a[data-original-title='Edit']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[href*='/delete']`;
    // Category selectors
    this.viewCategoryRowLink = `${this.actionsColumn} a[data-original-title='View']`
      .replace('%GRID', 'empty_category');
    this.editCategoryRowLink = `${this.dropdownToggleMenu} a[href*='/edit']`
      .replace('%GRID', 'empty_category');
    this.deleteCategoryRowLink = `${this.dropdownToggleMenu} a.js-delete-category-row-action`
      .replace('%GRID', 'empty_category');
    this.deleteModeModal = '#empty_category_grid_delete_categories_modal';
    this.deleteModeInput = '#delete_categories_delete_mode_%POSITION';
    this.deleteModeModalDiv = '#delete_categories_delete_mode';
    this.submitDeleteModeButton = `${this.deleteModeModal} button.js-submit-delete-categories`;
  }

  /* Reset Methods */
  /**
   * Get number of element in table grid
   * @param grid, which grid to get number of element from
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(grid) {
    return this.getNumberFromText(this.gridHeaderTitle.replace('%GRID', grid));
  }

  /**
   * Reset filters in table
   * @param grid, which grid to reset
   * @return {Promise<void>}
   */
  async resetFilter(grid) {
    const resetButton = await this.replaceAll(this.filterResetButton, '%GRID', grid);
    if (!(await this.elementNotVisible(resetButton, 2000))) {
      await this.clickAndWaitForNavigation(resetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @param grid, which grid to reset
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines(grid) {
    await this.resetFilter(grid);
    return this.getNumberOfElementInGrid(grid);
  }


  /* Filter Methods */
  /**
   * Filter Table
   * @param grid, which grid to filter
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(grid, filterType, filterBy, value = '') {
    const filterColumn = await this.replaceAll(this.filterColumn, '%GRID', grid);
    const filterSearchButton = await this.replaceAll(this.filterSearchButton, '%GRID', grid);
    switch (filterType) {
      case 'input':
        await this.setValue(filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(filterColumn.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter column not found : ${filterBy}`);
    }
    // click on search
    await this.clickAndWaitForNavigation(filterSearchButton);
  }

  /* Grid methods */
  /**
   * get text from a column
   * @param grid, which grid to get text from
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(grid, row, column) {
    return this.getTextContent(
      this.tableColumn
        .replace('%GRID', grid)
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * Go to edit element page in grid
   * @param grid
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async goToEditElementPage(grid, row) {
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%GRID', grid).replace('%ROW', row));
  }

  /**
   * Open dropdown menu in grid
   * @param grid
   * @param row
   * @return {Promise<void>}
   */
  async openDropdownMenu(grid, row) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row).replace('%GRID', grid)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%GRID', grid).replace('%ROW', row),
      ),
    ]);
  }

  /**
   * Delete Row in table
   * @param grid
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteProductInGrid(grid, row) {
    this.dialogListener(true);
    await this.openDropdownMenu(grid, row);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%GRID', grid).replace('%ROW', row));
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Categories methods */
  /**
   * View category in grid
   * @param row
   * @return {Promise<void>}
   */
  async viewCategoryInGrid(row) {
    await this.clickAndWaitForNavigation(this.viewCategoryRowLink.replace('%ROW', row));
  }

  /**
   * Go to edit category page
   * @param row
   * @return {Promise<void>}
   */
  async editCategoryInGrid(row) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.editCategoryRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table empty categories
   * @param grid
   * @param row, row to delete
   * @param deletionModePosition, which mode to choose for delete
   * @return {Promise<textContent>}
   */
  async deleteCategoryInGrid(grid, row, deletionModePosition) {
    this.dialogListener(true);
    await this.openDropdownMenu(grid, row);
    await Promise.all([
      this.page.click(this.deleteCategoryRowLink.replace('%ROW', row)),
      this.page.waitForSelector(this.deleteModeModal, {visible: true}),
    ]);
    // choose deletion mode
    await this.page.click(this.deleteModeInput.replace('%POSITION', deletionModePosition));
    await this.clickAndWaitForNavigation(this.submitDeleteModeButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
