// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_changeStatus';

/*
Pre-condition :
- Create order in FO
Scenario :
- View first order page
- Select same status and check result
- Select 'Payment accepted' status and check result
- Select 'Shipped' status and check result
 */
describe('BO - Orders - View and edit order : Change order status', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-Condition : Create order from FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change the order status and check result', async () => {
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
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it('should check that \'Update status button\' is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatUpdateStatusButtonIsDisabled1', baseContext);

      const isButtonDisabled = await orderPageTabListBlock.isUpdateStatusButtonDisabled(page);
      expect(isButtonDisabled, 'Update status button is not disabled!').to.eq(true);
    });

    it('should check that \'Partial refund\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton1', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should check that \'View invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton1', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should select the same status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSameStatus', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(dataOrderStatuses.awaitingBankWire.name);

      await orderPageTabListBlock.selectOrderStatus(page, actualStatus);
    });

    it('should check that \'Update status button\' still disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatUpdateStatusButtonIsDisabled2', baseContext);

      const isButtonDisabled = await orderPageTabListBlock.isUpdateStatusButtonDisabled(page);
      expect(isButtonDisabled, 'Update status button is not disabled!').to.eq(true);
    });

    it('should check that \'Partial refund\' button still not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton2', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should check that \'View invoice\' button still not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton2', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton3', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it(`should select the status '${dataOrderStatuses.canceled.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCanceledStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.canceled.name);
      expect(result).to.equal(dataOrderStatuses.canceled.name);
    });

    it('should check that the statuses number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber1', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      expect(statusesNumber).to.be.equal(2);
    });

    it(`should check that the actual status is '${dataOrderStatuses.canceled.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(dataOrderStatuses.canceled.name);
    });

    it('should check that \'Partial refund\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton3', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should check that \'View invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton4', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton5', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it(`should select the status '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectPaymentAcceptedStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(result).to.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should check that the statuses number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber2', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      expect(statusesNumber).to.be.equal(3);
    });

    it(`should check that the actual status is '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus2', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should check that \'Partial refund\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton4', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isPartialRefundButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is not visible!').to.eq(true);
    });

    it('should check that \'View invoice\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton6', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isViewInvoiceButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is not visible!').to.eq(true);
    });

    it('should check that \'View delivery slip\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewInvoiceButton7', baseContext);

      const isButtonVisible = await orderPageTabListBlock.isDeliverySlipButtonVisible(page);
      expect(isButtonVisible, 'Partial refund button is visible!').to.eq(false);
    });

    it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

      filePath = await orderPageTabListBlock.viewInvoice(page);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.eq(true);
    });

    it(`should select the status '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectShippedStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check that the statuses number is equal to 4', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusesNumber3', baseContext);

      const statusesNumber = await orderPageTabListBlock.getStatusesNumber(page);
      expect(statusesNumber).to.be.equal(4);
    });

    it(`should check that the actual status is '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkActualStatus3', baseContext);

      const actualStatus = await orderPageTabListBlock.getOrderStatus(page);
      expect(actualStatus).to.be.equal(dataOrderStatuses.shipped.name);
    });

    it('should click on \'View delivery slip\' button and check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice2', baseContext);

      filePath = await orderPageTabListBlock.viewDeliverySlip(page);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.eq(true);
    });
  });
});
