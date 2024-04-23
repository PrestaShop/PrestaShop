// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import boDesignPositionsPage from '@pages/BO/design/positions';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psEmailAlerts from '@pages/BO/modules/psEmailAlerts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {dataModules} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_configuration_boHeaderButtons';

describe('Mail alerts module - BO Header Buttons', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Header Buttons', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);
    });

    it('should click on the "Back" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBackButton', baseContext);

      await psEmailAlerts.clickHeaderBack(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should return to the configure page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnConfigureAfterBack', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);
    });

    it('should click on the "Translate" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnTranslateButton', baseContext);

      await psEmailAlerts.clickHeaderTranslate(page);

      const isModalVisible = await psEmailAlerts.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should close the "Translate" modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeTranslateModal', baseContext);

      await psEmailAlerts.closeTranslateModal(page);

      const isModalVisible = await psEmailAlerts.isModalTranslateVisible(page);
      expect(isModalVisible).to.be.equal(false);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);
    });

    it('should click on the "Manage hooks" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageHooksButton', baseContext);

      await psEmailAlerts.clickHeaderManageHooks(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsPage.pageTitle);

      const moduleFiltered = await boDesignPositionsPage.getModuleFilter(page);
      expect(moduleFiltered).to.be.equal(dataModules.psEmailAlerts.name);
    });
  });
});
