const CommonPage = require('../commonPage');

module.exports = class FO_HOME extends CommonPage {
  constructor(page) {
    super(page);

    //Selectors for home page
    this.logoHomePage = '#_desktop_logo';
    this.productImg = '#content article:nth-child(%NUMBER) img';
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
  async goToProductPage(id){
    await this.page.waitForSelector(this.logoHomePage, {visible: true});
    await this.waitForSelectorAndClick(this.productImg.replace('%NUMBER', id));
  }
};