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

const baseContext: string = 'functional_BO_design_positions_searchHook';

describe('BO - Design - Positions : Search for a hook', async () => {
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
    expect(pageTitle).to.contains(positionsPage.pageTitle);
  });

  const hooks: string[] = [
    'displayCustomerAccount',
    'displayFooter',
    'displayProductAdditionalInfo',
    'displayBackOfficeHeader',
  ];

  hooks.forEach((hook: string) => {
    it(`should search for the hook '${hook}' and check result`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `searchForHook_${hook}`,
        baseContext,
      );

      const textResult = await positionsPage.searchHook(page, hook);
      expect(textResult).to.equal(hook);
    });
  });
});
