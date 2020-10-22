require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewShoppingCarts extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'View';
  }

  /*
  Methods
   */
}

module.exports = new ViewShoppingCarts();
