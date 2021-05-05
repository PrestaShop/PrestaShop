require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Categories extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Categories';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.editHomeCategoryButton = '#main-div .breadcrumb a[href*=\'edit\']';
    // Header links
    this.addNewCategoryLink = '#page-header-desc-configuration-add[title=\'Add new category\']';
    // List of categories
    this.categoryGridPanel = '#category_grid_panel';
    this.categoryGridTitle = `${this.categoryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#category_grid';
    this.categoriesListTableRow = row => `${this.categoriesListForm} tbody tr:nth-child(${row})`;
    this.categoriesListTableColumn = (row, column) => `${this.categoriesListTableRow(row)} td.column-${column}`;
    this.categoriesListTableDraggableColumn = row => `${this.categoriesListTableRow(row)
    } td.column-position_drag span i`;
    this.categoriesListTableToggleDropDown = (row, column) => `${this.categoriesListTableColumn(row, column)
    } a[data-toggle='dropdown']`;
    this.categoriesListTableDeleteLink = (row, column) => `${this.categoriesListTableColumn(row, column)
    } a[data-category-delete-url]`;
    this.categoriesListTableViewLink = (row, column) => `${this.categoriesListTableColumn(row, column)
    } a[data-original-title='View']`;
    this.categoriesListTableEditLink = (row, column) => `${this.categoriesListTableColumn(row, column)
    } a[href*='edit']`;
    this.categoriesListStatusColumn = row => this.categoriesListTableColumn(row, 'active');
    this.categoriesListColumnValidIcon = row => `${this.categoriesListStatusColumn(row)} i.grid-toggler-icon-valid`;
    this.categoriesListColumnNotValidIcon = row => `${this.categoriesListStatusColumn(row)
    } i.grid-toggler-icon-not-valid`;
    // Filters
    this.categoryFilterInput = filterBy => `${this.categoriesListForm} #category_${filterBy}`;
    this.filterSearchButton = `${this.categoriesListForm} button[name='category[actions][search]']`;
    this.filterResetButton = `${this.categoriesListForm} button[name='category[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsDiv = `${this.categoriesListForm} tr.column-filters div.md-checkbox`;
    this.bulkActionsToggleButton = `${this.categoriesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.categoriesListForm} #category_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.categoriesListForm} #category_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.categoriesListForm} #category_grid_bulk_action_delete_selection`;
    // Sort Selectors
    this.tableHead = `${this.categoriesListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
    // Modal Dialog
    this.deleteCategoryModal = '#category_grid_delete_categories_modal.show';
    this.deleteCategoryModalDeleteButton = `${this.deleteCategoryModal} button.js-submit-delete-categories`;
    this.deleteCategoryModalModeInput = id => `${this.deleteCategoryModal} #delete_categories_delete_mode_${id}`;
    // Grid Actions
    this.categoryGridActionsButton = '#category-grid-actions-button';
    this.gridActionDropDownMenu = 'div.dropdown-menu[aria-labelledby=\'category-grid-actions-button\']';
    this.gridActionExportLink = `${this.gridActionDropDownMenu} a[href*='/export']`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.categoryGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.categoryGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.categoryGridPanel} [aria-label='Previous']`;
  }

  /*
  Methods
   */
  /**
   * Go to add new category page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewCategoryPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCategoryLink);
  }

  /**
   * Reset input filters
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.categoryGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of categories
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCategories(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.categoryFilterInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.categoryFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get Value of column Displayed
   * @param page
   * @param row, row in table
   * @return {Promise<boolean>}
   */
  async getStatus(page, row) {
    return this.elementVisible(page, this.categoriesListColumnValidIcon(row), 500);
  }

  /**
   * Quick edit toggle column value
   * @param page
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.categoriesListStatusColumn(row), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(`${this.categoriesListStatusColumn(row)} i`);
      await this.waitForVisibleSelector(
        page,
        (
          valueWanted
            ? this.categoriesListColumnValidIcon(row)
            : this.categoriesListColumnNotValidIcon(row)
        ),
        15000,
      );

      return true;
    }

    return false;
  }

  /**
   * Get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCategories(page, row, column) {
    return this.getTextContent(page, this.categoriesListTableColumn(row, column));
  }

  /**
   * Get all information from categories table
   * @param page
   * @param row
   * @returns {Promise<{name: string, description: string, id: string, position: *, status: boolean}>}
   */
  async getCategoryFromTable(page, row) {
    return {
      id: await this.getTextColumnFromTableCategories(page, row, 'id_category'),
      name: await this.getTextColumnFromTableCategories(page, row, 'name'),
      description: await this.getTextColumnFromTableCategories(page, row, 'description'),
      position: parseFloat(await this.getTextColumnFromTableCategories(page, row, 'position')),
      status: await this.getStatus(page, row),
    };
  }

  /**
   * Get content from all rows
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCategories(page, i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to Edit Category page
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(page, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.categoriesListTableToggleDropDown(row, 'actions')),
      this.waitForVisibleSelector(page, this.categoriesListTableEditLink(row, 'actions')),
    ]);
    // Click on edit
    await this.clickAndWaitForNavigation(page, this.categoriesListTableEditLink(row, 'actions'));
  }

  /**
   * View subcategories in list
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToViewSubCategoriesPage(page, row) {
    if (
      await this.elementVisible(page, this.categoriesListTableViewLink(row, 'actions'), 100)
    ) {
      await this.clickAndWaitForNavigation(page, this.categoriesListTableViewLink(row, 'actions'));
    } else {
      await this.clickAndWaitForNavigation(page, `${this.categoriesListTableColumn(row, 'name')} a`);
    }
  }

  /**
   * Delete Category
   * @param page
   * @param row, row in table
   * @param modeID, Deletion method to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCategory(page, row, modeID = '0') {
    // Click on dropDown
    await Promise.all([
      page.click(this.categoriesListTableToggleDropDown(row, 'actions')),
      this.waitForVisibleSelector(
        page,
        `${this.categoriesListTableToggleDropDown(row, 'actions')}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.categoriesListTableDeleteLink(row, 'actions')),
      this.waitForVisibleSelector(page, this.deleteCategoryModal),
    ]);
    await this.chooseOptionAndDelete(page, modeID);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose the option and delete
   * @param page
   * @param modeID, Deletion mode ID to choose in modal
   * @return {Promise<void>}
   */
  async chooseOptionAndDelete(page, modeID) {
    await page.click(this.deleteCategoryModalModeInput(modeID));
    await this.clickAndWaitForNavigation(page, this.deleteCategoryModalDeleteButton);
    await this.waitForVisibleSelector(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable categories by Bulk Actions
   * @param page
   * @param enable
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(`${this.selectAllRowsDiv} i`, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all Categories with Bulk Actions
   * @param page
   * @param modeID, Deletion mode ID to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCategoriesBulkActions(page, modeID = '0') {
    // Click on Select All
    await Promise.all([
      page.$eval(`${this.selectAllRowsDiv} i`, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, this.deleteCategoryModal),
    ]);
    await this.chooseOptionAndDelete(page, modeID);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change category position
   * @param page
   * @param categoryRow
   * @param position
   * @return {Promise<string>}
   */
  async changeCategoryPosition(page, categoryRow, position) {
    await this.dragAndDrop(
      page,
      this.categoriesListTableDraggableColumn(categoryRow),
      this.categoriesListTableDraggableColumn(position),
    );
    return this.getGrowlMessageContent(page);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @param page
   * @return {Promise<*>}
   */
  async exportDataToCsv(page) {
    await Promise.all([
      page.click(this.categoryGridActionsButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.gridActionExportLink),
      this.waitForHiddenSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);
    return download.path();
  }

  /**
   * Get category from table in csv format
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async getCategoryInCsvFormat(page, row) {
    const category = await this.getCategoryFromTable(page, row);
    return `${category.id};`
      + `${category.name};`
      + `"${category.description}";`
      + `${category.position - 1};`
      + `${category.status ? 1 : 0}`;
  }

  /**
   * Go to edit category page
   * @param page
   * @returns {Promise<void>}
   */
  async goToEditHomeCategoryPage(page) {
    await this.waitForSelectorAndClick(page, this.editHomeCategoryButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }
}

module.exports = new Categories();
