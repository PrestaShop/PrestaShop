require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class CMS extends FOBasePage {
  constructor(page) {
    super(page);
    this.pageNotFound = 'The page you are looking for was not found.';

    // Selectors
    this.pageTitle = '#main header h1';
    this.pageContent = '#content';
  }
};
