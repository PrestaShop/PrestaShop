import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  foClassicCartPage,
  foClassicCategoryPage,
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
const baseContext: string = 'functional_FO_classic_orderConfirmation_popularProduct';

describe('FO - Order confirmation : Popular product', async () => {
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

  it('should check popular product title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProducts', baseContext);

    const popularProductTitle = await foClassicCheckoutOrderConfirmationPage.getBlockTitle(page);
    expect(popularProductTitle).to.equal('Popular Products');
  });

  it('should check the number of popular products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPopularProductsNumber', baseContext);

    const productsNumber = await foClassicCheckoutOrderConfirmationPage.getProductsBlockNumber(page);
    expect(productsNumber).to.equal(8);
  });

  it('should quick view the first product in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

    await foClassicCheckoutOrderConfirmationPage.quickViewProduct(page, 1);

    const isQuickViewModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isQuickViewModalVisible).to.equal(true);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicModalQuickViewPage.addToCartByQuickView(page);
    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.eq(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should go to delivery address step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmAddressStep', baseContext);

    const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isDeliveryStep, 'Delivery Step boc is not displayed').to.eq(true);
  });

  it('should choose the shipping method', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'shippingMethodStep', baseContext);

    const isPaymentStep = await foClassicCheckoutPage.goToPaymentStep(page);
    expect(isPaymentStep, 'Payment Step bloc is not displayed').to.eq(true);
  });

  it('should choose the payment type and confirm the order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

    await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

    const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
    // Check the confirmation message
    expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should click on all products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnAllProductsPage', baseContext);

    await foClassicCheckoutOrderConfirmationPage.goToAllProductsPage(page);

    const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
  });
});
