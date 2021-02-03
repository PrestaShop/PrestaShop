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

  it('should enable demo mode and check fake stats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFakeStats', baseContext);

    await dashboardPage.setDemoMode(page, true);
    const TableOfTrafficValues = await dashboardPage.getAllTrafficValues(page, trafficActivity);

    await dashboardPage.setDemoMode(page, false);
    const TableOfTrafficFakerValues = await dashboardPage.getAllTrafficValues(page, trafficActivity);

    await expect(TableOfTrafficValues).to.not.deep.equal(TableOfTrafficFakerValues);
  });
});
