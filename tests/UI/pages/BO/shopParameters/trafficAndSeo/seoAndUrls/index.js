require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class SeoAndUrls extends BOBasePage {
  constructor() {
    super();

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
    this.filterSearchButton = `${this.gridTable} button[name='meta[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='meta[actions][reset]']`;
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
  }

  /* header methods */
  /**
   * Go to new seo page
   * @return {Promise<void>}
   */
  async goToNewSeoUrlPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewSeoPageLink);
  }

  /* Column methods */
  /**
   * Get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Go to edit file
   * @param page
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async goToEditSeoUrlPage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param page
   * @param row, row to delete
   * @returns {Promise<string>}
   */
  async deleteSeoUrlPage(page, row = 1) {
    this.dialogListener(page, true);
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        page,
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(row));
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /* Reset methods */
  /**
   * Reset filters in table
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /* Filter methods */
  /**
   * Filter Table
   * @param page
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value = '') {
    await this.setValue(page, this.filterColumn(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Enable/disable friendly url
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableFriendlyURL(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchFriendlyUrlLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveSeoAndUrlFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Enable/disable accented url
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableAccentedURL(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchAccentedUrlLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveSeoAndUrlFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new SeoAndUrls();
