// Import FO pages
import {SearchResultsPage} from '@pages/FO/classic/searchResults';
import {Page} from 'playwright';

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
    super('hummingbird');

    this.productPrice = '#js-product-list div.card span.product-miniature__price';
    this.productArticle = (number: number) => `#js-product-list .products article:nth-child(${number})`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} button.product-miniature__quickview_button`;
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).first().hover();
    await this.waitForVisibleSelector(page, this.productQuickViewLink(id));
    await page.locator(this.productQuickViewLink(id)).first().click();
  }
}

export default new SearchResults();
