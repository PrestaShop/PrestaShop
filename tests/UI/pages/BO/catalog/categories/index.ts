import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Categories page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Categories extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageRootTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly editHomeCategoryButton: string;

  private readonly addNewCategoryLink: string;

  private readonly categoryGridPanel: string;

  private readonly categoryGridTitle: string;

  private readonly categoriesListForm: string;

  private readonly categoriesListTableRow: (row: number) => string;

  private readonly categoriesListTableColumn: (row: number, column: string) => string;

  private readonly categoriesListTableDraggableColumn: (row: number) => string;

  private readonly categoriesListTableToggleDropDown: (row: number, column: string) => string;

  private readonly categoriesListTableDeleteLink: (row: number, column: string) => string;

  private readonly categoriesListTableViewLink: (row: number, column: string) => string;

  private readonly categoriesListTableEditLink: (row: number, column: string) => string;

  private readonly categoriesListColumnStatus: (row: number) => string;

  private readonly categoriesListColumnStatusToggleInput: (row: number) => string;

  private readonly categoryFilterInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllRowsDiv: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly deleteCategoryModal: string;

  private readonly deleteCategoryModalDeleteButton: string;

  private readonly deleteCategoryModalModeInput: (id: number) => string;

  private readonly categoryGridActionsButton: string;

  private readonly gridActionDropDownMenu: string;

  private readonly gridActionExportLink: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on categories page
   */
  constructor() {
    super();

    this.pageTitle = `Categories • ${global.INSTALL.SHOP_NAME}`;
    this.pageRootTitle = `Category Root • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.editHomeCategoryButton = '#main-div .breadcrumb a[href*=\'edit\']';

    // Header links
    this.addNewCategoryLink = '#page-header-desc-configuration-add[title=\'Add new category\']';

    // List of categories
    this.categoryGridPanel = '#category_grid_panel';
    this.categoryGridTitle = `${this.categoryGridPanel} h3.card-header-title`;
    this.categoriesListForm = '#category_grid';
    this.categoriesListTableRow = (row: number) => `${this.categoriesListForm} tbody tr:nth-child(${row})`;
    this.categoriesListTableColumn = (row: number, column: string) => `${this.categoriesListTableRow(row)} td.column-${column}`;
    this.categoriesListTableDraggableColumn = (row: number) => `${this.categoriesListTableRow(row)
    } td.column-position_drag span i`;
    this.categoriesListTableToggleDropDown = (row: number, column: string) => `${this.categoriesListTableColumn(row, column)
    } a[data-toggle='dropdown']`;
    this.categoriesListTableDeleteLink = (row: number, column: string) => `${this.categoriesListTableColumn(row, column)
    } a.grid-delete-row-link`;
    this.categoriesListTableViewLink = (row: number, column: string) => `${this.categoriesListTableColumn(row, column)
    } a.grid-view-row-link`;
    this.categoriesListTableEditLink = (row: number, column: string) => `${this.categoriesListTableColumn(row, column)
    } a.grid-edit-row-link`;

    this.categoriesListColumnStatus = (row: number) => `${this.categoriesListTableColumn(row, 'active')} .ps-switch`;
    this.categoriesListColumnStatusToggleInput = (row: number) => `${this.categoriesListColumnStatus(row)} input`;

    // Filters
    this.categoryFilterInput = (filterBy: string) => `${this.categoriesListForm} #category_${filterBy}`;
    this.filterSearchButton = `${this.categoriesListForm} .grid-search-button`;
    this.filterResetButton = `${this.categoriesListForm} .grid-reset-button`;

    // Bulk Actions
    this.selectAllRowsDiv = `${this.categoriesListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.categoriesListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.categoriesListForm} #category_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.categoriesListForm} #category_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.categoriesListForm} #category_grid_bulk_action_delete_selection`;

    // Sort Selectors
    this.tableHead = `${this.categoriesListForm} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Modal Dialog
    this.deleteCategoryModal = '#category_grid_delete_categories_modal.show';
    this.deleteCategoryModalDeleteButton = `${this.deleteCategoryModal} button.js-submit-delete-categories`;
    this.deleteCategoryModalModeInput = (id: number) => `${this.deleteCategoryModal} #delete_categories_delete_mode_${id}`;

    // Grid Actions
    this.categoryGridActionsButton = '#category-grid-actions-button';
    this.gridActionDropDownMenu = '#category-grid-actions-dropdown-menu';
    this.gridActionExportLink = '#category-grid-action-export';

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.categoryGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.categoryGridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.categoryGridPanel} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */
  /**
   * Go to add new category page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewCategoryPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCategoryLink);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await page.click(this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.categoryGridTitle);
  }

  /**
   * Reset Filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of categories
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterCategories(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.categoryFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.categoryFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get Value of column Displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.categoriesListColumnStatusToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(this.categoriesListColumnStatus(row));

      await this.waitForVisibleSelector(
        page,
        `${this.categoriesListColumnStatusToggleInput(row)}[value='${valueWanted ? 1 : 0}']:checked`,
      );

      return true;
    }

    return false;
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCategories(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.categoriesListTableColumn(row, column));
  }

  /**
   * Get all information from categories table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<{name: string, description: string, id: string, position: number, status: boolean}>}
   */
  async getCategoryFromTable(page: Page, row: number) {
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
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows column
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCategories(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to Edit Category page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(page: Page, row: number): Promise<void> {
    // Click on dropDown
    await Promise.all([
      page.click(this.categoriesListTableToggleDropDown(row, 'actions')),
      this.waitForVisibleSelector(page, this.categoriesListTableEditLink(row, 'actions')),
    ]);
    // Click on edit
    await this.clickAndWaitForURL(page, this.categoriesListTableEditLink(row, 'actions'));
  }

  /**
   * View subcategories in list
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToViewSubCategoriesPage(page: Page, row: number): Promise<void> {
    if (
      await this.elementVisible(page, this.categoriesListTableViewLink(row, 'actions'), 100)
    ) {
      await this.clickAndWaitForURL(page, this.categoriesListTableViewLink(row, 'actions'));
    } else {
      await this.clickAndWaitForURL(page, `${this.categoriesListTableColumn(row, 'name')} a`);
    }
  }

  /**
   * Delete Category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param modeID {number} Deletion method to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCategory(page: Page, row: number, modeID: number = 0): Promise<string> {
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
   * @param page {Page} Browser tab
   * @param modeID {number} Deletion mode ID to choose in modal
   * @return {Promise<void>}
   */
  async chooseOptionAndDelete(page: Page, modeID: number): Promise<void> {
    await this.setChecked(page, this.deleteCategoryModalModeInput(modeID));
    await this.clickAndWaitForURL(page, this.deleteCategoryModalDeleteButton);
    await this.waitForVisibleSelector(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable categories by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all Categories with Bulk Actions
   * @param page {Page} Browser tab
   * @param modeID {number} Deletion mode ID to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCategoriesBulkActions(page: Page, modeID: number = 0): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, (el: HTMLElement) => el.click()),
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
   * @param page {Page} Browser tab
   * @param actualPosition {number} Value of actual position
   * @param newPosition {number} Value of new position
   * @return {Promise<string|null>}
   */
  async changeCategoryPosition(page: Page, actualPosition: number, newPosition: number): Promise<string|null> {
    await this.dragAndDrop(
      page,
      this.categoriesListTableDraggableColumn(actualPosition),
      this.categoriesListTableDraggableColumn(newPosition),
    );

    return this.getGrowlMessageContent(page);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @param page {Page} Browser tab
   * @return {Promise<string|null>}
   */
  async exportDataToCsv(page: Page): Promise<string|null> {
    await Promise.all([
      page.click(this.categoryGridActionsButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    const [downloadPath] = await Promise.all([
      this.clickAndWaitForDownload(page, this.gridActionExportLink),
      this.waitForHiddenSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    return downloadPath;
  }

  /**
   * Get category from table in csv format
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async getCategoryInCsvFormat(page: Page, row: number): Promise<string> {
    const category = await this.getCategoryFromTable(page, row);

    return `${category.id};`
      + `${category.name};`
      + `"${category.description}";`
      + `${category.position - 1};`
      + `${category.status ? 1 : 0}`;
  }

  /**
   * Go to edit category page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToEditHomeCategoryPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.editHomeCategoryButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }
}

export default new Categories();
