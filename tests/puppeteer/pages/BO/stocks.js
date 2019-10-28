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
    //tags
    this.searchTagsList = 'form.search-form div.tags-wrapper span.tag';
    this.searchTagsListCloseSpan = 'form.search-form div.tags-wrapper span.tag i';

    // bulk
    this.bulkCheckbox = 'input#bulk-action';
    this.bulkEditQuantityInput = '#app > div.card.container-fluid.pa-2.clearfix > section > ' +
      'div.row.product-actions > div.col-md-8.qty.d-flex.align-items-center > div.ml-2 > div > input';

    this.productList = 'table.table';
    this.productRow = `${this.productList} tbody tr`;
    this.productRowNameColumn = `${this.productList} tbody tr:nth-child(%ROW) td:nth-child(1) div.media-body p`;
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

  /**
   * Get the number of lines in the main table
   * @returns {Promise<*>}
   */
  async getNumberOfProductsFromList() {
    return (await this.page.$$(this.productRow)).length;
  }

  async removeFilterTags() {
    const closeButtons = await this.page.$$(this.searchTagsListCloseSpan);
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }
  }

  async simpleFilter(filter) {
    this.removeFilterTags();
  }

};
