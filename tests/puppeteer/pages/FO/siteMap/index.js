require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SiteMap extends FOBasePage {
  constructor(page) {
    super(page);

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
   *
   * @param categoryID
   * @return {Promise<void>}
   */
  async getCategoryName(categoryID) {
    return this.getTextContent(this.categoryNameSelect(categoryID));
  }

  /**
   * check if category is visible
   * @param categoryID
   * @return {Promise<boolean>}
   */
  async isVisibleCategory(categoryID) {
    return this.elementVisible(this.categoryNameSelect(categoryID));
  }

  /**
   *
   * @param pageCategoryID
   * @return {Promise<void>}
   */
  async getPageCategoryName(pageCategoryID) {
    return this.getTextContent(this.categoryPageNameSelect(pageCategoryID));
  }

  /**
   * Is suppliers link visible
   * @returns {boolean}
   */
  isSuppliersLinkVisible() {
    return this.elementVisible(this.suppliersPageLink, 2000);
  }

  /**
   * Is brands link visible
   * @returns {boolean}
   */
  isBrandsLinkVisible() {
    return this.elementVisible(this.brandsPageLink, 2000);
  }
};
