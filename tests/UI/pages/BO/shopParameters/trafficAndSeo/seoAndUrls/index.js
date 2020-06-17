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
    this.filterColumn = filterBy => `${this.gridTable} #meta_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = row => `${this.actionsColumn(row)} a[href*='/edit']`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;
    // Set up URL form
    this.switchFriendlyUrlLabel = toggle => `label[for='meta_settings_form_set_up_urls_friendly_url_${toggle}']`;
    this.switchAccentedUrlLabel = toggle => `label[for='meta_settings_form_set_up_urls_accented_url_${toggle}']`;
    this.saveSeoAndUrlFormButton = '#main-div form:nth-child(1) div:nth-child(1) div.card-footer button';
    // Delete modal
    this.confirmDeleteModal = '#meta-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
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
    return this.getTextContent(this.tableColumn(row, column));
  }

  /**
   * Go to edit file
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async goToEditSeoUrlPage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteSeoUrlPage(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSeoUrlPage();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @return {Promise<void>}
   */
  async confirmDeleteSeoUrlPage() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
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
    await this.setValue(this.filterColumn(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Enable/disable friendly url
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableFriendlyURL(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchFriendlyUrlLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveSeoAndUrlFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/disable accented url
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableAccentedURL(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchAccentedUrlLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveSeoAndUrlFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
