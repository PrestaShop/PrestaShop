require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CartRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.addNewCartRuleButton = '#page-header-desc-cart_rule-new_cart_rule';
    this.catalogPriceRulesTab = '#subtab-AdminSpecificPriceRule';

    // Form selectors
    this.gridForm = '#form-cart_rule';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-cart_rule';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='cart_ruleFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncart_rule';
    this.filterResetButton = 'button[name=\'submitResetcart_rule\']';

    // Table body selectors
    this.tableBodyRows = `${this.gridTable} tbody tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnSelectRowCheckbox = row => `${this.tableBodyColumn(row)} input[name='cart_ruleBox[]']`;
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnPriority = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnCode = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnQuantity = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnExpirationDate = row => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnStatusLink = row => `${this.tableBodyColumn(row)}:nth-child(8) a`;
    this.tableColumnStatusEnableLink = row => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = row => `${this.tableColumnStatusLink(row)}.action-disabled`;

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
    this.bulkActionMenuButton = '#bulk_action_menu_cart_rule';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;

    // Pagination selectors
    this.paginationLabel = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationLabel} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;
  }

  /* Header methods */
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

  /* Table methods */
  /**
   * Go to edit cart rule page
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditCartRulePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
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
      case 'id_cart_rule':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'priority':
        columnSelector = this.tableColumnPriority(row);
        break;

      case 'code':
        columnSelector = this.tableColumnCode(row);
        break;

      case 'quantity':
        columnSelector = this.tableColumnQuantity(row);
        break;

      case 'date':
        columnSelector = this.tableColumnExpirationDate(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get cart rule status
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  getCartRuleStatus(page, row) {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set cart rule status
   * @param page
   * @param row
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async setCartRuleStatus(page, row, wantedStatus) {
    if (wantedStatus !== await this.getCartRuleStatus(page, row)) {
      await this.clickAndWaitForNavigation(page, this.tableColumnStatusLink(row));
    }
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
   * Get Number of cart rules
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of cart rules
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter cart rules
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterCartRules(page, filterType, filterBy, value) {
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
   * Bulk delete cart rules
   * @param page
   * @return {Promise<void>}
   */
  async bulkDeleteCartRules(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await this.bulkSelectRows(page);

    // Perform delete
    await page.click(this.bulkActionMenuButton);
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Bulk set status
   * @param page
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async bulkSetStatus(page, wantedStatus) {
    // Select all rows
    await this.bulkSelectRows(page);

    // Set status
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkEnableLink),
    ]);

    await this.clickAndWaitForNavigation(
      page,
      wantedStatus ? this.bulkEnableLink : this.bulkDisableLink,
    );
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, 'ul.pagination.pull-right li.active a');
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.waitForSelectorAndClick(page, this.paginationItems(number));
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
}

module.exports = new CartRules();
