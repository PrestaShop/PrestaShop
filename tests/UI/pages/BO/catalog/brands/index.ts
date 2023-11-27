import BOBasePage from '@pages/BO/BObasePage';

import BrandData from '@data/faker/brand';

import type {Page} from 'playwright';

/**
 * Brands page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Brands extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly suppliersNavItemLink: string;

  private readonly newBrandLink: string;

  private readonly newBrandAddressLink: string;

  private readonly gridPanel: (table: string) => string;

  private readonly gridTable: (table: string) => string;

  private readonly gridHeaderTitle: (table: string) => string;

  private readonly selectAllRowsLabel: (table: string) => string;

  private readonly bulkActionsToggleButton: (table: string) => string;

  private readonly confirmDeleteModal: (table: string) => string;

  private readonly filterColumn: (table: string, filterBy: string) => string;

  private readonly filterSearchButton: (table: string) => string;

  private readonly filterResetButton: (table: string) => string;

  private readonly tableBody: (table: string) => string;

  private readonly tableRow: (table: string, row: number) => string;

  private readonly tableColumn: (table: string, row: number, column: string) => string;

  private readonly actionsColumn: (table: string, row: number) => string;

  private readonly dropdownToggleButton: (table: string, row: number) => string;

  private readonly dropdownToggleMenu: (table: string, row: number) => string;

  private readonly deleteRowLink: (table: string, row: number) => string;

  private readonly tableHead: (table: string) => string;

  private readonly sortColumnDiv: (table: string, column: string) => string;

  private readonly sortColumnSpanButton: (table: string, column: string) => string;

  private readonly gridActionButton: (table: string) => string;

  private readonly gridActionDropDownMenu: (table: string) => string;

  private readonly gridActionExportLink: (table: string) => string;

  private readonly confirmDeleteButton: (table: string) => string;

  private readonly brandsTableColumnLogoImg: (row: number) => string;

  private readonly brandsTableColumnStatus: (row: number) => string;

  private readonly brandsTableColumnStatusToggleInput: (row: number) => string;

  private readonly viewBrandLink: (row: number) => string;

  private readonly editBrandLink: (row: number) => string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly deleteBrandsButton: string;

  private readonly editBrandAddressLink: (row: number) => string;

  private readonly deleteAddressesButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: (table: string) => string;

  private readonly paginationNextLink: (table: string) => string;

  private readonly paginationPreviousLink: (table: string) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on brands page
   */
  constructor() {
    super();

    this.pageTitle = `Brands • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header Selectors
    this.suppliersNavItemLink = '#subtab-AdminSuppliers';
    this.newBrandLink = '#page-header-desc-configuration-add_manufacturer';
    this.newBrandAddressLink = '#page-header-desc-configuration-add_manufacturer_address';

    // Table Selectors
    this.gridPanel = (table: string) => `#${table}_grid_panel`;
    this.gridTable = (table: string) => `#${table}_grid_table`;
    this.gridHeaderTitle = (table: string) => `${this.gridPanel(table)} h3.card-header-title`;

    // Bulk Actions
    this.selectAllRowsLabel = (table: string) => `${this.gridPanel(table)} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = (table: string) => `${this.gridPanel(table)} button.js-bulk-actions-btn`;
    this.confirmDeleteModal = (table: string) => `#${table}_grid_confirm_modal`;

    // Filters
    this.filterColumn = (table: string, filterBy: string) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = (table: string) => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = (table: string) => `${this.gridTable(table)} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = (table: string) => `${this.gridTable(table)} tbody`;
    this.tableRow = (table: string, row: number) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableColumn = (table: string, row: number, column: string) => `${this.tableRow(table, row)} td.column-${column}`;

    // Actions buttons in Row
    this.actionsColumn = (table: string, row: number) => `${this.tableRow(table, row)} td.column-actions`;
    this.dropdownToggleButton = (table: string, row: number) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table: string, row: number) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table: string, row: number) => `${this.dropdownToggleMenu(table, row)} a.grid-delete-row-link`;

    // Sort Selectors
    this.tableHead = (table: string) => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table: string, column: string) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table: string, column: string) => `${this.sortColumnDiv(table, column)} span.ps-sort`;

    // Grid Actions
    this.gridActionButton = (table: string) => `#${table}-grid-actions-button`;
    this.gridActionDropDownMenu = (table: string) => `#${table}-grid-actions-dropdown-menu`;
    this.gridActionExportLink = (table: string) => `#${table}-grid-action-export`;

    // Delete modal
    this.confirmDeleteModal = (table: string) => `#${table}-grid-confirm-modal`;
    this.confirmDeleteButton = (table: string) => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;

    // Brands list Selectors
    this.brandsTableColumnLogoImg = (row: number) => `${this.tableColumn('manufacturer', row, 'logo')} img`;
    this.brandsTableColumnStatus = (row: number) => `${this.tableColumn('manufacturer', row, 'active')} .ps-switch`;
    this.brandsTableColumnStatusToggleInput = (row: number) => `${this.brandsTableColumnStatus(row)} input`;
    this.viewBrandLink = (row: number) => `${this.actionsColumn('manufacturer', row)} a.grid-view-row-link`;
    this.editBrandLink = (row: number) => `${this.dropdownToggleMenu('manufacturer', row)} a.grid-edit-row-link`;
    this.bulkActionsEnableButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel('manufacturer')
    } #manufacturer_grid_bulk_action_disable_selection`;
    this.deleteBrandsButton = `${this.gridPanel('manufacturer')} #manufacturer_grid_bulk_action_delete_selection`;

    // Brand Addresses Selectors
    this.editBrandAddressLink = (row: number) => `${this.actionsColumn('manufacturer_address', row)
    } a.grid-edit-row-link`;
    this.deleteAddressesButton = `${this.gridPanel('manufacturer_address')
    } #manufacturer_address_grid_bulk_action_delete_selection`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = (table: string) => `${this.gridPanel(table)} .col-form-label`;
    this.paginationNextLink = (table: string) => `${this.gridPanel(table)} [data-role=next-page-link]`;
    this.paginationPreviousLink = (table: string) => `${this.gridPanel(table)} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */

  /**
   * Go to sub tab Suppliers
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabSuppliers(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.suppliersNavItemLink);
  }

  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset
   * @return {Promise<void>}
   */
  async resetFilter(page: Page, tableName: string): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton(tableName), 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton(tableName));
      await this.elementNotVisible(page, this.filterResetButton(tableName), 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of element
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page, tableName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Reset Filter and get number of elements in list
   * @param page {Page} Browser tab
   * @param tableName {string} tableName name to reset
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page, tableName: string): Promise<number> {
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
  async filterTable(page: Page, tableName: string, filterType: string, filterBy: string, value: string = ''): Promise<void> {
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
    await this.clickAndWaitForLoadState(page, this.filterSearchButton(tableName));
  }

  /**
   * Filter Brands
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/Select)
   * @param filterBy {string} Column name to filter by
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterBrands(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    await this.filterTable(page, 'manufacturer', filterType, filterBy, value);
  }

  /**
   * Filter Brands column active
   * @param page {Page} Browser tab
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterBrandsEnabled(page: Page, value: string): Promise<void> {
    await this.filterTable(page, 'manufacturer', 'select', 'active', value === '1' ? 'Yes' : 'No');
  }

  /**
   * Filter Addresses
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/Select)
   * @param filterBy {string} Column name to filter by
   * @param value {string} Value to put in filter
   * @return {Promise<void>}
   */
  async filterAddresses(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    await this.filterTable(page, 'manufacturer_address', filterType, filterBy, value);
  }

  /**
   * Get brand status
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get status
   * @return {Promise<boolean>}
   */
  async getBrandStatus(page: Page, row: number): Promise<boolean> {
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
  async setBrandStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getBrandStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForLoadState(page, this.brandsTableColumnStatus(row));
      return true;
    }

    return false;
  }

  /**
   * Go to new Brand Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewBrandPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newBrandLink);
  }

  /**
   * Go to new Brand Address Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewBrandAddressPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newBrandAddressLink);
  }

  /**
   * View Brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to view
   * @return {Promise<void>}
   */
  async viewBrand(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.viewBrandLink(row));
  }

  /**
   * Go to edit Brand page
   * @param page {Page} Browser tab
   * @param row {number} Row in table to edit
   * @returns {Promise<void>}
   */
  async goToEditBrandPage(page: Page, row: number = 1): Promise<void> {
    await Promise.all([
      page.locator(this.dropdownToggleButton('manufacturer', row)).click(),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton('manufacturer', row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForURL(page, this.editBrandLink(row));
  }

  /**
   * Go to edit brand address page
   * @param page {Page} Browser tab
   * @param row {number} Row in table to edit
   * @return {Promise<void>}
   */
  async goToEditBrandAddressPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editBrandAddressLink(row));
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete row from it
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteRowInTable(page: Page, tableName: string, row: number = 1): Promise<string> {
    await Promise.all([
      page.locator(this.dropdownToggleButton(tableName, row)).click(),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(tableName, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteRowLink(tableName, row)).click(),
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
  async confirmDelete(page: Page, tableName: string): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton(tableName));
  }

  /**
   * Delete Brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteBrand(page: Page, row: number = 1): Promise<string> {
    return this.deleteRowInTable(page, 'manufacturer', row);
  }

  /**
   * Delete Brand Address
   * @param page {Page} Browser tab
   * @param row {number} Row in table to delete
   * @return {Promise<string>}
   */
  async deleteBrandAddress(page: Page, row: number = 1): Promise<string> {
    return this.deleteRowInTable(page, 'manufacturer_address', row);
  }

  /**
   * Enable/disable brands by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} Status to select in bulk actions
   * @return {Promise<string>}
   */
  async bulkSetBrandsStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel('manufacturer')).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton('manufacturer')}:not([disabled])`, 40000),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton('manufacturer')).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton('manufacturer')}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await page.locator(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete with bulk actions
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk delete
   * @return {Promise<string>}
   */
  async deleteWithBulkActions(page: Page, tableName: string): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel(tableName)).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton(tableName)).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    if (tableName === 'manufacturer') {
      await page.locator(this.deleteBrandsButton).click();
      await this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`);
    } else if (tableName === 'manufacturer_address') {
      await page.locator(this.deleteAddressesButton).click();
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
  async getTextColumnFromTable(page: Page, tableName: string, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(tableName, row, column));
  }

  /**
   * Get text from a column from table brand
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get logo link
   * @param column {string} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTableBrands(page: Page, row: number, column: string): Promise<string> {
    return this.getTextColumnFromTable(page, 'manufacturer', row, column);
  }

  /**
   * Get logo link from brands table row
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get logo link
   * @return {Promise<string>}
   */
  async getLogoLinkFromBrandsTable(page: Page, row: number): Promise<string> {
    return this.getAttributeContent(page, this.brandsTableColumnLogoImg(row), 'src');
  }

  /**
   * Get all information from brands table
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @return {Promise<BrandData>}
   */
  async getBrandFromTable(page: Page, row: number): Promise<BrandData> {
    const adressesCount = await this.getTextColumnFromTableBrands(page, row, 'addresses_count');

    return new BrandData({
      id: parseInt(await this.getTextColumnFromTableBrands(page, row, 'id_manufacturer'), 10),
      logo: await this.getLogoLinkFromBrandsTable(page, row),
      name: await this.getTextColumnFromTableBrands(page, row, 'name'),
      addresses: parseInt(adressesCount === '--' ? '0' : adressesCount, 10),
      products: parseInt(await this.getTextColumnFromTableBrands(page, row, 'products_count'), 10),
      enabled: await this.getBrandStatus(page, row),
    });
  }

  /**
   * Get text from a column from table addresses
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @param column {string} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page: Page, row: number, column: string): Promise<string> {
    return this.getTextColumnFromTable(page, 'manufacturer_address', row, column);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows content
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, tableName: string, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable: string[] = [];
    let rowContent: string;

    for (let i: number = 1; i <= rowsNumber; i++) {
      rowContent = '';
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
      if (rowContent !== '') {
        allRowsContentTable.push(rowContent);
      }
    }

    return allRowsContentTable;
  }

  /**
   * Get content from all rows table brands
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContentBrandsTable(page: Page, column: string): Promise<string[]> {
    return this.getAllRowsColumnContent(page, 'manufacturer', column);
  }

  /**
   * Get content from all rows table addresses
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContentAddressesTable(page: Page, column: string): Promise<string[]> {
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
  async sortTable(page: Page, tableName: string, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
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
  async sortTableBrands(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    return this.sortTable(page, 'manufacturer', sortBy, sortDirection);
  }

  /**
   * Sort table addresses
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction (asc/desc)
   * @return {Promise<void>}
   */
  async sortTableAddresses(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    return this.sortTable(page, 'manufacturer_address', sortBy, sortDirection);
  }

  // Export methods
  /**
   * Click on lint to export categories to a csv file
   * @param page {Page} Browser tab
   * @param table {string} Which table to export
   * @return {Promise<string|null>}
   */
  async exportDataToCsv(page: Page, table: string): Promise<string | null> {
    await Promise.all([
      page.locator(this.gridActionButton(table)).click(),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu(table)}.show`),
    ]);

    return this.clickAndWaitForDownload(page, this.gridActionExportLink(table));
  }

  /**
   * Export brands data to csv file
   * @param page {Page} Browser tab
   * @return {Promise<string|null>}
   */
  async exportBrandsDataToCsv(page: Page): Promise<string | null> {
    return this.exportDataToCsv(page, 'manufacturer');
  }

  /**
   * Get category from table in csv format
   * @param page {Page} Browser tab
   * @param row {number} Row on table to get on csv file
   * @return {Promise<string>}
   */
  async getBrandInCsvFormat(page: Page, row: number): Promise<string> {
    const brand = await this.getBrandFromTable(page, row);

    return `${brand.id};`
      + `${brand.logo};`
      + `"${brand.name}";`
      + `${brand.addresses > 0 ? brand.addresses : '--'};`
      + `${brand.products};`
      + `${brand.enabled ? 1 : 0}`;
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page, tableName: string): Promise<string> {
    return this.getTextContent(page, this.paginationLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination limit
   * @param number {number} Pagination limit per page to choose
   * @return {Promise<string>}
   */
  async selectPaginationLimit(page: Page, tableName: string, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select next pagination
   * @return {Promise<string>}
   */
  async paginationNext(page: Page, tableName: string): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select previous pagination
   * @return {Promise<string>}
   */
  async paginationPrevious(page: Page, tableName: string): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }
}

export default new Brands();
