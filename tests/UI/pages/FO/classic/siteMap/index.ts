import FOBasePage from '@pages/FO/classic/FObasePage';

import type {Page} from 'playwright';

/**
 * Site map page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SiteMapPage extends FOBasePage {
  public readonly pageTitle: string;

  private readonly categoryNameSelect: (id: number) => string;

  private readonly categoryPageNameSelect: (id: number) => string;

  private readonly suppliersPageLink: string;

  private readonly bestSellersPageLink: string;

  private readonly brandsPageLink: string;

  private readonly categoryPageLink: (categoryIDd: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on site map page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Sitemap';

    // Selectors
    this.categoryNameSelect = (id: number) => `#category-page-${id}`;
    this.categoryPageNameSelect = (id: number) => `#cms-category-${id}`;
    this.suppliersPageLink = '#supplier-page';
    this.bestSellersPageLink = '#best-sales-page';
    this.brandsPageLink = '#manufacturer-page';
    this.categoryPageLink = (categoryID: number) => `#category-page-${categoryID}`;
  }

  /*
  Methods
   */
  /**
   * Get category name
   * @param page {Page} Browser tab
   * @param categoryID {number} ID of the category
   * @return {Promise<string>}
   */
  async getCategoryName(page: Page, categoryID: number): Promise<string> {
    return this.getTextContent(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Check if category is visible
   * @param page {Page} Browser tab
   * @param categoryID {number} ID of the category
   * @return {Promise<boolean>}
   */
  async isVisibleCategory(page: Page, categoryID: number): Promise<boolean> {
    return this.elementVisible(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Get page category name
   * @param page {Page} Browser tab
   * @param pageCategoryID {number} Id of the page category
   * @return {Promise<string>}
   */
  async getPageCategoryName(page: Page, pageCategoryID: number): Promise<string> {
    return this.getTextContent(page, this.categoryPageNameSelect(pageCategoryID));
  }

  /**
   * Is suppliers link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isSuppliersLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.suppliersPageLink, 2000);
  }

  /**
   * Is best sellers link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isBestSellersLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.bestSellersPageLink, 2000);
  }

  /**
   * Is brands link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isBrandsLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.brandsPageLink, 2000);
  }

  /**
   * Click on the created category
   * @param page {Page} Browser tab
   * @param categoryID {number} The category ID
   * @return {Promise<void>}
   */
  async viewCreatedCategory(page: Page, categoryID: number): Promise<void> {
    return this.clickAndWaitForURL(page, this.categoryPageLink(categoryID));
  }
}

const siteMapPage = new SiteMapPage();
export {siteMapPage, SiteMapPage};
