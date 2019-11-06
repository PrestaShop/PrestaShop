require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Profiles extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Profiles';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewProfileLink = '#page-header-desc-configuration-add[title=\'Add new profile\']';
    // List of profiles
    this.profileGridPanel = '#profile_grid_panel';
    this.profileGridTitle = `${this.profileGridPanel} h3.card-header-title`;
    this.profilesListForm = '#profile_grid';
    this.profilesListTableRow = `${this.profilesListForm} tbody tr:nth-child(%ROW)`;
    this.profilesListTableColumn = `${this.profilesListTableRow} td.column-%COLUMN`;
    this.profilesListTableToggleDropDown = `${this.profilesListTableColumn.replace('%COLUMN', 'actions')} 
    a[data-toggle='dropdown']`;
    this.profilesListTableDeleteLink = `${this.profilesListTableColumn.replace('%COLUMN', 'actions')} a[data-url]`;
    this.profilesListTableEditLink = `${this.profilesListTableColumn.replace('%COLUMN', 'actions')} a[href*='edit']`;
    this.profilesListColumnValidIcon = `${this.profilesListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-valid`;
    this.profilesListColumnNotValidIcon = `${this.profilesListTableColumn.replace('%COLUMN', 'active')} 
    i.grid-toggler-icon-not-valid`;
    // Filters
    this.profileFilterInput = `${this.profilesListForm} #profile_%FILTERBY`;
    this.filterSearchButton = `${this.profilesListForm} button[name='profile[actions][search]']`;
    this.filterResetButton = `${this.profilesListForm} button[name='profile[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.profilesListForm} .md-checkbox label`;
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
   * Reset input filters
   * @return {Promise<textContent>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberFromText(this.profileGridTitle);
  }

  /**
   * Go to Edit profile page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditProfilePage(row) {
    // Click on edit
    await Promise.all([
      this.page.click(this.profilesListTableEditLink.replace('%ROW', row)),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
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
        await this.setValue(this.profileFilterInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.profileFilterInput.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.filterSearchButton),
    ]);
  }

  /**
   * Get Value of column Displayed
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row) {
    if (await this.elementVisible(
      this.profilesListColumnValidIcon.replace('%ROW', row), 100)) return true;
    return false;
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      this.page.click(this.profilesListTableColumn.replace('%ROW', row).replace('%COLUMN', 'active'));
      if (valueWanted) {
        await this.page.waitForSelector(this.profilesListColumnValidIcon.replace('%ROW', row));
      } else {
        await this.page.waitForSelector(
          this.profilesListColumnNotValidIcon.replace('%ROW', row));
      }
      return true;
    }
    return false;
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
      this.page.click(this.profilesListTableToggleDropDown.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.profilesListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete
    await Promise.all([
      this.page.click(this.profilesListTableDeleteLink.replace('%ROW', row)),
      this.page.waitForSelector(this.alertSuccessBlockParagraph),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable profiles by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
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
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.page.waitForSelector(this.alertSuccessBlockParagraph),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
