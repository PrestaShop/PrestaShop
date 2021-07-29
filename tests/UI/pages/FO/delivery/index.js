require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Delivery extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Delivery';
  }
}

module.exports = new Delivery();
