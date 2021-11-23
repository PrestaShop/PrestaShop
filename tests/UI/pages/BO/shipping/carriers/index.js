require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Carriers page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Carriers extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on carriers page
   */
  constructor() {
    super();

    this.pageTitle = 'Carriers •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.growlMessageBlock = '#growls .growl-message:last-of-type';

    // Header links
    this.addNewCarrierLink = '#page-header-desc-carrier-new_carrier';

    // Form selectors
    this.gridForm = '#form-carrier';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-carrier';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='carrierFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncarrier';
    this.filterResetButton = 'button[name=\'submitResetcarrier\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnDelay = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnActive = row => `${this.tableBodyColumn(row)}:nth-child(6) a`;
    this.enableColumnValidIcon = row => `${this.tableColumnActive(row)} i.icon-check`;
    this.tableColumnIsFree = row => `${this.tableBodyColumn(row)}:nth-child(7) a`;
    this.tableColumnPosition = row => `${this.tableBodyColumn(row)}:nth-child(8)`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_carrier';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
  }

  /*
  Methods
   */
  /**
   * Go to add new carrier page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCarrierPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCarrierLink);
  }

  /* Filter methods */

  /**
   * Get Number of carriers
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
   * Reset and get number of image types
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter carriers table
   * @param page {Page} Browser tab
   * @param filterType {string} Type of the filter (input or select)
   * @param filterBy {string} Value to use for the select type filter
   * @param value {string|number} Value for the select filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
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
   * Go to edit carrier page
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @return {Promise<void>}
   */
  async gotoEditCarrierPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param columnName {string} Column name in the table
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_carrier':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'delay':
        columnSelector = this.tableColumnDelay(row);
        break;

      case 'active':
        columnSelector = this.tableColumnActive(row);
        break;

      case 'is_free':
        columnSelector = this.tableColumnIsFree(row);
        break;

      case 'a!position':
        columnSelector = this.tableColumnPosition(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if ((columnName === 'active') || (columnName === 'is_free')) {
      return this.getAttributeContent(page, columnSelector, 'title');
    }
    return this.getTextContent(page, columnSelector);
  }

  /**
   * Delete carrier from row
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @return {Promise<string>}
   */
  async deleteCarrier(page, row) {
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

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name in the table
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} column to sort with
   * @param sortDirection {string} asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_carrier':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!position':
        columnSelector = this.sortColumnDiv(8);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {Number} The pagination number value
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.waitForSelectorAndClick(page, this.paginationItems(number));
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  /* Bulk actions methods */
  /**
   * Bulk delete carriers
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteCarriers(page) {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set carriers status
   * @param page {Page} Browser tab
   * @param action {string} The action to perform in bulk
   * @returns {Promise<void>}
   */
  async bulkSetStatus(page, action) {
    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    if (action === 'Enable') {
      await this.clickAndWaitForNavigation(page, this.bulkEnableLink);
    } else {
      await this.clickAndWaitForNavigation(page, this.bulkDisableLink);
    }

    // not working, skipping it
    // Return successful message
    // return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get carrier status
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @returns {Promise<boolean>}
   */
  async getStatus(page, row = 1) {
    return this.elementVisible(page, this.enableColumnValidIcon(row), 100);
  }

  /**
   * Set carriers status
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param valueWanted {boolean} The carrier status value
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page, row = 1, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.tableColumnActive(row), 2000);

    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.tableColumnActive(row));
      return true;
    }

    return false;
  }

  /**
   * Change carrier position
   * @param page {Page} Browser tab
   * @param actualPosition {number} The actual row position
   * @param newPosition {number} The new position for the row
   * @return {Promise<string>}
   */
  async changePosition(page, actualPosition, newPosition) {
    await this.dragAndDrop(
      page,
      this.tableColumnPosition(actualPosition),
      this.tableColumnPosition(newPosition),
    );

    return this.getGrowlMessageContent(page);
  }
}

module.exports = new Carriers();
