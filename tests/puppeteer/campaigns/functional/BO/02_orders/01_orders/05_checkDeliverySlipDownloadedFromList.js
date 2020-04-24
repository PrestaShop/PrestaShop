require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
const {Statuses} = require('@data/demo/orderStatuses');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const FOLoginPage = require('@pages/FO/login');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_checkDeliverySlipDownloadedFromList';

let browser;
let page;
let deliverySlipFilename;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    foLoginPage: new FOLoginPage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
Create order in FO with bank wire payment
Go to BO orders page and change order status to 'shipped'
Check delivery slip creation
Download delivery slip from list and check pdf text
 */
describe('Check delivery slip downloaded from list', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${deliverySlipFilename}`);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);
      await this.pageObjects.homePage.goToFo();
      await this.pageObjects.homePage.changeLanguage('en');
      const isHomePage = await this.pageObjects.homePage.isHomePage();
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);
      await this.pageObjects.homePage.goToLoginPage();
      const pageTitle = await this.pageObjects.foLoginPage.getPageTitle();
      await expect(pageTitle, 'Fail to open FO login page').to.contains(this.pageObjects.foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);
      await this.pageObjects.foLoginPage.customerLogin(DefaultAccount);
      const isCustomerConnected = await this.pageObjects.foLoginPage.isCustomerConnected();
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);
      await this.pageObjects.foLoginPage.goToHomePage();
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the created product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      // Address step - Go to delivery step
      const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      // Payment step - Choose payment step
      await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
      const cardTitle = await this.pageObjects.orderConfirmationPage.getOrderConfirmationCardTitle();
      // Check the confirmation message
      await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);
      await this.pageObjects.orderConfirmationPage.logout();
      const isCustomerConnected = await this.pageObjects.orderConfirmationPage.isCustomerConnected();
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Check delivery slip file in BO', async () => {
    // Login into BO
    loginCommon.loginBO();

    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it(`should update order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);
      const textResult = await this.pageObjects.ordersPage.setOrderStatus(1, Statuses.shipped);
      await expect(textResult).to.equal(this.pageObjects.ordersPage.successfulUpdateMessage);
      const orderStatus = await this.pageObjects.ordersPage.getTextColumn('osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.shipped.status);
    });

    it('should download delivery slip', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);
      await this.pageObjects.ordersPage.downloadDeliverySlip(1);
      const doesFileExist = await files.doesFileExist('DE', 5000, true, '.pdf');
      await expect(doesFileExist).to.be.true;
    });

    it('should check delivery slip pdf text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipText', baseContext);
      deliverySlipFilename = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'DE', '.pdf');
      const orderInformation = await this.pageObjects.ordersPage.getOrderFromTable(1);
      // Check Reference in pdf
      const referenceExist = await files.isTextInPDF(deliverySlipFilename, orderInformation.reference);
      await expect(
        referenceExist,
        `Reference '${orderInformation.reference}' does not exist in delivery slip`,
      ).to.be.true;
      // Check country name in delivery Address in pdf
      const deliveryExist = await files.isTextInPDF(deliverySlipFilename, orderInformation.delivery);
      await expect(
        deliveryExist,
        `Country name '${orderInformation.delivery}' does not exist in delivery slip`,
      ).to.be.true;
      // Check customer name in pdf
      const customerExist = await files.isTextInPDF(deliverySlipFilename, orderInformation.customer.slice(3));
      await expect(
        customerExist,
        `Country name '${orderInformation.customer}' does not exist in delivery slip`,
      ).to.be.true;
      // Check total paid in pdf
      const totalPaidExist = await files.isTextInPDF(deliverySlipFilename, orderInformation.totalPaid);
      await expect(
        totalPaidExist,
        `Total paid '${orderInformation.totalPaid}' does not exist in delivery slip`,
      ).to.be.true;
    });
  });
});
