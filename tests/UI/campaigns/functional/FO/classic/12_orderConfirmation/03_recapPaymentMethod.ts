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
const baseContext: string = 'functional_FO_classic_orderConfirmation_recapPaymentMethod';

/*
Scenario:
- Add 1 products to cart
- Proceed to checkout and confirm the order
- Check the recap of payment method
*/
describe('FO - Order confirmation : Order details and totals - Recap of payment method', async () => {
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

    it(`should add the product ${dataProducts.demo_6.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foClassicSearchResultsPage.quickViewProduct(page, 1);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
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

      await foClassicCheckoutPage.chooseShippingMethod(page, dataCarriers.clickAndCollect.id);

      const isPaymentStep = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by check and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.checkPayment.moduleName);

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

      page = await boOrdersPage.changePage(browserContext, 0);
    });
  });

  describe('Check recap of payment method', async () => {
    it('should check the subtotal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSubTotal', baseContext);

      const orderSubTotal = await foClassicCheckoutOrderConfirmationPage.getOrderSubTotal(page);
      expect(orderSubTotal).to.equal(`€${dataProducts.demo_6.combinations[0].price.toFixed(2)}`);
    });

    it('should check the shipping total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingTotal', baseContext);

      const orderSubTotal = await foClassicCheckoutOrderConfirmationPage.getOrderShippingTotal(page);
      expect(orderSubTotal).to.equal('Free');
    });

    it('should check the total (tax incl.)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalTaxInc', baseContext);

      const orderTotalTaxInc = await foClassicCheckoutOrderConfirmationPage.getOrderTotal(page);
      expect(orderTotalTaxInc).to.equal(`€${dataProducts.demo_6.combinations[0].price.toFixed(2)}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const paymentMethod = await foClassicCheckoutOrderConfirmationPage.getPaymentMethod(page);
      expect(paymentMethod).to.contains(dataPaymentMethods.checkPayment.displayName);

      const orderReferenceValue = await foClassicCheckoutOrderConfirmationPage.getOrderReferenceValue(page);
      expect(orderReferenceValue).to.contains(orderReference);

      const shippingMethod = await foClassicCheckoutOrderConfirmationPage.getShippingMethod(page);
      expect(shippingMethod).to.contains(`${dataCarriers.clickAndCollect.name} ${dataCarriers.clickAndCollect.transitName}`);
    });
  });
});
