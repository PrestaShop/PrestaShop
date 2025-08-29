// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import hookModule from '@commonTests/BO/design/positions';

import {
  boDashboardPage,
  boDesignPositionsPage,
  boLoginPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_design_positions_unhookModuleInListByBulkActions';

describe('BO - Design - Positions : Unhook module in list by Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-Test : Hook a module (Hook : "GraphEngine" / Module : "ps_banner")
  hookModule(dataModules.psBanner, 'GraphEngine', `${baseContext}_preTest_1`);

  describe('Unhook module in list by Bulk actions', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Positions\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.positionsLink,
      );
      await boDesignPositionsPage.closeSfToolBar(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignPositionsPage.pageTitle);
    });

    it('should select a hook and display the selection box', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectHookModule', baseContext);

      const isSelectionBoxVisible = await boDesignPositionsPage.selectHookModule(
        page,
        'GraphEngine',
        dataModules.psBanner.tag,
      );
      expect(isSelectionBoxVisible).to.equal(true);

      const numSelectedHook = await boDesignPositionsPage.getSelectedHookCount(page);
      expect(numSelectedHook).to.be.equal(1);
    });

    it('should unhook the selection', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'unhookSelection', baseContext);

      const textResult = await boDesignPositionsPage.unhookSelection(page);
      expect(textResult).to.equal(boDesignPositionsPage.messageModuleRemovedFromHook);
    });
  });
});
