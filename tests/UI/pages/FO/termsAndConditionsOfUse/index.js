require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class TermsAndConditionsOfUse extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Terms and conditions of use';
  }
}

module.exports = new TermsAndConditionsOfUse();
