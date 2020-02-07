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
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
const files = require('@utils/files');
// Importing data
const {PaymentMethods} = require('@data/demo/orders');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orders');
const InvoiceOptionFaker = require('@data/faker/invoice');

let browser;
let page;
let invoiceData;
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
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

/*
Edit Invoice number, Legal free text, Footer text
Create order
Change the Order status to Shipped
Check the invoice file name
 */
describe('Edit \'Invoice number, Legal free text, Footer text\' and check the generated invoice file', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
    invoiceData = await (new InvoiceOptionFaker());
  });
  after(async () => {
    // Delete the invoice file
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}.pdf`);
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Edit the Invoice number, Legal free text and Footer text', async () => {
    it('should go to invoices page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.invoicesLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
    });

    it('should change the Invoice number, Legal free text, Footer text', async function () {
      await this.pageObjects.invoicesPage.setInputOptions(invoiceData);
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

  describe('Create an invoice and check the edited data', async () => {
    it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await this.pageObjects.ordersPage.goToOrder(1);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}'`, async function () {
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.be.true;
    });

    it('should download the invoice', async function () {
      fileName = await this.pageObjects.viewOrderPage.getFileName();
      await this.pageObjects.viewOrderPage.downloadInvoice();
      const exist = await files.checkFileExistence(`${fileName}.pdf`);
      await expect(exist).to.be.true;
    });

    it('should check that the invoice file name contain the \'Invoice number\'', async function () {
      fileName = await this.pageObjects.viewOrderPage.getFileName();
      expect(fileName).to.contains(invoiceData.invoiceNumber);
    });

    it('should check that the invoice contain the \'legal free text\'', async () => {
      // Check the existence of the legal free text
      const exist = await files.checkTextInPDF(`${fileName}.pdf`, invoiceData.legalFreeText);
      await expect(exist).to.be.true;
    });

    it('should check that the invoice contain the \'Footer text\'', async () => {
      // Check the existence of the Footer text
      const exist = await files.checkTextInPDF(`${fileName}.pdf`, invoiceData.footerText);
      await expect(exist).to.be.true;
    });
  });
});
