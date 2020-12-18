require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const merchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_activateMerchandiseReturns';

let browserContext;
let page;

/*

 */
describe('Activate/Deactivate merchandise return', async () => {
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

  it('should go to merchandise returns page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customerServiceParentLink,
      dashboardPage.merchandiseReturnsLink,
    );

    await merchandiseReturnsPage.closeSfToolBar(page);

    const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'activate', enable: true}},
    {args: {action: 'deactivate', enable: false}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} merchandise returns`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Returns`, baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, test.args.enable);
      await expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
});
