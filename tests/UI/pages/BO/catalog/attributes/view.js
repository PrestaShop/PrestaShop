require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewAttribute extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Attributes >';

    this.alertSuccessBlockParagraph = '.alert-success';
    this.growlMessageBlock = '#growls .growl-message:last-of-type';

    // Header selectors
    this.addNewValueLink = '#page-header-desc-attribute-new_value';

    // Form selectors
    this.gridForm = '#form-attribute_values';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-attribute';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='attribute_valuesFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonattribute_values';
    this.filterResetButton = 'button[name=\'submitResetattribute_values\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnSelectRowCheckbox = row => `${this.tableBodyColumn(row)} input[name='attribute_valuesBox[]']`;
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnValue = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnColor = row => `${this.tableBodyColumn(row)}:nth-child(4) div`;
    this.tableColumnPosition = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColorColumnPosition = row => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_attribute';
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

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Grid footer link
    this.backToListLink = '#desc-attribute-back';
  }

  /* Header methods */

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
    await this.waitForVisibleSelector(page, this.gridForm, 2000);
  }

  /**
   * Get Number of attribute values
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of attribute values
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
      case 'id_attribute':
        columnSelector = this.tableColumnId(row);
        break;

      case 'b!name':
        columnSelector = this.tableColumnValue(row);
        break;

      case 'a!color':
        columnSelector = this.tableColumnColor(row);
        break;

      case 'a!position':
        columnSelector = this.tableColorColumnPosition(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }
    if (columnName === 'a!color') {
      return this.getAttributeContent(page, columnSelector, 'style');
    }
    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to edit value page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async goToEditValuePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete value
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteValue(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go back to list of attributes
   * @param page
   * @return {Promise<void>}
   * @constructor
   */
  async backToAttributesList(page) {
    await this.clickAndWaitForNavigation(page, this.backToListLink);
  }

  /**
   * Change value position
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
  async bulkDeleteValues(page) {
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
      case 'id_attribute':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'b!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!color':
        columnSelector = this.sortColumnDiv(4);
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
}

module.exports = new ViewAttribute();
