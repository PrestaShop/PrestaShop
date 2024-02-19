import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * API Client page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class APIClient extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newApiClientLink: string;

  private readonly gridPanel: string;

  private readonly gridHeader: string;

  private readonly gridHeaderTitle: string;

  private readonly gridTable: string;

  private readonly gridTableBody: string;

  private readonly gridTableEmptyRow: string;

  private readonly gridTableRow: (row: number) => string;

  private readonly gridTableColumn: (row: number, column: string) => string;

  private readonly gridTableColumnAction: (row: number) => string;

  private readonly gridTableToggleDropDown: (row: number) => string;

  private readonly gridTableViewLink: (row: number) => string;

  private readonly gridTableDeleteLink: (row: number) => string;

  private readonly gridTableEditLink: (row: number) => string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on API Client page
   */
  constructor() {
    super();

    this.pageTitle = `API Clients • ${global.INSTALL.SHOP_NAME}`;

    // Selectors

    // Header
    this.newApiClientLink = '#page-header-desc-configuration-addApiClient';

    // Selectors grid panel
    this.gridPanel = '#api_client_grid_panel';
    this.gridHeader = `${this.gridPanel} .card-header`;
    this.gridHeaderTitle = `${this.gridHeader} h3`;

    // Table rows and columns
    this.gridTable = '#api_client_grid_table';
    this.gridTableBody = `${this.gridTable} tbody`;
    this.gridTableEmptyRow = `${this.gridTableBody} tr.empty_row`;
    this.gridTableRow = (row: number) => `${this.gridTable} tbody tr:nth-child(${row})`;
    this.gridTableColumn = (row: number, column: string) => `${this.gridTableRow(row)} td.column-${column}`;
    this.gridTableColumnAction = (row: number) => this.gridTableColumn(row, 'actions');
    this.gridTableToggleDropDown = (row: number) => `${this.gridTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.gridTableViewLink = (row: number) => `${this.gridTableColumnAction(row)} a.grid-view-row-link`;
    this.gridTableDeleteLink = (row: number) => `${this.gridTableColumnAction(row)} a.grid-delete-row-link`;
    this.gridTableEditLink = (row: number) => `${this.gridTableColumnAction(row)} a.grid-edit-row-link`;

    // Delete modal
    this.confirmDeleteModal = '#api_client-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /*
  Methods
   */
  /* Header methods */
  /**
   * Go to new API Client page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewAPIClientPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newApiClientLink);
  }

  /**
   * Go to edit API Client page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditAPIClientPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.gridTableEditLink(row));
  }

  /* Grid methods */
  /**
   * Get text for empty table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTextForEmptyTable(page: Page): Promise<string> {
    return this.getTextContent(page, this.gridTableEmptyRow);
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Order row in table
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, columnName: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.gridTableColumn(row, columnName));
  }

  /**
   * Get Value of column Displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.gridTableColumn(row, 'enabled')} input:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Delete webservice key
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteAPIClient(page: Page, row: number): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.locator(this.gridTableToggleDropDown(row)).click(),
      this.waitForVisibleSelector(page, `${this.gridTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete
    await Promise.all([
      page.locator(this.gridTableDeleteLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await page.locator(this.confirmDeleteButton).click();
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new APIClient();
