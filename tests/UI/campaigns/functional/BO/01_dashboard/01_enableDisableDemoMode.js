require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_dashboard_enableDisableDemoMode';

let browserContext;
let page;
let fakeStats = [32, 3, 0, 5, 41, 2, 256, 160, 160, 32, 624, 8661, 5196, 4330];

describe('Enable/Disable demo mode', async () => {
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

    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });

  const trafficActivity = ['online_visitor', 'active_shopping_cart', 'pending_orders', 'return_exchanges',
    'abandoned_cart', 'products_out_of_stock', 'new_messages', 'product_reviews', 'new_customers', 'new_registrations',
    'total_suscribers', 'visits', 'unique_visitors', 'dash_traffic_source'];

  it('should enable demo mode and get fake stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDemoMode', baseContext);

    await dashboardPage.setDemoModeStatus(page, true);
    fakeStats = await dashboardPage.getAllTrafficValues(page, trafficActivity);
  });

  it('should disable demo mode and check the difference', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'DisableDemoMode', baseContext);

    await dashboardPage.setDemoModeStatus(page, false);
    const realStats = await dashboardPage.getAllTrafficValues(page, trafficActivity);

    await expect(fakeStats).to.not.deep.equal(realStats);
  });
});
