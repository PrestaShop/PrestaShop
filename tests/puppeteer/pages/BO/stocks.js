require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Stocks extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';

    // Selectors
    this.MovementNavItemLink = '#head_tabs li:nth-child(2) > a';
  }

  /*
  Methods
   */

  /**
   * Change Tab to Movements in Stock Page
   * @return {Promise<void>}
   */
  async goToSubTabMovements() {
    await this.page.click(this.MovementNavItemLink, {waitUntil: 'networkidle2'});
  }
};
