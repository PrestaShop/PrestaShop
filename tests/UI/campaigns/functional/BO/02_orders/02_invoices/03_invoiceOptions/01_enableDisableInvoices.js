require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test Context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_enableDisableInvoices';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

describe('BO - Orders - Invoices : Enable/Disable invoices', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

      await orderConfirmationPage.logout(page);
      const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  const tests = [
    {
      args: {
        action: 'Disable',
        status: false,
        orderStatus: Statuses.shipped.status,
      },
    },
    {
      args: {
        action: 'Enable',
        status: true,
        orderStatus: Statuses.paymentAccepted.status,
      },
    },
  ];

  tests.forEach((test, index) => {
    describe(`${test.args.action} invoices then check that there is no invoice created`, async () => {
      if (index === 0) {
        it('should login in BO', async function () {
          await loginCommon.loginBO(this, page);
        });
      }

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

      it(`should ${test.args.action} invoices`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Invoices`, baseContext);

        await invoicesPage.enableInvoices(page, test.args.status);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });

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

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

        await ordersPage.goToOrder(page, 1);
        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${test.args.orderStatus}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateStatus${index}`, baseContext);

        const result = await viewOrderPage.modifyOrderStatus(page, test.args.orderStatus);
        await expect(result).to.equal(test.args.orderStatus);
      });

      it('should check that there is no invoice document created', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkInvoiceCreation${index}`, baseContext);

        const documentName = await viewOrderPage.getDocumentType(page);

        if (test.args.status) {
          await expect(documentName).to.be.equal('Invoice');
        } else {
          await expect(documentName).to.be.not.equal('Invoice');
        }
      });
    });
  });
});
