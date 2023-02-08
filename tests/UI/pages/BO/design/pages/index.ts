import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Pages page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Pages extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly addNewPageCategoryLink: string;

  private readonly addNewPageLink: string;

  private readonly gridPanel: (table: string) => string;

  private readonly gridTitle: (table: string) => string;

  private readonly gridTable: (table: string) => string;

  private readonly gridHeaderTitle: (table: string) => string;

  private readonly listForm: (table: string) => string;

  private readonly tableHead: (table: string) => string;

  private readonly sortColumnDiv: (table: string, column: string) => string;

  private readonly sortColumnSpanButton: (table: string, column: string) => string;

  private readonly listTableRow: (table: string, row: number) => string;

  private readonly listTableColumn: (table: string, row: number, column: string) => string;

  private readonly listTableStatusColumn: (table: string, row: number) => string;

  private readonly listTableStatusColumnToggleInput: (table: string, row: number) => string;

  private readonly selectAllRowsLabel: (table: string) => string;

  private readonly bulkActionsToggleButton: (table: string) => string;

  private readonly bulkActionsDeleteButton: (table: string) => string;

  private readonly bulkActionsEnableButton: (table: string) => string;

  private readonly bulkActionsDisableButton: (table: string) => string;

  private readonly confirmDeleteModal: (table: string) => string;

  private readonly confirmDeleteButton: (table: string) => string;

  private readonly filterColumn: (table: string, filterBy: string) => string;

  private readonly filterSearchButton: (table: string) => string;

  private readonly filterResetButton: (table: string) => string;

  private readonly listTableToggleDropDown: (table: string, row: number) => string;

  private readonly listTableEditLink: (table: string, row: number) => string;

  private readonly deleteRowLink: (table: string, row: number) => string;

  private readonly backToListButton: string;

  private readonly categoriesListTableViewLink: (row: number) => string;

  private readonly categoriesPaginationLimitSelect: string;

  private readonly categoriesPaginationLabel: string;

  private readonly categoriesPaginationNextLink: string;

  private readonly categoriesPaginationPreviousLink: string;

  private readonly pagesPaginationLimitSelect: string;

  private readonly pagesPaginationLabel: string;

  private readonly pagesPaginationNextLink: string;

  private readonly pagesPaginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pages page
   */
  constructor() {
    super();

    this.pageTitle = 'Pages';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header link
    this.addNewPageCategoryLink = '#page-header-desc-configuration-add_cms_category[title=\'Add new page category\']';
    this.addNewPageLink = '#page-header-desc-configuration-add_cms_page[title=\'Add new page\']';

    // Common Selectors
    this.gridPanel = (table: string) => `#${table}_grid_panel`;
    this.gridTitle = (table: string) => `${this.gridPanel(table)} h3.card-header-title`;
    this.gridTable = (table: string) => `#${table}_grid_table`;
    this.gridHeaderTitle = (table: string) => `${this.gridPanel(table)} h3.card-header-title`;
    this.listForm = (table: string) => `#${table}_grid`;

    // Sort Selectors
    this.tableHead = (table: string) => `${this.listForm(table)} thead`;
    this.sortColumnDiv = (table: string, column: string) => `${this.tableHead(table)}`
      + ` div.ps-sortable-column[data-sort-col-name='${column}']`;

    this.sortColumnSpanButton = (table: string, column: string) => `${this.sortColumnDiv(table, column)} span.ps-sort`;
    this.listTableRow = (table: string, row: number) => `${this.listForm(table)} tbody tr:nth-child(${row})`;
    this.listTableColumn = (table: string, row: number, column: string) => `${this.listTableRow(table, row)} td.column-${column}`;
    this.listTableStatusColumn = (table: string, row: number) => `${this.listTableColumn(table, row, 'active')} .ps-switch`;
    this.listTableStatusColumnToggleInput = (table: string, row: number) => `${this.listTableStatusColumn(table, row)} input`;

    // Bulk Actions
    this.selectAllRowsLabel = (table: string) => `${this.listForm(table)} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = (table: string) => `${this.listForm(table)} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = (table: string) => `#${table}_grid_bulk_action_delete_selection`;
    this.bulkActionsEnableButton = (table: string) => `#${table}_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = (table: string) => `#${table}_grid_bulk_action_disable_selection`;
    this.confirmDeleteModal = (table: string) => `#${table}-grid-confirm-modal`;
    this.confirmDeleteButton = (table: string) => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;

    // Filters
    this.filterColumn = (table: string, filterBy: string) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = (table: string) => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = (table: string) => `${this.gridTable(table)} .grid-reset-button`;

    // Actions buttons in Row
    this.listTableToggleDropDown = (table: string, row: number) => `${this.listTableColumn(table, row, 'actions')}`
      + ' a[data-toggle=\'dropdown\']';

    this.listTableEditLink = (table: string, row: number) => `${this.listTableColumn(table, row, 'actions')}`
      + ' a.grid-edit-row-link';

    this.deleteRowLink = (table: string, row: number) => `${this.listTableColumn(table, row, 'actions')} a.grid-delete-row-link`;

    // Categories selectors
    this.backToListButton = '#cms_page_category_grid_panel a.back-to-list-link';
    this.categoriesListTableViewLink = (row: number) => `${this.listTableColumn('cms_page_category', row, 'actions')}`
      + ' a.grid-view-row-link';

    this.categoriesPaginationLimitSelect = '#paginator_select_page_limit';
    this.categoriesPaginationLabel = `${this.listForm('cms_page_category')} .col-form-label`;
    this.categoriesPaginationNextLink = `${this.listForm('cms_page_category')} [data-role=next-page-link]`;
    this.categoriesPaginationPreviousLink = `${this.listForm('cms_page_category')} [data-role='previous-page-link']`;

    // Pages selectors
    this.pagesPaginationLimitSelect = '#paginator_select_page_limit';
    this.pagesPaginationLabel = `${this.listForm('cms_page')} .col-form-label`;
    this.pagesPaginationNextLink = `${this.listForm('cms_page')} [data-role=next-page-link]`;
    this.pagesPaginationPreviousLink = `${this.listForm('cms_page')} [data-role='previous-page-link']`;
  }

  /*
 Methods
  */

  // Common methods

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of lines
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page, tableName: string): Promise<number> {
    const resetButton = this.filterResetButton(tableName);

    if (await this.elementVisible(page, resetButton, 2000)) {
      await page.click(resetButton);
      await this.elementNotVisible(page, resetButton, 2000);
    }
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to filter
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page: Page, tableName: string, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value === '1' ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton(tableName));
  }

  /**
   * Delete row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete row from it
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteRowInTable(page: Page, tableName: string, row: number): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown(tableName, row)),
      this.waitForVisibleSelector(page, `${this.listTableToggleDropDown(tableName, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(tableName, row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, tableName);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete rows with bulk actions
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page: Page, tableName: string): Promise<string> {
    // Add listener to dialog to accept deletion
    await this.dialogListener(page);

    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(tableName), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, this.bulkActionsDeleteButton(tableName)),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton(tableName)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, tableName);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to confirm delete
   * @return {Promise<void>}
   */
  async confirmDeleteFromTable(page: Page, tableName: string): Promise<void> {
    await page.click(this.confirmDeleteButton(tableName));
    await this.elementNotVisible(page, this.confirmDeleteButton(tableName), 2000);
  }

  /**
   * Get value of column Displayed in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get status
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, tableName: string, row: number): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.listTableStatusColumnToggleInput(tableName, row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value in table
   * @param page {Page} Browser tab
   * @param tableName {string} table name to set status
   * @param row {number} row on table
   * @param valueWanted {boolean} Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, tableName: string, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, tableName, row) !== valueWanted) {
      await page.click(this.listTableStatusColumn(tableName, row));

      return true;
    }

    return false;
  }

  /**
   * Enable / disable column by Bulk Actions
   * @param page {Page} Browser tab
   * @param tableName {string}  Table name to bulk delete rows
   * @param enable {boolean} True if we need to bulk enable, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, tableName: string, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(tableName), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}`),
    ]);

    // Click on enable/disable and wait for modal
    await page.click(enable ? this.bulkActionsEnableButton(tableName) : this.bulkActionsDisableButton(tableName));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text column
   * @param row {number} Row on table
   * @param column{string} Column name to get text from it
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, tableName: string, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.listTableColumn(tableName, row, column));
  }

  /**
   * Get text column form table cms page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text from it
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPage(page: Page, row: number, column: string): Promise<string> {
    return this.getTextColumnFromTable(page, 'cms_page', row, column);
  }

  /**
   * Get text column form table cms page category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text from it
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPageCategory(page: Page, row: number, column: string): Promise<string> {
    return this.getTextColumnFromTable(page, 'cms_page_category', row, column);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows column content
   * @param column {string} Column name to get all rows column content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, tableName: string, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable: string[] = [];
    let rowContent: string;

    for (let i = 1; i <= rowsNumber; i++) {
      if (tableName === 'cms_page_category') {
        rowContent = await this.getTextColumnFromTableCmsPageCategory(page, i, column);
        allRowsContentTable.push(rowContent);
      } else if (tableName === 'cms_page') {
        rowContent = await this.getTextColumnFromTableCmsPage(page, i, column);
        allRowsContentTable.push(rowContent);
      }
    }

    return allRowsContentTable;
  }

  /**
   * Get content from all rows table cms page category
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows column content
   * @return {Promise<Array<string>>}
   */
  getAllRowsColumnContentTableCmsPageCategory(page: Page, column: string): Promise<string[]> {
    return this.getAllRowsColumnContent(page, 'cms_page_category', column);
  }

  /**
   * Get content from all rows table cms page
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows column content
   * @returns {Promise<Array<string>>}
   */
  getAllRowsColumnContentTableCmsPage(page: Page, column: string): Promise<string[]> {
    return this.getAllRowsColumnContent(page, 'cms_page', column);
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of elements
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page, tableName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridTitle(tableName));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, tableName: string, sortBy: string, sortDirection : string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Sort table cms page category
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTableCmsPageCategory(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    return this.sortTable(page, 'cms_page_category', sortBy, sortDirection);
  }

  /**
   * Sort table cms page
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTableCmsPage(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    return this.sortTable(page, 'cms_page', sortBy, sortDirection);
  }

  // Category methods

  /**
   * Go to Edit Category page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(page: Page, row: number): Promise<void> {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown('cms_page_category', row)),
      this.waitForVisibleSelector(
        page,
        `${this.listTableToggleDropDown('cms_page_category', row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on edit
    await this.clickAndWaitForURL(page, this.listTableEditLink('cms_page_category', row));
  }

  /**
   * Go to new Page Category page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewPageCategory(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewPageCategoryLink);
  }

  /**
   * Back to Pages page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async backToList(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.backToListButton);
  }

  /**
   * View Category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async viewCategory(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.categoriesListTableViewLink(row));
  }

  // Page methods

  /**
   * Go to new Page page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewPageLink);
  }

  /**
   * Go to Edit page Page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.listTableEditLink('cms_page', row));
  }

  /**
   * Select category pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectCategoryPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.categoriesPaginationLimitSelect, number);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationCategoryNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.categoriesPaginationNextLink);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationCategoryPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.categoriesPaginationPreviousLink);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Select pages pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit number
   * @returns {Promise<string>}
   */
  async selectPagesPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.pagesPaginationLimitSelect, number);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPagesNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.pagesPaginationNextLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPagesPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.pagesPaginationPreviousLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }
}

export default new Pages();
