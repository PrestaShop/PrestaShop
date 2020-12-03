require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class TaxRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Tax Rules •';

    // Selectors
    // HEADER buttons
    this.addNewTaxRulesGroupLink = 'a#page-header-desc-tax_rules_group-new_tax_rules_group';

    // Form selectors
    this.gridForm = '#form-tax_rules_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;
    this.gridTable = '#table-tax_rules_group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='tax_rules_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtontax_rules_group';
    this.filterResetButton = `${this.filterRow} button[name='submitResettax_rules_group']`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.editRowLink = row => `${this.tableRow(row)} a.edit`;
    this.tableBodyColumn = row => `${this.tableRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnActive = row => `${this.tableBodyColumn(row)}:nth-child(4) a`;

    // Bulk actions selectors
    this.toggleDropDown = row => `${this.tableRow(row)} button[data-toggle='dropdown']`;
    this.deleteRowLink = row => `${this.tableRow(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

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

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_tax_rules_group';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
  }

  /*
  Methods
   */

  /**
   * Go to add tax Rules group Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewTaxRulesGroupPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTaxRulesGroupLink);
  }

  /**
   * Go to edit tax rule page
   * @param page
   * @param id
   * @returns {Promise<void>}
   */
  async goToEditTaxRulePage(page, id = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(id));
  }

  /**
   * Reset filter
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of element in grid
   * @param page
   * @returns {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Get text column from table
   * @param page
   * @param row
   * @param columnName
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_tax_rules_group':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'active':
        columnSelector = this.tableColumnActive(row);
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
   * Reset and get number of lines
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter table
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Delete Tax Rule
   * @param page
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteTaxRule(page, row = 1) {
    // Click on dropDown
    await page.click(this.toggleDropDown(row));
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(row));
    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);
    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  // Sort methods
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
      const rowContent = await this.getTextColumnFromTable(page, i, columnName);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_tax_rules_group':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'name':
        columnSelector = this.sortColumnDiv(3);
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

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page
   * @return {Promise<void>}
   */
  async bulkSelectRows(page) {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      page.waitForSelector(this.selectAllLink, {state: 'hidden'}),
    ]);
  }

  /**
   * Delete tax rules by bulk action
   * @param page
   * @returns {Promise<string>}
   */
  async bulkDeleteTaxRules(page) {
    this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new TaxRules();
