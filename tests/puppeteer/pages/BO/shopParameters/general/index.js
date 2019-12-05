require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class shopParamsGeneral extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Preferences •';

    // Selectors
    this.maintenanceNavItemLink = '#subtab-AdminMaintenance';
  }

  /*
  Methods
   */

  /**
   * Change Tab to Maintenance in Shop Parameters General Page
   * @return {Promise<void>}
   */
  async goToSubTabMaintenance() {
    await this.page.click(this.maintenanceNavItemLink, {waitUntil: 'networkidle2'});
  }
};
