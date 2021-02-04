require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Stores extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Stores';
  }
}

module.exports = new Stores();
