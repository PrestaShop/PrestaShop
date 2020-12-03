require('module-alias/register');
// Using BOBasePage
const BOBasePage = require('@pages/BO/BObasePage');

class AutoUpgrade extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = '1-Click Upgrade > 1-Click Upgrade •';
    this.expectedStepsDoneForUpgradeTable = [
      'backupFiles',
      'backupDb',
      'upgradeFiles',
      'upgradeDb',
      'upgradeModules',
      'upgradeComplete',
    ];
    this.actualStepsDoneForUpgradeTable = [];

    // Selectors
    this.showExpertModeButton = 'input[type=\'button\'][value=\'More options (Expert mode)\']';
    this.channelSelect = '#channel';
    this.updateChannelSaveButton = '#advanced input[type=\'button\'][value=\'Save\']';
    this.upgradePrestashopNowButton = '#upgradeNow';
    this.upgradeResultMessageBloc = '#upgradeResultCheck>p';
  }

  /*
  Methods
   */

  /**
   * Select the channel from where to do the upgrade
   * @param page
   * @param upgradeChannelValue, value to select
   * @return {Promise<void>}
   */
  async chooseUpgradeChannel(page, upgradeChannelValue) {
    if (await this.elementVisible(page, this.showExpertModeButton)) {
      await page.click(this.showExpertModeButton);
    }
    await this.waitForVisibleSelector(page, this.channelSelect);
    await page.selectOption(page, this.channelSelect, upgradeChannelValue);
    await this.clickAndWaitForNavigation(page, this.updateChannelSaveButton);
  }

  /**
   * Select the channel from where to do the upgrade,
   * Upgrade Prestashop
   * And check successful message and that all steps are passed
   * @param page
   * @param upgradeChannelValue, value to select
   * @return {Promise<*>}
   */
  async upgradePrestashop(page, upgradeChannelValue) {
    await this.chooseUpgradeChannel(page, upgradeChannelValue);
    await this.waitForVisibleSelector(page, this.upgradePrestashopNowButton);
    page.on('response', async (response) => {
      if (await response.url().endsWith('ajax-upgradetab.php') && await response.status() === 200) {
        const jsonResponse = await response.json();
        await this.actualStepsDoneForUpgradeTable.push(jsonResponse.next);
      }
    });
    await page.click(this.upgradePrestashopNowButton);
    await this.waitForVisibleSelector(page, this.upgradeResultMessageBloc, 300000);
    return this.actualStepsDoneForUpgradeTable;
  }
}


module.exports = new AutoUpgrade();
