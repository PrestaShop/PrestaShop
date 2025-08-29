import testContext from '@utils/testContext';
import {expect} from 'chai';

import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBasePage,
  boOutstandingPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_outstanding_viewOrder';

/*
Pre-condition:
- Enable B2B
- Create order in FO
- Update order status to payment accepted
Scenario:
- View order from the outstanding page
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : View order', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used for the last order ID created
  let orderId: number;
  // Variable used for the last order reference created
  let orderReference: string;

  // New order by customer data
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

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

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

  // Pre-condition: Update order status to payment accepted
  describe('PRE-TEST: Update order status to payment accepted', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset filter and get the last orderID and reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrder', baseContext);

      await boOrdersPage.resetFilter(page);

      orderId = parseInt(
        await boOrdersPage.getTextColumn(page, 'id_order', 1),
        10,
      );
      expect(orderId).to.be.at.least(1);

      orderReference = await boOrdersPage.getTextColumn(page, 'reference', 1);
      expect(orderReference).to.not.equal('');
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.paymentAccepted);
      expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
    });

    it('should check that the status is updated successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusBO', baseContext);

      const orderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
      expect(orderStatus, 'Order status was not updated').to.equal(dataOrderStatuses.paymentAccepted.name);
    });
  });

  // 1 - View order from the outstanding page
  describe('View order from the outstanding page', async () => {
    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.outstandingLink,
      );
      await boOutstandingPage.closeSfToolBar(page);

      const pageTitle = await boOutstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOutstandingPage.pageTitle);
    });

    it('should reset filter and get the last outstanding ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await boOutstandingPage.resetFilter(page);

      const outstandingId = parseInt(
        await boOutstandingPage.getTextColumn(page, 'id_invoice', 1),
        10,
      );
      expect(outstandingId).to.be.at.least(1);
    });

    it('should view the Order and check the orderID and the reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrder', baseContext);

      await boOutstandingPage.viewOrder(page, 'actions', 1);

      const outstandingOrderId = await boOrdersViewBasePage.getOrderID(page);
      const outstandingOrderReference = await boOrdersViewBasePage.getOrderReference(page);

      [
        {args: {columnName: outstandingOrderId, result: orderId}},
        {args: {columnName: outstandingOrderReference, result: orderReference}},
      ].forEach((test) => {
        expect(test.args.columnName).to.be.equal(test.args.result);
      });
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
