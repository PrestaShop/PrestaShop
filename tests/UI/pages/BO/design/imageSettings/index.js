require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ImageSettings extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Image Settings • ';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newImageTypeLink = '#page-header-desc-image_type-new_image_type';

    // Form selectors
    this.gridForm = '#form-image_type';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-image_type';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='image_typeFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonimage_type';
    this.filterResetButton = 'button[name=\'submitResetimage_type\']';

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
    this.tableColumnStatus = (row, columnPos) => `${this.tableBodyColumn(row)}:nth-child(${columnPos}) span`;

    this.tableColumnProducts = row => this.tableColumnStatus(row, 6);
    this.tableColumnCategories = row => this.tableColumnStatus(row, 7);
    this.tableColumnManufacturers = row => this.tableColumnStatus(row, 8);
    this.tableColumnSuppliers = row => this.tableColumnStatus(row, 9);
    this.tableColumnStores = row => this.tableColumnStatus(row, 10);
  }

  /* Header methods */
  /**
   * Go to new image type page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewImageTypePage(page) {
    await this.clickAndWaitForNavigation(page, this.newImageTypeLink);
  }

  /* Filter methods */

  /**
   * Get Number of image types
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
   * Reset and get number of image types
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter image types
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
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
   * Get image type status for pages: products, categories, manufacturers, suppliers or stores
   * @param page
   * @param row
   * @param columnName
   * @return {Promise<boolean>}
   */
  async getImageTypeStatus(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'products':
        columnSelector = this.tableColumnProducts(row);
        break;

      case 'categories':
        columnSelector = this.tableColumnCategories(row);
        break;

      case 'manufacturers':
        columnSelector = this.tableColumnManufacturers(row);
        break;

      case 'suppliers':
        columnSelector = this.tableColumnSuppliers(row);
        break;

      case 'stores':
        columnSelector = this.tableColumnStores(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.elementVisible(page, `${columnSelector}.action-enabled`, 1000);
  }
}

module.exports = new ImageSettings();
