// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCustomers,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_selectAddress';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Go to FO
- Add product to cart
- Go to checkout page
- Login as a customer
- Select the second address
- Check that no payment method is available
Post-condition:
- Uninstall the theme hummingbird
*/
describe('FO - Checkout - Addresses: Select address', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new order and select the second address', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should go to the fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 4);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isStepCompleted = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isStepCompleted).to.equal(true);
    });

    it('should choose the second address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondAddress', baseContext);

      await foHummingbirdCheckoutPage.selectDeliveryAddress(page, 2);

      const isStepCompleted = await foHummingbirdCheckoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should continue to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'continueToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should check that no payment method is available', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoPaymentMethodAvailable', baseContext);

      const alertMessage = await foHummingbirdCheckoutPage.getNoPaymentAvailableMessage(page);
      expect(alertMessage).to.equal('Unfortunately, there is no payment method available.');
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
