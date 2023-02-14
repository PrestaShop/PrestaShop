// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import cartPage from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import homePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import productPage from '@pages/FO/product';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_checkout_shippingMethods_addOrderMessage';

/*
Scenario:
- Go to FO and login by default customer
- Add a product to cart and checkout
- In shipping methods, choose My carrier and add a message
- Go to payment step
- Click on edit shipping methods and check the message
- Choose the other carrier and check the message
 */

describe('FO - Checkout - Shipping methods : Add order message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const message: string = 'Morbi a metus. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. '
    + 'Nullam sapien sem, ornare ac, nonummy non, lobortis a, enim. Nunc tincidunt ante vitae massa. Duis ante orci, '
    + 'molestie vitae, vehicula venenatis, tincidunt ac, pede. Nulla accumsan, elit sit123456789&é"'
    + '"\'(-è_çà)=+°&~#\\{[|`\\^@]}^$ù*!:;,?./§%µ¤²';

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    // Go to FO
    await homePage.goToFo(page);

    // Change FO language
    await homePage.changeLanguage(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

    await homePage.goToLoginPage(page);

    const pageTitle = await foLoginPage.getPageTitle(page);
    await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
  });

  it('should sign in with customer credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

    await foLoginPage.customerLogin(page, Customers.johnDoe);

    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    // Go to home page
    await foLoginPage.goToHomePage(page);
    // Go to the first product page
    await homePage.goToProductPage(page, 1);
    // Add the product to the cart
    await productPage.addProductToTheCart(page);

    const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
    await expect(notificationsNumber).to.be.equal(1);
  });

  it('should go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

    // Proceed to checkout the shopping cart
    await cartPage.clickOnProceedToCheckout(page);

    // Address step - Go to delivery step
    const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
    await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
  });

  it(`should select '${Carriers.myCarrier.name}' and add a message`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    const isPaymentStepDisplayed = await checkoutPage.chooseShippingMethodAndAddComment(
      page,
      Carriers.myCarrier.id,
      message,
    );
    await expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.be.true;
  });

  it('should click on edit \'Shipping methods\' step and check the order message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditShippingStep', baseContext);

    await checkoutPage.clickOnEditShippingMethodStep(page);

    const orderMessage = await checkoutPage.getOrderMessage(page);
    await expect(orderMessage).to.equal(message);
  });

  it(`should choose the other carrier '${Carriers.default.name}' and check the order message`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAnotherCarrier', baseContext);

    await checkoutPage.chooseShippingMethod(page, Carriers.default.id);

    const orderMessage = await checkoutPage.getOrderMessage(page);
    await expect(orderMessage).to.equal(message);
  });

  it('should click on continue button and check the payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinue', baseContext);

    const isPaymentStep = await checkoutPage.goToPaymentStep(page);
    await expect(isPaymentStep).to.be.true;
  });
});
