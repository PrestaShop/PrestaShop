require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CustomerService extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Customer Service •';
  }

  /* Header Methods */
}

module.exports = new CustomerService();
