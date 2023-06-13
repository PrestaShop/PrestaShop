require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_enableDisableProductImage';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let filePath;

/*
Enable product image in invoice
Create order
Create invoice
Check that there is 2 images in the invoice (Logo and product image)
Disable product image in invoice
Create order
Create invoice
Check that there is 1 image in the invoice (Logo)
 */
describe('BO - Orders - Invoices : Enable/Disable product image in invoices', async () => {
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
    {args: {action: 'Enable', enable: true, imageNumber: 1}},
    {args: {action: 'Disable', enable: false, imageNumber: 0}},
  ];

  tests.forEach((test, index) => {
    describe(`${test.args.action} product image in invoice then check the invoice file created`, async () => {
      describe(`${test.args.action} product image`, async () => {
        it('should go to \'Orders > Invoices\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToInvoicesPage${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.invoicesLink,
          );

          await invoicesPage.closeSfToolBar(page);

          const pageTitle = await invoicesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(invoicesPage.pageTitle);
        });

        it(`should ${test.args.action} product image`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);

          await invoicesPage.enableProductImage(page, test.args.enable);
          const textMessage = await invoicesPage.saveInvoiceOptions(page);
          await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
        });
      });

      describe('Create new order in FO', async () => {
        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

          // Click on view my shop
          page = await invoicesPage.viewMyShop(page);

          // Change FO language
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage, 'Fail to open FO home page').to.be.true;
        });

        it('should go to login page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToLoginFO${index}`, baseContext);

          await homePage.goToLoginPage(page);
          const pageTitle = await foLoginPage.getPageTitle(page);
          await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
        });

        it('should sign in with default customer', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighInFO${index}`, baseContext);

          await foLoginPage.customerLogin(page, DefaultCustomer);
          const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
        });

        it('should add product to cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

          // Go to home page
          await foLoginPage.goToHomePage(page);

          // Go to the first product page
          await homePage.goToProductPage(page, 1);

          // Add the product to the cart
          await productPage.addProductToTheCart(page);

          const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
          await expect(notificationsNumber).to.be.equal(1);
        });

        it('should go to delivery step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

          // Proceed to checkout the shopping cart
          await cartPage.clickOnProceedToCheckout(page);

          // Address step - Go to delivery step
          const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
          await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
        });

        it('should go to payment step', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
        });

        it('should choose payment method and confirm the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

          // Payment step - Choose payment step
          await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

          // Check the confirmation message
          const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
          await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
        });

        it('should sign out from FO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `sighOutFO${index}`, baseContext);

          await orderConfirmationPage.logout(page);
          const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
          await expect(isCustomerConnected, 'Customer is connected').to.be.false;
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          // Close page and init page objects
          page = await orderConfirmationPage.closePage(browserContext, page, 0);

          const pageTitle = await invoicesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(invoicesPage.pageTitle);
        });
      });

      describe('Generate the invoice and check product image', async () => {
        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

          await invoicesPage.goToSubMenu(
            page,
            invoicesPage.ordersParentLink,
            invoicesPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCreatedOrderPage${index}`, baseContext);

          await ordersPage.goToOrder(page, 1);
          const pageTitle = await viewOrderPage.getPageTitle(page);
          await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
        });

        it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

          const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
          await expect(result).to.equal(Statuses.shipped.status);
        });

        it('should download the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `downloadInvoice${index}`, baseContext);

          // Download invoice
          filePath = await viewOrderPage.downloadInvoice(page);

          const exist = await files.doesFileExist(filePath);
          await expect(exist).to.be.true;
        });

        it('should check the product images in the PDF File', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${index}`, baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber).to.be.equal(test.args.imageNumber);

          await files.deleteFile(filePath);
        });
      });
    });
  });
});
