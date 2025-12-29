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

const baseContext: string = 'functional_FO_hummingbird_orderConfirmation_recapPaymentMethod';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Add 1 products to cart
- Proceed to checkout and confirm the order
- Check the recap of payment method
Post-condition:
- Uninstall the theme hummingbird
*/
describe('FO - Order confirmation : Order details and totals - Recap of payment method', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_0`);

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

    it(`should add the product ${dataProducts.demo_6.name} to cart by quick view`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDemo3ByQuickView', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
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

      await foHummingbirdCheckoutPage.chooseShippingMethod(page, dataCarriers.clickAndCollect.id);

      const isPaymentStep = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isPaymentStep).to.eq(true);
    });

    it('should Pay by check and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.checkPayment.moduleName);

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

      page = await boOrdersPage.changePage(browserContext, 0);
    });
  });

  describe('Check recap of payment method', async () => {
    it('should check the subtotal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSubTotal', baseContext);

      const orderSubTotal = await foHummingbirdCheckoutOrderConfirmationPage.getOrderSubTotal(page);
      expect(orderSubTotal).to.equal(`€${dataProducts.demo_6.combinations[0].price.toFixed(2)}`);
    });

    it('should check the shipping total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingTotal', baseContext);

      const orderSubTotal = await foHummingbirdCheckoutOrderConfirmationPage.getOrderShippingTotal(page);
      expect(orderSubTotal).to.equal('Free');
    });

    it('should check the total (tax incl.)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalTaxInc', baseContext);

      const orderTotalTaxInc = await foHummingbirdCheckoutOrderConfirmationPage.getOrderTotal(page);
      expect(orderTotalTaxInc).to.equal(`€${dataProducts.demo_6.combinations[0].price.toFixed(2)}`);
    });

    it('should check the order details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderDetails', baseContext);

      const paymentMethod = await foHummingbirdCheckoutOrderConfirmationPage.getPaymentMethod(page);
      expect(paymentMethod).to.contains(dataPaymentMethods.checkPayment.displayName);

      const orderReferenceValue = await foHummingbirdCheckoutOrderConfirmationPage.getOrderReferenceValue(page);
      expect(orderReferenceValue).to.contains(orderReference);

      const shippingMethod = await foHummingbirdCheckoutOrderConfirmationPage.getShippingMethod(page);
      expect(shippingMethod).to.contains(`${dataCarriers.clickAndCollect.name} - ${dataCarriers.clickAndCollect.transitName}`);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
