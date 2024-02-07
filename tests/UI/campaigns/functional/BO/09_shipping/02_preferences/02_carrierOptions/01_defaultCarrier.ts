// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import preferencesPage from '@pages/BO/shipping/preferences';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage as foCheckoutPage} from '@pages/FO/classic/checkout';
import foProductPage from '@pages/FO/classic/product';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {foProductPage} from '@pages/FO/classic/product';
import {homePage as foHomePage} from '@pages/FO/classic/home';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import CarrierData from '@data/faker/carrier';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shipping_preferences_carrierOptions_defaultCarrier';

/*
Go to shipping > preferences page
Set default carrier to 'My carrier'
Go to Fo and check the update
Reset default carrier to 'PrestaShop'
Go to Fo and check the reset
 */
describe('BO - Shipping - Preferences : Update default carrier and check it in FO', async () => {
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

  it('should go to \'Shipping > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.shippingPreferencesLink,
    );
    await preferencesPage.closeSfToolBar(page);

    const pageTitle = await preferencesPage.getPageTitle(page);
    expect(pageTitle).to.contains(preferencesPage.pageTitle);
  });

  const carriers: CarrierData[] = [
    Carriers.myCarrier,
    Carriers.default,
  ];

  carriers.forEach((carrier: CarrierData, index: number) => {
    describe(`Set default carrier to '${carrier.name}' and check result in FO`, async () => {
      it(`should set default carrier to ${carrier.name} in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await preferencesPage.setDefaultCarrier(page, carrier);
        expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await preferencesPage.viewMyShop(page);
        // Change FO language
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foProductPage.addProductToTheCart(page);
        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foCheckoutPage.clickOnSignIn(page);
          await foCheckoutPage.customerLogin(page, Customers.johnDoe);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should verify default carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkDefaultCarrier${index}`, baseContext);

        const selectedShippingMethod = await foCheckoutPage.getSelectedShippingMethod(page);
        expect(selectedShippingMethod, 'Wrong carrier was selected in FO').to.equal(carrier.name);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });
});
