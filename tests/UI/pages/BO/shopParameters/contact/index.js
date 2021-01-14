require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Contacts extends BOBasePage {
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
    this.filterSearchButton = `${this.contactsListForm} button[name='contact[actions][search]']`;
    this.filterResetButton = `${this.contactsListForm} button[name='contact[actions][reset]']`;
    // Actions buttons in Row
    this.contactsListTableActionsColumn = row => this.contactsListTableColumn(row, 'actions');
    this.listTableToggleDropDown = row => `${this.contactsListTableActionsColumn(row)} a[data-toggle='dropdown']`;
    this.listTableEditLink = row => `${this.contactsListTableActionsColumn(row)} a[href*='edit']`;
    this.deleteRowLink = row => `${this.contactsListTableActionsColumn(row)} a[data-method='POST']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.contactsGridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.contactsGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#contact_grid_bulk_action_delete_all';
    // Sort Selectors
    this.tableHead = `${this.contactsGridPanel} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /*
  Methods
   */

  /**
   * Click on tab stores
   * @param page
   * @return {Promise<void>}
   */
  async goToStoresPage(page) {
    await this.clickAndWaitForNavigation(page, this.storesTabLink);
  }

  /**
   * Reset input filters
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.contactsGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of contacts
   * @param page
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterContacts(page, filterBy, value = '') {
    await this.setValue(page, this.contactFilterInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableContacts(page, row, column) {
    return this.getTextContent(page, this.contactsListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableContacts(page, i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to new Contact page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewContactPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewContactButton);
  }

  /**
   * Go to Edit Contact page
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditContactPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.listTableEditLink(row));
  }

  /**
   * Delete Contact
   * @param page
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteContact(page, row) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.listTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(row));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all contacts in table with Bulk Actions
   * @param page
   * @return {Promise<string>}
   */
  async deleteContactsBulkActions(page) {
    // Add listener to dialog to accept deletion
    this.dialogListener(page);
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
    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
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
