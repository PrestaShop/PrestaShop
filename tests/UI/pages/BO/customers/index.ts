import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Customers page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Customers extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly addNewCustomerLink: string;

  private readonly customerGridPanel: string;

  private readonly customerGridTitle: string;

  private readonly CustomersEmptyTable: string;

  private readonly customersListForm: string;

  private readonly customersListTableRow: (row: number) => string;

  private readonly customersListTableColumn: (row: number, column: string) => string;

  private readonly customersListToggleColumn: (row: number, column: string) => string;

  private readonly customersListToggleColumnInput: (row: number, column: string) => string;

  private readonly customersListTableActionsColumn: (row: number) => string;

  private readonly customersListTableEditLink: (row: number) => string;

  private readonly customersListTableToggleDropDown: (row: number) => string;

  private readonly customersListTableViewLink: (row: number) => string;

  private readonly customersListTableDeleteLink: (row: number) => string;

  private readonly customerFilterColumnInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly setRequiredFieldsButton: string;

  private readonly requiredFieldCheckBox: (id: number) => string;

  private readonly requiredFieldsForm: string;

  private readonly saveButton: string;

  private readonly deleteCustomerModal: string;

  private readonly deleteCustomerModalDeleteButton: string;

  private readonly deleteCustomerModalMethodInput: (id: number) => string;

  private readonly customerGridActionsButton: string;

  private readonly gridActionDropDownMenu: string;

  private readonly gridActionExportLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on customers page
   */
  constructor() {
    super();

    this.pageTitle = `Customers • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewCustomerLink = '#page-header-desc-configuration-add[title=\'Add new customer\']';

    // List of customers
    this.customerGridPanel = '#customer_grid_panel';
    this.customerGridTitle = `${this.customerGridPanel} h3.card-header-title`;
    this.customersListForm = '#customer_grid';
    this.CustomersEmptyTable = `${this.customersListForm} tbody div.grid-table-empty`;
    this.customersListTableRow = (row: number) => `${this.customersListForm} tbody tr:nth-child(${row})`;
    this.customersListTableColumn = (row: number, column: string) => `${this.customersListTableRow(row)} td.column-${column}`;
    this.customersListToggleColumn = (row: number, column: string) => `${this.customersListTableColumn(row, column)} .ps-switch`;
    this.customersListToggleColumnInput = (row: number, column: string) => `${this.customersListToggleColumn(row, column)} input`;
    this.customersListTableActionsColumn = (row: number) => this.customersListTableColumn(row, 'actions');
    this.customersListTableEditLink = (row: number) => `${this.customersListTableActionsColumn(row)} a.grid-edit-row-link`;
    this.customersListTableToggleDropDown = (row: number) => `${this.customersListTableActionsColumn(row)}`
      + ' a[data-toggle=\'dropdown\']';
    this.customersListTableViewLink = (row: number) => `${this.customersListTableActionsColumn(row)} a.grid-view-row-link`;
    this.customersListTableDeleteLink = (row: number) => `${this.customersListTableActionsColumn(row)} a.grid-delete-row-link`;

    // Filters
    this.customerFilterColumnInput = (filterBy: string) => `${this.customersListForm} #customer_${filterBy}`;
    this.filterSearchButton = `${this.customersListForm} .grid-search-button`;
    this.filterResetButton = `${this.customersListForm} .grid-reset-button`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.customersListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.customersListForm} button.dropdown-toggle`;
    this.bulkActionsEnableButton = `${this.customersListForm} #customer_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.customersListForm} #customer_grid_bulk_action_disable_selection`;
    this.bulkActionsDeleteButton = `${this.customersListForm} #customer_grid_bulk_action_delete_selection`;

    // Sort Selectors
    this.tableHead = `${this.customersListForm} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.customerGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.customerGridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.customerGridPanel} [data-role='previous-page-link']`;

    // Required field section
    this.setRequiredFieldsButton = 'button[data-target=\'#customerRequiredFieldsContainer\']';
    this.requiredFieldCheckBox = (id: number) => `#required_fields_required_fields_${id}`;
    this.requiredFieldsForm = '#customerRequiredFieldsContainer';
    this.saveButton = `${this.requiredFieldsForm} button`;

    // Modal Dialog
    this.deleteCustomerModal = '#customer_grid_delete_customers_modal.show';
    this.deleteCustomerModalDeleteButton = `${this.deleteCustomerModal} button.js-submit-delete-customers`;
    this.deleteCustomerModalMethodInput = (id: number) => `${this.deleteCustomerModal} #delete_customers_delete_method_${id}`;

    // Grid Actions
    this.customerGridActionsButton = '#customer-grid-actions-button';
    this.gridActionDropDownMenu = '#customer-grid-actions-dropdown-menu';
    this.gridActionExportLink = '#customer-grid-action-export';
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.customerGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of customers
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterCustomers(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
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
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Filter Customers by select that contains values (Yes/No)
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterCustomersSwitch(page: Page, filterBy: string, value: string): Promise<void> {
    await this.filterCustomers(
      page,
      'select',
      filterBy,
      value === '1' ? 'Yes' : 'No',
    );
  }

  /**
   * Filter customer by registration date from and date to
   * @param page {Page} Browser tab
   * @param dateFrom {string} Date from to filter with
   * @param dateTo {string} Date to to filter with
   * @returns {Promise<void>}
   */
  async filterCustomersByRegistration(page: Page, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.customerFilterColumnInput('date_add_from')).fill(dateFrom);
    await page.locator(this.customerFilterColumnInput('date_add_to')).fill(dateTo);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get Value of columns Enabled, Newsletter or Partner Offers
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to check
   * @return {Promise<boolean>}
   */
  async getToggleColumnValue(page: Page, row: number, column: string): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.customersListToggleColumnInput(row, column)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Get customer status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getCustomerStatus(page: Page, row: number): Promise<boolean> {
    return this.getToggleColumnValue(page, row, 'active');
  }

  /**
   * Get newsletter status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getNewsletterStatus(page: Page, row: number): Promise<boolean> {
    return this.getToggleColumnValue(page, row, 'newsletter');
  }

  /**
   * Get partner offers status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getPartnerOffersStatus(page: Page, row: number): Promise<boolean> {
    return this.getToggleColumnValue(page, row, 'optin');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {String} Column to update
   * @param valueWanted {boolean} True if we want to enable, false to disable
   * @return {Promise<string|null|false>} Return message if action performed, false otherwise
   */
  async setToggleColumnValue(page: Page, row: number, column: string, valueWanted: boolean = true):
    Promise<string | null | false> {
    if (await this.getToggleColumnValue(page, row, column) !== valueWanted) {
      // Click and wait for message
      const [message] = await Promise.all([
        this.getGrowlMessageContent(page),
        page.click(this.customersListToggleColumn(row, column)),
      ]);

      await this.closeGrowlMessage(page);
      return message;
    }

    return false;
  }

  /**
   * Set customer status in a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable customer
   * @return {Promise<boolean>}
   */
  setCustomerStatus(page: Page, row: number, valueWanted: boolean = true): Promise<string | null | false> {
    return this.setToggleColumnValue(page, row, 'active', valueWanted);
  }

  /**
   * Set newsletter status in a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable newsletter status
   * @return {Promise<boolean>}
   */
  setNewsletterStatus(page: Page, row: number, valueWanted: boolean = true): Promise<string | null | false> {
    return this.setToggleColumnValue(page, row, 'newsletter', valueWanted);
  }

  /**
   * Set partner offers status in a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable partner offers status
   * @return {Promise<boolean>}
   */
  setPartnerOffersStatus(page: Page, row: number, valueWanted: boolean = true): Promise<string | null | false> {
    return this.setToggleColumnValue(page, row, 'optin', valueWanted);
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column text to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCustomers(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.customersListTableColumn(row, column));
  }

  /**
   * * Get all information for a customer in table
   * @param page {Page} Browser tab
   * @param row {number} Row of customer in table
   * @returns {Promise<object>}
   */
  async getCustomerFromTable(page: Page, row: number) {
    return {
      id: await this.getTextColumnFromTableCustomers(page, row, 'id_customer'),
      socialTitle: await this.getTextColumnFromTableCustomers(page, row, 'social_title'),
      firstName: await this.getTextColumnFromTableCustomers(page, row, 'firstname'),
      lastName: await this.getTextColumnFromTableCustomers(page, row, 'lastname'),
      email: await this.getTextColumnFromTableCustomers(page, row, 'email'),
      group: await this.getTextColumnFromTableCustomers(page, row, 'default_group'),
      sales: await this.getTextColumnFromTableCustomers(page, row, 'total_spent'),
      status: await this.getCustomerStatus(page, row),
      newsletter: await this.getNewsletterStatus(page, row),
      partnerOffers: await this.getPartnerOffersStatus(page, row),
    };
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCustomers(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to Customer Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewCustomerPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCustomerLink);
  }

  /**
   * View Customer in list
   * @param page {Page} Browser tab
   * @param row {number} row on table
   * @return {Promise<void>}
   */
  async goToViewCustomerPage(page: Page, row: number): Promise<void> {
    await Promise.all([
      page.click(this.customersListTableToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.customersListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForURL(page, this.customersListTableViewLink(row));
  }

  /**
   * Go to Edit customer page
   * @param page {Page} Browser tab
   * @param row {number} row on table
   * @return {Promise<void>}
   */
  async goToEditCustomerPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.customersListTableEditLink(row));
  }

  /**
   * Delete Customer
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param allowRegistrationAfterDelete {boolean} True if we want to allow registration after delete
   * @returns {Promise<string>}
   */
  async deleteCustomer(page: Page, row: number, allowRegistrationAfterDelete: boolean = true): Promise<string> {
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all Customers with Bulk Actions
   * @param page {Page} Browser tab
   * @param allowRegistrationAfterDelete {boolean} True if we want to allow registration after delete
   * @returns {Promise<string>}
   */
  async deleteCustomersBulkActions(page: Page, allowRegistrationAfterDelete: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose if customer can register after delete and perform delete action
   * @param page {Page} Browser tab
   * @param allowRegistrationAfterDelete {boolean} True if we want to allow registration after delete
   * @return {Promise<void>}
   */
  async chooseRegistrationAndDelete(page: Page, allowRegistrationAfterDelete: boolean): Promise<void> {
    // Choose deletion method
    await this.setChecked(page, this.deleteCustomerModalMethodInput(allowRegistrationAfterDelete ? 0 : 1));

    // Click on delete button and wait for action to finish
    await this.clickAndWaitForURL(page, this.deleteCustomerModalDeleteButton);
    await this.waitForVisibleSelector(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable customers by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we want to enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForLoadState(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Set required fields
   * @param page {Page} Browser tab
   * @param id {number} Value of checkbox id
   * @param valueWanted {boolean} True if we want to select required field checkbox
   * @returns {Promise<string>}
   */
  async setRequiredFields(page: Page, id: number, valueWanted: boolean = true): Promise<string> {
    // Check if form is open
    if (await this.elementNotVisible(page, `${this.requiredFieldsForm}.show`, 1000)) {
      await Promise.all([
        this.waitForSelectorAndClick(page, this.setRequiredFieldsButton),
        this.waitForVisibleSelector(page, `${this.requiredFieldsForm}.show`),
      ]);
    }

    // Click on checkbox if not selected
    await this.setCheckedWithIcon(page, this.requiredFieldCheckBox(id), valueWanted);

    // Save setting
    await this.clickAndWaitForLoadState(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  // Export methods
  /**
   * Click on link to export customers to a csv file
   * @param page {Page} Browser tab
   * @return {Promise<string|null>}
   */
  async exportDataToCsv(page: Page): Promise<string | null> {
    await Promise.all([
      page.click(this.customerGridActionsButton),
      this.waitForVisibleSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    const [downloadPath] = await Promise.all([
      this.clickAndWaitForDownload(page, this.gridActionExportLink),
      this.waitForHiddenSelector(page, `${this.gridActionDropDownMenu}.show`),
    ]);

    return downloadPath;
  }

  /**
   * Get customer from table in csv format
   * @param page {Page} Browser tab
   * Adding an empty csv case after email is for company column which is always empty (Except when B2B mode is enabled)
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async getCustomerInCsvFormat(page: Page, row: number): Promise<string> {
    const customer = await this.getCustomerFromTable(page, row);

    return `${customer.id};`
      + `${customer.socialTitle};`
      + `${customer.firstName};`
      + `${customer.lastName};`
      + `${customer.email};`
      + `${customer.group};;`
      + `${customer.sales !== '--' ? customer.sales : ''};`
      + `${customer.status ? 1 : 0};`
      + `${customer.newsletter ? 1 : 0};`
      + `${customer.partnerOffers ? 1 : 0}`;
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.scrollTo(page, this.paginationNextLink);
    await this.clickAndWaitForURL(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.scrollTo(page, this.paginationPreviousLink);
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Get text when customers table is empty
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTextWhenTableIsEmpty(page: Page): Promise<string> {
    return this.getTextContent(page, this.CustomersEmptyTable, true);
  }
}

export default new Customers();
