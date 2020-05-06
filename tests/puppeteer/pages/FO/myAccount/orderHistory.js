require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class OrderHistory extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order history';

    // Selectors
    this.reorderLink = id => `#content td.order-actions a[href*='Reorder=&id_order=${id}']`;
  }

  /*
  Methods
   */

  /**
   * Is reorder link visible
   * @param idOrder
   * @returns {boolean}
   */
  isReorderLinkVisible(idOrder = 1) {
    return this.elementVisible(this.reorderLink(idOrder), 1000);
  }
};
