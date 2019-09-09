// Using chai
const {expect} = require('chai');

// Importing pages
const HomePage = require('../../../pages/FO/home');
const CartPage = require('../../../pages/FO/cart');
const LoginPage = require('../../../pages/FO/login');
const CheckoutPage = require('../../../pages/FO/checkout');
const OrderConfirmationPage = require('../../../pages/FO/orderConfirmation');
const customer = require('../../data/FO/customer');
const CartData = require('../../data/FO/cart');

let page;
let homePage;
let cartPage;
let loginPage;
let checkoutPage;
let orderConfirmationPage;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  await page.setExtraHTTPHeaders({
    'Accept-Language': 'en-GB',
  });
  homePage = await (new HomePage(page));
  cartPage = await (new CartPage(page));
  loginPage = await (new LoginPage(page));
  checkoutPage = await (new CheckoutPage(page));
  orderConfirmationPage = await (new OrderConfirmationPage(page));
};

/*
  Order a product and check order confirmation
 */
global.scenario('Order a product and check order confirmation', () => {
  test('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should go to login page', async () => {
    await homePage.goToLoginPage();
    const pageTitle = await loginPage.getPageTitle();
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });
  test('should sign In in FO With default account', async () => {
    await loginPage.customerLogin(customer.defaultAccount);
    const connected = await homePage.isCustomerConnected();
    await expect(connected, 'Customer is not connected in FO').to.be.true;
  });
  test('should go to home page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should add first product to cart and Proceed to checkout', async () => {
    await homePage.addProductToCartByQuickView('1', '1');
    await homePage.proceedToCheckout();
    const pageTitle = await cartPage.getPageTitle();
    await expect(pageTitle).to.equal(cartPage.pageTitle);
  });
  test('should check the cart details', async () => {
    await cartPage.checkProductInCart(CartData.customCartData.firstProduct, '1');
  });
  test('should proceed to checkout and check Step Address', async () => {
    await cartPage.clickOnProceedToCheckout();
    const isCheckoutPage = await checkoutPage.isCheckoutPage();
    await expect(isCheckoutPage, 'Browser is not in checkout Page').to.be.true;
    const isStepPIComplete = await checkoutPage.isStepCompleted(checkoutPage.personalInformationStepSection);
    await expect(isStepPIComplete, 'Step Personal information is not complete').to.be.true;
  });
  test('should validate Step Address and go to Delivery Step', async () => {
    const isStepAddressComplete = await checkoutPage.goToDeliveryStep();
    await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
  });
  test('should validate Step Delivery and go to Payment Step', async () => {
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep();
    await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
  });
  test('should Pay by back wire and confirm order', async () => {
    await checkoutPage.choosePaymentAndOrder('ps_wirepayment');
    const pageTitle = await orderConfirmationPage.getPageTitle();
    await expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);
    const cardTitle = await orderConfirmationPage.getTextContent(orderConfirmationPage.orderConfirmationCardTitleH3);
    await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });
}, init, true);
