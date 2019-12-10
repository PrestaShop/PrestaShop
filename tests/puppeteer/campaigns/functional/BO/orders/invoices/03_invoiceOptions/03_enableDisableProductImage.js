require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
const files = require('@utils/files');
// Importing data
const {PaymentMethods} = require('@data/demo/orders');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
let fileName;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
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
Enable product image in invoice
Create order
Create invoice
Check that there is 2 images in the invoice (Logo and product image)
Disable product image in invoice
Create order
Create invoice
Check that there is 1 image in the invoice (Logo
 */
describe('Test enable/disable product image in invoices', async () => {
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
    describe(`${test.args.action} product image in invoice then check the invoice file created`, async () => {
      describe(`${test.args.action} product image`, async () => {
        it('should go to invoices page', async function () {
          await this.pageObjects.boBasePage.goToSubMenu(
            this.pageObjects.boBasePage.ordersParentLink,
            this.pageObjects.boBasePage.invoicesLink,
          );
          await this.pageObjects.boBasePage.closeSfToolBar();
          const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
        });

        it(`should ${test.args.action} product image`, async function () {
          await this.pageObjects.invoicesPage.enableProductImage(test.args.enable);
          const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
          await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
        });
      });

      describe('Create new order in FO', async () => {
        it('should go to FO and create an order', async function () {
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

      describe('Generate the invoice and check product image', async () => {
        it('should go to the orders page', async function () {
          await this.pageObjects.boBasePage.goToSubMenu(
            this.pageObjects.boBasePage.ordersParentLink,
            this.pageObjects.boBasePage.ordersLink,
          );
          const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
        });

        it('should go to the created order page', async function () {
          await this.pageObjects.ordersPage.goToOrder(1);
          const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
        });

        it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
          const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
          await expect(result).to.be.true;
        });

        it('should download the invoice', async function () {
          fileName = await this.pageObjects.viewOrderPage.getFileName();
          await this.pageObjects.viewOrderPage.downloadInvoice();
          const exist = await files.checkFileExistence(
            global.BO.DOWNLOAD_PATH,
            `${fileName}.pdf`,
          );
          await expect(exist).to.be.true;
        });

        it('should check the product images in the PDF File', async () => {
          const imageNumber = await files.getImageNumberInPDF(
            global.BO.DOWNLOAD_PATH,
            `${fileName}.pdf`,
          );
          await expect(imageNumber).to.be.equal(test.args.imageNumber);
          files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}.pdf`);
        });
      });
    });
  });
});
