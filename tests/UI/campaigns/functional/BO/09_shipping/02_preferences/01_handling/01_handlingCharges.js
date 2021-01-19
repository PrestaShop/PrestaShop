require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const preferencesPage = require('@pages/BO/shipping/preferences');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');

// Import data
const {Carriers} = require('@data/demo/carriers');
const {DefaultAccount} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shipping_preferences_handling_handlingCharges';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

/*
 */
describe('Handling charges', async () => {
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

  it('should go to \'Shipping > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.shippingPreferencesLink,
    );

    const pageTitle = await preferencesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(preferencesPage.pageTitle);
  });
});
