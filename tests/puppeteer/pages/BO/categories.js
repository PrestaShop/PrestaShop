// Importing page
const BOBasePage = require('../BO/BObasePage');

module.exports = class Categories extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Categories';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewCategoryLink = '#page-header-desc-configuration-add[title=\'Add new category\']';
    // List of categories
    this.categpryGridPanel = '#category_grid_panel';
    this.categoryGridTitle = `${this.categpryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#category_grid';
    this.categoriesListTableRow = `${this.categoriesListForm} tbody tr:nth-child(%ROW)`;
    this.categoriesListTableColumn = `${this.categoriesListTableRow} td.column-%COLUMN`;
    this.categoriesListTableEditLink = `${this.categoriesListTableColumn} a[data-original-title='Edit']`;
    this.categoriesListTableToggleDropDown = `${this.categoriesListTableColumn} a[data-toggle='dropdown']`;
    this.categoriesListTableViewLink = `${this.categoriesListTableColumn} a[data-original-title="View"]`;
    this.categoriesListTableDeleteLink = `${this.categoriesListTableColumn} a[data-category-delete-url]`;
    this.categoriesListColumnValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-valid`;
    this.categoriesListColumnNotValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-not-valid`;

    // Filters input
    this.categoryFilterInput = `${this.categoriesListForm} #category_%FILTERBY`;
    this.filterSearchButton = `${this.categoriesListForm} button[name='category[actions][search]']`;
    this.filterResetButton = `${this.categoriesListForm} button[name='category[actions][reset]']`;

    // Modal Dialog
    this.deleteCategoryModal = '#category_grid_delete_categories_modal.show';
    this.deleteCategoryModalDeleteButton = `${this.deleteCategoryModal} button.js-submit-delete-categories`;
    this.deleteCategoryModalModeInput = `${this.deleteCategoryModal} #delete_categories_delete_mode_%ID`;
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

  /**
   * Go to Category Page
   * @return {Promise<void>}
   */
  async goToAddNewCategoryPage() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.addNewCategoryLink),
    ]);
  }

  /**
   * Go to Edit Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(row) {
    await Promise.all([
      this.page.click(this.categoriesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Go to Sub Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToSubCategoryPage(row) {
    await Promise.all([
      this.page.click(`${this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'name')} a`),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Delete Category
   * @param row, row in table
   * @param associateProductsToParentAndDisable, Deletion method to choose in modal
   * @return {Promise<textContent>}
   */
  async deleteCategory(row, associateProductsToParentAndDisable = true) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoriesListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.categoriesListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.categoriesListTableDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(this.deleteCategoryModal, {visible: true}),
    ]);
    await this.chooseOptionAndDelete(associateProductsToParentAndDisable);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Choose if associate product to the parent category, then disable them, delete category and perform delete action
   * @param associateThenDisable
   * @return {Promise<void>}
   */
  async chooseOptionAndDelete(associateThenDisable) {
    // Choose deletion method
    if (associateThenDisable) await this.page.click(this.deleteCategoryModalModeInput.replace('%ID', '0'));
    else await this.page.click(this.deleteCategoryModalModeInput.replace('%ID', '1'));
    // Click on delete button and wait for action to finish
    await Promise.all([
      this.page.click(this.deleteCategoryModalDeleteButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
  }
};
