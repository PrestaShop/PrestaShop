require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class multistoreSettings extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Multistore • ';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newShopGroupLink = '#page-header-desc-shop_group-new';

    // Form selectors
    this.gridForm = '#form-shop_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop_group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='shop_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonshop_group';
    this.filterResetButton = 'button[name=\'submitResetshop_group\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnWidth = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnHeight = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnStatus = (row, columnPos, status) => `${this.tableBodyColumn(row)}:nth-child(${columnPos})`
      + ` span.action-${status}`;

    this.tableColumnProducts = (row, status) => this.tableColumnStatus(row, 6, status);
    this.tableColumnCategories = (row, status) => this.tableColumnStatus(row, 7, status);
    this.tableColumnManufacturers = (row, status) => this.tableColumnStatus(row, 8, status);
    this.tableColumnSuppliers = (row, status) => this.tableColumnStatus(row, 9, status);
    this.tableColumnStores = (row, status) => this.tableColumnStatus(row, 10, status);

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';
  }

  /* Header methods */
  /**
   * Go to new shop group page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewShopGroupPage(page) {
    await this.clickAndWaitForNavigation(page, this.newShopGroupLink);
  }

  /* Filter methods */

  /**
   * Get Number of elements
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
   * Reset and get number of shop groups
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shop groups
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
   * Get text from column in table
   * @param page
   * @param row
   * @param columnName
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_image_type':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'width':
        columnSelector = this.tableColumnWidth(row);
        break;

      case 'height':
        columnSelector = this.tableColumnHeight(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
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

  /**
   * Go to edit shop group page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditShopGroupPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete shop group from row
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteShopGroup(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new multistoreSettings();
