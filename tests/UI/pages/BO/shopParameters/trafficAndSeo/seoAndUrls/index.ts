import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Seo and urls page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class SeoAndUrls extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewSeoPageLink: string;

  public readonly successfulSettingsUpdateMessage: string;

  private readonly searchEnginesSubTabLink: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly accentedUrlToggleInput: (toggle: number) => string;

  private readonly saveSeoAndUrlFormButton: string;

  private readonly displayAttributesToggleInput: (toggle: number) => string;

  private readonly saveSeoOptionsFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on seo and urls page
   */
  constructor() {
    super();

    this.pageTitle = 'SEO & URLs •';

    // Header selectors
    this.addNewSeoPageLink = '#page-header-desc-configuration-add';
    this.successfulSettingsUpdateMessage = 'Update successful';

    // Sub tabs selectors
    this.searchEnginesSubTabLink = '#subtab-AdminSearchEngines';

    // Grid selectors
    this.gridPanel = '#meta_grid_panel';
    this.gridTable = '#meta_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Sort Selectors
    this.tableHead = `${this.gridPanel} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #meta_grid_bulk_action_delete_selection`;

    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #meta_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;

    // Actions buttons in Row
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-edit-row-link`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;

    // Delete modal
    this.confirmDeleteModal = '#meta-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;

    // Set up URL form
    this.accentedUrlToggleInput = (toggle: number) => `#meta_settings_set_up_urls_form_accented_url_${toggle}`;
    this.saveSeoAndUrlFormButton = '#form-set-up-urls-save-button';

    // Seo options form
    this.displayAttributesToggleInput = (toggle: number) => '#meta_settings_seo_options_form_product_attributes_in_title_'
      + `${toggle}`;
    this.saveSeoOptionsFormButton = '#meta_settings_seo_options_form_save_button';
  }

  /* header methods */
  /**
   * Go to new seo page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewSeoUrlPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewSeoPageLink);
  }

  /**
   * Go to search engines page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSearchEnginesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.searchEnginesSubTabLink);
  }

  /* Bulk actions methods */

  /**
   * Delete seo pages by bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteSeoUrlPage(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on button bulk action
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await this.confirmDeleteSeoUrlPage(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Column methods */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name of the value to return
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name of the value to return
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let row = 1; row <= rowsNumber; row++) {
      const rowContent = await this.getTextColumnFromTable(page, row, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to edit file
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditSeoUrlPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteSeoUrlPage(page: Page, row: number = 1): Promise<string> {
    await Promise.all([
      page.locator(this.dropdownToggleButton(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteRowLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSeoUrlPage(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteSeoUrlPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Reset methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await page.locator(this.filterResetButton).click();
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /* Filter methods */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterBy: string, value: string = ''): Promise<void> {
    await this.setValue(page, this.filterColumn(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
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
   * @param number {number} Pagination limit number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Enable/disable accented url
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableAccentedURL(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.accentedUrlToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveSeoAndUrlFormButton);
    await this.elementNotVisible(page, this.accentedUrlToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable attributes in product meta title
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Tue if we need to enable attributes in product meta title status
   * @return {Promise<string>}
   */
  async setStatusAttributesInProductMetaTitle(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.displayAttributesToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveSeoOptionsFormButton);
    await this.elementNotVisible(page, this.displayAttributesToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new SeoAndUrls();
