require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
const DeliverySlipOptionsFaker = require('@data/faker/deliverySlipOptions');

// Importing test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_deliverSlipsOptions_deliverySlipNumber';

let browserContext;
let page;
let fileName;

const deliverySlipData = new DeliverySlipOptionsFaker();

/*
Edit Delivery slip number
Create order
Change the Order status to Shipped
Check the delivery slip file name
 */

describe('Edit \'Delivery slip number\' and check the generated file name', async () => {
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

  describe('Edit the Delivery slip number', async () => {
    it('should go to delivery slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPageToUpdateNumber', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.deliverySlipslink,
      );

      await deliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it('should change the Delivery slip number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDeliverySlipsNumber', baseContext);

      await deliverySlipsPage.changeNumber(page, deliverySlipData.number);
      const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
      await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe('Create new order in FO', async () => {
    it('should go to FO and create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderInFO', baseContext);

      // Click on view my shop
      page = await deliverySlipsPage.viewMyShop(page);

      // Change FO language
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
      await checkoutPage.customerLogin(page, DefaultCustomer);

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

      // Close tab and go back to BO
      page = await orderConfirmationPage.closePage(browserContext, page, 0);
    });
  });

  describe('Create a delivery slip and check the edited data', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await deliverySlipsPage.goToSubMenu(
        page,
        deliverySlipsPage.ordersParentLink,
        deliverySlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);
      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check that the delivery slip file name contain the \'Delivery slip number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipsDocumentName', baseContext);

      // Get delivery slips filename
      fileName = await viewOrderPage.getFileName(page, 3);
      expect(fileName).to.contains(deliverySlipData.number);
    });
  });
});
