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
    this.selectAllLabel = `${this.taxesGridPanelDiv} #tax_grid .md-checkbox label`;
    this.taxesGridTable = `${this.taxesGridPanelDiv} #tax_grid_table`;
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
  }

  /*
  Methods
   */

  /**
   * Reset Filter in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.resetFilterButton),
    ]);
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
        await this.selectByVisibleText(this.taxesFilterColumnInput.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.searchFilterButton),
    ]);
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
    if (await this.getToggleColumnValue(row, 'active') !== valueWanted) {
      await Promise.all([
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
        this.page.click(this.taxesGridColumn.replace('%ROW', row).replace('%COLUMN', 'active')),
      ]);
      return true;
    }
    return false;
  }

  /**
   * Go to add tax Page
   * @return {Promise<void>}
   */
  async goToAddNewTaxPage() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.addNewTaxLink),
    ]);
  }

  /**
   * Go to Edit tax page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditTaxPage(row) {
    await Promise.all([
      this.page.click(this.taxesGridColumnEditlink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
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
      this.page.waitForSelector(
        `${this.taxesGridColumnToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete
    await Promise.all([
      this.page.click(this.taxesGridDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
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
      this.page.waitForSelector(`${this.selectAllLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click to change status
    await Promise.all([
      this.page.click(enable ? this.enableSelectionButton : this.disableSelectionButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Taxes with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteTaxesBulkActions() {
    // Add listener to dialog to accept deletion
    this.dialogListener(true);
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllLabel),
      this.page.waitForSelector(`${this.selectAllLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete
    await Promise.all([
      this.page.click(this.deleteSelectionButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
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
    await Promise.all([
      this.page.click(this.saveTaxOptionButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
