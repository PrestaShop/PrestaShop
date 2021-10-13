require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Search page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Search extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on search page
   */
  constructor() {
    super();

    this.pageTitle = 'Search •';
    this.successfulCreationMessage = 'Successful creation';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.successfulUpdateMessage = 'Update successful';
    this.settingsUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // Header links
    this.addNewAliasLink = '#page-header-desc-alias-new_alias';

    // Tabs
    this.tagsTabLink = '#subtab-AdminTags';

    // Form selectors
    this.gridForm = '#form-alias';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-alias';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='aliasFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonalias';
    this.filterResetButton = 'button[name=\'submitResetalias\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Columns selectors
    this.tableColumnAliases = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnSearch = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnStatus = row => `${this.tableBodyColumn(row)}:nth-child(4) a`;
    this.tableColumnStatusEnabledIcon = row => `${this.tableColumnStatus(row)}.action-enabled`;
    this.tableColumnStatusDisabledIcon = row => `${this.tableColumnStatus(row)}.action-disabled`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_alias';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
    this.bulkEnableButton = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableButton = `${this.bulkActionDropdownMenu} li:nth-child(5)`;

    // Search form
    this.aliasForm = '#alias_fieldset_search';
    this.fuzzySearchLabel = status => `#PS_SEARCH_FUZZY_${status}`;
    this.saveFormButton = `${this.aliasForm} button[name='submitOptionsalias']`;
  }

  /*
  Methods
   */

  /* Header methods */
  /**
   * Go to add new alias page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewAliasPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewAliasLink);
  }

  /**
   * Go to tags page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToTagsPage(page) {
    await this.clickAndWaitForNavigation(page, this.tagsTabLink);
  }

  /* Filter methods */
  /**
   * Get number of lines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter aliases
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */
  /**
   * Go to edit page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditAliasPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'alias':
        columnSelector = this.tableColumnAliases(row);
        break;

      case 'result':
        columnSelector = this.tableColumnSearch(row);
        break;

      case 'active':
        columnSelector = this.tableColumnStatus(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if (columnName === 'active') {
      return this.getAttributeContent(page, columnSelector, 'title');
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Delete alias from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteAlias(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page) {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Delete by bulk action
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteAliases(page) {
    await this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable / disable by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable status
   * @returns {Promise<void>}
   */
  async bulkSetStatus(page, enable = true) {
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on enable/Disable and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkEnableButton : this.bulkDisableButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get alias status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getStatus(page, row) {
    return this.elementVisible(page, this.tableColumnStatusEnabledIcon(row), 500);
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} Value wanted in column
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.tableColumnStatus(row), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(this.tableColumnStatus(row));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.tableColumnStatusEnabledIcon(row) : this.tableColumnStatusDisabledIcon(row)),
      );
      return true;
    }
    return false;
  }

  // Methods for search form
  /**
   * Enable/Disable fuzzy search
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable fuzzy search
   * @returns {Promise<string>}
   */
  async setFuzzySearch(page, toEnable = true) {
    await page.check(this.fuzzySearchLabel(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new Search();
