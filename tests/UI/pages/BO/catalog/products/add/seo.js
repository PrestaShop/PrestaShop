require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');

/**
 * SEO form, contains functions that can be used on the form
 * @class
 * @extends BOBasePage
 */
class SEO extends AddProductBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    // Selectors of tab: SEO
    this.friendlyUrlInput = '#form_step5_link_rewrite_1';
    this.resetUrlButton = '#seo-url-regenerate';
  }

  /*
  Methods
   */
  /**
   * Get friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getFriendlyURL(page) {
    await this.reloadPage(page);
    await this.goToFormStep(page, 5);

    return this.getAttributeContent(page, this.friendlyUrlInput, 'value');
  }

  /**
   * Reset friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFriendlyURL(page) {
    await this.goToFormStep(page, 5);
    await this.waitForVisibleSelector(page, this.resetUrlButton);
    await this.scrollTo(page, this.resetUrlButton);
    await page.click(this.resetUrlButton);
    await this.goToFormStep(page, 1);
  }
}

module.exports = new SEO();
