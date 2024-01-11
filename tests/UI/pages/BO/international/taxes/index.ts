import BOBasePage from '@pages/BO/BObasePage';

import type TaxOptionData from '@data/faker/taxOption';

import type {Page} from 'playwright';

/**
 * Taxes page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Taxes extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly taxRulesSubTab: string;

  private readonly addNewTaxLink: string;

  private readonly taxesGridPanelDiv: string;

  private readonly gridHeaderTitle: string;

  private readonly bulkActionsToggleButton: string;

  private readonly enableSelectionButton: string;

  private readonly disableSelectionButton: string;

  private readonly deleteSelectionButton: string;

  private readonly selectAllLabel: string;

  private readonly taxesGridTable: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly taxesFilterColumnInput: (filterBy: string) => string;

  private readonly resetFilterButton: string;

  private readonly searchFilterButton: string;

  private readonly taxesGridRow: (row: number) => string;

  private readonly taxesGridColumn: (row: number, column: string) => string;

  private readonly taxesGridStatusColumn: (row: number) => string;

  private readonly taxesGridStatusColumnToggleInput: (row: number) => string;

  private readonly taxesGridActionsColumn: (row: number) => string;

  private readonly taxesGridColumnEditLink: (row: number) => string;

  private readonly taxesGridColumnToggleDropDown: (row: number) => string;

  private readonly taxesGridDeleteLink: (row: number) => string;

  private readonly taxStatusToggleInput: (toggle: number) => string;

  private readonly displayTaxInCartToggleInput: (toggle: number) => string;

  private readonly taxAddressTypeSelect: string;

  private readonly useEcoTaxToggleInput: (toggle: number) => string;

  private readonly ecoTaxSelect: string;

  private readonly saveTaxOptionButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on taxes page
   */
  constructor() {
    super();

    this.pageTitle = `Taxes • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // SubTab
    this.taxRulesSubTab = '#subtab-AdminTaxRulesGroup';

    // HEADER buttons
    this.addNewTaxLink = 'a#page-header-desc-configuration-add';

    // Grid
    this.taxesGridPanelDiv = '#tax_grid_panel';
    this.gridHeaderTitle = `${this.taxesGridPanelDiv} h3.card-header-title`;

    // Bulk Actions
    this.bulkActionsToggleButton = `${this.taxesGridPanelDiv} button.js-bulk-actions-btn`;
    this.enableSelectionButton = `${this.taxesGridPanelDiv} #tax_grid_bulk_action_enable_selection`;
    this.disableSelectionButton = `${this.taxesGridPanelDiv} #tax_grid_bulk_action_disable_selection`;
    this.deleteSelectionButton = `${this.taxesGridPanelDiv} #tax_grid_bulk_action_delete_selection`;
    this.selectAllLabel = `${this.taxesGridPanelDiv} #tax_grid tr.column-filters .md-checkbox i`;
    this.taxesGridTable = `${this.taxesGridPanelDiv} #tax_grid_table`;
    this.confirmDeleteModal = '#tax-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Filters
    this.taxesFilterColumnInput = (filterBy: string) => `${this.taxesGridTable} #tax_${filterBy}`;
    this.resetFilterButton = `${this.taxesGridTable} .grid-reset-button`;
    this.searchFilterButton = `${this.taxesGridTable} .grid-search-button`;
    this.taxesGridRow = (row: number) => `${this.taxesGridTable} tbody tr:nth-child(${row})`;
    this.taxesGridColumn = (row: number, column: string) => `${this.taxesGridRow(row)} td.column-${column}`;
    this.taxesGridStatusColumn = (row: number) => `${this.taxesGridColumn(row, 'active')} .ps-switch`;
    this.taxesGridStatusColumnToggleInput = (row: number) => `${this.taxesGridStatusColumn(row)} input`;
    this.taxesGridActionsColumn = (row: number) => this.taxesGridColumn(row, 'actions');
    this.taxesGridColumnEditLink = (row: number) => `${this.taxesGridActionsColumn(row)} a.grid-edit-row-link`;
    this.taxesGridColumnToggleDropDown = (row: number) => `${this.taxesGridActionsColumn(row)} a[data-toggle='dropdown']`;
    this.taxesGridDeleteLink = (row: number) => `${this.taxesGridActionsColumn(row)} a.grid-delete-row-link`;

    // Form Taxes Options
    this.taxStatusToggleInput = (toggle: number) => `#form_enable_tax_${toggle}`;
    this.displayTaxInCartToggleInput = (toggle: number) => `#form_display_tax_in_cart_${toggle}`;
    this.taxAddressTypeSelect = '#form_tax_address_type';
    this.useEcoTaxToggleInput = (toggle: number) => `#form_use_eco_tax_${toggle}`;
    this.ecoTaxSelect = '#form_eco_tax_rule_group';
    this.saveTaxOptionButton = '#form-tax-options-save-button';

    // Sort Selectors
    this.tableHead = `${this.taxesGridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.taxesGridPanelDiv} .col-form-label`;
    this.paginationNextLink = `${this.taxesGridPanelDiv} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.taxesGridPanelDiv} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */

  /**
   * Reset Filter in table
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.resetFilterButton, 2000)) {
      await page.locator(this.resetFilterButton).click();
      await this.elementNotVisible(page, this.resetFilterButton, 2000);
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
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page) : Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of Taxes
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterTaxes(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.taxesFilterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.taxesFilterColumnInput(filterBy), value === '1' ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.searchFilterButton);
  }

  /**
   * Get toggle column value for a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.taxesGridStatusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Update Enable column for the value wanted
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.locator(this.taxesGridStatusColumn(row)).click();
      return true;
    }

    return false;
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableTaxes(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.taxesGridColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text value
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber: number = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableTaxes(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to add tax Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewTaxPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewTaxLink);
  }

  /**
   * Go to Edit tax page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditTaxPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.taxesGridColumnEditLink(row));
  }

  /**
   * Delete Tax
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteTax(page: Page, row: number): Promise<string> {
    // Add listener to dialog to accept deletion
    await this.dialogListener(page, true);
    // Click on dropDown
    await Promise.all([
      page.locator(this.taxesGridColumnToggleDropDown(row)).click(),
      this.waitForVisibleSelector(page, `${this.taxesGridColumnToggleDropDown(row)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.taxesGridDeleteLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteTaxes(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable / disable taxes by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await page.locator(enable ? this.enableSelectionButton : this.disableSelectionButton).click();
    await this.elementNotVisible(page, enable ? this.enableSelectionButton : this.disableSelectionButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all Taxes with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteTaxesBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteSelectionButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteTaxes(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteTaxes(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /**
   * Update Tax Options
   * @param page {Page} Browser tab
   * @param taxOptionData {TaxOptionData} Data to set on new/edit tax option
   * @returns {Promise<string>}
   */
  async updateTaxOption(page: Page, taxOptionData: TaxOptionData): Promise<string> {
    await this.setChecked(page, this.taxStatusToggleInput(taxOptionData.enabled ? 1 : 0));
    if (taxOptionData.enabled) {
      await this.setChecked(page, this.displayTaxInCartToggleInput(taxOptionData.displayInShoppingCart ? 1 : 0));
    }

    await this.selectByVisibleText(page, this.taxAddressTypeSelect, taxOptionData.basedOn);

    await this.setChecked(page, this.useEcoTaxToggleInput(taxOptionData.useEcoTax ? 1 : 0));

    if (taxOptionData.useEcoTax && taxOptionData.ecoTax !== null) {
      await this.selectByVisibleText(page, this.ecoTaxSelect, taxOptionData.ecoTax);
    }

    // Click on save tax Option
    await page.locator(this.saveTaxOptionButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable eco tax
   * @param page {Page} Browser tab
   * @param enableEcoTax {boolean} True if we need to enable ecoTax
   * @returns {Promise<string>}
   */
  async enableEcoTax(page: Page, enableEcoTax: boolean = true): Promise<string> {
    await this.setChecked(page, this.useEcoTaxToggleInput(enableEcoTax ? 1 : 0));

    // Click on save tax Option
    await page.locator(this.saveTaxOptionButton).click();
    await this.elementNotVisible(page, this.useEcoTaxToggleInput(!enableEcoTax ? 1 : 0));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to Tax Rules page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToTaxRulesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.taxRulesSubTab);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
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
   * @param number {number} Value to select on pagination limit
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

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

export default new Taxes();
