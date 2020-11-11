require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_email_sortAndPagination';

let browserContext;
let page;

let numberOfEmails = 0;

/*
Create an order to have 2 email logs in email table
Filter email logs list
Delete email log
Delete email logs by bulk action
 */
describe('Filter, delete and bulk actions email log', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Create order to have email logs', async () => {
    it('should go to FO and create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderInFO', baseContext);

      // Click on view my shop
      page = await dashboardPage.viewMyShop(page);

      // Change language in FO
      await homePage.changeLanguage(page, 'en');

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the created product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Checkout the order

      // Personal information step - Login
      await checkoutPage.clickOnSignIn(page);
      await checkoutPage.customerLogin(page, DefaultAccount);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);

      // Logout from FO
      await orderConfirmationPage.logout(page);

      // Go Back to BO
      page = await orderConfirmationPage.closePage(browserContext, page, 0);
    });
  });

  /*describe('Delete email logs by bulk action', async () => {
    it('should delete all email logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await emailPage.deleteEmailLogsBulkActions(page);
      await expect(deleteTextResult).to.be.equal(emailPage.successfulMultiDeleteMessage);
    });
  });*/
});
