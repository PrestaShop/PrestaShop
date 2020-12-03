require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const positionsPage = require('@pages/BO/design/positions');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_positions_searchHook';


let browserContext;
let page;

describe('Search for a hook', async () => {
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

  it('should go to \'design > positions\' page', async function () {
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

  const tests = [
    {args: {hookName: 'displayAdminAfterHeader'}},
    {args: {hookName: 'displayAdminNavBarBeforeEnd'}},
    {args: {hookName: 'displayAfterBodyOpeningTag'}},
    {args: {hookName: 'displayBackOfficeHeader'}},
  ];

  tests.forEach((test) => {
    it(`should search for the hook '${test.args.hookName}' and check result`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `searchForHook_${test.args.hookName}`,
        baseContext,
      );

      const textResult = await positionsPage.searchHook(page, test.args.hookName);
      await expect(textResult).to.equal(test.args.hookName);
    });
  });
});
