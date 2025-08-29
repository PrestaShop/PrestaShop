// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {expect} from 'chai';

import {
  boDashboardPage,
  boDeliverySlipsPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  FakerOrderDeliverySlipOptions,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 5,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const deliverySlipData: FakerOrderDeliverySlipOptions = new FakerOrderDeliverySlipOptions();

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

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Update the Delivery slip number', async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPageToUpdateNumber', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.deliverySlipslink,
      );
      await boDeliverySlipsPage.closeSfToolBar(page);

      const pageTitle = await boDeliverySlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDeliverySlipsPage.pageTitle);
    });

    it('should change the Delivery slip number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDeliverySlipsNumber', baseContext);

      await boDeliverySlipsPage.changeNumber(page, deliverySlipData.number);

      const textMessage = await boDeliverySlipsPage.saveDeliverySlipOptions(page);
      expect(textMessage).to.contains(boDeliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe('Create a delivery slip and check the update data', async () => {
    it('should go to the \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDeliverySlipsPage.goToSubMenu(
        page,
        boDeliverySlipsPage.ordersParentLink,
        boDeliverySlipsPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check that the delivery slip file name contain the \'Delivery slip number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipsDocumentName', baseContext);

      // Get delivery slips filename
      fileName = await boOrdersViewBlockTabListPage.getFileName(page, 3);
      expect(fileName).to.contains(deliverySlipData.number);
    });
  });
});
