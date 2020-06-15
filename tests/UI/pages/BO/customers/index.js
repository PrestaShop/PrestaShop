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
    this.customersListTableRow = row => `${this.customersListForm} tbody tr:nth-child(${row})`;
    this.customersListTableColumn = (row, column) => `${this.customersListTableRow(row)} td.column-${column}`;
    this.customersListTableActionsColumn = row => this.customersListTableColumn(row, 'actions');
    this.customersListTableEditLink = row => `${this.customersListTableActionsColumn(row)}`
      + ' a[data-original-title=\'Edit\']';
    this.customersListTableToggleDropDown = row => `${this.customersListTableActionsColumn(row)}`
      + ' a[data-toggle=\'dropdown\']';
    this.customersListTableViewLink = row => `${this.customersListTableActionsColumn(row)} a[href*='/view']`;
    this.customersListTableDeleteLink = row => `${this.customersListTableActionsColumn(row)}`
      + ' a[data-customer-delete-url]';
    this.customersListColumnValidIcon = (row, column) => `${this.customersListTableColumn(row, column)}`
      + ' i.grid-toggler-icon-valid';
    // Filters
    this.customerFilterColumnInput = filterBy => `${this.customersListForm} #customer_${filterBy}`;
    this.filterSearchButton = `${this.customersListForm} button[name='customer[actions][search]']`;
    this.filterResetButton = `${this.customersListForm} button[name='customer[actions][reset]']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.customersListForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.customersListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.customersListForm} #customer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.customersListForm} #customer_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.customersListForm} #customer_grid_bulk_action_delete_selection`;
    // Sort Selectors
    this.tableHead = `${this.customersListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.customerGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.customerGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.customerGridPanel} [aria-label='Previous']`;
    // Required field section
    this.setRequiredFieldsButton = 'button[data-target=\'#customerRequiredFieldsContainer\']';
    this.requiredFieldCheckBox = id => `#required_fields_required_fields_${id}`;
    this.requiredFieldsForm = '#customerRequiredFieldsContainer';
    this.saveButton = `${this.requiredFieldsForm} button`;
    // Modal Dialog
    this.deleteCustomerModal = '#customer_grid_delete_customers_modal.show';
    this.deleteCustomerModalDeleteButton = `${this.deleteCustomerModal} button.js-submit-delete-customers`;
    this.deleteCustomerModalMethodInput = id => `${this.deleteCustomerModal} #delete_customers_delete_method_${id}`;
    // Grid Actions
    this.customerGridActionsButton = '#customer-grid-actions-button';
    this.gridActionDropDownMenu = 'div.dropdown-menu[aria-labelledby=\'customer-grid-actions-button\']';
    this.gridActionExportLink = `${this.gridActionDropDownMenu} a[href*='/export']`;
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
    return this.getNumberFromText(this.customerGridTitle);
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
   * Filter list of customers
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCustomers(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.customerFilterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          this.customerFilterColumnInput(filterBy),
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
   * @return {Promise<boolean>}
   */
  async getToggleColumnValue(row, column) {
    await this.waitForVisibleSelector(this.customersListTableColumn(row, column), 2000);
    return this.elementVisible(this.customersListColumnValidIcon(row, column), 100);
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
      await this.clickAndWaitForNavigation(`${this.customersListTableColumn(row, column)} i`);
      return true;
    }
    return false;
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableCustomers(row, column) {
    return this.getTextContent(this.customersListTableColumn(row, column));
  }

  /**
   * Get all information for a customer in table
   * @param row, row of customer in table
   * @return {Promise<{object}>}
   */
  async getCustomerFromTable(row) {
    return {
      id: await this.getTextColumnFromTableCustomers(row, 'id_customer'),
      socialTitle: await this.getTextColumnFromTableCustomers(row, 'social_title'),
      firstName: await this.getTextColumnFromTableCustomers(row, 'firstname'),
      lastName: await this.getTextColumnFromTableCustomers(row, 'lastname'),
      email: await this.getTextColumnFromTableCustomers(row, 'email'),
      sales: await this.getTextColumnFromTableCustomers(row, 'total_spent'),
      status: await this.getToggleColumnValue(row, 'active'),
      newsletter: await this.getToggleColumnValue(row, 'newsletter'),
      partnerOffers: await this.getToggleColumnValue(row, 'optin'),
    };
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
      const rowContent = await this.getTextColumnFromTableCustomers(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
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
      this.page.click(this.customersListTableToggleDropDown(row)),
      this.waitForVisibleSelector(`${this.customersListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.customersListTableViewLink(row));
  }

  /**
   * Go to Edit customer page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCustomerPage(row) {
    await this.clickAndWaitForNavigation(this.customersListTableEditLink(row));
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
      this.page.click(this.customersListTableToggleDropDown(row)),
      this.waitForVisibleSelector(`${this.customersListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.customersListTableDeleteLink(row)),
      this.waitForVisibleSelector(this.deleteCustomerModal),
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
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(this.deleteCustomerModal),
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
    if (allowRegistrationAfterDelete) await this.page.click(this.deleteCustomerModalMethodInput(0));
    else await this.page.click(this.deleteCustomerModalMethodInput(1));
    // Click on delete button and wait for action to finish
    await this.clickAndWaitForNavigation(this.deleteCustomerModalDeleteButton);
    await this.waitForVisibleSelector(this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable customers by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeCustomersEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton, 'networkidle');
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  /**
   * Set required fields
   * @param id
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setRequiredFields(id, valueWanted = true) {
    // Check if form is open
    if (await this.elementNotVisible(`${this.requiredFieldsForm}.show`, 1000)) {
      await Promise.all([
        this.waitForSelectorAndClick(this.setRequiredFieldsButton),
        this.waitForVisibleSelector(`${this.requiredFieldsForm}.show`),
      ]);
    }

    // Click on checkbox if not selected
    const isCheckboxSelected = await this.isCheckboxSelected(this.requiredFieldCheckBox(id));
    if (valueWanted !== isCheckboxSelected) {
      await this.page.$eval(`${this.requiredFieldCheckBox(id)} + i`, el => el.click());
    }

    // Save setting
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  // Export methods
  /**
   * Click on link to export customers to a csv file
   * @return {Promise<*>}
   */
  async exportDataToCsv() {
    await Promise.all([
      this.page.click(this.customerGridActionsButton),
      this.waitForVisibleSelector(`${this.gridActionDropDownMenu}.show`),
    ]);

    const [download] = await Promise.all([
      this.page.waitForEvent('download'),
      this.page.click(this.gridActionExportLink),
      this.page.waitForSelector(`${this.gridActionDropDownMenu}.show`, {state: 'hidden'}),
    ]);
    return download.path();
  }

  /**
   * Get customer from table in csv format
   * Adding an empty csv case after email is for company column which is always empty (Except when B2B mode is enabled)
   * @param row
   * @return {Promise<string>}
   */
  async getCustomerInCsvFormat(row) {
    const customer = await this.getCustomerFromTable(row);
    return `${customer.id};`
      + `${customer.socialTitle};`
      + `${customer.firstName};`
      + `${customer.lastName};`
      + `${customer.email};;`
      + `${customer.sales !== '--' ? customer.sales : ''};`
      + `${customer.status ? 1 : 0};`
      + `${customer.newsletter ? 1 : 0};`
      + `${customer.partnerOffers ? 1 : 0}`;
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel() {
    return this.getTextContent(this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(number) {
    await this.selectByVisibleText(this.paginationLimitSelect, number);
    return this.getPaginationLabel();
  }

  /**
   * Click on next
   * @returns {Promise<string>}
   */
  async paginationNext() {
    await this.clickAndWaitForNavigation(this.paginationNextLink);
    return this.getPaginationLabel();
  }

  /**
   * Click on previous
   * @returns {Promise<string>}
   */
  async paginationPrevious() {
    await this.clickAndWaitForNavigation(this.paginationPreviousLink);
    return this.getPaginationLabel();
  }
};
