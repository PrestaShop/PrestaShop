require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Site map page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SiteMap extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on site map page
   */
  constructor() {
    super();

    this.pageTitle = 'Sitemap';

    // Selectors
    this.categoryNameSelect = id => `#category-page-${id}`;
    this.categoryPageNameSelect = id => `#cms-category-${id}`;
    this.suppliersPageLink = '#supplier-page';
    this.brandsPageLink = '#manufacturer-page';
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
  async getCategoryName(page, categoryID) {
    return this.getTextContent(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Check if category is visible
   * @param page {Page} Browser tab
   * @param categoryID {number} ID of the category
   * @return {Promise<boolean>}
   */
  async isVisibleCategory(page, categoryID) {
    return this.elementVisible(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Get page category name
   * @param page {Page} Browser tab
   * @param pageCategoryID {number} Id of the page category
   * @return {Promise<string>}
   */
  async getPageCategoryName(page, pageCategoryID) {
    return this.getTextContent(page, this.categoryPageNameSelect(pageCategoryID));
  }

  /**
   * Is suppliers link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isSuppliersLinkVisible(page) {
    return this.elementVisible(page, this.suppliersPageLink, 2000);
  }

  /**
   * Is brands link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isBrandsLinkVisible(page) {
    return this.elementVisible(page, this.brandsPageLink, 2000);
  }
}

module.exports = new SiteMap();
