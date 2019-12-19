require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class SqlManager extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'SQL Manager •';

    // Header Selectors
    this.dbBackupSubTabLink = '#subtab-AdminBackup';
  }

  /* Header Methods */
  /**
   * Go to db Backup page
   * @return {Promise<void>}
   */
  async goToDbBackupPage() {
    await this.clickAndWaitForNavigation(this.dbBackupSubTabLink);
  }
};
