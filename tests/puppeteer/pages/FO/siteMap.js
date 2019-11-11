require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SiteMap extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Sitemap';

    // Selectors
    this.categoryNameSelect = '#category-page-%ID';
    this.categoryPageNameSelect = '#cms-category-%ID';
  }

  /*
Methods
 */

  /**
   * get category name
   * @param id, Category id
   * @return {Promise<textContent>}
   */
  async getCategoryName(id) {
    return this.getTextContent(this.categoryPageNameSelect.replace('%ID', id));
  }
};
