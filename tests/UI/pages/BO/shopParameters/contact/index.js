require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Contacts page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Contacts extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on contacts page
   */
  constructor() {
    super();

    this.pageTitle = 'Contacts';

    // Selectors
    // Header selectors
    this.storesTabLink = '#subtab-AdminStores';
    this.addNewContactButton = '#page-header-desc-configuration-add';

    // List of contacts
    this.contactsGridPanel = '#contact_grid_panel';
    this.contactsGridTitle = `${this.contactsGridPanel} h3.card-header-title`;
    this.contactsListForm = '#contact_grid';
    this.contactsListTableRow = row => `${this.contactsListForm} tbody tr:nth-child(${row})`;
    this.contactsListTableColumn = (row, column) => `${this.contactsListTableRow(row)} td.column-${column}`;

    // Filters
    this.contactFilterInput = filterBy => `${this.contactsListForm} #contact_${filterBy}`;
    this.filterSearchButton = `${this.contactsListForm} .grid-search-button`;
    this.filterResetButton = `${this.contactsListForm} .grid-reset-button`;

    // Actions buttons in Row
    this.contactsListTableActionsColumn = row => this.contactsListTableColumn(row, 'actions');
    this.listTableToggleDropDown = row => `${this.contactsListTableActionsColumn(row)} a[data-toggle='dropdown']`;
    this.listTableEditLink = row => `${this.contactsListTableActionsColumn(row)} a.grid-edit-row-link`;
    this.deleteRowLink = row => `${this.contactsListTableActionsColumn(row)} a.grid-delete-row-link`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.contactsGridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.contactsGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#contact_grid_bulk_action_delete_selection';

    // Sort Selectors
    this.tableHead = `${this.contactsGridPanel} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Delete modal
    this.confirmDeleteModal = '#contact-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /*
  Methods
   */

  /**
   * Click on tab stores
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToStoresPage(page) {
    await this.clickAndWaitForNavigation(page, this.storesTabLink);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.contactsGridTitle);
  }

  /**
   * Reset Filter and get number of elements in list
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of contacts
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterContacts(page, filterBy, value = '') {
    await this.setValue(page, this.contactFilterInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableContacts(page, row, column) {
    return this.getTextContent(page, this.contactsListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableContacts(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to new Contact page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewContactPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewContactButton);
  }

  /**
   * Go to Edit Contact page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditContactPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.listTableEditLink(row));
  }

  /**
   * Delete Contact
   * @param page {Page} Browser tab
   * @param row Row on table
   * @returns {Promise<string>}
   */
  async deleteContact(page, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.listTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);

    // Click on delete
    await Promise.all([
      page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteContact(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteContact(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Delete all contacts in table with Bulk Actions
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async deleteContactsBulkActions(page) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, this.bulkActionsToggleButton),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await this.confirmDeleteContact(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }
}

module.exports = new Contacts();
