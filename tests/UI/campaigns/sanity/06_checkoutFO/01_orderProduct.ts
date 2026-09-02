import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'sanity_checkoutFO_orderProduct';

/*
  Order a product and check order confirmation
 */
describe('BO - Checkout : Order a product and check order confirmation', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Steps
  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await foHummingbirdHomePage.goTo(page, global.FO.URL);

    const result = await foHummingbirdHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await foHummingbirdHomePage.goToLoginPage(page);

    const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
    expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
  });

  it('should sign In in FO with default account', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

    await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const connected = await foHummingbirdHomePage.isCustomerConnected(page);
    expect(connected, 'Customer is not connected in FO').to.eq(true);
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    const isHomepage = await foHummingbirdHomePage.isHomePage(page);

    if (!isHomepage) {
      await foHummingbirdHomePage.goToHomePage(page);
    }

    const result = await foHummingbirdHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

    await foHummingbirdHomePage.quickViewProduct(page, 1);

    const isQuickViewModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isQuickViewModalVisible).to.equal(true);
  });

  it('should add first product to cart and Proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
    await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
  });

  it('should check the cart details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

    const result = await foHummingbirdCartPage.getProductDetail(page, 1);
    await Promise.all([
      expect(result.name).to.equal(dataProducts.demo_1.name),
      expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
      expect(result.quantity).to.equal(1),
    ]);
  });

  it('should proceed to checkout and check Step Address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

    await foHummingbirdCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);

    const isStepPersonalInformationComplete = await foHummingbirdCheckoutPage.isStepCompleted(
      page,
      foHummingbirdCheckoutPage.personalInformationStepForm,
    );
    expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
  });

  it('should validate Step Address and go to Delivery Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

    const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should validate Step Delivery and go to Payment Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should Pay by back wire and confirm order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    const pageTitle = await foHummingbirdCheckoutOrderConfirmationPage.getPageTitle(page);
    expect(pageTitle).to.equal(foHummingbirdCheckoutOrderConfirmationPage.pageTitle);

    const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });
});
