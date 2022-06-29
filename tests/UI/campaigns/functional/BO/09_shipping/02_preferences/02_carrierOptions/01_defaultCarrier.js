require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Common tests login BO
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const preferencesPage = require('@pages/BO/shipping/preferences');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');

// Import data
const {Carriers} = require('@data/demo/carriers');
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_shipping_preferences_carriersOptions_defaultCarrier';

// Browser and tab
let browserContext;
let page;

/*
Go to shipping > preferences page
Set default carrier to 'My carrier'
Go to Fo and check the update
Reset default carrier to 'PrestaShop'
Go to Fo and check the reset
 */
describe('BO - Shipping - Preferences : Update default carrier and check it in FO', async () => {
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

    await preferencesPage.closeSfToolBar(page);

    const pageTitle = await preferencesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(preferencesPage.pageTitle);
  });

  const carriers = [
    Carriers.myCarrier,
    Carriers.default,
  ];

  carriers.forEach((carrier, index) => {
    describe(`Set default carrier to '${carrier.name}' and check result in FO`, async () => {
      it(`should set default carrier to ${carrier.name} in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await preferencesPage.setDefaultCarrier(page, carrier);
        await expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await preferencesPage.viewMyShop(page);

        // Change FO language
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foHomePage.goToProductPage(page, 1);

        // Add the product to the cart
        await foProductPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await foCartPage.clickOnProceedToCheckout(page);

        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foCheckoutPage.clickOnSignIn(page);
          await foCheckoutPage.customerLogin(page, DefaultCustomer);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should verify default carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkDefaultCarrier${index}`, baseContext);

        const selectedShippingMethod = await foCheckoutPage.getSelectedShippingMethod(page);
        await expect(selectedShippingMethod, 'Wrong carrier was selected in FO').to.equal(carrier.name);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });
});
