require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Stores page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Stores extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on stores page
   */
  constructor() {
    super();

    this.pageTitle = 'Stores •';
    this.contactFormSuccessfulUpdateMessage = 'The settings have been successfully updated.';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newStoreLink = '#page-header-desc-store-new_store';

    // Form selectors
    this.gridForm = '#form-store';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-store';

    // Sort selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='storeFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonstore';
    this.filterResetButton = 'button[name=\'submitResetstore\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnAddress = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnCity = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnPostalCode = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnState = row => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnCountry = row => `${this.tableBodyColumn(row)}:nth-child(8)`;
    this.tableColumnPhone = row => `${this.tableBodyColumn(row)}:nth-child(9)`;
    this.tableColumnFax = row => `${this.tableBodyColumn(row)}:nth-child(10)`;
    this.tableColumnStatus = row => `${this.tableBodyColumn(row)}:nth-child(11) a`;

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
    this.bulkActionMenuButton = '#bulk_action_menu_store';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.enableSelectionLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.disableSelectionLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Contact details form
    this.contactDetailsForm = '#store_fieldset_contact';
    this.nameInput = `${this.contactDetailsForm} input[name='PS_SHOP_NAME']`;
    this.emailInput = `${this.contactDetailsForm} input[name='PS_SHOP_EMAIL']`;
    this.registrationNumberTextarea = '#conf_id_PS_SHOP_DETAILS textarea[name=\'PS_SHOP_DETAILS\']';
    this.address1Input = `${this.contactDetailsForm} input[name='PS_SHOP_ADDR1']`;
    this.address2Input = `${this.contactDetailsForm} input[name='PS_SHOP_ADDR2']`;
    this.postcodeInput = `${this.contactDetailsForm} input[name='PS_SHOP_CODE']`;
    this.cityInput = `${this.contactDetailsForm} input[name='PS_SHOP_CITY']`;
    this.countrySelect = '#PS_SHOP_COUNTRY_ID';
    this.phoneInput = `${this.contactDetailsForm} input[name='PS_SHOP_PHONE']`;
    this.faxInput = `${this.contactDetailsForm} input[name='PS_SHOP_FAX']`;
    this.saveButton = `${this.contactDetailsForm} button[name='submitOptionsstore']`;
  }

  /* Header methods */
  /**
   * Go to new store page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewStorePage(page) {
    await this.clickAndWaitForNavigation(page, this.newStoreLink);
  }

  /* Filter methods */

  /**
   * Get Number of stores
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of stores
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter stores
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
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
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_store':
        columnSelector = this.tableColumnId(row);
        break;

      case 'sl!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'sl!address1':
        columnSelector = this.tableColumnAddress(row);
        break;

      case 'city':
        columnSelector = this.tableColumnCity(row);
        break;

      case 'postcode':
        columnSelector = this.tableColumnPostalCode(row);
        break;

      case 'st!name':
        columnSelector = this.tableColumnState(row);
        break;

      case 'cl!name':
        columnSelector = this.tableColumnCountry(row);
        break;

      case 'phone':
        columnSelector = this.tableColumnPhone(row);
        break;

      case 'fax':
        columnSelector = this.tableColumnFax(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get column content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name of the value to return
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    // Get text column from each row
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get Store status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getStoreStatus(page, row) {
    return this.elementVisible(page, `${this.tableColumnStatus(row)}.action-enabled`, 1000);
  }

  /**
   * Set store status by clicking on column status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable status
   * @return {Promise<void>}
   */
  async setStoreStatus(page, row, wantedStatus) {
    const actualStatus = await this.getStoreStatus(page, row);

    if (actualStatus !== wantedStatus) {
      await this.clickAndWaitForNavigation(page, this.tableColumnStatus(row));
    }
  }

  /**
   * Go to edit store page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditStorePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete store from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteStore(page, row) {
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

  /* Bulk actions methods */

  /**
   * Select all rows for bulk action
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async selectAllRow(page) {
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Enable / disable stores by bulk actions
   * @param page {Page} Browser tab
   * @param statusWanted {boolean} True if we need to enable status
   * @return {Promise<void>}
   */
  async bulkUpdateStoreStatus(page, statusWanted) {
    // Select all rows
    await this.selectAllRow(page);

    // Perform bulk update status
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(
        page,
        this.enableSelectionLink,
      ),
    ]);

    await this.clickAndWaitForNavigation(
      page,
      statusWanted ? this.enableSelectionLink : this.disableSelectionLink,
    );
  }

  /**
   * Bulk delete stores
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteStores(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await this.selectAllRow(page);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_store':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'sl!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'sl!address1':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'city':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'postcode':
        columnSelector = this.sortColumnDiv(6);
        break;

      case 'st!name':
        columnSelector = this.sortColumnDiv(7);
        break;

      case 'cl!name':
        columnSelector = this.sortColumnDiv(8);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  /* Form functions */
  /**
   * Se contact details
   * @param page {Page} Browser tab
   * @param storeContactData {StoreData} Store contact data to set on contact detail form
   * @returns {Promise<string>}
   */
  async setContactDetails(page, storeContactData) {
    // Set name
    await this.setValue(page, this.nameInput, storeContactData.name);

    // Set email and registration number inputs
    await this.setValue(page, this.emailInput, storeContactData.email);
    await this.setValue(page, this.registrationNumberTextarea, storeContactData.registrationNumber);

    // Set address inputs
    await this.setValue(page, this.address1Input, storeContactData.address1);
    await this.setValue(page, this.address2Input, storeContactData.address2);
    await this.setValue(page, this.postcodeInput, storeContactData.postcode);
    await this.setValue(page, this.cityInput, storeContactData.city);
    await this.selectByVisibleText(page, this.countrySelect, storeContactData.country);

    // Set phone inputs
    await this.setValue(page, this.phoneInput, storeContactData.phone);
    await this.setValue(page, this.faxInput, storeContactData.fax);

    // Save contact details
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination limit number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForNavigation(page, this.paginationItems(number));

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

module.exports = new Stores();
