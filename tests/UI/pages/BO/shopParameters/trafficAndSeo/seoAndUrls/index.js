require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class SeoAndUrls extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'SEO & URLs •';

    // Header selectors
    this.addNewSeoPageLink = '#page-header-desc-configuration-add';
    this.successfulSettingsUpdateMessage = 'The settings have been successfully updated.';

    // Sub tabs selectors
    this.searchEnginesSubTabLink = '#subtab-AdminSearchEngines';

    // Grid selectors
    this.gridPanel = '#meta_grid_panel';
    this.gridTable = '#meta_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Sort Selectors
    this.tableHead = `${this.gridPanel} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #meta_grid_bulk_action_delete_seo_urls`;

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

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;


    // Set up URL form selectors
    this.switchFriendlyUrlLabel = toggle => `label[for='meta_settings_form_set_up_urls_friendly_url_${toggle}']`;
    this.switchAccentedUrlLabel = toggle => `label[for='meta_settings_form_set_up_urls_accented_url_${toggle}']`;
    this.saveSeoAndUrlFormButton = '#main-div form:nth-child(1) div:nth-child(1) div.card-footer button';
    // Seo options form
    this.switchDisplayAttributesLabel = toggle => 'label[for=\'meta_settings_form_seo_options_product'
      + `_attributes_in_title_${toggle}']`;
    this.saveSeoOptionsFormButton = '#main-div form:nth-child(1) div:nth-child(4) div.card-footer button';
  }

  /* header methods */
  /**
   * Go to new seo page
   * @return {Promise<void>}
   */
  async goToNewSeoUrlPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewSeoPageLink);
  }

  /**
   * Go to search engines page
   * @param page
   * @return {Promise<void>}
   */
  async goToSearchEnginesPage(page) {
    await this.clickAndWaitForNavigation(page, this.searchEnginesSubTabLink);
  }

  /* Bulk actions methods */

  /**
   * Delete seo pages by bulk actions
   * @param page
   * @returns {Promise<string>}
   */
  async bulkDeleteSeoUrlPage(page) {
    // Confirm delete in js modal
    this.dialogListener(page, true);

    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on button bulk action
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);
    return this.getAlertSuccessBlockParagraphContent(page);
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
   * Get content from all rows
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let row = 1; row <= rowsNumber; row++) {
      const rowContent = await this.getTextColumnFromTable(page, row, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
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

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  /* Form methods */
  /**
   * Enable/disable friendly url
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async enableDisableFriendlyURL(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchFriendlyUrlLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveSeoAndUrlFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable attributes in product meta title
   * @param page
   * @param toEnable
   * @return {Promise<string>}
   */
  async setStatusAttributesInProductMetaTitle(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchDisplayAttributesLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveSeoOptionsFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new SeoAndUrls();
