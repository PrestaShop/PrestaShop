require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login tests
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const positionsPage = require('@pages/BO/design/positions');

const baseContext = 'functional_BO_design_positions_searchHook';

let browserContext;
let page;

describe('BO - Design - Positions : Search for a hook', async () => {
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

  const hooks = ['displayCustomerAccount', 'displayFooter', 'displayProductAdditionalInfo', 'displayBackOfficeHeader'];

  hooks.forEach((hook) => {
    it(`should search for the hook '${hook}' and check result`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `searchForHook_${hook}`,
        baseContext,
      );

      const textResult = await positionsPage.searchHook(page, hook);
      await expect(textResult).to.equal(hook);
    });
  });
});
