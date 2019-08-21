const CommonPage = require('../commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.logoHomePage = '#_desktop_logo';
    this.productImg = '#content div:nth-child(%NUMBER) article img';
    this.userInfoLink = '#_desktop_user_info';
    this.contactLink = '#contact-link';
  }

  /**
   * Check home page
   */
  async checkHomePage() {
    await this.page.waitForSelector(this.logoHomePage, {visible: true});
  }

  /**
   * Go the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await this.page.waitForSelector(this.logoHomePage, {visible: true});
    await this.waitForSelectorAndClick(this.productImg.replace('%NUMBER', id), 5000);
  }
};
