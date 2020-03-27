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
    this.contactsListTableRow = `${this.contactsListForm} tbody tr:nth-child(%ROW)`;
    this.contactsListTableColumn = `${this.contactsListTableRow} td.column-%COLUMN`;
    // Filters
    this.contactFilterInput = `${this.contactsListForm} #contact_%FILTERBY`;
    this.filterSearchButton = `${this.contactsListForm} button[name='contact[actions][search]']`;
    this.filterResetButton = `${this.contactsListForm} button[name='contact[actions][reset]']`;
    // Actions buttons in Row
    this.contactsListTableActionsColumn = this.contactsListTableColumn.replace('%COLUMN', 'actions');
    this.listTableToggleDropDown = `${this.contactsListTableActionsColumn} a[data-toggle='dropdown']`;
    this.listTableEditLink = `${this.contactsListTableActionsColumn} a[href*='edit']`;
    this.deleteRowLink = `${this.contactsListTableActionsColumn} a[data-method='POST']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.contactsGridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.contactsGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#contact_grid_bulk_action_delete_all';
    // Sort Selectors
    this.tableHead = `${this.contactsGridPanel} thead`;
    this.sortColumnDiv = `${this.tableHead} div.ps-sortable-column[data-sort-col-name='%COLUMN']`;
    this.sortColumnSpanButton = `${this.sortColumnDiv} span.ps-sort`;
  }

  /*
  Methods
   */

  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.contactsGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
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
    await this.setValue(this.contactFilterInput.replace('%FILTERBY', filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableContacts(row, column) {
    return this.getTextContent(
      this.contactsListTableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
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
    this.clickAndWaitForNavigation(this.listTableEditLink.replace('%ROW', row));
  }

  /**
   * Delete Contact
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteContact(row) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.listTableToggleDropDown.replace('%ROW', row)),
      this.waitForVisibleSelector(
        `${this.listTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
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
      this.page.click(this.selectAllRowsLabel),
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
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
    const sortColumnDiv = `${this.sortColumnDiv.replace('%COLUMN', sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton.replace('%COLUMN', sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
