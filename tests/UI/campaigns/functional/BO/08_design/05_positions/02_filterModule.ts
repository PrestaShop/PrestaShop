// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignPositionsPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_positions_filterModule';

describe('BO - Design - Positions : Filter module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const moduleName: string = 'Wishlist';

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

  it(`should filter by module '${moduleName}' and check the result`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterModule', baseContext);

    await boDesignPositionsPage.filterModule(page, moduleName);

    const numberOfHooks = await boDesignPositionsPage.getNumberOfHooks(page);
    expect(numberOfHooks).to.eq(5);
  });

  const hooks: string[] = [
    'displayAdminCustomers',
    'displayCustomerAccount',
    'displayFooter',
    'displayMyAccountBlock',
    'displayProductActions',
  ];
  hooks.forEach((hook: string) => {
    it(`should check the hook '${hook}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkHooks${hook}`, baseContext);

      const isVisible = await boDesignPositionsPage.isHookVisible(page, hook);
      expect(isVisible).to.eq(true);

      const firstModuleName = await boDesignPositionsPage.getModulesInHook(page, hook);
      expect(firstModuleName).to.contain(moduleName);
    });
  });

  it('should filter by \'All modules\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterAllModules', baseContext);

    await boDesignPositionsPage.filterModule(page, 'All modules');

    const numberOfHooks = await boDesignPositionsPage.getNumberOfHooks(page);
    expect(numberOfHooks).to.above(5);
  });
});
