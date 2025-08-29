import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

// context
const baseContext: string = 'functional_FO_classic_orderConfirmation_listOfOrderedProducts';

/*
Scenario:
- Add 3 products to cart
- Proceed to checkout and confirm the order
- Check the payment confirmation details
*/
describe('FO - Order confirmation : List of ordered products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;

  // before and after functions
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

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicHomePage.goToHomePage(page);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it(`should add the product ${dataProducts.demo_3.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_3.name);
      await foClassicSearchResultsPage.quickViewProduct(page, 1);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.closeBlockCartModal(page);
    });

    it(`should add the product ${
      dataProducts.demo_5.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo5ByQuickView', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_5.name);
      await foClassicSearchResultsPage.quickViewProduct(page, 1);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.closeBlockCartModal(page);
    });

    it(`should add the product ${dataProducts.demo_12.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo12ByQuickView', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_12.name);
      await foClassicSearchResultsPage.quickViewProduct(page, 1);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it(`should update the quantity for the product ${dataProducts.demo_5.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemoQuantity', baseContext);

      await foClassicCartPage.editProductQuantity(page, 2, 2);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(4);
    });

    it(`should update the quantity for the product ${dataProducts.demo_12.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDemo12Quantity', baseContext);

      await foClassicCartPage.editProductQuantity(page, 3, 2);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(5);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isCustomerConnected, 'Customer is not connected!').to.equal(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should select the first carrier and go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await foClassicCheckoutPage.chooseShippingMethod(page, dataCarriers.myCarrier.id);

      const isPaymentStep = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const pageTitle = await foClassicCheckoutOrderConfirmationPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCheckoutOrderConfirmationPage.pageTitle);

      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
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

      const paymentMethod = await foClassicCheckoutOrderConfirmationPage.getPaymentMethod(page);
      expect(paymentMethod).to.contains(dataPaymentMethods.wirePayment.displayName);

      const paymentInformation = await foClassicCheckoutOrderConfirmationPage.getPaymentInformation(page);
      expect(paymentInformation).to.contains('Please send us a '
        + `${dataPaymentMethods.wirePayment.name.toLowerCase()}`)
        .and.to.contains(`Amount €${totalToPay}`)
        .and.to.contains(`Please specify your order reference ${orderReference}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const orderDetails = await foClassicCheckoutOrderConfirmationPage.getOrderDetails(page);
      expect(orderDetails).to.equal(`Order reference: ${orderReference} Payment method: `
        + `${dataPaymentMethods.wirePayment.displayName} Shipping method: `
        + `${dataCarriers.myCarrier.name} ${dataCarriers.myCarrier.transitName}`);
    });

    it('should check the details of the first product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductDetails', baseContext);

      const result = await foClassicCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 1);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_3.coverImage),
        expect(result.details).to.equal(`${dataProducts.demo_3.name} (Size: S)`),
        expect(result.prices).to.equal(`€${dataProducts.demo_3.finalPrice} 1 €${dataProducts.demo_3.finalPrice}`),
      ]);
    });

    it('should check the details of the second product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductDetails', baseContext);

      const result = await foClassicCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 2);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_5.coverImage),
        expect(result.details).to.equal(`${dataProducts.demo_5.name} (Dimension: 40x60cm)`),
        expect(result.prices).to.equal(`€${dataProducts.demo_5.finalPrice.toFixed(2)}`
          + ` 2 €${(dataProducts.demo_5.finalPrice * 2).toFixed(2)}`),
      ]);
    });

    it('should check the details of the third product in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdProductDetails', baseContext);

      const result = await foClassicCheckoutOrderConfirmationPage.getProductDetailsInRow(page, 3);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.coverImage),
        expect(result.details).to.equal(`${dataProducts.demo_12.name}`),
        expect(result.prices).to.equal(
          `€${dataProducts.demo_12.finalPrice} 2 €${(dataProducts.demo_12.finalPrice * 2).toFixed(2)}`,
        ),
      ]);
    });
  });
});
