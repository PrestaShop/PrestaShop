require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class PricesDrop extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Prices drop';
  }
}

module.exports = new PricesDrop();
