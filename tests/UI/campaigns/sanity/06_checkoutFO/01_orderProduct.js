require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_checkoutFO_orderProduct';

// Importing pages
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const loginPage = require('@pages/FO/login');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

const {DefaultCustomer} = require('@data/demo/customer');
const CartData = require('@data/FO/cart');
const {PaymentMethods} = require('@data/demo/paymentMethods');

let browserContext;
let page;

/*
  Order a product and check order confirmation
 */
describe('Order a product and check order confirmation', async () => {
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
    await expect(result).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await homePage.goToLoginPage(page);
    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should sign In in FO With default account', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

    await loginPage.customerLogin(page, DefaultCustomer);
    const connected = await homePage.isCustomerConnected(page);
    await expect(connected, 'Customer is not connected in FO').to.be.true;
  });

  it('should go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

    await homePage.goToHomePage(page);
    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should add first product to cart and Proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await homePage.addProductToCartByQuickView(page, 1, '1');
    await homePage.proceedToCheckout(page);
    const pageTitle = await cartPage.getPageTitle(page);
    await expect(pageTitle).to.equal(cartPage.pageTitle);
  });

  it('should check the cart details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCartDetails', baseContext);

    const result = await cartPage.getProductDetail(page, 1);
    await Promise.all([
      expect(result.name).to.equal(CartData.customCartData.firstProduct.name),
      expect(result.price).to.equal(CartData.customCartData.firstProduct.price),
      expect(result.quantity).to.equal(CartData.customCartData.firstProduct.quantity),
    ]);
  });

  it('should proceed to checkout and check Step Address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

    await cartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
    await expect(isCheckoutPage, 'Browser is not in checkout Page').to.be.true;

    const isStepPersonalInformationComplete = await checkoutPage.isStepCompleted(
      page,
      checkoutPage.personalInformationStepForm,
    );
    await expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.be.true;
  });

  it('should validate Step Address and go to Delivery Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);
    const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
    await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
  });

  it('should validate Step Delivery and go to Payment Step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
  });

  it('should Pay by back wire and confirm order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

    await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
    const pageTitle = await orderConfirmationPage.getPageTitle(page);
    await expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
    await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });
});
