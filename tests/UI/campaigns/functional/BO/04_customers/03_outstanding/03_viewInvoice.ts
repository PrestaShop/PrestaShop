// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import pages
import outstandingPage from '@pages/BO/customers/outstanding';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_outstanding_viewInvoice';

/*
Pre-condition:
- Enable B2B
- Create order in FO
- Update order status to payment accepted
Scenario:
- View invoice from the outstanding page
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : View invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used for the last order reference created
  let orderReference: string;
  // Variable used for the temporary invoice file
  let filePath: string|null;
  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

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

  // Pre-condition: Update order status to payment accepted
  describe('PRE-TEST: Update order status to payment accepted', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset filter and get the last reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrder', baseContext);

      await ordersPage.resetFilter(page);

      orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
      await expect(orderReference).to.not.equal(null);
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await ordersPage.setOrderStatus(page, 1, OrderStatuses.paymentAccepted);
      await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
    });

    it('should check that the status is updated successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusBO', baseContext);

      const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
      await expect(orderStatus, 'Order status was not updated').to.equal(OrderStatuses.paymentAccepted.name);
    });
  });

  // 1 - View invoice from the outstanding
  describe('View invoice from the outstanding page', async () => {
    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );
      await outstandingPage.closeSfToolBar(page);

      const pageTitle = await outstandingPage.getPageTitle(page);
      await expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should reset filter and get the last outstanding ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await outstandingPage.resetFilter(page);

      const outstandingId = parseInt(
        await outstandingPage.getTextColumn(page, 'id_invoice', 1),
        10,
      );
      await expect(outstandingId).to.be.at.least(1);
    });

    it('should view the Invoice and check the order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await outstandingPage.viewInvoice(page, 'invoice', 1);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'The file is not existing!').to.be.true;
    });

    it('should check reference in the invoice pdf file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceReference', baseContext);

      // Check Reference in pdf
      const referenceOrder = await files.isTextInPDF(filePath, orderReference);
      await expect(referenceOrder).to.be.true;
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
