import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new order in FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should add the product ${dataProducts.demo_3.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_3.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.closeBlockCartModal(page);
    });

    it(`should add the product ${dataProducts.demo_5.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo5ByQuickView', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_5.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.closeBlockCartModal(page);
    });

    it(`should add the product ${dataProducts.demo_12.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo12ByQuickView', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_12.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it(`should update the quantity for the product ${dataProducts.demo_5.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemoQuantity', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 2, 2);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(4);
    });

    it(`should update the quantity for the product ${dataProducts.demo_12.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemo12Quantity', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 3, 2);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foHummingbirdCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const isPaymentStep = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const pageTitle = await foHummingbirdCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Get the order reference from the BO', async () => {
    it('should login in BO', async function () {
      page = await utilsPlaywright.newTab(browserContext);
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should get the order reference of the first order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

      orderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
      expect(orderReference).to.not.eq(null);
    });
  });

  describe('Check list of ordered products', async () => {
    it('should check the payment information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentInformation', baseContext);

      page = await boOrdersPage.changePage(browserContext, 0);
      const totalToPay: string = (dataProducts.demo_3.finalPrice + (2 * dataProducts.demo_5.finalPrice)
        + (2 * dataProducts.demo_12.finalPrice) + dataCarriers.myCarrier.priceTTC).toFixed(2);

      const paymentInformation = await foHummingbirdCheckoutOrderConfirmationPage.getPaymentInformation(page);
      expect(paymentInformation).to.contains('You have chosen payment by '
        + `${dataPaymentMethods.wirePayment.displayName.toLowerCase()}`)
        .and.to.contains(`Amount €${totalToPay}`)
        .and.to.contains(`Please specify your order reference ${orderReference}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const orderDetails = await foHummingbirdCheckoutOrderConfirmationPage.getOrderDetails(page);
      expect(orderDetails).to.equal(`Order reference: ${orderReference} Payment method: `
        + `${dataPaymentMethods.wirePayment.displayName} Shipping method: `
        + `${dataCarriers.myCarrier.name} - ${dataCarriers.myCarrier.transitName}`);
    });

    it('should check the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

      const productsNumber = await foHummingbirdCheckoutOrderConfirmationPage.getNumberOfProducts(page);
      expect(productsNumber).to.equal(3);
    });

    it('should check the details of the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductDetails', baseContext);

      const result = await foHummingbirdCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 1);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_3.coverImage),
        expect(result.details).to.contains(dataProducts.demo_3.name),
        expect(result.details).to.contains('Size: S'),
        expect(result.details).to.contains(`Reference: ${dataProducts.demo_3.reference}`),
        expect(result.prices).to.equal(`€${dataProducts.demo_3.finalPrice}`),
      ]);
    });

    it('should check the details of the second product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductDetails', baseContext);

      const result = await foHummingbirdCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 2);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_5.coverImage),
        expect(result.details).to.contains(dataProducts.demo_5.name),
        expect(result.details).to.contains('Dimension: 40x60cm'),
        expect(result.details).to.contains(`Reference: ${dataProducts.demo_5.reference}`),
        expect(result.prices).to.equal(`€${(dataProducts.demo_5.finalPrice * 2).toFixed(2)}`),
      ]);
    });

    it('should check the details of the third product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdProductDetails', baseContext);

      const result = await foHummingbirdCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 3);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.coverImage),
        expect(result.details).to.contains(dataProducts.demo_12.name),
        expect(result.details).to.contains(`Reference: ${dataProducts.demo_12.reference}`),
        expect(result.prices).to.equal(`€${(dataProducts.demo_12.finalPrice * 2).toFixed(2)}`),
      ]);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
