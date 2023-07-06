require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Taxes page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Taxes extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on taxes page
   */
  constructor() {
    super();

    this.pageTitle = 'Taxes •';
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
    this.taxesFilterColumnInput = filterBy => `${this.taxesGridTable} #tax_${filterBy}`;
    this.resetFilterButton = `${this.taxesGridTable} .grid-reset-button`;
    this.searchFilterButton = `${this.taxesGridTable} .grid-search-button`;
    this.taxesGridRow = row => `${this.taxesGridTable} tbody tr:nth-child(${row})`;
    this.taxesGridColumn = (row, column) => `${this.taxesGridRow(row)} td.column-${column}`;
    this.taxesGridStatusColumn = row => `${this.taxesGridColumn(row, 'active')} .ps-switch`;
    this.taxesGridStatusColumnToggleInput = row => `${this.taxesGridStatusColumn(row)} input`;
    this.taxesGridActionsColumn = row => this.taxesGridColumn(row, 'actions');
    this.taxesGridColumnEditLink = row => `${this.taxesGridActionsColumn(row)} a.grid-edit-row-link`;
    this.taxesGridColumnToggleDropDown = row => `${this.taxesGridActionsColumn(row)} a[data-toggle='dropdown']`;
    this.taxesGridDeleteLink = row => `${this.taxesGridActionsColumn(row)} a.grid-delete-row-link`;

    // Form Taxes Options
    this.taxStatusToggleInput = toggle => `#form_enable_tax_${toggle}`;
    this.displayTaxInCartToggleInput = toggle => `#form_display_tax_in_cart_${toggle}`;
    this.taxAddressTypeSelect = '#form_tax_address_type';
    this.useEcoTaxToggleInput = toggle => `#form_use_eco_tax_${toggle}`;
    this.ecoTaxSelect = '#form_eco_tax_rule_group';
    this.saveTaxOptionButton = '#form-tax-options-save-button';

    // Sort Selectors
    this.tableHead = `${this.taxesGridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

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
  async resetFilter(page) {
    if (await this.elementVisible(page, this.resetFilterButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.resetFilterButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
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
  async filterTaxes(page, filterType, filterBy, value = '') {
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
    await this.clickAndWaitForNavigation(page, this.searchFilterButton);
  }


  /**
   * Get toggle column value for a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page, row) {
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
  async setStatus(page, row, valueWanted = true) {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.taxesGridStatusColumn(row));
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
  async getTextColumnFromTableTaxes(page, row, column) {
    return this.getTextContent(page, this.taxesGridColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text value
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

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
  async goToAddNewTaxPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTaxLink);
  }

  /**
   * Go to Edit tax page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditTaxPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.taxesGridColumnEditLink(row));
  }

  /**
   * Delete Tax
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteTax(page, row) {
    // Add listener to dialog to accept deletion
    this.dialogListener(page, true);
    // Click on dropDown
    await Promise.all([
      page.click(this.taxesGridColumnToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.taxesGridColumnToggleDropDown(row)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.taxesGridDeleteLink(row)),
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
  async bulkSetStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await this.clickAndWaitForNavigation(page, enable ? this.enableSelectionButton : this.disableSelectionButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all Taxes with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteTaxesBulkActions(page) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteSelectionButton),
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
  async confirmDeleteTaxes(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Update Tax Options
   * @param page {Page} Browser tab
   * @param taxOptionData {{enabled: boolean, displayInShoppingCart: boolean, basedOn: string,
   * useEcoTax: boolean, ecoTax: ?string}} Data to set on new/edit tax option
   * @returns {Promise<string>}
   */
  async updateTaxOption(page, taxOptionData) {
    await this.setChecked(page, this.taxStatusToggleInput(taxOptionData.enabled ? 1 : 0));
    if (taxOptionData.enabled) {
      await this.setChecked(page, this.displayTaxInCartToggleInput(taxOptionData.displayInShoppingCart ? 1 : 0));
    }

    await this.selectByVisibleText(page, this.taxAddressTypeSelect, taxOptionData.basedOn);

    await this.setChecked(page, this.useEcoTaxToggleInput(taxOptionData.useEcoTax ? 1 : 0));

    if (taxOptionData.useEcoTax && taxOptionData.ecoTax !== undefined) {
      await this.selectByVisibleText(page, this.ecoTaxSelect, taxOptionData.ecoTax);
    }

    // Click on save tax Option
    await this.clickAndWaitForNavigation(page, this.saveTaxOptionButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable eco tax
   * @param page {Page} Browser tab
   * @param enableEcoTax {boolean} True if we need to enable ecoTax
   * @returns {Promise<string>}
   */
  async enableEcoTax(page, enableEcoTax = true) {
    await this.setChecked(page, this.useEcoTaxToggleInput(enableEcoTax ? 1 : 0));

    // Click on save tax Option
    await this.clickAndWaitForNavigation(page, this.saveTaxOptionButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to Tax Rules page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToTaxRulesPage(page) {
    await this.clickAndWaitForNavigation(page, this.taxRulesSubTab);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
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
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value to select on pagination limit
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

module.exports = new Taxes();
