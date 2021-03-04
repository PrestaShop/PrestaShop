require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderHistory extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order details';

    // Selectors
    this.orderReturnForm = '#order-return-form';
  }

  /*
  Methods
   */

  /**
   * Is orderReturn form visible
   * @param page
   * @returns {boolean}
   */
  isOrderReturnFormVisible(page) {
    return this.elementVisible(page, this.orderReturnForm, 1000);
  }
}

module.exports = new OrderHistory();
