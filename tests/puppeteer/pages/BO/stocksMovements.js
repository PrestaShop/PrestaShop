require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class StockMovements extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';

    // Selectors
    this.StocksNavItemLink = '#head_tabs li:nth-child(1) > a';
  }

  /*
  Methods
   */

  /**
   * Change Tab to Stock in Stock Page
   * @return {Promise<void>}
   */
  async goToSubTabStock() {
    await this.page.click(this.StocksNavItemLink, {waitUntil: 'networkidle2'});
  }
};
