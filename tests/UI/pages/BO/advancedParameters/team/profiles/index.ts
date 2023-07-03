import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Profiles page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Profiles extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewProfileLink: string;

  private readonly profileGridPanel: string;

  private readonly profileGridTitle: string;

  private readonly profilesListForm: string;

  private readonly profilesListTableRow: (row: number) => string;

  private readonly profilesListTableColumn: (row: number, column: string) => string;

  private readonly profilesListTableColumnAction: (row: number) => string;

  private readonly profilesListTableToggleDropDown: (row: number) => string;

  private readonly profilesListTableDeleteLink: (row: number) => string;

  private readonly profilesListTableEditLink: (row: number) => string;

  private readonly profileFilterInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly pagesPaginationLimitSelect: string;

  private readonly pagesPaginationLabel: string;

  private readonly pagesPaginationNextLink: string;

  private readonly pagesPaginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on profiles page
   */
  constructor() {
    super();

    this.pageTitle = 'Profiles';

    // Selectors
    // Header links
    this.addNewProfileLink = '#page-header-desc-configuration-add[title=\'Add new profile\']';

    // List of profiles
    this.profileGridPanel = '#profile_grid_panel';
    this.profileGridTitle = `${this.profileGridPanel} h3.card-header-title`;
    this.profilesListForm = '#profile_grid';
    this.profilesListTableRow = (row: number) => `${this.profilesListForm} tbody tr:nth-child(${row})`;
    this.profilesListTableColumn = (row: number, column: string) => `${this.profilesListTableRow(row)} td.column-${column}`;
    this.profilesListTableColumnAction = (row: number) => this.profilesListTableColumn(row, 'actions');
    this.profilesListTableToggleDropDown = (row: number) => `${this.profilesListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.profilesListTableDeleteLink = (row: number) => `${this.profilesListTableColumnAction(row)} a.grid-delete-row-link`;
    this.profilesListTableEditLink = (row: number) => `${this.profilesListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.profileFilterInput = (filterBy: string) => `${this.profilesListForm} #profile_${filterBy}`;
    this.filterSearchButton = `${this.profilesListForm} .grid-search-button`;
    this.filterResetButton = `${this.profilesListForm} .grid-reset-button`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.profilesListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.profilesListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.profilesListForm} #profile_grid_bulk_action_delete_selection`;

    // Delete modal
    this.confirmDeleteModal = '#profile-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Pages selectors
    this.pagesPaginationLimitSelect = '#paginator_select_page_limit';
    this.pagesPaginationLabel = `${this.profilesListForm} .col-form-label`;
    this.pagesPaginationNextLink = `${this.profilesListForm} [data-role=next-page-link]`;
    this.pagesPaginationPreviousLink = `${this.profilesListForm} [data-role='previous-page-link']`;

    // Sort Selectors
    this.tableHead = `${this.profileGridPanel} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /*
  Methods
   */

  /**
   * Go to new profile page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewProfilePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewProfileLink);
  }

  /**
   * get text from a column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.profilesListTableColumn(row, column));
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.profileGridTitle);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to Edit profile page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditProfilePage(page: Page, row: number): Promise<void> {
    // Click on edit
    await this.clickAndWaitForURL(page, this.profilesListTableEditLink(row));
  }

  /**
   * Filter list of profiles
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @returns {Promise<void>}
   */
  async filterProfiles(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.profileFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.profileFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Delete profile
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteProfile(page: Page, row: number): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.click(this.profilesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.profilesListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.profilesListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteProfiles(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteProfiles(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /**
   * Delete all profiles with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, this.bulkActionsDeleteButton),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await this.confirmDeleteProfiles(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select profiles pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.pagesPaginationLimitSelect, number);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Profiles pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.pagesPaginationNextLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Profiles pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.pagesPaginationPreviousLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get text content
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextContent(page, this.profilesListTableColumn(i, column));
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }
}

export default new Profiles();
