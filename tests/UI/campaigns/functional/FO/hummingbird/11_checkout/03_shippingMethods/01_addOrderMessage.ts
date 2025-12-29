import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyOrderDetailsPage,
  foHummingbirdMyOrderHistoryPage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_checkout_shippingMethods_addOrderMessage';

/*
Scenario:
- Go to FO and login by default customer
- Add a product to cart and checkout
- In shipping methods, choose My carrier and add a message
- Go to payment step
- Click on edit shipping methods and check the message
- Choose the other carrier and check the message
- Choose a payment method and confirm the order
- Go to order details page and check the message
 */

describe('FO - Checkout - Shipping methods : Add order message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const message: string = 'Morbi a metus. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. '
    + 'Nullam sapien sem, ornare ac, nonummy non, lobortis a, enim. Nunc tincidunt ante vitae massa. Duis ante orci, '
    + 'molestie vitae, vehicula venenatis, tincidunt ac, pede. Nulla accumsan, elit sit123456789&é"'
    + '"\'(-è_çà)=+°&~#\\{[|`\\^@]}^$ù*!:;,?./§%µ¤²';
  const editMessage: string = 'Test message';

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_0`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Add order message', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // Go to FO
      await foHummingbirdHomePage.goToFo(page);

      // Change FO language
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foHummingbirdLoginPage.goToHomePage(page);
      // Go to the first product page
      await foHummingbirdHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foHummingbirdProductPage.addProductToTheCart(page);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should select '${dataCarriers.myCarrier.name}' and add a message`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      const isPaymentStepDisplayed = await foHummingbirdCheckoutPage.chooseShippingMethodAndAddComment(
        page,
        dataCarriers.myCarrier.id,
        message,
      );
      expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.eq(true);
    });

    it('should click on edit \'Shipping methods\' step and check the order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditShippingStep', baseContext);

      await foHummingbirdCheckoutPage.clickOnEditShippingMethodStep(page);

      const orderMessage = await foHummingbirdCheckoutPage.getOrderMessage(page);
      expect(orderMessage).to.equal(message);
    });

    it(`should choose the other carrier '${dataCarriers.clickAndCollect.name}' and edit the order message`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAnotherCarrier', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethodWithoutValidation(page, dataCarriers.clickAndCollect.id, editMessage);

      const isPaymentStep = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should choose a payment method and validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle: string = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to order history and details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);
      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details and check the messages box', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetailsPage', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page);

      const orderMessage = await foHummingbirdMyOrderDetailsPage.getBoxMessages(page);
      expect(orderMessage).to.contain(editMessage);
    });
  });

  // Pre-condition : Install Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_0`);
});
