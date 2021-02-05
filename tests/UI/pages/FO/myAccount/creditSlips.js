require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class CreditSlips extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Credit slip';
  }
}

module.exports = new CreditSlips();
