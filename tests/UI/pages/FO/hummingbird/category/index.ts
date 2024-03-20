// Import FO pages
import {CategoryPage} from '@pages/FO/classic/category/index';
import type {Page} from 'playwright';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Category extends CategoryPage {
  private readonly searchFilterPriceSlider: string;

  private readonly searchFiltersLabel: string;

  private readonly filterTypeButton: (facetType: string) => string;

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
    this.productPrice = (number: number) => `${this.productArticle(number)} span.product-miniature__price`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview `
      + 'button';

    // Categories SideBlock
    this.sideBlockCategories = '.category-tree';
    this.sideBlockCategoriesItem = `${this.sideBlockCategories} ul.category-tree__list ul li.category-tree__item`;
    this.sideBlockCategory = (text: string) => `${this.sideBlockCategoriesItem} a:text("${text}")`;
    this.searchFilters = '#search-filters';
    this.filterTypeButton = (facetType: string) => `.facet.accordion-item button:text("${facetType}")`;
    this.searchFiltersLabel = `${this.searchFilters} label.form-check-label`;
    this.clearAllFiltersLink = `${this.searchFilters} button.js-search-filters-clear-all`;
    this.searchFilterPriceSlider = 'div.faceted-slider';
    this.searchFiltersSlider = 'div.noUi-base';
    this.closeOneFilter = (row: number) => `#js-active-search-filters ul li:nth-child(${row + 1}) a i`;
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
   * @return {Promise<void>}
   */
  async filterByCheckbox(page: Page, facetType: string, checkboxName: string): Promise<void> {
    await page.locator(this.filterTypeButton(facetType)).click();
    if (facetType === 'Color') {
      await page.locator(`${this.searchFiltersLabel} span[style*="${checkboxName}"]`).click();
    } else {
      await page.locator(`${this.searchFiltersLabel} a[href*="${checkboxName}"]`).click();
    }
    await page.locator(this.filterTypeButton(facetType)).click();
  }

  /**
   * Get product href
   * @param page {Page} Browser tab
   * @param productRow {number} Product row
   * @return {Promise<string>}
   */
  async getProductHref(page: Page, productRow: number): Promise<string> {
    return this.getAttributeContent(page, `${this.productArticle(productRow)} div.card a.product-miniature__link`, 'href');
  }

  /**
   * Get maximum price from slider
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getMaximumPrice(page: Page): Promise<number> {
    await page.locator('.facet.accordion-item button:text("Price")').click();

    return parseInt(await this.getAttributeContent(page, this.searchFilterPriceSlider, 'data-slider-max'), 10);
  }

  /**
   * Get minimum price from slider
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getMinimumPrice(page: Page): Promise<number> {
    return parseInt(await this.getAttributeContent(page, this.searchFilterPriceSlider, 'data-slider-min'), 10);
  }
}

export default new Category();
