// Import utils
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
import InvoiceData from '@data/faker/invoice';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_invoicePrefix';

/*
Edit invoice prefix
Change the Order status to Shipped
Check the invoice file name
Back to the default invoice prefix value
Check the invoice file name
 */
describe('BO - Orders - Invoices : Update invoice prefix and check the generated invoice file name', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const invoiceData: InvoiceData = new InvoiceData();
  const defaultPrefix: string = '#IN';

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

  describe('Update the invoice prefix', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToUpdatePrefix', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.invoicesLink,
      );
      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it(`should update the invoice prefix to ${invoiceData.prefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateInvoicePrefix', baseContext);

      await invoicesPage.changePrefix(page, invoiceData.prefix);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  describe('Update the order status and check the invoice file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'UpdateStatusForUpdatedPrefix', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it(`should check that the invoice file name contain the prefix '${invoiceData.prefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await orderPageTabListBlock.getFileName(page);
      expect(fileName).to.contains(invoiceData.prefix.replace('#', '').trim());
    });
  });

  describe('Back to the default invoice prefix value', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageForDefaultPrefix', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it(`should change the invoice prefix to '${defaultPrefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultPrefix', baseContext);

      await invoicesPage.changePrefix(page, defaultPrefix);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  describe('Check the default prefix in the invoice file Name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForDefaultPrefix', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForDefaultPrefix', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should check that the invoice file name contain the default prefix ${defaultPrefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultPrefixInInvoice', baseContext);

      // Get invoice file name
      fileName = await orderPageTabListBlock.getFileName(page);
      expect(fileName).to.contains(defaultPrefix.replace('#', '').trim());
    });
  });
});
