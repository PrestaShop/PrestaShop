const BOCommonPage = require('./BO_commonPage');

module.exports = class BO_SHOPPARAMSGENERAL extends BOCommonPage {
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
