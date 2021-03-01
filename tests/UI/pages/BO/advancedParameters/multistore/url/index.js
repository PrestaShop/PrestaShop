require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopURLSettings extends BOBasePage {
  constructor() {
    super();

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.addNewUrlButton = '#page-header-desc-shop_url-new';

    // Form selectors
    this.gridForm = '#sql_form_shop_url';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop_url';

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
  }

  /* Methods */

  /**
   * Go to add new url page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewUrl(page) {
    await this.clickAndWaitForNavigation(page, this.addNewUrlButton);
  }

  /**
   * Delete shop URL
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteShopURL(page, row) {
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
}

module.exports = new ShopURLSettings();
