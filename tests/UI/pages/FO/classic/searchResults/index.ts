import FOBasePage from '@pages/FO/classic/FObasePage';

import type {Page} from 'playwright';

/**
 * Search page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SearchResultsPage extends FOBasePage {
  public readonly pageTitle: string;

  private readonly productListTopDiv: string;

  private readonly totalProduct: string;

  private readonly productArticle: (number: number) => string;

  private readonly productImg: (number: number) => string;

  private readonly productDescriptionDiv: (number: number) => string;

  private readonly productQuickViewLink: (number: number) => string;

  protected productPrice: string;

  private readonly productNoMatches: string;

  private readonly quickViewModalDiv: string;

  private readonly quickViewCoverImage: string;

  private readonly quickViewThumbImage: (position: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on search page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Search';

    // Selectors for search Results page
    this.productListTopDiv = '#js-product-list-top';
    this.totalProduct = `${this.productListTopDiv} .total-products`;
    this.productArticle = (number: number) => `#js-product-list .products div:nth-child(${number}) article`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = (number: number) => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} a.quick-view`;
    this.productPrice = '#js-product-list div.product-description span.price';
    this.productNoMatches = '#product-search-no-matches';

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewCoverImage = `${this.quickViewModalDiv} img.js-qv-product-cover`;
    this.quickViewThumbImage = (position: number) => `${this.quickViewModalDiv} li:nth-child(${position}) img.js-thumb`;
  }

  // Methods
  /**
   * Check if there are results
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasResults(page: Page): Promise<boolean> {
    return (await page.locator(this.productNoMatches).count()) === 0;
  }

  /**
   * Get search product results number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getSearchResultsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.totalProduct);
  }

  /**
   * Go to the product page
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
   * @returns {Promise<void>}
   */
  async goToProductPage(page: Page, id: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).hover();
    let displayed: boolean = false;

    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        (selector) => {
          const element: HTMLElement|null = document.querySelector(selector);

          if (!element) {
            return false;
          }
          return window.getComputedStyle(element, ':after').getPropertyValue('display') === 'block';
        },
        this.productDescriptionDiv(id),
      );
      await page.waitForTimeout(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.waitForVisibleSelector(page, this.quickViewModalDiv),
      page.locator(this.productQuickViewLink(id)).evaluate((el: HTMLElement) => el.click()),
    ]);
  }

  /**
   * Is quick view product modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuickViewProductModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.quickViewModalDiv, 2000);
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param position {number} Position of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, position: number): Promise<string> {
    await page.locator(this.quickViewThumbImage(position)).click();
    await this.waitForVisibleSelector(page, `${this.quickViewThumbImage(position)}.selected`);

    return this.getAttributeContent(page, this.quickViewCoverImage, 'src');
  }

  /**
   * Get the product price value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductPrice(page: Page): Promise<string> {
    return this.getTextContent(page, this.productPrice);
  }
}

const searchResultsPage = new SearchResultsPage();
export {searchResultsPage, SearchResultsPage};
