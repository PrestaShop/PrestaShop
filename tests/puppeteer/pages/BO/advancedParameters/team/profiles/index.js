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
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.profilesListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.profilesListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.profilesListTableDeleteLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all profiles with Bulk Actions
   * @return {Promise<textContent>}
   */
  async deleteBulkActions() {
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
