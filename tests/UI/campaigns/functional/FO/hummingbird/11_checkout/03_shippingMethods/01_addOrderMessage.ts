// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';
import productPage from '@pages/FO/hummingbird/product';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';
import myAccountPage from '@pages/FO/hummingbird/myAccount';
import orderHistoryPage from '@pages/FO/hummingbird/myAccount/orderHistory';
import orderDetailsPage from '@pages/FO/hummingbird/myAccount/orderDetails';

// Import data
import Carriers from '@data/demo/carriers';
import PaymentMethods from '@data/demo/paymentMethods';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
  installHummingbird(`${baseContext}_preTest_0`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Add order message', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // Go to FO
      await homePage.goToFo(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await loginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(loginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await loginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await loginPage.goToHomePage(page);
      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it(`should select '${Carriers.myCarrier.name}' and add a message`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      const isPaymentStepDisplayed = await checkoutPage.chooseShippingMethodAndAddComment(
        page,
        Carriers.myCarrier.id,
        message,
      );
      expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.eq(true);
    });

    it('should click on edit \'Shipping methods\' step and check the order message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditShippingStep', baseContext);

      await checkoutPage.clickOnEditShippingMethodStep(page);

      const orderMessage = await checkoutPage.getOrderMessage(page);
      expect(orderMessage).to.equal(message);
    });

    it(`should choose the other carrier '${Carriers.default.name}' and edit the order message`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAnotherCarrier', baseContext);

      await checkoutPage.chooseShippingMethodWithoutValidation(page, Carriers.default.id, editMessage);

      const isPaymentStep = await checkoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should choose a payment method and validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle: string = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to order history and details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details and check the messages box', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetailsPage', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const orderMessage = await orderDetailsPage.getBoxMessages(page);
      expect(orderMessage).to.contain(editMessage);
    });
  });

  // Pre-condition : Install Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_0`);
});
