require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Dashboard extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Dashboard • ';

    // Selectors
  }

  /*
  Methods
   */
}

module.exports = new Dashboard();
