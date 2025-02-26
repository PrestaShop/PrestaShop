import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
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

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
  });

  it('should sign In in FO with default account', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const connected = await foClassicHomePage.isCustomerConnected(page);
    expect(connected, 'Customer is not connected in FO').to.eq(true);
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    const isHomepage = await foClassicHomePage.isHomePage(page);

    if (!isHomepage) {
      await foClassicHomePage.goToHomePage(page);
    }

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);

    const isQuickViewModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isQuickViewModalVisible).to.equal(true);
  });

  it('should add first product to cart and Proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicModalQuickViewPage.addToCartByQuickView(page);
    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should check the cart details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

    const result = await foClassicCartPage.getProductDetail(page, 1);
    await Promise.all([
      expect(result.name).to.equal(dataProducts.demo_1.name),
      expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
      expect(result.quantity).to.equal(1),
    ]);
  });

  it('should proceed to checkout and check Step Address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage, 'Browser is not in checkout Page').to.eq(true);

    const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
      page,
      foClassicCheckoutPage.personalInformationStepForm,
    );
    expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
  });

  it('should validate Step Address and go to Delivery Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

    const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should validate Step Delivery and go to Payment Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
  });

  it('should Pay by back wire and confirm order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    const pageTitle = await foClassicCheckoutOrderConfirmationPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCheckoutOrderConfirmationPage.pageTitle);

    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });
});
