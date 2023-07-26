import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * AuthorizationServer page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AuthorizationServer extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newAuthorizedAppLink: string;

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
   * Setting up texts and selectors to use on AuthorizationServer page
   */
  constructor() {
    super();

    this.pageTitle = `Authorization Server Management • ${global.INSTALL.SHOP_NAME}`;

    // Selectors

    // Header
    this.newAuthorizedAppLink = '#page-header-desc-configuration-addApplication';

    // Selectors grid panel
    this.gridPanel = '#authorized_application_grid_panel';
    this.gridHeader = `${this.gridPanel} .card-header`;
    this.gridHeaderTitle = `${this.gridHeader} h3`;

    // Table rows and columns
    this.gridTable = '#authorized_application_grid_table';
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
    this.confirmDeleteModal = '#authorized_application-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /*
  Methods
   */
  /* Header methods */
  /**
   * Go to new Authorized App page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewAuthorizedAppPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newAuthorizedAppLink);
  }

  /**
   * Go to edit Authorized App page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditAuthorizedAppPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.gridTableEditLink(row));
  }

  /**
   * Go to view Authorized App page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToViewAuthorizedAppPage(page: Page, row: number): Promise<void> {
    // Click on dropDown
    await Promise.all([
      page.click(this.gridTableToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.gridTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete
    return this.clickAndWaitForURL(page, this.gridTableViewLink(row));
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
   * Delete webservice key
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteAuthorizationApplication(page: Page, row: number): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.click(this.gridTableToggleDropDown(row)),
      this.waitForVisibleSelector(page, `${this.gridTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    // Click on delete
    await Promise.all([
      page.click(this.gridTableDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await page.click(this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AuthorizationServer();
