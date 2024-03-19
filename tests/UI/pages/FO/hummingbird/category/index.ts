// Import FO pages
import {CategoryPage} from '@pages/FO/classic/category/index';
import {Page} from 'playwright';

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

    // Categories SideBlock
    this.sideBlockCategories = '.category-tree';
    this.sideBlockCategoriesItem = `${this.sideBlockCategories} ul.category-tree__list ul li.category-tree__item`;
    this.sideBlockCategory = (text: string) => `${this.sideBlockCategoriesItem} a:text("${text}")`;
    this.searchFiltersCheckbox = (facetType: string) => `${this.searchFilter(facetType)} label.form-check-label a`;
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

  /**
   * Filter by checkbox
   * @param page {Page} Browser tab
   * @param facetType {string} Type of filter
   * @param checkboxName {string} Checkbox name
   * @param toEnable {boolean} True if we need to enable
   * @return {Promise<void>}
   */
  async filterByCheckbox(page: Page, facetType: string, checkboxName: string, toEnable: boolean): Promise<void> {
    await page.locator(`.facet.accordion-item button:text("${facetType}")`);
    await this.setChecked(
      page,
      `${this.searchFiltersCheckbox(facetType)}[href*=${checkboxName}]`,
      toEnable,
      true,
    );
    await page.waitForTimeout(2000);
  }
}

export default new Category();
