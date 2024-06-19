// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import positionsPage from '@pages/BO/design/positions';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_positions_searchHook';

describe('BO - Design - Positions : Search for a hook', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.positionsLink,
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
