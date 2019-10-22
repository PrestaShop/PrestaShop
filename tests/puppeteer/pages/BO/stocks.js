require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Stocks extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';

    // Selectors
    this.MovementNavItemLink = '#head_tabs li:nth-child(2) > a';
    this.searchInput = 'form.search-form input.input';
    this.searchButton = 'form.search-form button.search-button';

    //bullk
    this.bulkCheckbox = 'input#bulk-action';
    this.bulkEditQuantityInput = '#app > div.card.container-fluid.pa-2.clearfix > section > div.row.product-actions > div.col-md-8.qty.d-flex.align-items-center > div.ml-2 > div > input';
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
