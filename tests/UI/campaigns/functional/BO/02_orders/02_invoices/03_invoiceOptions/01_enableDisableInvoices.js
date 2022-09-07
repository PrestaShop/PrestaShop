require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

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
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create order in FO
Scenario:
- Enable/Disable invoices then check the invoice in view order page
 */
describe('BO - Orders - Invoices : Enable/Disable invoices', async () => {
  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  const tests = [
    {
      args: {
        action: 'Disable',
        status: false,
        orderStatus: Statuses.shipped.status,
        isInvoiceCreated: 'no invoice document created',
      },
    },
    {
      args: {
        action: 'Enable',
        status: true,
        orderStatus: Statuses.paymentAccepted.status,
        isInvoiceCreated: 'an invoice document created',
      },
    },
  ];

  tests.forEach((test, index) => {
    describe(`${test.args.action} invoices then check that there is ${test.args.isInvoiceCreated}`, async () => {
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
        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${test.args.orderStatus}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateStatus${index}`, baseContext);

        const result = await orderPageTabListBlock.modifyOrderStatus(page, test.args.orderStatus);
        await expect(result).to.equal(test.args.orderStatus);
      });

      it(`should check that there is ${test.args.isInvoiceCreated}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkInvoiceCreation${index}`, baseContext);

        const documentName = await orderPageTabListBlock.getDocumentType(page);

        if (test.args.status) {
          await expect(documentName).to.be.equal('Invoice');
        } else {
          await expect(documentName).to.be.not.equal('Invoice');
        }
      });
    });
  });
});
