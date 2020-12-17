require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewCustomer extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Customer Service > View •';

    // Selectors
    this.threadBadge = 'span.badge';
  }

  /*
  Methods
   */

  getBadgeNumber(page) {
    return this.getTextContent(page, this.threadBadge);
  }

  getCustomerMessage(page) {
    return this.getTextContent(page, '#content > div.row > div > div:nth-child(4) > div:nth-child(3) > div');
  }
}

module.exports = new ViewCustomer();
