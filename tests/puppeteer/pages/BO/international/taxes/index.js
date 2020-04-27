require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Taxes extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.confirmDeleteModal = '#tax_grid_confirm_modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Filters
    this.taxesFilterColumnInput = `${this.taxesGridTable} #tax_%FILTERBY`;
    this.resetFilterButton = `${this.taxesGridTable} button[name='tax[actions][reset]']`;
    this.searchFilterButton = `${this.taxesGridTable} button[name='tax[actions][search]']`;
    this.taxesGridRow = `${this.taxesGridTable} tbody tr:nth-child(%ROW)`;
    this.taxesGridColumn = `${this.taxesGridRow} td.column-%COLUMN`;
    this.taxesGridColumnEditlink = `${this.taxesGridColumn} a[data-original-title='Edit']`;
    this.taxesGridColumnToggleDropDown = `${this.taxesGridColumn} a[data-toggle='dropdown']`;
    this.taxesGridDeleteLink = `${this.taxesGridColumn} a[data-url*='delete']`;
    this.toggleColumnValidIcon = `${this.taxesGridColumn} i.grid-toggler-icon-valid`;
    this.toggleColumnNotValidIcon = `${this.taxesGridColumn} i.grid-toggler-icon-not-valid`;

    // Form Taxes Options
    this.enabledTaxSwitchlabel = 'label[for=\'form_options_enable_tax_%ID\']';
    this.displayTaxInCartSwitchlabel = 'label[for=\'form_options_display_tax_in_cart_%ID\']';
    this.taxAddressTypeSelect = '#form_options_tax_address_type';
    this.useEcoTaxSwitchlabel = 'label[for=\'form_options_use_eco_tax_%ID\']';
    this.ecoTaxSelect = '#form_options_eco_tax_rule_group';
    this.saveTaxOptionButton = '.card-footer button';

    // Sort Selectors
    this.tableHead = `${this.taxesGridTable} thead`;
    this.sortColumnDiv = `${this.tableHead} div.ps-sortable-column[data-sort-col-name='%COLUMN']`;
    this.sortColumnSpanButton = `${this.sortColumnDiv} span.ps-sort`;
  }

  /*
  Methods
   */

  /**
   * Reset Filter in table
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.resetFilterButton, 2000)) {
      await this.clickAndWaitForNavigation(this.resetFilterButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter list of Taxes
   * @param filterType, input or select to choose method of filter
   * @param filterBy, colomn to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterTaxes(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.taxesFilterColumnInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          this.taxesFilterColumnInput.replace('%FILTERBY', filterBy),
          value ? 'Yes' : 'No',
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.searchFilterButton);
  }


  /**
   * Get toggle column value for a row
   * @param row
   * @param column
   * @return {Promise<string>}
   */
  async getToggleColumnValue(row, column) {
    return this.elementVisible(
      this.toggleColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column), 100);
  }

  /**
   * Update Enable column for the value wanted
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row, valueWanted = true) {
    await this.waitForVisibleSelector(
      this.taxesGridColumn.replace('%ROW', row).replace('%COLUMN', 'active'),
      2000,
    );
    if (await this.getToggleColumnValue(row, 'active') !== valueWanted) {
      await this.clickAndWaitForNavigation(this.taxesGridColumn.replace('%ROW', row).replace('%COLUMN', 'active'));
      return true;
    }
    return false;
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableTaxes(row, column) {
    return this.getTextContent(
      this.taxesGridColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableTaxes(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to add tax Page
   * @return {Promise<void>}
   */
  async goToAddNewTaxPage() {
    await this.clickAndWaitForNavigation(this.addNewTaxLink);
  }

  /**
   * Go to Edit tax page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditTaxPage(row) {
    await this.clickAndWaitForNavigation(
      this.taxesGridColumnEditlink.replace('%ROW', row).replace('%COLUMN', 'actions'),
    );
  }

  /**
   * Delete Tax
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteTax(row) {
    // Add listener to dialog to accept deletion
    this.dialogListener(true);
    // Click on dropDown
    await Promise.all([
      this.page.click(this.taxesGridColumnToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.waitForVisibleSelector(
        `${this.taxesGridColumnToggleDropDown}[aria-expanded='true']`
          .replace('%ROW', row)
          .replace('%COLUMN', 'actions'),
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.taxesGridDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions'));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable taxes by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeTaxesEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllLabel),
      this.waitForVisibleSelector(`${this.selectAllLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await this.clickAndWaitForNavigation(enable ? this.enableSelectionButton : this.disableSelectionButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Taxes with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteTaxesBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllLabel),
      this.waitForVisibleSelector(`${this.selectAllLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.deleteSelectionButton),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteTaxes();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @return {Promise<void>}
   */
  async confirmDeleteTaxes() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
  }

  /**
   * Update Tax Options
   * @param taxOptionData
   * @return {Promise<textContent>}
   */
  async updateTaxOption(taxOptionData) {
    if (taxOptionData.enabled) {
      await this.page.click(this.enabledTaxSwitchlabel.replace('%ID', '1'));
      if (taxOptionData.displayInShoppingCart) {
        await this.page.click(this.displayTaxInCartSwitchlabel.replace('%ID', '1'));
      } else {
        await this.page.click(this.displayTaxInCartSwitchlabel.replace('%ID', '0'));
      }
    } else {
      await this.page.click(this.enabledTaxSwitchlabel.replace('%ID', '0'));
    }
    await this.selectByVisibleText(this.taxAddressTypeSelect, taxOptionData.basedOn);
    if (taxOptionData.useEcoTax) {
      await this.page.click(this.useEcoTaxSwitchlabel.replace('%ID', '1'));
      if (taxOptionData.ecoTax !== undefined) {
        await this.selectByVisibleText(this.ecoTaxSelect, taxOptionData.ecoTax);
      }
    } else {
      await this.page.click(this.useEcoTaxSwitchlabel.replace('%ID', '0'));
    }
    // Click on save tax Option
    await this.clickAndWaitForNavigation(this.saveTaxOptionButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Go to Tax Rules page
   * @return {Promise<void>}
   */
  async goToTaxRulesPage() {
    await this.clickAndWaitForNavigation(this.taxRulesSubTab);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv.replace('%COLUMN', sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton.replace('%COLUMN', sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
