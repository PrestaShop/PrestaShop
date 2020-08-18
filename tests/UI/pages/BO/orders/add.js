require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddOrder extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Create order •';
    this.noCustomerFoundText = 'No customers found';

    // Customer selectors
    this.customerSearchInput = '#customer-search-input';
    this.customerSearchLoadingNoticeBlock = '#customer-search-loading-notice';
    // Empty results
    this.customerSearchEmptyResultBlock = '#customer-search-empty-result-warn';
    this.customerSearchEmptyResultParagraphe = `${this.customerSearchEmptyResultBlock} .alert-text`;
    // Full results
    this.customerSearchFullResultsBlock = '.js-customer-search-results';
    this.customerSearchResultBlock = pos => `${this.customerSearchFullResultsBlock} `
      + `.js-customer-search-result:nth-child(${pos})`;
    this.customerSearchResultNameTitle = pos => `${this.customerSearchResultBlock(pos)} .js-customer-name`;
  }

  /**
   * Fill customer search input and wait for results to load
   * @param page
   * @param customerToSearch
   * @return {Promise<void>}
   */
  async searchCustomer(page, customerToSearch) {
    await this.setValue(page, this.customerSearchInput, customerToSearch);

    await page.waitForSelector(this.customerSearchLoadingNoticeBlock, {state: 'hidden'});
  }

  /**
   * Get Error message when when no customer was found after searching
   * @param page
   * @return {Promise<string>}
   */
  getNoCustomerFoundError(page) {
    return this.getTextContent(page, this.customerSearchEmptyResultParagraphe);
  }

  /**
   *
   * @param page
   * @param cardPosition, position of the card in results
   * @return {Promise<string>}
   */
  getCustomerNameFromResult(page, cardPosition = 1) {
    return this.getTextContent(page, this.customerSearchResultNameTitle(cardPosition));
  }
}

module.exports = new AddOrder();
