// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignPositionsPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  modPsEmailAlertsBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_configuration_boHeaderButtons';

describe('Mail alerts module - BO Header Buttons', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO - Header Buttons', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should click on the "Back" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBackButton', baseContext);

      await modPsEmailAlertsBoMain.clickHeaderBack(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should return to the configure page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnConfigureAfterBack', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should click on the "Translate" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnTranslateButton', baseContext);

      await modPsEmailAlertsBoMain.clickHeaderTranslate(page);

      const isModalVisible = await modPsEmailAlertsBoMain.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should close the "Translate" modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeTranslateModal', baseContext);

      await modPsEmailAlertsBoMain.closeTranslateModal(page);

      const isModalVisible = await modPsEmailAlertsBoMain.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(false);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should click on the "Manage hooks" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageHooksButton', baseContext);

      await modPsEmailAlertsBoMain.clickHeaderManageHooks(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsPage.pageTitle);

      const moduleFiltered = await boDesignPositionsPage.getModuleFilter(page);
      expect(moduleFiltered).to.be.equal(dataModules.psEmailAlerts.name);
    });
  });
});
