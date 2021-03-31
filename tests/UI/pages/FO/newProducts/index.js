require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class NewProducts extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'New products';
  }
}

module.exports = new NewProducts();
