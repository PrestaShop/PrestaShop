// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_checkoutFO_orderProduct';

/*
  Order a product and check order confirmation
 */
describe('BO - Checkout : Order a product and check order confirmation', async () => {
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

  // Steps
  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await homePage.goTo(page, global.FO.URL);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await homePage.goToLoginPage(page);

    const pageTitle = await loginPage.getPageTitle(page);
    expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should sign In in FO with default account', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

    await loginPage.customerLogin(page, Customers.johnDoe);

    const connected = await homePage.isCustomerConnected(page);
    expect(connected, 'Customer is not connected in FO').to.eq(true);
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    const isHomepage = await homePage.isHomePage(page);

    if (!isHomepage) {
      await homePage.goToHomePage(page);
    }

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

    await homePage.quickViewProduct(page, 1);

    const isQuickViewModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
    expect(isQuickViewModalVisible).to.equal(true);
  });

  it('should add first product to cart and Proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await quickViewModal.addToCartByQuickView(page);
    await blockCartModal.proceedToCheckout(page);

    const pageTitle = await cartPage.getPageTitle(page);
    expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should check the cart details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

    const result = await cartPage.getProductDetail(page, 1);
    await Promise.all([
      expect(result.name).to.equal(Products.demo_1.name),
      expect(result.price).to.equal(Products.demo_1.finalPrice),
      expect(result.quantity).to.equal(1),
    ]);
  });

  it('should proceed to checkout and check Step Address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

    await cartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);

    const isStepPersonalInformationComplete = await checkoutPage.isStepCompleted(
      page,
      checkoutPage.personalInformationStepForm,
    );
    expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
  });

  it('should validate Step Address and go to Delivery Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

    const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should validate Step Delivery and go to Payment Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should Pay by back wire and confirm order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

    const pageTitle = await orderConfirmationPage.getPageTitle(page);
    expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });
});
