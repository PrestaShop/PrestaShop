require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Attributes extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Attributes • ';

    this.alertSuccessBlockParagraph = '.alert-success';
    this.growlMessageBlock = '#growls .growl-message:last-of-type';

    // Help card selectors
    this.helpCardLink = '#toolbar-nav a.btn-help';
    this.helpContainterBlock = '#help-container';

    // Header selectors
    this.addNewAttributeLink = '#page-header-desc-attribute_group-new_attribute_group';
    this.addNewValueLink = '#page-header-desc-attribute_group-new_value';
    this.featuresSubtabLink = '#subtab-AdminFeatures';

    // Form selectors
    this.gridForm = '#form-attribute_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-attribute_group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='attribute_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonattribute_group';
    this.filterResetButton = 'button[name=\'submitResetattribute_group\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnSelectRowCheckbox = row => `${this.tableBodyColumn(row)} input[name='attribute_groupBox[]']`;
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnValues = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnPosition = row => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsViewLink = row => `${this.tableColumnActions(row)} a[title='View']`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.edit`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_attribute_group';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header methods */

  /**
   * Click on features subtab and go to page
   * @param page
   * @return {Promise<void>}
   */
  async goToFeaturesPage(page) {
    await this.clickAndWaitForNavigation(page, this.featuresSubtabLink);
  }

  /**
   * Go to add new attribute page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddAttributePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewAttributeLink);
  }

  /**
   * Go to add new value page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewValuePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewValueLink);
  }

  /* Filter methods */
  /**
   * Reset all filters
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get Number of attributes
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of attributes
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter table
   * @param page
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value) {
    await this.setValue(page, this.filterColumn(filterBy), value.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Column methods */
  /**
   * Get text column from table
   * @param page
   * @param row
   * @param columnName
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_attribute_group':
        columnSelector = this.tableColumnId(row);
        break;

      case 'b!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'values':
        columnSelector = this.tableColumnValues(row);
        break;

      case 'a!position':
        columnSelector = this.tableColumnPosition(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to view attribute page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async viewAttribute(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsViewLink(row));
  }

  /**
   * Open row actions dropdown menu
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async openRowActionsDropdown(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsEditLink(row)),
    ]);
  }

  /**
   * Go to edit attribute page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async goToEditAttributePage(page, row) {
    await this.openRowActionsDropdown(page, row);

    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete attribute
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteAttribute(page, row) {
    await this.openRowActionsDropdown(page, row);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change attribute position
   * @param page
   * @param actualPosition
   * @param newPosition
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

  /* Bulk actions methods */
  /**
   * Bulk delete attributes
   * @param page
   * @return {Promise<string>}
   */
  async bulkDeleteAttributes(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForNavigation(page, this.paginationItems(number));

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

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_attribute_group':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'b!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!position':
        columnSelector = this.sortColumnDiv(5);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  /**
   * Get content from all rows
   * @param page
   * @param columnName
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  // Help card methods
  /**
   * @override
   * Open help side bar
   * @param page
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page) {
    await page.click(this.helpCardLink);

    return this.elementVisible(page, this.helpContainterBlock, 2000);
  }

  /**
   * @override
   * Close help side bar
   * @param page
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page) {
    await page.click(this.helpCardLink);

    return this.elementNotVisible(page, this.helpContainterBlock, 2000);
  }

  /**
   * @override
   * Get help card URL
   * @param page
   * @returns {Promise<string>}
   */
  async getHelpDocumentURL(page) {
    return this.getAttributeContent(page, this.helpCardLink, 'href');
  }
}

module.exports = new Attributes();
