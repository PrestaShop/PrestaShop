require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

module.exports = class Localization extends LocalizationBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Localization • ';
  }
};
