require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const CreditSlipsPage = require('@pages/BO/orders/creditSlips/index');
// Importing data
const {PaymentMethods} = require('@data/demo/orders');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
const today = new Date();
// Create date today (yyy-mm-dd)
const dateToday = `${today.getFullYear()}/${today.getMonth()}/${today.getDay()}`;
let numberOfCreditSlips = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
    creditSlipsPage: new CreditSlipsPage(page),
  };
};

describe('Create, filter and check credit slips file', async () => {
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

  describe('Create 2 credit slips for the same order', async () => {
    it('should go to FO and create an order', async function () {
      // Click on view my shop
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the created product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Edit the product quantity
      await this.pageObjects.cartPage.editProductQuantity(1, 5);
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

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];
    tests.forEach((test) => {
      it('should add a partial refund', async function () {
        await this.pageObjects.viewOrderPage.clickOnPartialRefund();
        const textMessage = await this.pageObjects.viewOrderPage.addPartialRefundProduct(
          test.args.productID,
          test.args.quantity,
        );
        await expect(textMessage).to.contains(this.pageObjects.viewOrderPage.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        const documentName = await this.pageObjects.viewOrderPage.getDocumentName(test.args.documentRow);
        await expect(documentName).to.be.equal('Credit Slip');
      });
    });
  });

  describe('Filter Credit slips', async () => {
    it('should go to Credit slips page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.creditSlips,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.creditSlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      numberOfCreditSlips = await this.pageObjects.creditSlipsPage.resetAndGetNumberOfLines();
      await expect(numberOfCreditSlips).to.be.above(0);
    });

    const tests = [
      {args: {filterBy: 'id_credit_slip', filterValue: 1, columnName: 'id_order_slip'}},
      {args: {filterBy: 'id_order', filterValue: 4, columnName: 'id_order'}},
      {args: {filterBy: 'date_issued_from', filterValue: dateToday, columnName: 'date_issued_from'}},
      {args: {filterBy: 'date_issued_to', filterValue: dateToday, columnName: 'date_issued_to'}},
    ];
    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.creditSlipsPage.filterCreditSlips(
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfCreditSlipsAfterFilter = await this.pageObjects.creditSlipsPage.getNumberOfElementInGrid();
        await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);
        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await this.pageObjects.creditSlipsPage.getTextColumnFromTableCreditSlips(
            i,
            test.args.columnName,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });
    });
  });
});
