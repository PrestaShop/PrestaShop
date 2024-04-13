// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import deliverySlipsPage from '@pages/BO/orders/deliverySlips';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Products from '@data/demo/products';
import DeliverySlipOptionsData from '@data/faker/deliverySlipOptions';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_deliverySlips_deliverySlipOptions_deliverySlipNumber';

/*
Pre-condition:
- Create order in FO
Scenario:
- Edit Delivery slip number
- Change the Order status to Shipped
- Check the delivery slip file name
 */

describe('BO - Orders - Delivery slips : Update \'Delivery slip number\'', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const orderByCustomerData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 5,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const deliverySlipData: DeliverySlipOptionsData = new DeliverySlipOptionsData();

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

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Update the Delivery slip number', async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPageToUpdateNumber', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.deliverySlipslink,
      );
      await deliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it('should change the Delivery slip number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDeliverySlipsNumber', baseContext);

      await deliverySlipsPage.changeNumber(page, deliverySlipData.number);

      const textMessage = await deliverySlipsPage.saveDeliverySlipOptions(page);
      expect(textMessage).to.contains(deliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe('Create a delivery slip and check the update data', async () => {
    it('should go to the \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await deliverySlipsPage.goToSubMenu(
        page,
        deliverySlipsPage.ordersParentLink,
        deliverySlipsPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check that the delivery slip file name contain the \'Delivery slip number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipsDocumentName', baseContext);

      // Get delivery slips filename
      fileName = await orderPageTabListBlock.getFileName(page, 3);
      expect(fileName).to.contains(deliverySlipData.number);
    });
  });
});
