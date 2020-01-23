require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class SeoAndUrls extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'SEO & URLs •';

    // Header selectors
    this.addNewSeoPageLink = '#page-header-desc-configuration-add';
    this.successfulSettingsUpdateMessage = 'The settings have been successfully updated.';

    // Selectors grid panel
    this.gridPanel = '#meta_grid_panel';
    this.gridTable = '#meta_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #meta_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='meta[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='meta[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.editRowLink = `${this.actionsColumn} a[href*='/edit']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
    // Set up URL form
    this.switchFriendlyUrlLabel = 'label[for=\'meta_settings_form_set_up_urls_friendly_url_%TOGGLE\']';
    this.saveSeoAndUrlFormButton = '#main-div form:nth-child(1) div:nth-child(1) div.card-footer button';
  }

  /* header methods */
  /**
   * Go to new seo page
   * @return {Promise<void>}
   */
  async goToNewSeoUrlPage() {
    await this.clickAndWaitForNavigation(this.addNewSeoPageLink);
  }

  /* Column methods */
  /**
   * Get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * Go to edit file
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async goToEditSeoUrlPage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteSeoUrlPage(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Reset methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
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

  /* Filter methods */
  /**
   * Filter Table
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterBy, value = '') {
    await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Enable/disable friendly url
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableFriendlyURL(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchFriendlyUrlLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveSeoAndUrlFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }
};
