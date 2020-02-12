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
    this.gridPanel = '#%TABLE_grid_panel';
    this.gridTable = '#%TABLE_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.confirmDeleteModal = '#%TABLE_grid_confirm_modal';
    this.confirmDeleteButton = 'button.btn-confirm-submit';
    // Filters
    this.filterColumn = `${this.gridTable} #%TABLE_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='%TABLE[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='%TABLE[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;

    // Brands list Selectors
    this.brandsTableEnableColumn = `${this.tableColumn
      .replace('%TABLE', 'manufacturer').replace('%COLUMN', 'active')}`;
    this.brandsEnableColumnValidIcon = `${this.brandsTableEnableColumn} i.grid-toggler-icon-valid`;
    this.brandsEnableColumnNotValidIcon = `${this.brandsTableEnableColumn} i.grid-toggler-icon-not-valid`;
    this.viewBrandLink = `${this.actionsColumn} a[data-original-title='View']`
      .replace('%TABLE', 'manufacturer');
    this.editBrandLink = `${this.dropdownToggleMenu} a[href*='/edit']`.replace('%TABLE', 'manufacturer');
    this.bulkActionsEnableButton = `${this.gridPanel} #manufacturer_grid_bulk_action_enable_selection`
      .replace('%TABLE', 'manufacturer');
    this.bulkActionsDisableButton = `${this.gridPanel} #manufacturer_grid_bulk_action_disable_selection`
      .replace('%TABLE', 'manufacturer');
    this.deleteBrandsButton = `${this.gridPanel} #manufacturer_grid_bulk_action_delete_selection`
      .replace('%TABLE', 'manufacturer');

    // Brand Addresses Selectors
    this.editBrandAddressLink = `${this.actionsColumn} a[data-original-title='Edit']`
      .replace('%TABLE', 'manufacturer_address');
    this.deleteAddressesButton = `${this.gridPanel} #manufacturer_address_grid_bulk_action_delete_selection`
      .replace('%TABLE', 'manufacturer_address');
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
    const resetButton = await this.replaceAll(this.filterResetButton, '%TABLE', table);
    if (await this.elementVisible(resetButton, 2000)) {
      await this.clickAndWaitForNavigation(resetButton);
    }
  }

  /**
   * get number of elements in grid
   * @param table
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(table) {
    return this.getNumberFromText(this.gridHeaderTitle.replace('%TABLE', table));
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
    const filterColumn = await this.replaceAll(this.filterColumn, '%TABLE', table);
    const searchButton = await this.replaceAll(this.filterSearchButton, '%TABLE', table);
    switch (filterType) {
      case 'input':
        await this.setValue(filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(searchButton);
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
    return this.elementVisible(
      this.brandsEnableColumnValidIcon.replace('%ROW', row), 100);
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.brandsTableEnableColumn.replace('%ROW', row));
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
    await this.clickAndWaitForNavigation(this.viewBrandLink.replace('%ROW', row));
  }

  async goToEditBrandPage(row = '1') {
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%TABLE', 'manufacturer').replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`
          .replace('%TABLE', 'manufacturer').replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.editBrandLink.replace('%ROW', row));
  }

  /**
   *
   * @param row
   * @return {Promise<void>}
   */
  async goToEditBrandAddressPage(row = '1') {
    await this.clickAndWaitForNavigation(this.editBrandAddressLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param table, brand or address
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteRowInTable(table, row = '1') {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%TABLE', table).replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`
          .replace('%TABLE', table).replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%TABLE', table).replace('%ROW', row));
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
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
      this.page.click(this.selectAllRowsLabel.replace('%TABLE', 'manufacturer')),
      this.page.waitForSelector(
        `${this.bulkActionsToggleButton}:not([disabled])`.replace('%TABLE', 'manufacturer'),
        {visible: true},
      ),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton.replace('%TABLE', 'manufacturer')),
      this.page.waitForSelector(
        `${this.bulkActionsToggleButton}[aria-expanded='true']`.replace('%TABLE', 'manufacturer'),
        {visible: true},
      ),
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
      this.page.click(this.selectAllRowsLabel.replace('%TABLE', table)),
      this.page.waitForSelector(
        `${this.selectAllRowsLabel}:not([disabled])`.replace('%TABLE', table),
        {visible: true},
      ),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton.replace('%TABLE', table)),
      this.page.waitForSelector(
        `${this.bulkActionsToggleButton}[aria-expanded='true']`.replace('%TABLE', table),
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    if (table === 'manufacturer') {
      this.page.click(this.deleteBrandsButton);
      await this.page.waitForSelector(
        `${this.confirmDeleteModal.replace('%TABLE', 'manufacturer')}.show`,
        {visible: true},
      );
    } else if (table === 'manufacturer_address') {
      this.page.click(this.deleteAddressesButton);
      await this.page.waitForSelector(
        `${this.confirmDeleteModal.replace('%TABLE', 'manufacturer_address')}.show`,
        {visible: true},
      );
    }
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
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
    return this.getTextContent(
      this.tableColumn
        .replace('%TABLE', table)
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
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
   * get text from a column from table addresses
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextColumnFromTable('manufacturer_address', row, column);
  }
};
