require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class LegalNotice extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Legal Notice';
  }
}

module.exports = new LegalNotice();
