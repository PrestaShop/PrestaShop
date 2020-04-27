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
    this.categoryGridPanel = '#category_grid_panel';
    this.categoryGridTitle = `${this.categoryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#category_grid';
    this.categoriesListTableRow = `${this.categoriesListForm} tbody tr:nth-child(%ROW)`;
    this.categoriesListTableColumn = `${this.categoriesListTableRow} td.column-%COLUMN`;
    this.categoriesListTableDraggableColumn = `${this.categoriesListTableRow} td.column-position_drag span i`;
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
    this.selectAllRowsLabel = `${this.categoriesListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.categoriesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.categoriesListForm} #category_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.categoriesListForm} #category_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.categoriesListForm} #category_grid_bulk_action_delete_selection`;
    // Sort Selectors
    this.tableHead = `${this.categoriesListForm} thead`;
    this.sortColumnDiv = `${this.tableHead} div.ps-sortable-column[data-sort-col-name='%COLUMN']`;
    this.sortColumnSpanButton = `${this.sortColumnDiv} span.ps-sort`;
    // Modal Dialog
    this.deleteCategoryModal = '#category_grid_delete_categories_modal.show';
    this.deleteCategoryModalDeleteButton = `${this.deleteCategoryModal} button.js-submit-delete-categories`;
    this.deleteCategoryModalModeInput = `${this.deleteCategoryModal} #delete_categories_delete_mode_%ID`;
    // Grid Actions
    this.categoryGridActionsButton = '#category-grid-actions-button';
    this.gridActionDropDownMenu = 'div.dropdown-menu[aria-labelledby=\'category-grid-actions-button\']';
    this.gridActionExportLink = `${this.gridActionDropDownMenu} a[href*='/export']`;
  }

  /*
  Methods
   */
  /**
   * Go to add new category page
   * @return {Promise<void>}
   */
  async goToAddNewCategoryPage() {
    await this.clickAndWaitForNavigation(this.addNewCategoryLink);
  }

  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.categoryGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
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
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row, column) {
    return this.elementVisible(
      this.categoriesListColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column),
      100,
    );
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, column, valueWanted = true) {
    await this.waitForVisibleSelector(
      this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', column),
      2000,
    );
    if (await this.getToggleColumnValue(row, column) !== valueWanted) {
      this.page.click(this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
      await this.waitForVisibleSelector(
        (valueWanted ? this.categoriesListColumnValidIcon : this.categoriesListColumnNotValidIcon)
          .replace('%ROW', row)
          .replace('%COLUMN', column)
        ,
      );
      return true;
    }
    return false;
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableCategories(row, column) {
    return this.getTextContent(
      this.categoriesListTableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * Get all information from categories table
   * @param row
   * @return {Promise<{object}>}
   */
  async getCategoryFromTable(row) {
    return {
      id: await this.getTextColumnFromTableCategories(row, 'id_category'),
      name: await this.getTextColumnFromTableCategories(row, 'name'),
      description: await this.getTextColumnFromTableCategories(row, 'description'),
      position: parseFloat(await this.getTextColumnFromTableCategories(row, 'position')),
      status: await this.getToggleColumnValue(row, 'active'),
    };
  }

  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCategories(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
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
      this.waitForVisibleSelector(this.categoriesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
    ]);
    // Click on edit
    await this.clickAndWaitForNavigation(
      this.categoriesListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
    );
  }

  /**
   * View subcategories in list
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToViewSubCategoriesPage(row) {
    if (
      await this.elementVisible(
        this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
        100,
      )
    ) {
      await this.clickAndWaitForNavigation(
        this.categoriesListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
      );
    } else {
      await this.clickAndWaitForNavigation(
        `${this.categoriesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'name')} a`,
      );
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
      this.waitForVisibleSelector(
        `${this.categoriesListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.categoriesListTableDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.waitForVisibleSelector(this.deleteCategoryModal),
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
    await this.clickAndWaitForNavigation(this.deleteCategoryModalDeleteButton);
    await this.waitForVisibleSelector(this.alertSuccessBlockParagraph);
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
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
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
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(this.deleteCategoryModal),
    ]);
    await this.chooseOptionAndDelete(modeID);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Change category position
   * @param categoryRow
   * @param position
   * @return {Promise<string>}
   */
  async changeCategoryPosition(categoryRow, position) {
    await this.dragAndDrop(
      this.categoriesListTableDraggableColumn.replace('%ROW', categoryRow),
      this.categoriesListTableDraggableColumn.replace('%ROW', position),
    );
    return this.getTextContent(this.growlMessageBlock);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv.replace('%COLUMN', sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton.replace('%COLUMN', sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 500) && i < 2) {
      await this.page.hover(this.sortColumnDiv.replace('%COLUMN', sortBy));
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @return {Promise<void>}
   */
  async exportDataToCsv() {
    await Promise.all([
      this.page.click(this.categoryGridActionsButton),
      this.waitForVisibleSelector(`${this.gridActionDropDownMenu}.show`),
    ]);
    await Promise.all([
      this.page.click(this.gridActionExportLink),
      this.page.waitForSelector(`${this.gridActionDropDownMenu}.show`, {hidden: true}),
    ]);
  }

  /**
   * Get category from table in csv format
   * @param row
   * @return {Promise<string>}
   */
  async getCategoryInCsvFormat(row) {
    const category = await this.getCategoryFromTable(row);
    return `${category.id};`
      + `${category.name};`
      + `"${category.description}";`
      + `${category.position - 1};`
      + `${category.status ? 1 : 0}`;
  }
};
