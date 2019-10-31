require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Information about customer';

    // Selectors
    this.cardDiv = 'div.card:nth-child(%ID)';
    this.cardHeaderDiv = `${this.cardDiv} h3.card-header`;
  }

  /*
  Methods
   */
};
