import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foClassicCartPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_FO_classic_cart';

describe('Check FO pages in the checkout process', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should go to the home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicLoginPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should sign in with customer credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'customerLogin', baseContext);

    await foClassicLoginPage.customerLogin(page, orderData.customer);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicLoginPage.goToHomePage(page);
    await foClassicHomePage.goToProductPage(page, orderData.products[0].product.id);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should add product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicProductPage.addProductToTheCart(page, orderData.products[0].quantity);

    const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to delivery step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to payment step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should choose payment method and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    await foClassicCheckoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
