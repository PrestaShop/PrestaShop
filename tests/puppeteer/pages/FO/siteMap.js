require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SiteMap extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Sitemap';

    // Selectors
    this.categoryNameSelect = '#category-page-%ID';
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
    return this.getTextContent(this.categoryNameSelect.replace('%ID', categoryID));
  }

  /**
   * check if category is visible
   * @param categoryID
   * @return {Promise<boolean|true>}
   */
  async isVisibleCategory(categoryID) {
    return this.elementVisible(this.categoryNameSelect.replace('%ID', categoryID));
  }
};
