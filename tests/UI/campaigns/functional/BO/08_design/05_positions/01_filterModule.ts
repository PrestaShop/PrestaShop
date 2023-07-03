// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import positionsPage from '@pages/BO/design/positions';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_positions_filterModule';

describe('BO - Design - Positions : Filter module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const moduleName: string = 'Wishlist';

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Design > Positions\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.positionsLink,
    );
    await positionsPage.closeSfToolBar(page);

    const pageTitle = await positionsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(positionsPage.pageTitle);
  });

  it(`should filter by module '${moduleName}' and check the result`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterModule', baseContext);

    await positionsPage.filterModule(page, moduleName);

    const numberOfHooks = await positionsPage.getNumberOfHooks(page);
    await expect(numberOfHooks).to.eq(5);
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

      const isVisible = await positionsPage.isHookVisible(page, hook);
      await expect(isVisible).to.be.true;

      const firstModuleName = await positionsPage.getModulesInHook(page, hook);
      await expect(firstModuleName).to.contain(moduleName);
    });
  });

  it('should filter by \'All modules\' and check the result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterAllModules', baseContext);

    await positionsPage.filterModule(page, 'All modules');

    const numberOfHooks = await positionsPage.getNumberOfHooks(page);
    await expect(numberOfHooks).to.above(5);
  });
});
