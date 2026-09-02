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

      const textResult = await boDesignPositionsPage.searchHook(page, hook);
      expect(textResult).to.equal(hook);
    });
  });
});
