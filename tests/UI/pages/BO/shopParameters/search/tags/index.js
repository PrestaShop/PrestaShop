require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Tags extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Tags •';

    // Selectors
    // Header links
    this.addNewTagLink = '#page-header-desc-tag-new_tag';

    // Form selectors
    this.gridForm = '#form-tag';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-tag';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='tagFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtontag';
    this.filterResetButton = 'button[name=\'submitResettag\']';

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

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnLanguage = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnProducts = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
  }

  /*
  Methods
   */

  /* Header methods */
  /**
   * Go to add new tag page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewTagPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTagLink);
  }

  /* Filter methods */
  /**
   * Get Number of lines
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

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
   * Reset and get number of lines
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter Table
   * @param page
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Column methods */
  /**
   * Go to edit page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditTagPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page
   * @param row
   * @param columnName
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_tag':
        columnSelector = this.tableColumnId(row);
        break;

      case 'l!name':
        columnSelector = this.tableColumnLanguage(row);
        break;

      case 'a!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'products':
        columnSelector = this.tableColumnProducts(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }
}

module.exports = new Tags();
