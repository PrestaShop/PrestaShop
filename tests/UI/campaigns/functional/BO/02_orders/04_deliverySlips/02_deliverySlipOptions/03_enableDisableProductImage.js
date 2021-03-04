require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Importing test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_deliverSlipsOptions_enableDisableProductImage';

let browserContext;
let page;

let filePath;

/*
Enable product image in delivery slip
Create order
Create delivery slip
Check that there is 2 images in the delivery slip (Logo and product image)
Disable product image in delivery slip
Create order
Create delivery slip
Check that there is 1 image in the delivery slip (Logo)
 */
describe('Test enable/disable product image in delivery slips', async () => {
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

  const tests = [
    {args: {action: 'Enable', enable: true, imageNumber: 2}},
    {args: {action: 'Disable', enable: false, imageNumber: 1}},
  ];

  tests.forEach((test) => {
    describe(`${test.args.action} product image in delivery slip then check the file created`, async () => {
      describe(`${test.args.action} product image`, async () => {
        it('should go to delivery slips page', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `goToDeliverySlipsPage_${test.args.action}`,
            baseContext,
          );

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.deliverySlipslink,
          );

          await deliverySlipsPage.closeSfToolBar(page);

          const pageTitle = await deliverySlipsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
        });

        it(`should ${test.args.action} product image`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);

          await deliverySlipsPage.setEnableProductImage(page, test.args.enable);
          const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
          await expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
        });
      });

      describe('Create new order in FO', async () => {
        it('should go to FO and create an order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createOrderInFO_${test.args.action}`, baseContext);

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

          // Go back to BO
          page = await orderConfirmationPage.closePage(browserContext, page, 0);
        });
      });

      describe('Generate the delivery slip and check product image', async () => {
        it('should go to the orders page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage_${test.args.action}`, baseContext);

          await deliverySlipsPage.goToSubMenu(
            page,
            deliverySlipsPage.ordersParentLink,
            deliverySlipsPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `goToCreatedOrderPage_${test.args.action}`,
            baseContext,
          );

          await ordersPage.goToOrder(page, 1);
          const pageTitle = await viewOrderPage.getPageTitle(page);
          await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
        });

        it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `updateOrderStatus_${test.args.action}`,
            baseContext,
          );

          const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
          await expect(result).to.equal(Statuses.shipped.status);
        });

        it('should download the delivery slip', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `downloadDeliverySlips_${test.args.action}`,
            baseContext,
          );

          filePath = await viewOrderPage.downloadDeliverySlip(page);

          const exist = await files.doesFileExist(filePath);
          await expect(exist).to.be.true;
        });

        it('should check the product images in the PDF File', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkProductImage_${test.args.action}`,
            baseContext,
          );

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber).to.be.equal(test.args.imageNumber);
        });
      });
    });
  });
});
