require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class BestSales extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Best sales';
  }
}

module.exports = new BestSales();
