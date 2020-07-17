require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddOrder extends BOBasePage {
  constructor(page) {
    super(page);
  }
};
