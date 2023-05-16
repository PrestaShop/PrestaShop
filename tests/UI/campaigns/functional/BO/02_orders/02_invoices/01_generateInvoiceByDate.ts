// Import utils
import date from '@utils/date';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
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

const baseContext: string = 'functional_BO_orders_invoices_generateInvoiceByDate';

// Generate PDF file by date
describe('BO - Orders - Invoices : Generate PDF file by date', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  const todayDate: string = date.getDateFormat('yyyy-mm-dd');
  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');

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

  describe('Create an invoice by changing the first order status', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters and get number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      await expect(result).to.equal(OrderStatuses.shipped.name);
    });
  });

  describe('Generate invoice by date', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGeneratedInvoicesPdfFile', baseContext);

      // Generate PDF
      filePath = await invoicesPage.generatePDFByDateAndDownload(page, todayDate, todayDate);

      const exist = await files.doesFileExist(filePath);
      await expect(exist, 'File does not exist').to.be.true;
    });

    it('should check the error message when there is no invoice in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentInvoice', baseContext);

      // Generate PDF
      const textMessage = await invoicesPage.generatePDFByDateAndFail(page, futureDate, futureDate);

      await expect(textMessage).to.equal(invoicesPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
