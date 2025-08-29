import testContext from '@utils/testContext';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
          await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

          await boLoginPage.goTo(page, global.BO.URL);
          await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDashboardPage.pageTitle);
        });
      }

      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToInvoicesPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.invoicesLink,
        );
        await boInvoicesPage.closeSfToolBar(page);

        const pageTitle = await boInvoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
      });

      it(`should ${test.args.action} invoices`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Invoices`, baseContext);

        await boInvoicesPage.enableInvoices(page, test.args.status);

        const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

        await boInvoicesPage.goToSubMenu(
          page,
          boInvoicesPage.ordersParentLink,
          boInvoicesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it(`should change the order status to '${test.args.orderStatus}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateStatus${index}`, baseContext);

        const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, test.args.orderStatus);
        expect(result).to.equal(test.args.orderStatus);
      });

      it(`should check that there is ${test.args.isInvoiceCreated}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkInvoiceCreation${index}`, baseContext);

        const documentName = await boOrdersViewBlockTabListPage.getDocumentType(page);

        if (test.args.status) {
          expect(documentName).to.be.equal('Invoice');
        } else {
          expect(documentName).to.be.not.equal('Invoice');
        }
      });
    });
  });
});
