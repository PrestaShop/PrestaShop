require('module-alias/register');
const ModuleConfiguration = require('@pages/BO/modules/moduleConfiguration');

/**
 * Module configuration page for module : Product comments, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class ProductComments extends ModuleConfiguration.constructor {
  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    // Selectors
    // "Waiting for approval" table selectors
    this.waitingApprovalTable = '#table-waiting-approval-productcomments-list';
    this.waitingApprovalTableBody = `${this.waitingApprovalTable} tbody`;
    this.waitingApprovalTableRows = `${this.waitingApprovalTableBody} tr.odd`;
    this.waitingApprovalTableRow = row => `${this.waitingApprovalTableRows}:nth-child(${row})`;
    this.waitingApprovalTableColumn = (row, column) => `${this.waitingApprovalTableRow(row)} td.product-comment-${column}`;
    this.approveWaitingReviewButton = row => `${this.waitingApprovalTableRow(row)} a.btn-success`;
    this.waitingApprovalToggleDropdownButton = row => `${this.waitingApprovalTableRow(row)} button.dropdown-toggle`;
    this.waitingApprovalReviewDeleteButton = row => `${this.waitingApprovalTableRow(row)} a.delete`;

    // "Reported reviews" table selectors
    this.reportedReviewsTable = '#table-reported-productcomments-list';
    this.reportedReviewsTableBody = `${this.reportedReviewsTable} tbody`;
    this.reportedReviewsTableRows = `${this.reportedReviewsTableBody} tr.odd`;
    this.reportedReviewsTableRow = row => `${this.reportedReviewsTableRows}:nth-child(${row})`;
    this.reportedReviewsTableColumn = (row, column) => `${this.reportedReviewsTableRows} td.product-comment-${column}`;
    this.reportedReviewToggleDropdownButton = row => `${this.reportedReviewsTableRow(row)} button.dropdown-toggle`;
    this.reportedReviewDeleteButton = row => `${this.reportedReviewsTableRow(row)} .btn-group a`;
    this.confirmNotAbusiveReviewButton = row => `${this.reportedReviewsTableRow(row)} .dropdown-toggle a`;

    // "Approved review" table selectors
    this.approvedReviewsTable = '#table-approved-productcomments-list';
    this.approvedReviewsTableBody = `${this.approvedReviewsTable} tbody`;
    this.approvedReviewsTableRows = `${this.approvedReviewsTableBody} tr.odd`;
    this.approvedReviewsTableRow = row => `${this.approvedReviewsTableRows}:nth-child(${row})`;
    this.approvedReviewsTableColumn = (row, column) => `${this.approvedReviewsTableRows} td.product-comment-${column}`;
    this.approvedReviewDeleteButton = row => `${this.approvedReviewsTableRow(row)} .btn-group a`;

    // Delete review confirmation modal selectors
    this.confirmReviewDeletionButton = '#popup_ok';
  }

  /* Methods */

  async getTableReviewCount(page, table) {
    let selector;
    switch (table) {
      case 'waiting':
        selector = this.waitingApprovalTableRows
        break;
      case 'reported':
        selector = this.reportedReviewsTableRows
        break;
      case 'approved':
        selector = this.approvedReviewsTableRows
        break;
      default:
       throw new Error(`Table ${table} not found`)
    }
    return page.$$eval(selector, divs => divs.length);
  }
  /**
   * Open row dropdown for a product review
   * @param page {Page} Browser tab
   * @param table {String} The review table
   * @param row {Number} The review row
   * @return {Promise<void>}
   */
  async openProductReviewDropdown(page, table, row = 1) {
    switch (table) {
      case 'waiting':
        await Promise.all([
          this.waitForVisibleSelector(page, this.waitingApprovalToggleDropdownButton(row)),
          page.click(this.waitingApprovalToggleDropdownButton(row)),
        ]);
        break;
      case 'reported':
        await Promise.all([
          this.waitForVisibleSelector(page, this.reportedReviewToggleDropdownButton(row)),
          page.click(this.reportedReviewToggleDropdownButton(row)),
        ]);
        break;
      default:
      // Do nothing
    }
  }

  /**
   *
   * @param page
   * @param table
   * @param row
   * @returns {Promise
   * <{date: string,
   * product: string,
   * author: string,
   * rating: string,
   * id: string,
   * title: string,
   * content: string}>
   * }
   */
  async getReviewDataFromTable(page, table, row = 1) {
    switch (table) {
      case 'waiting':
        return {
          id: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'id')),
          title: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'title')),
          content: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'content')),
          rating: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'rating')),
          author: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'author')),
          product: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'product-name')),
          date: await this.getTextContent(page, this.waitingApprovalTableColumn(row, 'date')),
        };
      case 'reported':
        return {
          id: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'id')),
          title: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'title')),
          content: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'content')),
          rating: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'rating')),
          author: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'author')),
          product: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'product-name')),
          date: await this.getTextContent(page, this.reportedReviewsTableColumn(row, 'date')),
        };
      case 'approved':
        return {
          id: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'id')),
          title: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'title')),
          content: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'content')),
          rating: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'rating')),
          author: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'author')),
          product: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'product-name')),
          date: await this.getTextContent(page, this.approvedReviewsTableColumn(row, 'date')),
        };
      default:
      // Do nothing
    }
  }

  /**
   *
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async approveReview(page, row = 1) {
    page.click(this.approveWaitingReviewButton(row));
  }

  /**
   *
   * @param page
   * @param table
   * @param row
   * @returns {Promise<void>}
   */
  async deleteReview(page, table, row = 1) {
    switch (table) {
      case 'waiting':
        await this.openProductReviewDropdown(page, 'waiting', row);
        page.click(this.waitingApprovalReviewDeleteButton(row));
        await this.waitForVisibleSelector(page, this.confirmReviewDeletionButton);
        page.click(this.confirmReviewDeletionButton);
        break;
      case 'reported':
        page.click(this.reportedReviewDeleteButton(row));
        await this.waitForVisibleSelector(page, this.confirmReviewDeletionButton);
        page.click(this.confirmReviewDeletionButton);
        break;
      case 'approved':
        page.click(this.approvedReviewDeleteButton(row));
        await this.waitForVisibleSelector(page, this.confirmReviewDeletionButton);
        page.click(this.confirmReviewDeletionButton);
        break;
      default:
      // Do nothing
    }
  }

  /**
   *
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async confirmNotAbusiveReview(page, row = 1) {
    await this.openProductReviewDropdown(page, 'reported', row);
    await this.waitForVisibleSelector(page, this.confirmNotAbusiveReviewButton(row));
    page.click(this.confirmNotAbusiveReviewButton(row));
  }
}

module.exports = new ProductComments();
