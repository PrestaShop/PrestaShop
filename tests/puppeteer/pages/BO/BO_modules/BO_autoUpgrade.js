const BOCommonPage = require('../BO_commonPage');

module.exports = class BO_AUTOUPGRADE extends BOCommonPage {
  constructor(page) {
    super(page);

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

  async chooseUpgradeChannel(upgradeChannelValue) {
    if (await this.elementVisible(this.showExpertModeButton)) {
      await this.page.click(this.showExpertModeButton);
    }
    await this.page.waitForSelector(this.channelSelect, {visible: true});
    await this.page.select(this.channelSelect, upgradeChannelValue);
    await this.page.click(this.updateChannelSaveButton);
    await this.page.waitForNavigation({waitUntil: 'networkidle0'});
  }

  async upgradePrestashop(upgradeChannelValue) {
    await this.chooseUpgradeChannel(upgradeChannelValue);
    await this.page.waitForSelector(this.upgradePrestashopNowButton, {visible: true});
    this.page.on('response', async (response) => {
      if (await response.url().endsWith('ajax-upgradetab.php') && await response.status() === 200) {
        const jsonResponse = await response.json();
        await this.actualStepsDoneForUpgradeTable.push(jsonResponse.next);
      }
    });
    await this.page.click(this.upgradePrestashopNowButton);
    await this.page.waitForSelector(this.upgradeResultMessageBloc, {visible: true, timeout: 300000});
    await global.expect(this.actualStepsDoneForUpgradeTable).to.include.members(this.expectedStepsDoneForUpgradeTable);
  }
};
