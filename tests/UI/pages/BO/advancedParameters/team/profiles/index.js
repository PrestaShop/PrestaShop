require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Profiles extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Profiles';

    // Selectors
    // Header links
    this.addNewProfileLink = '#page-header-desc-configuration-add[title=\'Add new profile\']';
    // List of profiles
    this.profileGridPanel = '#profile_grid_panel';
    this.profileGridTitle = `${this.profileGridPanel} h3.card-header-title`;
    this.profilesListForm = '#profile_grid';
    this.profilesListTableRow = row => `${this.profilesListForm} tbody tr:nth-child(${row})`;
    this.profilesListTableColumn = (row, column) => `${this.profilesListTableRow(row)} td.column-${column}`;
    this.profilesListTableColumnAction = row => this.profilesListTableColumn(row, 'actions');
    this.profilesListTableToggleDropDown = row => `${this.profilesListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.profilesListTableDeleteLink = row => `${this.profilesListTableColumnAction(row)} a[data-url]`;
    this.profilesListTableEditLink = row => `${this.profilesListTableColumnAction(row)} a[href*='edit']`;
    // Filters
    this.profileFilterInput = filterBy => `${this.profilesListForm} #profile_${filterBy}`;
    this.filterSearchButton = `${this.profilesListForm} button[name='profile[actions][search]']`;
    this.filterResetButton = `${this.profilesListForm} button[name='profile[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.profilesListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.profilesListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.profilesListForm} #profile_grid_bulk_action_bulk_delete_profiles`;
    // Delete modal
    this.confirmDeleteModal = '#profile-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Pages selectors
    this.pagesPaginationLimitSelect = '#paginator_select_page_limit';
    this.pagesPaginationLabel = `${this.profilesListForm} .col-form-label`;
    this.pagesPaginationNextLink = `${this.profilesListForm} #pagination_next_url`;
    this.pagesPaginationPreviousLink = `${this.profilesListForm} [aria-label='Previous']`;
    // Sort Selectors
    this.tableHead = `${this.profileGridPanel} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /*
  Methods
   */

  /**
   * Go to new profile page
   * @return {Promise<void>}
   */
  async goToAddNewProfilePage() {
    await this.clickAndWaitForNavigation(this.addNewProfileLink);
  }

  /**
   * get text from a column from table
   * @param row
   * @param column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(this.profilesListTableColumn(row, column));
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.profileGridTitle);
  }

  /**
   * Reset input filters
   * @return {Promise<textContent>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberOfElementInGrid();
  }

  /**
   * Go to Edit profile page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditProfilePage(row) {
    // Click on edit
    await this.clickAndWaitForNavigation(this.profilesListTableEditLink(row));
  }

  /**
   * Filter list of profiles
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterProfiles(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.profileFilterInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.profileFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Delete profile
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteProfile(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.profilesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.profilesListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.profilesListTableDeleteLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteProfiles();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }


  /**
   * Confirm delete with in modal
   * @return {Promise<void>}
   */
  async confirmDeleteProfiles() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
  }

  /**
   * Delete all profiles with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteBulkActions() {
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

  /**
   * Select profiles pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(number) {
    await this.selectByVisibleText(this.pagesPaginationLimitSelect, number);
    return this.getTextContent(this.pagesPaginationLabel);
  }

  /**
   * profiles pagination next
   * @returns {Promise<string>}
   */
  async paginationNext() {
    await this.clickAndWaitForNavigation(this.pagesPaginationNextLink);
    return this.getTextContent(this.pagesPaginationLabel);
  }

  /**
   * profiles pagination previous
   * @returns {Promise<string>}
   */
  async paginationPrevious() {
    await this.clickAndWaitForNavigation(this.pagesPaginationPreviousLink);
    return this.getTextContent(this.pagesPaginationLabel);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(this.profilesListTableColumn(i, column));
      if (column === 'active') {
        rowContent = await this.getToggleColumnValue(i).toString();
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Sort table
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
