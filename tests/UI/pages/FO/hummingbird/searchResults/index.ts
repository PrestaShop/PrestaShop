// Import FO pages
import {SearchResultsPage} from '@pages/FO/searchResults';

/**
 * Password Reminder page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SearchResults extends SearchResultsPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on my account page
   */
  constructor() {
    super();

    this.theme = 'hummingbird';

    this.productPrice = '#js-product-list div.card span.product-miniature__price';
  }
}

export default new SearchResults();
