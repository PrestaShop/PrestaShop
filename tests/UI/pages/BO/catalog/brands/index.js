require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Brands extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.selectAllRowsLabel = table => `${this.gridPanel(table)} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = table => `${this.gridPanel(table)} button.js-bulk-actions-btn`;
    this.confirmDeleteModal = table => `#${table}_grid_confirm_modal`;
    this.confirmDeleteButton = 'button.btn-confirm-submit';
    // Filters
    this.filterColumn = (table, filterBy) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = table => `${this.gridTable(table)} button[name='${table}[actions][search]']`;
    this.filterResetButton = table => `${this.gridTable(table)} button[name='${table}[actions][reset]']`;
    // Table rows and columns
    this.tableBody = table => `${this.gridTable(table)} tbody`;
    this.tableRow = (table, row) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableColumn = (table, row, column) => `${this.tableRow(table, row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = (table, row) => `${this.tableRow(table, row)} td.column-actions`;
    this.dropdownToggleButton = (table, row) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table, row) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table, row) => `${this.dropdownToggleMenu(table, row)} a[data-url*='/delete']`;
    // Sort Selectors
    this.tableHead = table => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table, column) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table, column) => `${this.sortColumnDiv(table, column)} span.ps-sort`;

    // Grid Actions
    this.gridActionButton = table => `#${table}-grid-actions-button`;
    this.gridActionDropDownMenu = table => `div.dropdown-menu[aria-labelledby='${table}-grid-actions-button']`;
    this.gridActionExportLink = table => `${this.gridActionDropDownMenu(table)} a[href*='/export']`;

    // Delete modal
    this.confirmDeleteModal = table => `#${table}-grid-confirm-modal`;
    this.confirmDeleteButton = table => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;

    // Brands list Selectors
    this.brandsTableColumnLogoImg = row => `${this.tableColumn('manufacturer', row, 'logo')} img`;
    this.brandsTableEnableColumn = row => `${this.tableColumn('manufacturer', row, 'active')}`;
    this.brandsEnableColumnValidIcon = row => `${this.brandsTableEnableColumn(row)} i.grid-toggler-icon-valid`;
    this.brandsEnableColumnNotValidIcon = row => `${this.brandsTableEnableColumn(row)} i.grid-toggler-icon-not-valid`;
    this.viewBrandLink = row => `${this.actionsColumn('manufacturer', row)} a[data-original-title='View']`;
    this.editBrandLink = row => `${this.dropdownToggleMenu('manufacturer', row)} a[href*='/edit']`;
    this.bulkActionsEnableButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel('manufacturer')
    } #manufacturer_grid_bulk_action_disable_selection`;
    this.deleteBrandsButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_delete_selection`;

    // Brand Addresses Selectors
    this.editBrandAddressLink = row => `${this.actionsColumn('manufacturer_address', row)
    } a[data-original-title='Edit']`;
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
   * Go to Tab Suppliers
   * @return {Promise<void>}
   */
  async goToSubTabSuppliers() {
    await this.clickAndWaitForNavigation(this.suppliersNavItemLink);
  }

  /**
   * Reset filters in table
   * @param table, what table to reset
   * @return {Promise<void>}
   */
  async resetFilter(table) {
    if (await this.elementVisible(this.filterResetButton(table), 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton(table));
    }
  }

  /**
   * get number of elements in grid
   * @param table
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(table) {
    return this.getNumberFromText(this.gridHeaderTitle(table));
  }

  /**
   * Reset Filter And get number of elements in list
   * @param table, what table to reset
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines(table) {
    await this.resetFilter(table);
    return this.getNumberOfElementInGrid(table);
  }

  /**
   * Filter Table
   * @param table, table to filter
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(table, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn(table, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn(table, filterBy), value);
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton(table));
  }

  /**
   * Filter Brands
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterBrands(filterType, filterBy, value = '') {
    await this.filterTable('manufacturer', filterType, filterBy, value);
  }

  /**
   * Filter Brands column active
   * @param value
   * @return {Promise<void>}
   */
  async filterBrandsEnabled(value) {
    await this.filterTable('manufacturer', 'select', 'active', value ? 'Yes' : 'No');
  }

  /**
   * Filter Addresses
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterAddresses(filterType, filterBy, value = '') {
    await this.filterTable('manufacturer_address', filterType, filterBy, value);
  }

  /**
   * Get toggle column value for a row (Brands list)
   * @param row
   * @return {Promise<string>}
   */
  async getToggleColumnValue(row) {
    return this.elementVisible(this.brandsEnableColumnValidIcon(row), 100);
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row, valueWanted = true) {
    await this.waitForVisibleSelector(this.brandsTableEnableColumn(row), 2000);
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.brandsTableEnableColumn(row));
      return true;
    }
    return false;
  }

  /**
   * Go to New Brand Page
   * @return {Promise<void>}
   */
  async goToAddNewBrandPage() {
    await this.clickAndWaitForNavigation(this.newBrandLink);
  }

  /**
   * Go to new Brand Address Page
   * @return {Promise<void>}
   */
  async goToAddNewBrandAddressPage() {
    await this.clickAndWaitForNavigation(this.newBrandAddressLink);
  }

  /**
   * View Brand
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async viewBrand(row = '1') {
    await this.clickAndWaitForNavigation(this.viewBrandLink(row));
  }

  async goToEditBrandPage(row = '1') {
    await Promise.all([
      this.page.click(this.dropdownToggleButton('manufacturer', row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton('manufacturer', row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.editBrandLink(row));
  }

  /**
   *
   * @param row
   * @return {Promise<void>}
   */
  async goToEditBrandAddressPage(row = '1') {
    await this.clickAndWaitForNavigation(this.editBrandAddressLink(row));
  }

  /**
   * Delete Row in table
   * @param table, brand or address
   * @param row, row to delete
   * @return {Promise<string>}
   */
  async deleteRowInTable(table, row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton(table, row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton(table, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.deleteRowLink(table, row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal(table)}.show`),
    ]);
    await this.confirmDelete(table);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with modal
   * @param table, brand or address
   * @return {Promise<void>}
   */
  async confirmDelete(table) {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton(table));
  }

  /**
   * Delete Brand
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteBrand(row = '1') {
    return this.deleteRowInTable('manufacturer', row);
  }

  /**
   * Delete Brand Address
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteBrandAddress(row = '1') {
    return this.deleteRowInTable('manufacturer_address', row);
  }

  /**
   * Enable / disable brands by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeBrandsEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel('manufacturer'), el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton('manufacturer')}:not([disabled])`, 40000),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton('manufacturer')),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton('manufacturer')}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete with bulk actions
   * @param table, in which table
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions(table) {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton(table)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    if (table === 'manufacturer') {
      this.page.click(this.deleteBrandsButton);
      await this.waitForVisibleSelector(`${this.confirmDeleteModal(table)}.show`);
    } else if (table === 'manufacturer_address') {
      this.page.click(this.deleteAddressesButton);
      await this.waitForVisibleSelector(`${this.confirmDeleteModal('manufacturer_address')}.show`);
    }
    await this.confirmDelete(table);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * get text from a column
   * @param table, manufacturer or address
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(table, row, column) {
    return this.getTextContent(this.tableColumn(table, row, column));
  }

  /**
   * get text from a column from table brand
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableBrands(row, column) {
    return this.getTextColumnFromTable('manufacturer', row, column);
  }

  /**
   * Get logo link from brands table row
   * @param row
   * @return {Promise<string>}
   */
  async getLogoLinkFromBrandsTable(row) {
    return this.getAttributeContent(this.brandsTableColumnLogoImg(row), 'src');
  }

  /**
   * Get all information from categories table
   * @param row
   * @return {Promise<{object}>}
   */
  async getBrandFromTable(row) {
    return {
      id: await this.getTextColumnFromTableBrands(row, 'id_manufacturer'),
      logo: await this.getLogoLinkFromBrandsTable(row),
      name: await this.getTextColumnFromTableBrands(row, 'name'),
      addresses: await this.getTextColumnFromTableBrands(row, 'addresses_count'),
      products: await this.getTextColumnFromTableBrands(row, 'products_count'),
      status: await this.getToggleColumnValue(row),
    };
  }

  /**
   * get text from a column from table addresses
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextColumnFromTable('manufacturer_address', row, column);
  }

  /**
   * Get content from all rows
   * @param table
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(table, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(table);
    const allRowsContentTable = [];
    let rowContent;
    for (let i = 1; i <= rowsNumber; i++) {
      switch (table) {
        case 'manufacturer':
          rowContent = await this.getTextColumnFromTableBrands(i, column);
          break;
        case 'manufacturer_address':
          rowContent = await this.getTextColumnFromTableAddresses(i, column);
          break;
        default:
        // Nothing to do
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Get content from all rows table brands
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentBrandsTable(column) {
    return this.getAllRowsColumnContent('manufacturer', column);
  }

  /**
   * Get content from all rows table addresses
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentAddressesTable(column) {
    return this.getAllRowsColumnContent('manufacturer_address', column);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param table, table to sort
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(table, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(table, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(table, sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  /**
   * Sort table brands
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableBrands(sortBy, sortDirection = 'asc') {
    return this.sortTable('manufacturer', sortBy, sortDirection);
  }

  /**
   * Sort table addresses
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableAddresses(sortBy, sortDirection = 'asc') {
    return this.sortTable('manufacturer_address', sortBy, sortDirection);
  }

  /**
   * Get alert text message
   * @returns {Promise<string>}
   */
  getAlertTextMessage() {
    return this.getTextContent(this.alertTextBlock);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @param table, which table to export
   * @return {Promise<*>}
   */
  async exportDataToCsv(table) {
    await Promise.all([
      this.page.click(this.gridActionButton(table)),
      this.waitForVisibleSelector(`${this.gridActionDropDownMenu(table)}.show`),
    ]);

    const [download] = await Promise.all([
      this.page.waitForEvent('download'), // wait for download to start
      this.page.click(this.gridActionExportLink(table)),
    ]);

    return download.path();
  }

  /**
   * Export brands data to csv file
   * @return {Promise<*>}
   */
  async exportBrandsDataToCsv() {
    return this.exportDataToCsv('manufacturer');
  }

  /**
   * Export brand addresses data to csv file
   * @return {Promise<*>}
   */
  async exportAddressesDataToCsv() {
    return this.exportDataToCsv('manufacturer_address');
  }

  /**
   * Get category from table in csv format
   * @param row
   * @return {Promise<string>}
   */
  async getBrandInCsvFormat(row) {
    const brand = await this.getBrandFromTable(row);
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
   * @return {Promise<string>}
   */
  getPaginationLabel(table) {
    return this.getTextContent(this.paginationLabel(table));
  }

  /**
   * Select pagination limit
   * @param table
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(table, number) {
    await this.selectByVisibleText(this.paginationLimitSelect, number);
    return this.getPaginationLabel(table);
  }

  /**
   * Click on next
   * @param table
   * @returns {Promise<string>}
   */
  async paginationNext(table) {
    await this.clickAndWaitForNavigation(this.paginationNextLink(table));
    return this.getPaginationLabel(table);
  }

  /**
   * Click on previous
   * @param table
   * @returns {Promise<string>}
   */
  async paginationPrevious(table) {
    await this.clickAndWaitForNavigation(this.paginationPreviousLink(table));
    return this.getPaginationLabel(table);
  }
};
