require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class OrderHistory extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order history';

    // Selectors
    this.reorderLink = '#content td.order-actions a[href*=\'Reorder=&id_order=%ID\']';
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
    return this.elementVisible(this.reorderLink.replace('%ID', idOrder), 1000);
  }
};
