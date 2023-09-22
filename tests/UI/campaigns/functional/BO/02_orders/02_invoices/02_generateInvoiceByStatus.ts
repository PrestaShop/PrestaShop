// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import invoicesPage from '@pages/BO/orders/invoices';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_invoices_generateInvoiceByStatus';

// 1 : Generate PDF file by status
describe('BO - Orders - Invoices : Generate PDF file by status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Create 2 invoices by changing the order status', async () => {
    [
      {args: {orderRow: 1, status: OrderStatuses.shipped.name}},
      {args: {orderRow: 2, status: OrderStatuses.paymentAccepted.name}},
    ].forEach((orderToEdit, index: number) => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index + 1}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );
        await ordersPage.closeSfToolBar(page);

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it(`should go to the order page n°${orderToEdit.args.orderRow}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index + 1}`, baseContext);

        await ordersPage.goToOrder(page, orderToEdit.args.orderRow);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${orderToEdit.args.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index + 1}`, baseContext);

        const result = await orderPageTabListBlock.modifyOrderStatus(page, orderToEdit.args.status);
        expect(result).to.equal(orderToEdit.args.status);
      });
    });
  });

  describe('Generate invoice by status', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should check the error message when we don\'t select a status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoSelectedStatusMessageError', baseContext);

      // Generate PDF
      const textMessage = await invoicesPage.generatePDFByStatusAndFail(page);
      expect(textMessage).to.equal(invoicesPage.errorMessageWhenNotSelectStatus);
    });

    it('should check the error message when there is no invoice in the status selected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceMessageError', baseContext);

      // Choose one status
      await invoicesPage.chooseStatus(page, OrderStatuses.canceled.name);

      // Generate PDF
      const textMessage = await invoicesPage.generatePDFByStatusAndFail(page);
      expect(textMessage).to.equal(invoicesPage.errorMessageWhenGenerateFileByStatus);
    });

    it('should choose the statuses, generate the invoice and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectStatusesAndCheckInvoiceExistence', baseContext);

      // Choose 2 statuses
      await invoicesPage.chooseStatus(page, OrderStatuses.paymentAccepted.name);
      await invoicesPage.chooseStatus(page, OrderStatuses.shipped.name);

      // Generate PDF
      filePath = await invoicesPage.generatePDFByStatusAndDownload(page);
      expect(filePath).to.not.eq(null);

      // Check that file exist
      if (filePath) {
        const exist = await files.doesFileExist(filePath);
        expect(exist).to.eq(true);
      }
    });

    it('should choose one status, generate the invoice and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectOneStatusAndCheckInvoiceExistence', baseContext);

      // Choose one status
      await invoicesPage.chooseStatus(page, OrderStatuses.paymentAccepted.name);

      // Generate PDF
      filePath = await invoicesPage.generatePDFByStatusAndDownload(page);
      expect(filePath).to.not.eq(null);

      // Check that file exist
      if (filePath) {
        const exist = await files.doesFileExist(filePath);
        expect(exist).to.eq(true);
      }
    });
  });
});
