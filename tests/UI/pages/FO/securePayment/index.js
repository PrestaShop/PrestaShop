require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class SecurePayment extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Secure payment';
  }
}

module.exports = new SecurePayment();
