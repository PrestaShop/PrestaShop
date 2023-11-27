import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : Product comments, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class ProductComments extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly reviewsTable: (table: string) => string;

  private readonly reviewsTableBody: (table: string) => string;

  private readonly reviewsTableRows: (table: string) => string;

  private readonly reviewsTableRow: (table: string, row: number) => string;

  private readonly reviewsTableColumn: (table: string, row: number, column: string) => string;

  private readonly reviewsTableEmptyRows: (table: string) => string;

  private readonly deleteReviewButton: (table: string, row: number) => string;

  private readonly toggleDropdownButton: (table: string, row: number) => string;

  private readonly approveWaitingReviewButton: (table: string, row: number) => string;

  private readonly confirmNotAbusiveReviewButton: (table: string, row: number) => string;

  private readonly confirmReviewDeletionButton: string;

  /**
   * @constructs
   * Setting selectors to use on product comments module configuration  page
   */
  constructor() {
    super();
    this.pageTitle = 'Product Comments';

    // Selectors
    // Table Selectors
    this.reviewsTable = (table: string) => `#table-${table}-productcomments-list`;
    this.reviewsTableBody = (table: string) => `${this.reviewsTable(table)} tbody`;
    this.reviewsTableRows = (table: string) => `${this.reviewsTableBody(table)} tr`;
    this.reviewsTableRow = (table: string, row: number) => `${this.reviewsTableRows(table)}:nth-child(${row})`;
    this.reviewsTableColumn = (table: string, row: number, column: string) => `${this.reviewsTableRow(table, row)}`
      + ` td.product-comment-${column}`;
    this.reviewsTableEmptyRows = (table: string) => `${this.reviewsTableRows(table)} td.list-empty`;
    // Buttons Selectors
    this.deleteReviewButton = (table: string, row: number) => `${this.reviewsTableRow(table, row)} .btn-group [title='Delete']`;
    this.toggleDropdownButton = (table: string, row: number) => `${this.reviewsTableRow(table, row)} button.dropdown-toggle`;
    // "Waiting for approval" buttons selectors
    this.approveWaitingReviewButton = (table: string, row: number) => `${this.reviewsTableRow(table, row)} a.btn-success`;
    // "Reported reviews" buttons selectors
    this.confirmNotAbusiveReviewButton = (table: string, row: number) => `${this.reviewsTableRow(table, row)} .dropdown-toggle a`;
    // Delete review confirmation modal selectors
    this.confirmReviewDeletionButton = '#popup_ok';
  }

  /* Methods */

  /**
   * Get the review count for a table
   * @param page {Page} Browser tab
   * @param table {String} The review table (3 options available: 'waiting-approval', 'reported', 'approved')
   * @returns {Promise<number>}
   */
  async getTableReviewCount(page: Page, table: string): Promise<number> {
    if (await this.elementVisible(page, this.reviewsTableEmptyRows(table), 3000)) {
      return 0;
    }
    const selector = this.reviewsTableRows(table);

    return page.$$eval(selector, (rows) => rows.length);
  }

  /**
   * Get the review count for the 'waiting approval' table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getWaitingApprovalReviewCount(page: Page): Promise<number> {
    return this.getTableReviewCount(page, 'waiting-approval');
  }

  /**
   * Get the review count for the 'reported review' table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getReportedReviewCount(page: Page): Promise<number> {
    return this.getTableReviewCount(page, 'reported');
  }

  /**
   * Get the review count for the 'approved review' table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getApprovedReviewCount(page: Page): Promise<number> {
    return this.getTableReviewCount(page, 'approved');
  }

  /**
   * Open  the button dropdown to perform some action
   * @param page {Page} Browser tab
   * @param table {String} The review table (2 options available: 'waiting-approval', 'reported')
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async openProductReviewDropdown(page: Page, table: string, row: number = 1): Promise<void> {
    await this.waitForVisibleSelector(page, this.toggleDropdownButton(table, row));
    await page.locator(this.toggleDropdownButton(table, row)).click();
  }

  /**
   * Get all the content for a review in review table
   * @param page {Page} The browser tab
   * @param table {String} The review table (3 options available: 'waiting-approval', 'reported', 'approved')
   * @param row {number} The review row (default is set to 1)
   * @returns {Promise<{id: string, title: string, content: string, rating: string, author: string,
   * product: string, date: string}>}
   */
  async getReviewDataFromTable(page: Page, table: string, row: number = 1) {
    return {
      id: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'id')),
      title: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'title')),
      content: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'content')),
      rating: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'rating')),
      author: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'author')),
      product: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'product-name')),
      date: await this.getTextContent(page, this.reviewsTableColumn(table, row, 'date')),
    };
  }

  /**
   * Get all the content for a review in 'waiting approval' table
   * @param page {Page} Browser tab
   * @returns {Promise<{id: string, title: string, content: string, rating: string, author: string,
   * product: string, date: string}>}
   */
  getReviewDataFromWaitingApprovalTable(page: Page) {
    return this.getReviewDataFromTable(page, 'waiting-approval');
  }

  /**
   * Get all the content for a review in 'reported review' table
   * @param page {Page} Browser tab
   * @returns {Promise<{id: string, title: string, content: string, rating: string, author: string,
   * product: string, date: string}>}
   */
  getReviewDataFromReportedReviewTable(page: Page) {
    return this.getReviewDataFromTable(page, 'reported');
  }

  /**
   * Get all the content for a review in 'approved review' table
   * @param page {Page} Browser tab
   * @returns {Promise<{id: string, title: string, content: string, rating: string, author: string,
   * product: string, date: string}>}
   */
  getReviewDataFromApprovedReviewTable(page: Page) {
    return this.getReviewDataFromTable(page, 'approved');
  }

  /**
   * Approve a review in the "waiting for approval table"
   * @param page {Page} Browser tab
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async approveReview(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.approveWaitingReviewButton('waiting-approval', row));
  }

  /**
   * Delete a review in a table
   * @param page {Page} Browser tab
   * @param table {String} The reviews table
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async deleteReview(page: Page, table: string, row: number = 1): Promise<void> {
    await page.locator(this.deleteReviewButton(table, row)).click();
    await this.clickAndWaitForURL(page, this.confirmReviewDeletionButton);
  }

  /**
   * Delete a review in the "waiting approval" table
   * @param page {Page} Browser tab
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async deleteWaitingApprovalReview(page: Page, row: number = 1): Promise<void> {
    // Need to open dropdown before delete
    await this.openProductReviewDropdown(page, 'waiting-approval', row);
    await this.deleteReview(page, 'waiting-approval', row);
  }

  /**
   * Delete a review in the "reported review" table
   * @param page {Page} Browser tab
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async deleteReportedReview(page: Page, row: number = 1): Promise<void> {
    await this.deleteReview(page, 'reported', row);
  }

  /**
   * Delete a review in the "approved review" table
   * @param page {Page} Browser tab
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async deleteApprovedReview(page: Page, row: number = 1): Promise<void> {
    await this.deleteReview(page, 'approved', row);
  }

  /**
   * Confirm a review in the "reported review" table
   * @param page {Page} Browser tab
   * @param table {string} The review table
   * @param row {number} The review row
   * @returns {Promise<void>}
   */
  async confirmNotAbusiveReview(page: Page, table: string, row: number = 1): Promise<void> {
    await this.openProductReviewDropdown(page, 'reported', row);
    await this.waitForVisibleSelector(page, this.confirmNotAbusiveReviewButton(table, row));
    await page.locator(this.confirmNotAbusiveReviewButton(table, row)).click();
  }
}

export default new ProductComments();
