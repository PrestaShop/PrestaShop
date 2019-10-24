require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');

// Importing pages
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const LoginPage = require('@pages/FO/login');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
const {DefaultAccount} = require('@data/demo/customer');
const CartData = require('@data/FO/cart');
const {PaymentMethods} = require('@data/demo/orders');

let browser;
let page;

// creating pages objects in a function
const init = async function () {
  return {
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
    loginPage: new LoginPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
  Order a product and check order confirmation
 */
describe('Order a product and check order confirmation', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  it('should open the shop page', async function () {
    await this.pageObjects.homePage.goTo(global.FO.URL);
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
  });
  it('should go to login page', async function () {
    await this.pageObjects.homePage.goToLoginPage();
    const pageTitle = await this.pageObjects.loginPage.getPageTitle();
    await expect(pageTitle).to.equal(this.pageObjects.loginPage.pageTitle);
  });
  it('should sign In in FO With default account', async function () {
    await this.pageObjects.loginPage.customerLogin(DefaultAccount);
    const connected = await this.pageObjects.homePage.isCustomerConnected();
    await expect(connected, 'Customer is not connected in FO').to.be.true;
  });
  it('should go to home page', async function () {
    await this.pageObjects.homePage.goToHomePage();
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
  });
  it('should add first product to cart and Proceed to checkout', async function () {
    await this.pageObjects.homePage.addProductToCartByQuickView('1', '1');
    await this.pageObjects.homePage.proceedToCheckout();
    const pageTitle = await this.pageObjects.cartPage.getPageTitle();
    await expect(pageTitle).to.equal(this.pageObjects.cartPage.pageTitle);
  });
  it('should check the cart details', async function () {
    const result = await this.pageObjects.cartPage.checkProductInCart(CartData.customCartData.firstProduct, '1');
    await Promise.all([
      expect(result.name).to.be.true,
      expect(result.price).to.be.true,
      expect(result.quantity).to.be.true,
    ]);
  });
  it('should proceed to checkout and check Step Address', async function () {
    await this.pageObjects.cartPage.clickOnProceedToCheckout();
    const isCheckoutPage = await this.pageObjects.checkoutPage.isCheckoutPage();
    await expect(isCheckoutPage, 'Browser is not in checkout Page').to.be.true;
    const isStepPIComplete = await this.pageObjects.checkoutPage
      .isStepCompleted(this.pageObjects.checkoutPage.personalInformationStepSection);
    await expect(isStepPIComplete, 'Step Personal information is not complete').to.be.true;
  });
  it('should validate Step Address and go to Delivery Step', async function () {
    const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
    await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
  });
  it('should validate Step Delivery and go to Payment Step', async function () {
    const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
    await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
  });
  it('should Pay by back wire and confirm order', async function () {
    await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
    const pageTitle = await this.pageObjects.orderConfirmationPage.getPageTitle();
    await expect(pageTitle).to.equal(this.pageObjects.orderConfirmationPage.pageTitle);
    const cardTitle = await this.pageObjects.orderConfirmationPage
      .getTextContent(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitleH3);
    await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
  });
});
