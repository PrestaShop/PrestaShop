import loginCommon from '@commonTests/BO/loginBO';

import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boDesignPositionsHookModulePage,
  boDesignPositionsPage,
  type FakerModule,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

function hookModule(
  module: FakerModule,
  hookName: string,
  baseContext: string = 'commonTests-hookModule',
): void {
  describe(`Hook module ${module.name} on hook ${hookName}`, async () => {
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

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Design > Positions\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDesignPositionsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.positionsLink,
      );
      await boDesignPositionsPage.closeSfToolBar(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignPositionsPage.pageTitle);
    });

    it('should go to "Hook a module" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewHook', baseContext);

      await boDesignPositionsPage.clickHeaderHookModule(page);

      const pageTitle = await boDesignPositionsHookModulePage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsHookModulePage.pageTitle);
    });

    it('should register the hook', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'registerHook', baseContext);

      await boDesignPositionsHookModulePage.setModule(page, module);
      await boDesignPositionsHookModulePage.setHook(page, hookName);

      const message = await boDesignPositionsHookModulePage.saveForm(page);
      expect(message).to.be.equal(boDesignPositionsPage.messageModuleAddedFromHook);
    });
  });
}

export default hookModule;
