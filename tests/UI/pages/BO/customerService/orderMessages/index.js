require('module-alias/register');
const MigratedListingBasePage = require('@pages/BO/migratedListingBasePage.js');

const tableName = 'order_message';

class OrderMessages extends MigratedListingBasePage {
  constructor() {
    super(tableName);

    this.pageTitle = 'Order Messages •';

    // Selectors header
    this.newOrderMessageLink = '#page-header-desc-configuration-add';
  }

  /* Header Methods */
  /**
   * Go to new order message page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewOrderMessagePage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderMessageLink);
  }
}

module.exports = new OrderMessages();
