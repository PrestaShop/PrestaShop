// Import utils
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import ordersPage from '@pages/BO/orders';
import invoicesPage from '@pages/BO/orders/invoices';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

import {
  boDashboardPage,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_enableDisableInvoices';

/*
Pre-condition:
- Create order in FO
Scenario:
- Enable/Disable invoices then check the invoice in view order page
 */
describe('BO - Orders - Invoices : Enable/Disable invoices', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  [
    {
      args: {
        action: 'Disable',
        status: false,
        orderStatus: dataOrderStatuses.shipped.name,
        isInvoiceCreated: 'no invoice document created',
      },
    },
    {
      args: {
        action: 'Enable',
        status: true,
        orderStatus: dataOrderStatuses.paymentAccepted.name,
        isInvoiceCreated: 'an invoice document created',
      },
    },
  ].forEach((test, index: number) => {
    describe(`${test.args.action} invoices then check that there is ${test.args.isInvoiceCreated}`, async () => {
      if (index === 0) {
        it('should login in BO', async function () {
          await loginCommon.loginBO(this, page);
        });
      }

      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToInvoicesPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.invoicesLink,
        );
        await invoicesPage.closeSfToolBar(page);

        const pageTitle = await invoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it(`should ${test.args.action} invoices`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Invoices`, baseContext);

        await invoicesPage.enableInvoices(page, test.args.status);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${test.args.orderStatus}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateStatus${index}`, baseContext);

        const result = await orderPageTabListBlock.modifyOrderStatus(page, test.args.orderStatus);
        expect(result).to.equal(test.args.orderStatus);
      });

      it(`should check that there is ${test.args.isInvoiceCreated}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkInvoiceCreation${index}`, baseContext);

        const documentName = await orderPageTabListBlock.getDocumentType(page);

        if (test.args.status) {
          expect(documentName).to.be.equal('Invoice');
        } else {
          expect(documentName).to.be.not.equal('Invoice');
        }
      });
    });
  });
});
