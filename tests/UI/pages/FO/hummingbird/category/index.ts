// Import FO pages
import {CategoryPage} from '@pages/FO/classic/category/index';
import {Page} from "playwright";

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Category extends CategoryPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    this.headerNamePage = '#js-product-list-header h1';
    this.paginationText = 'div.pagination-number';
    this.productItemListDiv = 'div.products div.card';
    this.paginationNext = `${this.productListDiv} nav div.pagination-list-container a.next`;
    this.productArticle = (number: number) => `${this.productListDiv} article:nth-child(${number})`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview `
      + 'button';
  }

  /**
   * Quick view product
   * @param page {Page} Browser tab
   * @param id {number} Product row in the list
   * @returns {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).hover();
    await this.waitForVisibleSelector(page, this.productQuickViewLink(id));
    await page.locator(this.productQuickViewLink(id)).click();
  }
}

export default new Category();
