require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CartRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.addNewCartRuleButton = '#page-header-desc-cart_rule-new_cart_rule';
    this.catalogPriceRulesTab = '#subtab-AdminSpecificPriceRule';
    this.editCartRuleButton = '#table-cart_rule a.edit';
    // Table selectors
    this.table = '#table-cart_rule';
    this.tableBodyRows = `${this.table} tbody tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;
    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';
  }

  /* Methods */
  /**
   * Change Tab to Catalog Price Rules in Discounts Page
   * @param page
   * @returns {Promise<void>}
   */
  async goToCatalogPriceRulesTab(page) {
    await this.clickAndWaitForNavigation(page, this.catalogPriceRulesTab);
    await this.waitForVisibleSelector(page, `${this.catalogPriceRulesTab}.current`);
  }

  /**
   * Go to add new cart rule page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewCartRulesPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCartRuleButton);
  }

  /**
   * Go to edit cart rule page
   * @param page
   * @returns {Promise<void>}
   */
  async goToEditCartRulePage(page) {
    await this.clickAndWaitForNavigation(page, this.editCartRuleButton);
  }

  /**
   * Delete cart rule
   * @param page
   * @param row
   * @returns {Promise<unknown>}
   */
  async deleteCartRule(page, row = 1) {
    await page.click(this.tableColumnActionsToggleButton(row));

    await this.waitForSelectorAndClick(page, this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new CartRules();
