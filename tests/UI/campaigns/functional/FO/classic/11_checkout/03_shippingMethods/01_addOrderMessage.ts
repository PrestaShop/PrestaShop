import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_checkout_shippingMethods_addOrderMessage';

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

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    // Go to FO
    await foClassicHomePage.goToFo(page);

    // Change FO language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
  });

  it('should sign in with customer credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    // Go to home page
    await foClassicLoginPage.goToHomePage(page);
    // Go to the first product page
    await foClassicHomePage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Address step - Go to delivery step
    const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it(`should select '${dataCarriers.myCarrier.name}' and add a message`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    const isPaymentStepDisplayed = await foClassicCheckoutPage.chooseShippingMethodAndAddComment(
      page,
      dataCarriers.myCarrier.id,
      message,
    );
    expect(isPaymentStepDisplayed, 'Payment Step is not displayed').to.eq(true);
  });

  it('should click on edit \'Shipping methods\' step and check the order message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditShippingStep', baseContext);

    await foClassicCheckoutPage.clickOnEditShippingMethodStep(page);

    const orderMessage = await foClassicCheckoutPage.getOrderMessage(page);
    expect(orderMessage).to.equal(message);
  });

  it(`should choose the other carrier '${dataCarriers.clickAndCollect.name}' and edit the order message`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAnotherCarrier', baseContext);

    await foClassicCheckoutPage.chooseShippingMethodWithoutValidation(page, dataCarriers.clickAndCollect.id, editMessage);

    const isPaymentStep = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isPaymentStep).to.eq(true);
  });

  it('should choose a payment method and validate the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle: string = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should go to order history and details page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);
    await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

    const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
    expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
  });

  it('should go to order details and check the messages box', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetailsPage', baseContext);

    await foClassicMyOrderHistoryPage.goToDetailsPage(page);

    const orderMessage = await foClassicMyOrderDetailsPage.getBoxMessages(page);
    expect(orderMessage).to.contain(editMessage);
  });
});
