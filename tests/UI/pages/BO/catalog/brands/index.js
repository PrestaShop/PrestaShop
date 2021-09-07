require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Brands page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Brands extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on brands page
   */
  constructor() {
    super();

    this.pageTitle = 'Brands •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header Selectors
    this.suppliersNavItemLink = '#subtab-AdminSuppliers';
    this.newBrandLink = '#page-header-desc-configuration-add_manufacturer';
    this.newBrandAddressLink = '#page-header-desc-configuration-add_manufacturer_address';

    // Table Selectors
    this.gridPanel = table => `#${table}_grid_panel`;
    this.gridTable = table => `#${table}_grid_table`;
    this.gridHeaderTitle = table => `${this.gridPanel(table)} h3.card-header-title`;

    // Bulk Actions
    this.selectAllRowsLabel = table => `${this.gridPanel(table)} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = table => `${this.gridPanel(table)} button.js-bulk-actions-btn`;
    this.confirmDeleteModal = table => `#${table}_grid_confirm_modal`;
    this.confirmDeleteButton = 'button.btn-confirm-submit';

    // Filters
    this.filterColumn = (table, filterBy) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = table => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = table => `${this.gridTable(table)} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = table => `${this.gridTable(table)} tbody`;
    this.tableRow = (table, row) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableColumn = (table, row, column) => `${this.tableRow(table, row)} td.column-${column}`;

    // Actions buttons in Row
    this.actionsColumn = (table, row) => `${this.tableRow(table, row)} td.column-actions`;
    this.dropdownToggleButton = (table, row) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table, row) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table, row) => `${this.dropdownToggleMenu(table, row)} a.grid-delete-row-link`;

    // Sort Selectors
    this.tableHead = table => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table, column) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table, column) => `${this.sortColumnDiv(table, column)} span.ps-sort`;

    // Grid Actions
    this.gridActionButton = table => `#${table}-grid-actions-button`;
    this.gridActionDropDownMenu = table => `#${table}-grid-actions-dropdown-menu`;
    this.gridActionExportLink = table => `#${table}-grid-action-export`;

    // Delete modal
    this.confirmDeleteModal = table => `#${table}-grid-confirm-modal`;
    this.confirmDeleteButton = table => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;

    // Brands list Selectors
    this.brandsTableColumnLogoImg = row => `${this.tableColumn('manufacturer', row, 'logo')} img`;
    this.brandsTableColumnStatus = row => `${this.tableColumn('manufacturer', row, 'active')} .ps-switch`;
    this.brandsTableColumnStatusToggleInput = row => `${this.brandsTableColumnStatus(row)} input`;
    this.viewBrandLink = row => `${this.actionsColumn('manufacturer', row)} a.grid-view-row-link`;
    this.editBrandLink = row => `${this.dropdownToggleMenu('manufacturer', row)} a.grid-edit-row-link`;
    this.bulkActionsEnableButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel('manufacturer')
    } #manufacturer_grid_bulk_action_disable_selection`;
    this.deleteBrandsButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_delete_selection`;

    // Brand Addresses Selectors
    this.editBrandAddressLink = row => `${this.actionsColumn('manufacturer_address', row)
    } a.grid-edit-row-link`;
    this.deleteAddressesButton = `${this.gridPanel('manufacturer_address')
    } #manufacturer_address_grid_bulk_action_delete_selection`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = table => `${this.gridPanel(table)} .col-form-label`;
    this.paginationNextLink = table => `${this.gridPanel(table)} #pagination_next_url`;
    this.paginationPreviousLink = table => `${this.gridPanel(table)} [aria-label='Previous']`;
  }

  /*
  Methods
   */

  /**
   * Go to sub tab Suppliers
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabSuppliers(page) {
    await this.clickAndWaitForNavigation(page, this.suppliersNavItemLink);
  }

  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset
   * @return {Promise<void>}
   */
  async resetFilter(page, tableName) {
    if (await this.elementVisible(page, this.filterResetButton(tableName), 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(tableName));
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of element
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page, tableName) {
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Reset Filter and get number of elements in list
   * @param page {Page} Browser tab
   * @param tableName {string} tableName name to reset
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, tableName) {
    await this.resetFilter(page, tableName);
    return this.getNumberOfElementInGrid(page, tableName);
  }

  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to filter
   * @param filterType {string} Type of filter (input/Select)
   * @param filterBy {string} Column name to filter by
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, tableName, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value);
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(tableName));
  }

  /**
   * Filter Brands
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/Select)
   * @param filterBy {string} Column name to filter by
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterBrands(page, filterType, filterBy, value = '') {
    await this.filterTable(page, 'manufacturer', filterType, filterBy, value);
  }

  /**
   * Filter Brands column active
   * @param page {Page} Browser tab
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterBrandsEnabled(page, value) {
    await this.filterTable(page, 'manufacturer', 'select', 'active', value ? 'Yes' : 'No');
  }

  /**
   * Filter Addresses
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/Select)
   * @param filterBy {string} Column name to filter by
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterAddresses(page, filterType, filterBy, value = '') {
    await this.filterTable(page, 'manufacturer_address', filterType, filterBy, value);
  }

  /**
   * Get brand status
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get status
   * @return {Promise<boolean>}
   */
  async getBrandStatus(page, row) {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.brandsTableColumnStatusToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param page {Page} Browser tab
   * @param row {number} Row in table to update status
   * @param valueWanted {boolean} Status to set for the brand
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setBrandStatus(page, row, valueWanted = true) {
    if (await this.getBrandStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.brandsTableColumnStatus(row));
      return true;
    }

    return false;
  }

  /**
   * Go to new Brand Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewBrandPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBrandLink);
  }

  /**
   * Go to new Brand Address Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewBrandAddressPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBrandAddressLink);
  }

  /**
   * View Brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to view
   * @return {Promise<void>}
   */
  async viewBrand(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.viewBrandLink(row));
  }

  /**
   * Go to edit Brand page
   * @param page {Page} Browser tab
   * @param row {number} Row in table to edit
   * @returns {Promise<void>}
   */
  async goToEditBrandPage(page, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton('manufacturer', row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton('manufacturer', row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.editBrandLink(row));
  }

  /**
   * Go to edit brand address page
   * @param page {Page} Browser tab
   * @param row {number} Row in table to edit
   * @return {Promise<void>}
   */
  async goToEditBrandAddressPage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.editBrandAddressLink(row));
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete row from it
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteRowInTable(page, tableName, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(tableName, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(tableName, row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.confirmDelete(page, tableName);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with modal
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to confirm deletion
   * @return {Promise<void>}
   */
  async confirmDelete(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton(tableName));
  }

  /**
   * Delete Brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteBrand(page, row = 1) {
    return this.deleteRowInTable(page, 'manufacturer', row);
  }

  /**
   * Delete Brand Address
   * @param page {Page} Browser tab
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteBrandAddress(page, row = 1) {
    return this.deleteRowInTable(page, 'manufacturer_address', row);
  }

  /**
   * Enable/disable brands by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} Status to select in bulk actions
   * @return {Promise<string>}
   */
  async bulkSetBrandsStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel('manufacturer'), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton('manufacturer')}:not([disabled])`, 40000),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton('manufacturer')),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton('manufacturer')}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete with bulk actions
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk delete
   * @return {Promise<string>}
   */
  async deleteWithBulkActions(page, tableName) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(tableName), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    if (tableName === 'manufacturer') {
      await page.click(this.deleteBrandsButton);
      await this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`);
    } else if (tableName === 'manufacturer_address') {
      await page.click(this.deleteAddressesButton);
      await this.waitForVisibleSelector(page, `${this.confirmDeleteModal('manufacturer_address')}.show`);
    }
    await this.confirmDelete(page, tableName);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text column from it
   * @param row {number} Row in table to get text column
   * @param column {string} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page, tableName, row, column) {
    return this.getTextContent(page, this.tableColumn(tableName, row, column));
  }

  /**
   * Get text from a column from table brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get logo link
   * @param column {string} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTableBrands(page, row, column) {
    return this.getTextColumnFromTable(page, 'manufacturer', row, column);
  }

  /**
   * Get logo link from brands table row
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get logo link
   * @return {Promise<string>}
   */
  async getLogoLinkFromBrandsTable(page, row) {
    return this.getAttributeContent(page, this.brandsTableColumnLogoImg(row), 'src');
  }

  /**
   * Get all information from brands table
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @return {Promise<object>}
   */
  async getBrandFromTable(page, row) {
    return {
      id: await this.getTextColumnFromTableBrands(page, row, 'id_manufacturer'),
      logo: await this.getLogoLinkFromBrandsTable(page, row),
      name: await this.getTextColumnFromTableBrands(page, row, 'name'),
      addresses: await this.getTextColumnFromTableBrands(page, row, 'addresses_count'),
      products: await this.getTextColumnFromTableBrands(page, row, 'products_count'),
      status: await this.getBrandStatus(page, row),
    };
  }

  /**
   * Get text from a column from table addresses
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @param column {string} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextColumnFromTable(page, 'manufacturer_address', row, column);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows content
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, tableName, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable = [];
    let rowContent;

    for (let i = 1; i <= rowsNumber; i++) {
      switch (tableName) {
        case 'manufacturer':
          rowContent = await this.getTextColumnFromTableBrands(page, i, column);
          break;
        case 'manufacturer_address':
          rowContent = await this.getTextColumnFromTableAddresses(page, i, column);
          break;
        default:
        // Nothing to do
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get content from all rows table brands
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentBrandsTable(page, column) {
    return this.getAllRowsColumnContent(page, 'manufacturer', column);
  }

  /**
   * Get content from all rows table addresses
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentAddressesTable(page, column) {
    return this.getAllRowsColumnContent(page, 'manufacturer_address', column);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction (asc/desc)
   * @return {Promise<void>}
   */
  async sortTable(page, tableName, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Sort table brands
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction (asc/desc)
   * @return {Promise<void>}
   */
  async sortTableBrands(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'manufacturer', sortBy, sortDirection);
  }

  /**
   * Sort table addresses
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction (asc/desc)
   * @return {Promise<void>}
   */
  async sortTableAddresses(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'manufacturer_address', sortBy, sortDirection);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @param page {Page} Browser tab
   * @param table {string} Which table to export
   * @return {Promise<string>}
   */
  async exportDataToCsv(page, table) {
    await Promise.all([
      page.click(this.gridActionButton(table)),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu(table)}.show`),
    ]);

    return this.clickAndWaitForDownload(page, this.gridActionExportLink(table));
  }

  /**
   * Export brands data to csv file
   * @param page {Page} Browser tab
   * @return {Promise<*>}
   */
  async exportBrandsDataToCsv(page) {
    return this.exportDataToCsv(page, 'manufacturer');
  }

  /**
   * Get category from table in csv format
   * @param page {Page} Browser tab
   * @param row {number} Row on table to get on csv file
   * @return {Promise<string>}
   */
  async getBrandInCsvFormat(page, row) {
    const brand = await this.getBrandFromTable(page, row);

    return `${brand.id};`
      + `${brand.logo};`
      + `"${brand.name}";`
      + `${brand.addresses};`
      + `${brand.products};`
      + `${brand.status ? 1 : 0}`;
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page, tableName) {
    return this.getTextContent(page, this.paginationLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination limit
   * @param number {string} Pagination limit per page to choose
   * @return {Promise<string>}
   */
  async selectPaginationLimit(page, tableName, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select next pagination
   * @return {Promise<string>}
   */
  async paginationNext(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select previous pagination
   * @return {Promise<string>}
   */
  async paginationPrevious(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }
}

module.exports = new Brands();
