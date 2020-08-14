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

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_image_type';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
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
        columnSelector = this.tableColumnProducts;
        break;

      case 'categories':
        columnSelector = this.tableColumnCategories;
        break;

      case 'manufacturers':
        columnSelector = this.tableColumnManufacturers;
        break;

      case 'suppliers':
        columnSelector = this.tableColumnSuppliers;
        break;

      case 'stores':
        columnSelector = this.tableColumnStores;
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.elementVisible(page, columnSelector(row, 'enabled'), 1000);
  }

  /**
   * Go to edit imageType page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditImageTypePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete image type from row
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteImageType(page, row) {
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

  /* Bulk actions methods */
  /**
   * Bulk delete image types
   * @param page
   * @return {Promise<string>}
   */
  async bulkDeleteImageTypes(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      page.waitForSelector(this.selectAllLink, {state: 'hidden'}),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new ImageSettings();
