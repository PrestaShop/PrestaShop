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
    // Table Selectors
    this.reviewsTable = table => `#table-${table}-productcomments-list`;
    this.reviewsTableBody = table => `${this.reviewsTable(table)} tbody`;
    this.reviewsTableRows = table => `${this.reviewsTableBody(table)} tr`;
    this.reviewsTableRow = (table, row) => `${this.reviewsTableRows(table)}:nth-child(${row})`;
    this.reviewsTableColumn = (table, row, column) => `${this.reviewsTableRow(table, row)} td.product-comment-${column}`;
    // Buttons Selectors
    this.deleteReviewButton = (table, row) => `${this.reviewsTableRow(table, row)} .btn-group [title='Delete']`;
    this.toggleDropdownButton = (table, row) => `${this.reviewsTableRow(table, row)} button.dropdown-toggle`;
    // "Waiting for approval" buttons selectors
    this.approveWaitingReviewButton = (table, row) => `${this.reviewsTableRow(table, row)} a.btn-success`;
    // "Reported reviews" buttons selectors
    this.confirmNotAbusiveReviewButton = (table, row) => `${this.reviewsTableRow(table, row)} .dropdown-toggle a`;
    // Delete review confirmation modal selectors
    this.confirmReviewDeletionButton = '#popup_ok';
  }

  /* Methods */

  /**
   *
   * @param page {Page} Browser tab
   * @param table {String} The review table (3 options available: 'waiting-approval', 'reported', 'approved')
   * @returns {Promise<number|*>}
   */
  async getTableReviewCount(page, table) {
    if (await this.elementVisible(page, ".list-empty", 3)) {
      return 0;
    }
    else {
      let selector = this.reviewsTableRows(table);
      return page.$$eval(selector, divs => divs.length);
    }
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param table {String} The review table (2 options available: 'waiting-approval', 'reported')
   * @param row {Number} The review row
   * @returns {Promise<void>}
   */
  async openProductReviewDropdown(page, table, row = 1) {
    await this.waitForVisibleSelector(page, this.toggleDropdownButton(table, row));
    await page.click(this.toggleDropdownButton(table, row));
  }

  /**
   *
   * @param page {Page} The browser tab
   * @param table {String} The review table (3 options available: 'waiting-approval', 'reported', 'approved')
   * @param row {Number} The review row (default is set to 1)
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
  *
  * @param page {Page} Browser tab
  * @param table {String} The reviews table (the table is set by default)
  * @param row {Number} The review row
  * @returns {Promise<void>}
  */
 async approveReview(page, table = "waiting-approval", row = 1) {
   await page.click(this.approveWaitingReviewButton(table, row));
 }

 /**
  *
  * @param page {Page} Browser tab
  * @param table {String} The reviews table (available options: 'waiting-approval', 'reported', 'deleted'
  * @param row {Number} The review row
  * @returns {Promise<void>}
  */
 async deleteReview(page, table, row = 1) {
   if (table === 'waiting-approval') {
     await this.openProductReviewDropdown(page, table, row);
     await page.click(this.deleteReviewButton(table, row));
     await this.waitForVisibleSelector(page, this.confirmReviewDeletionButton);
     await page.click(this.confirmReviewDeletionButton);
   }
   else {
     await page.click(this.deleteReviewButton(table, row));
     await this.waitForVisibleSelector(page, this.confirmReviewDeletionButton);
     await page.click(this.confirmReviewDeletionButton);
   }
 }

 /**
  *
  * @param page {Page} Browser tab
  * @param row {Number} The review row
  * @returns {Promise<void>}
  */
 async confirmNotAbusiveReview(page, row = 1) {
   await this.openProductReviewDropdown(page, 'reported', row);
   await this.waitForVisibleSelector(page, this.confirmNotAbusiveReviewButton(row));
   await page.click(this.confirmNotAbusiveReviewButton(row));
 }
}

module.exports = new ProductComments();
