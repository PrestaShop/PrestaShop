require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

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
    this.categoriesListTableToggleDropDown = `${this.categoriesListTableColumn} a[data-toggle='dropdown']`;
    this.categoriesListTableDeleteLink = `${this.categoriesListTableColumn} a[data-category-delete-url]`;
    this.categoriesListTableViewLink = `${this.categoriesListTableColumn} a[data-original-title='View']`;
    this.categoriesListTableEditLink = `${this.categoriesListTableColumn} a[href*='edit']`;
    this.categoriesListColumnValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-valid`;
    this.categoriesListColumnNotValidIcon = `${this.categoriesListTableColumn} i.grid-toggler-icon-not-valid`;
    // Filters
    this.categoryFilterInput = `${this.categoriesListForm} #category_%FILTERBY`;
    this.filterSearchButton = `${this.categoriesListForm} button[name='category[actions][search]']`;
    this.filterResetButton = `${this.categoriesListForm} button[name='category[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.categoriesListForm} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.categoriesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.categoriesListForm} #category_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.categoriesListForm} #category_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.categoriesListForm} #category_grid_bulk_action_delete_selection`;
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
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberFromText(this.categoryGridTitle);
  }

  /**
   * Filter list of categories
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCategories(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.categoryFilterInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.categoryFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
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
      this.categoriesListColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column), 100)) return true;
    return false;
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, column, valueWanted = true) {
    if (await this.getToggleColumnValue(row, column) !== valueWanted) {
      this.page.click(this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
      if (valueWanted) {
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
   * Go to Edit Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.categoriesListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(this.categoriesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
    ]);
    // Click on edit
    await Promise.all([
      this.page.click(this.categoriesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * View subcategories in list
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToViewSubCategoriesPage(row) {
    if (await this.elementVisible(
      this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions'), 100)) {
      await Promise.all([
        this.page.click(this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      ]);
    } else {
      await Promise.all([
        this.page.click(`${this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'name')} a`),
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      ]);
    }
  }

  /**
   * Delete Category
   * @param row, row in table
   * @param modeID, Deletion method to choose in modal
   * @return {Promise<textContent>}
   */
  async deleteCategory(row, modeID = '0') {
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
    await this.chooseOptionAndDelete(modeID);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Choose the option and delete
   * @param modeID, Deletion mode ID to choose in modal
   * @return {Promise<void>}
   */
  async chooseOptionAndDelete(modeID) {
    await this.page.click(this.deleteCategoryModalModeInput.replace('%ID', modeID));
    await Promise.all([
      this.page.click(this.deleteCategoryModalDeleteButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
  }

  /**
   * Enable / disable categories by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeCategoriesEnabledColumnBulkActions(enable = true) {
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
      this.page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Categories with Bulk Actions
   * @param modeID, Deletion mode ID to choose in modal
   * @return {Promise<textContent>}
   */
  async deleteCategoriesBulkActions(modeID = '0') {
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
      this.page.waitForSelector(this.deleteCategoryModal, {visible: true}),
    ]);
    await this.chooseOptionAndDelete(modeID);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
