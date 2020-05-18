require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const DeliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const files = require('@utils/files');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_deliverSlipsOptions_enableDisableProductImage';

let browser;
let page;
let fileName;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    deliverySlipsPage: new DeliverySlipsPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

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
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

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
          await this.pageObjects.boBasePage.goToSubMenu(
            this.pageObjects.boBasePage.ordersParentLink,
            this.pageObjects.boBasePage.deliverySlipslink,
          );
          await this.pageObjects.boBasePage.closeSfToolBar();
          const pageTitle = await this.pageObjects.deliverySlipsPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.deliverySlipsPage.pageTitle);
        });

        it(`should ${test.args.action} product image`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ProductImage`, baseContext);
          await this.pageObjects.deliverySlipsPage.setEnableProductImage(test.args.enable);
          const textMessage = await this.pageObjects.deliverySlipsPage.saveDeliverySlipOptions();
          await expect(textMessage).to.contains(this.pageObjects.deliverySlipsPage.successfulUpdateMessage);
        });
      });

      describe('Create new order in FO', async () => {
        it('should go to FO and create an order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createOrderInFO_${test.args.action}`, baseContext);
          // Click on view my shop
          page = await this.pageObjects.boBasePage.viewMyShop();
          this.pageObjects = await init();
          await this.pageObjects.foBasePage.changeLanguage('en');
          // Go to the first product page
          await this.pageObjects.homePage.goToProductPage(1);
          // Add the created product to the cart
          await this.pageObjects.productPage.addProductToTheCart();
          // Proceed to checkout the shopping cart
          await this.pageObjects.cartPage.clickOnProceedToCheckout();
          // Checkout the order
          // Personal information step - Login
          await this.pageObjects.checkoutPage.clickOnSignIn();
          await this.pageObjects.checkoutPage.customerLogin(DefaultAccount);
          // Address step - Go to delivery step
          const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
          await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
          // Delivery step - Go to payment step
          const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
          await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
          // Payment step - Choose payment step
          await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
          const cardTitle = await this.pageObjects.orderConfirmationPage
            .getTextContent(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitleH3);
          // Check the confirmation message
          await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
          // Logout from FO
          await this.pageObjects.foBasePage.logout();
          page = await this.pageObjects.orderConfirmationPage.closePage(browser, 1);
          this.pageObjects = await init();
        });
      });

      describe('Generate the delivery slip and check product image', async () => {
        it('should go to the orders page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage_${test.args.action}`, baseContext);
          await this.pageObjects.boBasePage.goToSubMenu(
            this.pageObjects.boBasePage.ordersParentLink,
            this.pageObjects.boBasePage.ordersLink,
          );
          const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `goToCreatedOrderPage_${test.args.action}`,
            baseContext,
          );
          await this.pageObjects.ordersPage.goToOrder(1);
          const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
        });

        it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `updateOrderStatus_${test.args.action}`,
            baseContext,
          );
          const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
          await expect(result).to.equal(Statuses.shipped.status);
        });

        it('should download the delivery slip', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `downloadDeliverySlips_${test.args.action}`,
            baseContext,
          );
          fileName = await this.pageObjects.viewOrderPage.getFileName(3);
          await this.pageObjects.viewOrderPage.downloadDeliverySlip();
          const exist = await files.doesFileExist(`${fileName}.pdf`);
          await expect(exist).to.be.true;
        });

        it('should check the product images in the PDF File', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkProductImage_${test.args.action}`,
            baseContext,
          );
          const imageNumber = await files.getImageNumberInPDF(
            `${fileName}.pdf`,
          );
          await expect(imageNumber).to.be.equal(test.args.imageNumber);
          await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}.pdf`);
        });
      });
    });
  });
});
