
require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class CMS extends FOBasePage {
  constructor() {
    super();
    this.pageNotFound = 'The page you are looking for was not found.';

    // Selectors
    this.pageTitle = '#main header h1';
    this.pageContent = '#content';
  }
}

module.exports = new CMS();
