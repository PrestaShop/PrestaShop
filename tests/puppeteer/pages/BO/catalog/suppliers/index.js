require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Suppliers extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Suppliers • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors header
    this.brandsNavItemLink = '#subtab-AdminManufacturers';
    this.newSupplierLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#supplier_grid_panel';
    this.gridTable = '#supplier_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_disable_selection`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_delete`;
    // Filters
    this.filterColumn = `${this.gridTable} #supplier_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='supplier[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='supplier[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.viewRowLink = `${this.actionsColumn} a[data-original-title='View']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.editRowLink = `${this.dropdownToggleMenu} a[href*='/edit']`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
    // enable column
    this.enableColumn = this.tableColumn.replace('%COLUMN', 'active');
    this.enableColumnValidIcon = `${this.enableColumn} i.grid-toggler-icon-valid`;
    this.enableColumnNotValidIcon = `${this.enableColumn} i.grid-toggler-icon-not-valid`;
  }

  /*
  Methods
   */

  /* Header Methods */
  /**
   * Go to Tab Brands
   * @return {Promise<void>}
   */
  async goToSubTabBrands() {
    await this.clickAndWaitForNavigation(this.brandsNavItemLink);
  }

  /**
   * Go to New Supplier Page
   * @return {Promise<void>}
   */
  async goToAddNewSupplierPage() {
    await this.clickAndWaitForNavigation(this.newSupplierLink);
  }

  /* Column Methods */
  /**
   * View Supplier
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async viewSupplier(row = 1) {
    await this.clickAndWaitForNavigation(this.viewRowLink.replace('%ROW', row));
  }

  /**
   * Edit Supplier
   * @param row
   * @return {Promise<void>}
   */
  async goToEditSupplierPage(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteSupplier(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get toggle column value for a row
   * @param row
   * @return {Promise<string>}
   */
  async getToggleColumnValue(row = 1) {
    return this.elementVisible(
      this.enableColumnValidIcon.replace('%ROW', row),
      100,
    );
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row = 1, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.enableColumn.replace('%ROW', row));
      return true;
    }
    return false;
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Filter Supplier column active
   * @param value
   * @return {Promise<void>}
   */
  async filterSupplierEnabled(value) {
    await this.filterTable('select', 'active', value ? 'Yes' : 'No');
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeSuppliersEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete with bulk actions
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions() {
    this.dialogListener(true);
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
