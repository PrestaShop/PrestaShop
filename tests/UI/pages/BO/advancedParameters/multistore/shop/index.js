require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopSettings extends BOBasePage {
  constructor() {
    super();

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.gridForm = '#form-shop';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='shopFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonshop';
    this.filterResetButton = 'button[name=\'submitResetshop\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;
    this.tableColumn = (row, column) => `${this.tableBodyRow(row)} td:nth-child(${column})`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;
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
  }

  /**
   * Reset and get number of shops
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shops
   * @param page
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value) {
    await this.setValue(page, this.filterColumn(filterBy), value.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Delete shop from row
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteShop(page, row) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    // Click on delete
    await page.click(this.tableColumnActionsDeleteLink(row));

    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
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
      case 'id_shop':
        columnSelector = this.tableColumn(row, 1);
        break;

      case 'a!name':
        columnSelector = this.tableColumn(row, 2);
        break;

      case 'gs!name':
        columnSelector = this.tableColumn(row, 3);
        break;

      case 'cl!name':
        columnSelector = this.tableColumn(row, 4);
        break;

      case 'url':
        columnSelector = this.tableColumn(row, 5);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to edit shop group page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditShopPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Go to set shop url
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToSetURL(page, row) {
    await this.clickAndWaitForNavigation(page, `${this.tableColumn(row, 5)} a`);
  }
}

module.exports = new ShopSettings();
