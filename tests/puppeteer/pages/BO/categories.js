// Importing page
const BOBasePage = require('../BO/BObasePage');

module.exports = class Categories extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Categories';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // List of categories
    this.categpryGridPanel = '#category_grid_panel';
    this.categoryGridTitle = `${this.categpryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#category_grid';
    this.categoriesListTableRow = `${this.categoriesListForm} tbody tr:nth-child(%ROW)`;
    this.categoriesListTableColumn = `${this.categoriesListTableRow} td.column-%COLUMN`;
    this.categoriesListColumnValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-valid`;
    this.categoriesListColumnNotValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-not-valid`;

    // Filters input
    this.categoryFilterInput = `${this.categoriesListForm} #category_%FILTERBY`;
    this.filterSearchButton = `${this.categoriesListForm} button[name='category[actions][search]']`;
    this.filterResetButton = `${this.categoriesListForm} button[name='category[actions][reset]']`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @return {Promise<void>}
   */
  async resetFilter() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.filterResetButton),
    ]);
  }

  /**
   * Filter list of categories
   * @param filterType, input or select to choose method of filter
   * @param filterBy, colomn to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCategories(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.categoryFilterInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.categoryFilterInput.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.filterSearchButton),
    ]);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row, column) {
    if (await this.elementVisible(
      this.categoriesListColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column), 100)) return 'Yes';
    return 'No';
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>}
   */
  async updateToggleColumnValue(row, column, valueWanted = 'Yes') {
    if (await this.getToggleColumnValue(row, column) !== valueWanted) {
      await Promise.all([
        this.page.click(this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', column)),
      ]);
      if (valueWanted === 'Yes') {
        await this.page.waitForSelector(this.categoriesListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'));
      } else {
        await this.page.waitForSelector(
          this.categoriesListColumnNotValidIcon.replace('%ROW', 1).replace('%COLUMN', 'active'));
      }
      return true;
    }
    return false;
  }
};
