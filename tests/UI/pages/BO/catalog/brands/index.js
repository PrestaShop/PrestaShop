require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Brands extends BOBasePage {
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
   * Go to Tab Suppliers
   * @param page
   * @return {Promise<void>}
   */
  async goToSubTabSuppliers(page) {
    await this.clickAndWaitForNavigation(page, this.suppliersNavItemLink);
  }

  /**
   * Reset filters in table
   * @param page
   * @param table, what table to reset
   * @return {Promise<void>}
   */
  async resetFilter(page, table) {
    if (await this.elementVisible(page, this.filterResetButton(table), 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(table));
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @param table
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page, table) {
    return this.getNumberFromText(page, this.gridHeaderTitle(table));
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @param table, what table to reset
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, table) {
    await this.resetFilter(page, table);
    return this.getNumberOfElementInGrid(page, table);
  }

  /**
   * Filter Table
   * @param page
   * @param table, table to filter
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, table, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(table, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(table, filterBy), value);
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(table));
  }

  /**
   * Filter Brands
   * @param page
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterBrands(page, filterType, filterBy, value = '') {
    await this.filterTable(page, 'manufacturer', filterType, filterBy, value);
  }

  /**
   * Filter Brands column active
   * @param page
   * @param value
   * @return {Promise<void>}
   */
  async filterBrandsEnabled(page, value) {
    await this.filterTable(page, 'manufacturer', 'select', 'active', value ? 'Yes' : 'No');
  }

  /**
   * Filter Addresses
   * @param page
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterAddresses(page, filterType, filterBy, value = '') {
    await this.filterTable(page, 'manufacturer_address', filterType, filterBy, value);
  }

  /**
   * Get brand status
   * @param page
   * @param row
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
   * @param page
   * @param row
   * @param valueWanted
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
   * Go to New Brand Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewBrandPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBrandLink);
  }

  /**
   * Go to new Brand Address Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewBrandAddressPage(page) {
    await this.clickAndWaitForNavigation(page, this.newBrandAddressLink);
  }

  /**
   * View Brand
   * @param page
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async viewBrand(page, row = '1') {
    await this.clickAndWaitForNavigation(page, this.viewBrandLink(row));
  }

  async goToEditBrandPage(page, row = '1') {
    await Promise.all([
      page.click(this.dropdownToggleButton('manufacturer', row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton('manufacturer', row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.editBrandLink(row));
  }

  /**
   * Go to edit brand address page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async goToEditBrandAddressPage(page, row = '1') {
    await this.clickAndWaitForNavigation(page, this.editBrandAddressLink(row));
  }

  /**
   * Delete Row in table
   * @param page
   * @param table, brand or address
   * @param row, row to delete
   * @return {Promise<string>}
   */
  async deleteRowInTable(page, table, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton(table, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(table, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(table, row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(table)}.show`),
    ]);
    await this.confirmDelete(page, table);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with modal
   * @param page
   * @param table, brand or address
   * @return {Promise<void>}
   */
  async confirmDelete(page, table) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton(table));
  }

  /**
   * Delete Brand
   * @param page
   * @param row, row to delete
   * @return {Promise<string>}
   */
  async deleteBrand(page, row = '1') {
    return this.deleteRowInTable(page, 'manufacturer', row);
  }

  /**
   * Delete Brand Address
   * @param page
   * @param row, row to delete
   * @return {Promise<string>}
   */
  async deleteBrandAddress(page, row = '1') {
    return this.deleteRowInTable(page, 'manufacturer_address', row);
  }

  /**
   * Enable / disable brands by Bulk Actions
   * @param page
   * @param enable
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
   * @param page
   * @param table, in which table
   * @return {Promise<string>}
   */
  async deleteWithBulkActions(page, table) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(table)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    if (table === 'manufacturer') {
      page.click(this.deleteBrandsButton);
      await this.waitForVisibleSelector(page, `${this.confirmDeleteModal(table)}.show`);
    } else if (table === 'manufacturer_address') {
      page.click(this.deleteAddressesButton);
      await this.waitForVisibleSelector(page, `${this.confirmDeleteModal('manufacturer_address')}.show`);
    }
    await this.confirmDelete(page, table);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get text from a column
   * @param page
   * @param table, manufacturer or address
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page, table, row, column) {
    return this.getTextContent(page, this.tableColumn(table, row, column));
  }

  /**
   * Get text from a column from table brand
   * @param page
   * @param row
   * @param column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableBrands(page, row, column) {
    return this.getTextColumnFromTable(page, 'manufacturer', row, column);
  }

  /**
   * Get logo link from brands table row
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async getLogoLinkFromBrandsTable(page, row) {
    return this.getAttributeContent(page, this.brandsTableColumnLogoImg(row), 'src');
  }

  /**
   * Get all information from categories table
   * @param page
   * @param row
   * @return {Promise<{addresses: string, name: string, logo: string, id: string, products: string, status: string}>}
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
   * @param page
   * @param row
   * @param column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextColumnFromTable(page, 'manufacturer_address', row, column);
  }

  /**
   * Get content from all rows
   * @param page
   * @param table
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, table, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, table);
    const allRowsContentTable = [];
    let rowContent;

    for (let i = 1; i <= rowsNumber; i++) {
      switch (table) {
        case 'manufacturer':
          rowContent = await this.getTextColumnFromTableBrands(page, i, column);
          break;
        case 'manufacturer_address':
          rowContent = await this.getTextColumnFromTableAddresses(page, i, column);
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
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentBrandsTable(page, column) {
    return this.getAllRowsColumnContent(page, 'manufacturer', column);
  }

  /**
   * Get content from all rows table addresses
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContentAddressesTable(page, column) {
    return this.getAllRowsColumnContent(page, 'manufacturer_address', column);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param table, table to sort
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, table, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(table, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(table, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Sort table brands
   * @param page
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableBrands(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'manufacturer', sortBy, sortDirection);
  }

  /**
   * Sort table addresses
   * @param page
   * @param sortBy
   * @param sortDirection
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
   * @param page
   * @return {Promise<*>}
   */
  async exportBrandsDataToCsv(page) {
    return this.exportDataToCsv(page, 'manufacturer');
  }

  /**
   * Get category from table in csv format
   * @param page
   * @param row
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
   * @param page
   * @param table
   * @return {Promise<string>}
   */
  getPaginationLabel(page, table) {
    return this.getTextContent(page, this.paginationLabel(table));
  }

  /**
   * Select pagination limit
   * @param page
   * @param table
   * @param number
   * @return {Promise<string>}
   */
  async selectPaginationLimit(page, table, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page, table);
  }

  /**
   * Click on next
   * @param page
   * @param table
   * @return {Promise<string>}
   */
  async paginationNext(page, table) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink(table));
    return this.getPaginationLabel(page, table);
  }

  /**
   * Click on previous
   * @param page
   * @param table
   * @return {Promise<string>}
   */
  async paginationPrevious(page, table) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink(table));
    return this.getPaginationLabel(page, table);
  }
}

module.exports = new Brands();
