require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Information about customer';

    // Selectors
    this.cardDiv = 'div.card:nth-child(%ID)';
    this.cardHeaderTitle = `${this.cardDiv} h3.card-header`;
  }

  /*
  Methods
   */

  /**
   * get text from card header
   * @param cardHeaderID
   * @return {Promise<textContent>}
   */
  async getTextFromCardHeader(cardHeaderID) {
    return this.getTextContent(this.cardHeaderTitle.replace('%ID', cardHeaderID));
  }
};
