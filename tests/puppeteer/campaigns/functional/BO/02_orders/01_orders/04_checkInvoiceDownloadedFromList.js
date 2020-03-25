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
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_checkInvoiceDownloadedFromList';

let browser;
let page;
let invoiceFilename;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
Create order in FO win bank wire payment
Go to BO orders page and change order status to 'payment accepted'
Check invoice creation
Download invoice from list and check pdf text
 */
describe('Check invoice downloaded from list', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${invoiceFilename}`);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Create an order in FO', async () => {
    it('should go to FO and create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderInFO', baseContext);
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

  describe('Check invoice file in BO', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it(`should update order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);
      const textResult = await this.pageObjects.ordersPage.setOrderStatus(1, Statuses.paymentAccepted);
      await expect(textResult).to.equal(this.pageObjects.ordersPage.successfulUpdateMessage);
      const orderStatus = await this.pageObjects.ordersPage.getTextColumn('osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
    });

    it('should download invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);
      await this.pageObjects.ordersPage.downloadInvoice(1);
      const fileExist = await files.checkFileExistence('IN', 5000, true, '.pdf');
      await expect(fileExist).to.be.true;
    });

    it('should check invoice pdf text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceText', baseContext);
      invoiceFilename = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'IN', '.pdf');
      const orderInformation = await this.pageObjects.ordersPage.getOrderFromTable(1);
      // Check Reference in pdf
      const referenceExist = await files.checkTextInPDF(invoiceFilename, orderInformation.reference);
      await expect(referenceExist, `Reference '${orderInformation.reference}' does not exist in invoice`).to.be.true;
      // Check country name in delivery Address in pdf
      const deliveryExist = await files.checkTextInPDF(invoiceFilename, orderInformation.delivery);
      await expect(deliveryExist, `Country name '${orderInformation.delivery}' does not exist in invoice`).to.be.true;
      // Check customer name in pdf
      const customerExist = await files.checkTextInPDF(invoiceFilename, orderInformation.customer.slice(3));
      await expect(customerExist, `Country name '${orderInformation.customer}' does not exist in invoice`).to.be.true;
      // Check total paid in pdf
      const totalPaidExist = await files.checkTextInPDF(invoiceFilename, orderInformation.totalPaid);
      await expect(totalPaidExist, `Total paid '${orderInformation.totalPaid}' does not exist in invoice`).to.be.true;
    });
  });
});
