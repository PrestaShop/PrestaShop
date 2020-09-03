require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class SiteMap extends FOBasePage {
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
   * @param page
   * @param categoryID
   * @return {Promise<string>}
   */
  async getCategoryName(page, categoryID) {
    return this.getTextContent(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Check if category is visible
   * @param page
   * @param categoryID
   * @return {Promise<boolean>}
   */
  async isVisibleCategory(page, categoryID) {
    return this.elementVisible(page, this.categoryNameSelect(categoryID));
  }

  /**
   * Get page category name
   * @param page
   * @param pageCategoryID
   * @return {Promise<string>}
   */
  async getPageCategoryName(page, pageCategoryID) {
    return this.getTextContent(page, this.categoryPageNameSelect(pageCategoryID));
  }

  /**
   * Is suppliers link visible
   * @param page
   * @returns {boolean}
   */
  isSuppliersLinkVisible(page) {
    return this.elementVisible(page, this.suppliersPageLink, 2000);
  }

  /**
   * Is brands link visible
   * @param page
   * @returns {boolean}
   */
  isBrandsLinkVisible(page) {
    return this.elementVisible(page, this.brandsPageLink, 2000);
  }
}

module.exports = new SiteMap();
