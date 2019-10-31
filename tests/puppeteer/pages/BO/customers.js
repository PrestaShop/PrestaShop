require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Customers extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Manage your Customers • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewCustomerLink = '#page-header-desc-configuration-add[title=\'Add new customer\']';
    // List of customers
    this.customerGridPanel = '#customer_grid_panel';
    this.customerGridTitle = `${this.customerGridPanel} h3.card-header-title`;
    this.customersListForm = '#customer_grid';
    this.customersListTableRow = `${this.customersListForm} tbody tr:nth-child(%ROW)`;
    this.customersListTableColumn = `${this.customersListTableRow} td.column-%COLUMN`;
    this.customersListTableEditLink = `${this.customersListTableColumn} a[data-original-title='Edit']`;
    this.customersListTableToggleDropDown = `${this.customersListTableColumn} a[data-toggle='dropdown']`;
    this.customersListTableViewLink = `${this.customersListTableColumn} a[href*='/view']`;
    this.customersListTableDeleteLink = `${this.customersListTableColumn} a[data-customer-delete-url]`;
    this.customersListColumnValidIcon = `${this.customersListTableColumn} i.grid-toggler-icon-valid`;
    this.customersListColumnNotValidIcon = `${this.customersListTableColumn} i.grid-toggler-icon-not-valid`;
    // Filters
    this.customerFilterColumnInput = `${this.customersListForm} #customer_%FILTERBY`;
    this.filterSearchButton = `${this.customersListForm} button[name='customer[actions][search]']`;
    this.filterResetButton = `${this.customersListForm} button[name='customer[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.customersListForm} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.customersListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.customersListForm} #customer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.customersListForm} #customer_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.customersListForm} #customer_grid_bulk_action_delete_selection`;


    // Modal Dialog
    this.deleteCustomerModal = '#customer_grid_delete_customers_modal.show';
    this.deleteCustomerModalDeleteButton = `${this.deleteCustomerModal} button.js-submit-delete-customers`;
    this.deleteCustomerModalMethodInput = `${this.deleteCustomerModal} #delete_customers_delete_method_%ID`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberFromText(this.customerGridTitle);
  }

  /**
   * Filter list of customers
   * @param filterType, input or select to choose method of filter
   * @param filterBy, colomn to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCustomers(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.customerFilterColumnInput.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          this.customerFilterColumnInput.replace('%FILTERBY', filterBy),
          value,
        );
        break;
      default:
        // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Filter Customers by select that contains values (Yes/No)
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterCustomersSwitch(filterBy, value) {
    await this.filterCustomers(
      'select',
      filterBy,
      value ? 'Yes' : 'No',
    );
  }

  /**
   * Get Value of columns Enabled, Newsletter or Partner Offers
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(row, column) {
    if (
      await this.elementVisible(
        this.customersListColumnValidIcon.replace('%ROW', row).replace('%COLUMN', column),
        100,
      )
    ) return true;
    return false;
  }

  /**
   * Quick edit toggle column value
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>}, return true if action is done, false otherwise
   */
  async updateToggleColumnValue(row, column, valueWanted = true) {
    if (await this.getToggleColumnValue(row, column) !== valueWanted) {
      await this.clickAndWaitForNavigation(
        this.customersListTableColumn.replace('%ROW', row).replace('%COLUMN', column),
      );
      return true;
    }
    return false;
  }

  /**
   * Go to Customer Page
   * @return {Promise<void>}
   */
  async goToAddNewCustomerPage() {
    await this.clickAndWaitForNavigation(this.addNewCustomerLink);
  }

  /**
   * View Customer in list
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToViewCustomerPage(row) {
    await Promise.all([
      this.page.click(this.customersListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.customersListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    await this.clickAndWaitForNavigation(
      this.customersListTableViewLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
    );
  }

  /**
   * Go to Edit customer page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCustomerPage(row) {
    await this.clickAndWaitForNavigation(
      this.customersListTableEditLink.replace('%ROW', row).replace('%COLUMN', 'actions'),
    );
  }

  /**
   * Delete Customer
   * @param row, row in table
   * @param allowRegistrationAfterDelete, Deletion method to choose in modal
   * @return {Promise<textContent>}
   */
  async deleteCustomer(row, allowRegistrationAfterDelete = true) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.customersListTableToggleDropDown.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(
        `${this.customersListTableToggleDropDown
          .replace('%ROW', row).replace('%COLUMN', 'actions')}[aria-expanded='true']`,
        {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.customersListTableDeleteLink.replace('%ROW', row).replace('%COLUMN', 'actions')),
      this.page.waitForSelector(this.deleteCustomerModal, {visible: true}),
    ]);
    await this.chooseRegistrationAndDelete(allowRegistrationAfterDelete);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Customers with Bulk Actions
   * @param allowRegistrationAfterDelete, Deletion method to choose in modal
   * @return {Promise<textContent>}
   */
  async deleteCustomersBulkActions(allowRegistrationAfterDelete = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.page.waitForSelector(this.deleteCustomerModal, {visible: true}),
    ]);
    await this.chooseRegistrationAndDelete(allowRegistrationAfterDelete);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Choose if customer can register after delete and perform delete action
   * @param allowRegistrationAfterDelete
   * @return {Promise<void>}
   */
  async chooseRegistrationAndDelete(allowRegistrationAfterDelete) {
    // Choose deletion method
    if (allowRegistrationAfterDelete) await this.page.click(this.deleteCustomerModalMethodInput.replace('%ID', '0'));
    else await this.page.click(this.deleteCustomerModalMethodInput.replace('%ID', '1'));
    // Click on delete button and wait for action to finish
    await this.clickAndWaitForNavigation(this.deleteCustomerModalDeleteButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
  }

  /**
   * Enable / disable customers by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeCustomersEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
