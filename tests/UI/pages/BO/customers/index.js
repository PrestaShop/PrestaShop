require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Customers extends BOBasePage {
  constructor() {
    super();

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
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.customerGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of customers
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterCustomers(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.customerFilterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.customerFilterColumnInput(filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Filter Customers by select that contains values (Yes/No)
   * @param page
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterCustomersSwitch(page, filterBy, value) {
    await this.filterCustomers(
      page,
      'select',
      filterBy,
      value ? 'Yes' : 'No',
    );
  }

  /**
   * Get Value of columns Enabled, Newsletter or Partner Offers
   * @param page
   * @param row, row in table
   * @param column, column to check
   * @return {Promise<boolean>}
   */
  async getToggleColumnValue(page, row, column) {
    await this.waitForVisibleSelector(page, this.customersListTableColumn(row, column), 2000);
    return this.elementVisible(page, this.customersListColumnValidIcon(row, column), 100);
  }

  /**
   * Quick edit toggle column value
   * @param page
   * @param row, row in table
   * @param column, column to update
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>}, return true if action is done, false otherwise
   */
  async updateToggleColumnValue(page, row, column, valueWanted = true) {
    if (await this.getToggleColumnValue(page, row, column) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, `${this.customersListTableColumn(row, column)} i`);
      return true;
    }
    return false;
  }

  /**
   * get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCustomers(page, row, column) {
    return this.getTextContent(page, this.customersListTableColumn(row, column));
  }

  /**
   * * Get all information for a customer in table
   * @param page
   * @param row, row of customer in table
   * @param row
   * @returns {Promise<{firstName: string, lastName: string, newsletter: boolean, socialTitle: string,
   * id: string, partnerOffers: boolean, email: string, sales: string, status: boolean}>}
   */
  async getCustomerFromTable(page, row) {
    return {
      id: await this.getTextColumnFromTableCustomers(page, row, 'id_customer'),
      socialTitle: await this.getTextColumnFromTableCustomers(page, row, 'social_title'),
      firstName: await this.getTextColumnFromTableCustomers(page, row, 'firstname'),
      lastName: await this.getTextColumnFromTableCustomers(page, row, 'lastname'),
      email: await this.getTextColumnFromTableCustomers(page, row, 'email'),
      sales: await this.getTextColumnFromTableCustomers(page, row, 'total_spent'),
      status: await this.getToggleColumnValue(page, row, 'active'),
      newsletter: await this.getToggleColumnValue(page, row, 'newsletter'),
      partnerOffers: await this.getToggleColumnValue(page, row, 'optin'),
    };
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
      const rowContent = await this.getTextColumnFromTableCustomers(page, i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to Customer Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewCustomerPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCustomerLink);
  }

  /**
   * View Customer in list
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToViewCustomerPage(page, row) {
    await Promise.all([
      page.click(this.customersListTableToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.customersListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.customersListTableViewLink(row));
  }

  /**
   * Go to Edit customer page
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCustomerPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.customersListTableEditLink(row));
  }

  /**
   * Delete Customer
   * @param page
   * @param row, row in table
   * @param allowRegistrationAfterDelete, Deletion method to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCustomer(page, row, allowRegistrationAfterDelete = true) {
    // Click on dropDown
    await Promise.all([
      page.click(this.customersListTableToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.customersListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.customersListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, this.deleteCustomerModal),
    ]);
    await this.chooseRegistrationAndDelete(page, allowRegistrationAfterDelete);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all Customers with Bulk Actions
   * @param page
   * @param allowRegistrationAfterDelete, Deletion method to choose in modal
   * @returns {Promise<string>}
   */
  async deleteCustomersBulkActions(page, allowRegistrationAfterDelete = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, this.deleteCustomerModal),
    ]);
    await this.chooseRegistrationAndDelete(page, allowRegistrationAfterDelete);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Choose if customer can register after delete and perform delete action
   * @param page
   * @param allowRegistrationAfterDelete
   * @return {Promise<void>}
   */
  async chooseRegistrationAndDelete(page, allowRegistrationAfterDelete) {
    // Choose deletion method
    if (allowRegistrationAfterDelete) {
      await page.click(this.deleteCustomerModalMethodInput(0));
    } else {
      await page.click(this.deleteCustomerModalMethodInput(1));
    }

    // Click on delete button and wait for action to finish
    await this.clickAndWaitForNavigation(page, this.deleteCustomerModalDeleteButton);
    await this.waitForVisibleSelector(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable customers by Bulk Actions
   * @param page
   * @param enable
   * @returns {Promise<string>}
   */
  async changeCustomersEnabledColumnBulkActions(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(page, sortColumnDiv);
  }

  /**
   * Set required fields
   * @param page
   * @param id
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setRequiredFields(page, id, valueWanted = true) {
    // Check if form is open
    if (await this.elementNotVisible(page, `${this.requiredFieldsForm}.show`, 1000)) {
      await Promise.all([
        this.waitForSelectorAndClick(page, this.setRequiredFieldsButton),
        this.waitForVisibleSelector(page, `${this.requiredFieldsForm}.show`),
      ]);
    }

    // Click on checkbox if not selected
    const isCheckboxSelected = await this.isCheckboxSelected(page, this.requiredFieldCheckBox(id));
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.requiredFieldCheckBox(id)} + i`, el => el.click());
    }

    // Save setting
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  // Export methods
  /**
   * Click on link to export customers to a csv file
   * @param page
   * @return {Promise<*>}
   */
  async exportDataToCsv(page) {
    await Promise.all([
      page.click(this.customerGridActionsButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.gridActionExportLink),
      page.waitForSelector(`${this.gridActionDropDownMenu}.show`, {state: 'hidden'}),
    ]);
    return download.path();
  }

  /**
   * Get customer from table in csv format
   * @param page
   * Adding an empty csv case after email is for company column which is always empty (Except when B2B mode is enabled)
   * @param row
   * @return {Promise<string>}
   */
  async getCustomerInCsvFormat(page, row) {
    const customer = await this.getCustomerFromTable(page, row);
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
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }
}

module.exports = new Customers();
