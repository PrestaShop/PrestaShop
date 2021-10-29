require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import pages
// FO pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const foOrderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const CartData = require('@data/FO/cart');
const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import test context
const testContext = require('@utils/testContext');

// context
const baseContext = 'functional_FO_userAccount_orderHistory_orderDetails_reorderFromOrderDetail';

let browserContext;
let page;

/*
Go to the FO homepage
Login to customer account
Make an order
Go to userAccount page
Go to order history and details
Go to the order detail
Click on the reorder link
Proceed checkout
Go back to the order list
Check if the reorder is displayed
Go to the order detail
Check if the reorder contain the same product than the "original" order
 */
describe('FO - Account : Reorder from order detail', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to FO and make an order', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);
      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFo', baseContext);

      await foHomePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHomePage.goToHomePage(page);
      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.addProductToCartByQuickView(page, 1, '1');
      await foHomePage.proceedToCheckout(page);
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

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      await expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Go to order detail and proceed reorder', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      const pageTitle = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);
      const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should click on reorder link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnReorderLink', baseContext);

      await orderDetailsPage.clickOnReorderLink(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage, 'Browser is not in checkout Page').to.be.true;
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStepForReorder', baseContext);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStepForReorder', baseContext);

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should Pay by bank wire and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmReorder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const pageTitle = await orderConfirmationPage.getPageTitle(page);
      await expect(pageTitle).to.equal(orderConfirmationPage.pageTitle);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Go to new order detail and check content', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      const pageTitle = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go back to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrderHistoryPage', baseContext);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);
      const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFoToOrderDetails', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should check the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTheOrderedProduct', baseContext);

      const orderedProduct = await orderDetailsPage.getProductName(page);
      await expect(orderedProduct).to.contain(CartData.customCartData.firstProduct.name);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await orderConfirmationPage.logout(page);
      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });
});
