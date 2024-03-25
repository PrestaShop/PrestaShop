// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import ordersPage from '@pages/BO/orders';
import dashboardPage from '@pages/BO/dashboard';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import orderConfirmationPage from '@pages/FO/hummingbird/checkout/orderConfirmation';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Products from '@data/demo/products';
import PaymentMethods from '@data/demo/paymentMethods';
import Carriers from '@data/demo/carriers';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_hummingbird_orderConfirmation_listOfOrderedProducts';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Add 3 products to cart
- Proceed to checkout and confirm the order
- Check the payment confirmation details
Post-condition:
- Uninstall the theme hummingbird
*/
describe('FO - Order confirmation : List of ordered products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create new order in FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should add the product ${Products.demo_3.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await homePage.searchProduct(page, Products.demo_3.name);
      await searchResultsPage.quickViewProduct(page, 1);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.closeBlockCartModal(page);
    });

    it(`should add the product ${Products.demo_5.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo5ByQuickView', baseContext);

      await homePage.searchProduct(page, Products.demo_5.name);
      await searchResultsPage.quickViewProduct(page, 1);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.closeBlockCartModal(page);
    });

    it(`should add the product ${Products.demo_12.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo12ByQuickView', baseContext);

      await homePage.searchProduct(page, Products.demo_12.name);
      await searchResultsPage.quickViewProduct(page, 1);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it(`should update the quantity for the product ${Products.demo_5.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemoQuantity', baseContext);

      await cartPage.editProductQuantity(page, 2, 2);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(4);
    });

    it(`should update the quantity for the product ${Products.demo_5.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemoQuantity', baseContext);

      await cartPage.editProductQuantity(page, 3, 2);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await checkoutPage.chooseShippingMethod(page, Carriers.myCarrier.id);

      const isPaymentStep = await checkoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Get the order reference from the BO', async () => {
    it('should login in BO', async function () {
      page = await helper.newTab(browserContext);
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should get the order reference of the first order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

      orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
      expect(orderReference).to.not.eq(null);
    });
  });

  describe('Check list of ordered products', async () => {
    it('should check the payment information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentInformation', baseContext);

      page = await ordersPage.changePage(browserContext, 0);
      const totalToPay: string = (Products.demo_3.finalPrice + (2 * Products.demo_5.finalPrice)
        + (2 * Products.demo_12.finalPrice) + Carriers.myCarrier.priceTTC).toFixed(2);

      const paymentInformation = await orderConfirmationPage.getPaymentInformation(page);
      expect(paymentInformation).to.contains(`You have chosen payment by ${PaymentMethods.wirePayment.displayName.toLowerCase()}`)
        .and.to.contains(`Amount €${totalToPay}`)
        .and.to.contains(`Please specify your order reference ${orderReference}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const orderDetails = await orderConfirmationPage.getOrderDetails(page);
      console.log(orderDetails);
      expect(orderDetails).to.equal(`Order reference: ${orderReference} Payment method: `
        + `${PaymentMethods.wirePayment.displayName} Shipping method: ${Carriers.myCarrier.name} - ${Carriers.myCarrier.delay}`);
    });

    it('should check the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

      const productsNumber = await orderConfirmationPage.getNumberOfProducts(page);
      expect(productsNumber).to.equal(3);
    });

    it('should check the details of the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductDetails', baseContext);

      const result = await orderConfirmationPage.getProductDetailsInRow(page, 1);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_3.coverImage),
        expect(result.details).to.equal(`${Products.demo_3.name} (Size: S) Reference ${Products.demo_3.reference}`),
        expect(result.prices).to.equal(`€${Products.demo_3.finalPrice} (x1) €${Products.demo_3.finalPrice}`),
      ]);
    });

    it('should check the details of the second product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductDetails', baseContext);

      const result = await orderConfirmationPage.getProductDetailsInRow(page, 2);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_5.coverImage),
        expect(result.details).to.equal(`${Products.demo_5.name} (Dimension: 40x60cm) Reference ${Products.demo_5.reference}`),
        expect(result.prices).to.equal(`€${Products.demo_5.finalPrice.toFixed(2)}`
          + ` (x2) €${(Products.demo_5.finalPrice * 2).toFixed(2)}`),
      ]);
    });

    it('should check the details of the third product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdProductDetails', baseContext);

      const result = await orderConfirmationPage.getProductDetailsInRow(page, 3);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.coverImage),
        expect(result.details).to.equal(`${Products.demo_12.name} Reference ${Products.demo_12.reference}`),
        expect(result.prices).to.equal(`€${Products.demo_12.finalPrice} (x2) €${(Products.demo_12.finalPrice * 2).toFixed(2)}`),
      ]);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
