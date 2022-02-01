require('module-alias/register');
const ViewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

/**
 * View order page, contains functions that can be used on view/edit order page
 * @class
 * @extends ViewOrderBasePage
 */
class ViewOrderPage extends ViewOrderBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view/edit order page
   */
  constructor() {
    super();
  }

  /*
  Methods
   */
}

module.exports = new ViewOrderPage();
