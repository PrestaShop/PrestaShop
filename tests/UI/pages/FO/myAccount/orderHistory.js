require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderHistory extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order history';

    // Selectors
    this.reorderLink = id => `#content td.order-actions a[href*='Reorder=&id_order=${id}']`;
  }

  /*
  Methods
   */

  /**
   * Is reorder link visible
   * @param page
   * @param idOrder
   * @returns {boolean}
   */
  isReorderLinkVisible(page, idOrder = 1) {
    return this.elementVisible(page, this.reorderLink(idOrder), 1000);
  }
}

module.exports = new OrderHistory();
