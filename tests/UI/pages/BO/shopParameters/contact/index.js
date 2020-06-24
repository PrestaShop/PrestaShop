require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Contacts extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Contacts';

    // Selectors
    // Header selectors
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
   * Reset input filters
   * @returns {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.contactsGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter list of contacts
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterContacts(filterBy, value = '') {
    await this.setValue(this.contactFilterInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableContacts(row, column) {
    return this.getTextContent(this.contactsListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableContacts(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to new Contact page
   * @return {Promise<void>}
   */
  async goToAddNewContactPage() {
    await this.clickAndWaitForNavigation(this.addNewContactButton);
  }

  /**
   * Go to Edit Contact page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditContactPage(row) {
    await this.clickAndWaitForNavigation(this.listTableEditLink(row));
  }

  /**
   * Delete Contact
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteContact(row) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.listTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.listTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.deleteRowLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all contacts in table with Bulk Actions
   * @return {Promise<string>}
   */
  async deleteContactsBulkActions() {
    // Add listener to dialog to accept deletion
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(this.bulkActionsToggleButton),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
