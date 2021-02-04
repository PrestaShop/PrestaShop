require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class AboutUs extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'About us';
  }
}

module.exports = new AboutUs();
